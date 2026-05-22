# Understanding confirmed — Branch Deposit Audit (Debt Repayment section)

## What was delivered across all changes

### 1. Controller — `RiskController.php`
- `branchDepositAudit()` passes three new variables to the blade:
  - `$debtCards` — accumulated / paid / balance from `office_debts` table
  - `$depositCardStats` — per-deposit-type requirement vs received (one card per type)
  - `$depositCardTotals` — grand-total across all types
- `getDepositDateRange('overall')` now returns `[null, "28th of previous month"]` instead of `[null, null]`
- All non-overall periods are ceiling-capped so date filters never extend beyond the most recently processed month
- `officeDebtsByDebtType()` returns debt records **grouped by office** (one row per branch) with a 12-month inline grid showing which months carry debt

### 2. Service — `OfficeDebtService.php`
- `runMonthlyCheck(?Carbon $asOfDate = null, ?int $officeId = null)` — optional `$officeId` scopes the run to a single branch; leave `null` to process all active offices
- `pastMonthsWithDeadlinePassed()` scans only the **current-year** months back to January — no prior years are touched
- `firstOrCreate()` on keys `(office_id, deposit_type_id, debt_month, debt_year)` makes every re-run idempotent — no duplicate rows, and shortfall is accumulated (added) to `original_amount` / `outstanding_amount` on every subsequent run

### 3. Artisan command — `GenerateMonthlyOfficeDebts.php`
- `debts:generate-monthly [--office=ID] [--as-of=YYYY-MM-DD] [--dry-run]`
- Existing flags unchanged; `--office` is new and optional

### 4. Blade — `branch-deposit-audit.blade.php`
- "Deposit Compliance" section: each deposit type has a card showing **Required** (fixed monthly target × offices, always with period-appropriate months-span), **Received**, and **Balance** — the months-span in Required is derived from the active date-period filter
- An info-circle `(ℹ)` tooltip on the section heading explains how periods map to months-count in Required
- Outstanding Branch Debt card: pulls `$debtCards`
- Debt Repayment (`da-body-debt`): each office row is clickable; expanding a row shows the full per-office debt breakdown inline (contracts back on second click)

### Key rules applied
- **"Overall" period**: bounded at the 28th of the last completed month — current (partial) month is excluded
- **No future dates**: all date helpers cap at or before `today`; periods whose natural end extends past `today` are trimmed to the hard ceiling
- **No duplicates**: `firstOrCreate` on the service's business key guarantees safe re-runs
