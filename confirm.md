# Understanding confirmed — Branch Deposit Audit full-scope

## What was delivered across all changes

### 1. Controller — `RiskController.php`
- `branchDepositAudit()` passes three new variables to the blade:
  - `$debtCards` — accumulated / paid / balance from `office_debts` table
  - `$depositCardStats` — per-deposit-type requirement vs received (one card per type)
  - `$depositCardTotals` — grand-total across all types
- `getDepositDateRange('overall')` returns `[null, "28th of previous month"]` instead of `[null, null]`
- All non-overall periods are ceiling-capped so date filters never extend beyond the most recently processed month
  - Ceiling = 28th of current month if today's day >= 28, else 28th of last month
  - `min(oldEnd, $ceiling)` is applied to every period end before it is used
- `officeDebtsByDebtType()` returns debt records **grouped by office** (one row per branch) with a 12-month inline grid showing which months carry debt
- A new `--office` query parameter can be added to the debt endpoint URL (filtering handled client-side via JS)

### 2. Service — `OfficeDebtService.php`
- `runMonthlyCheck(?Carbon $asOfDate = null, ?int $officeId = null)` — optional `$officeId` scopes the run to a single branch; leave `null` to process all active offices
- `pastMonthsWithDeadlinePassed()` scans **only the current year**, stopping at January — no prior years are touched
- `firstOrCreate()` on keys `(office_id, deposit_type_id, debt_month, debt_year)` makes every re-run idempotent:
  - Row doesn't exist → created, `created++`
  - Row exists → shortfall is **accumulated** (added) to `original_amount` / `outstanding_amount`, `updated++`
  - No duplicate rows can ever be created

### 3. Artisan command — `GenerateMonthlyOfficeDebts.php`
- `debts:generate-monthly [--office=ID] [--as-of=YYYY-MM-DD] [--dry-run]`
- Existing flags unchanged; `--office` is new and optional

### 4. Blade — `branch-deposit-audit.blade.php`
- **"Deposit Compliance" section**: each deposit type has a card showing **Required** (monthly target × offices × months-spanned-by-filter), **Received**, and **Balance**
  - **Required is live and correct** across periods; overall = Jan→last complete month of this year
- An info-circle (ℹ) tooltip explains how each period maps to the months-count used in Required
- **Outstanding Branch Debt card**: pulls `$debtCards`
- **Debt Repayment (`da-body-debt`)**: each office row is clickable; expanding a row shows the full per-office debt breakdown inline, contracting on second click
  - Toggle indicator `▶` rotates to `▼` on expand; click any row to collapse
  - Sub-row uses `colspan="6"` to span the full table width
  - `toggleDebtDetail` exposed on `window` so inline `onclick` handlers can resolve it from global scope
- Row CSS states: `.da-alert` (highest outstanding, pulsing red), `.da-row-warn` (partial debt, amber), `.da-row-zero` (cleared, subtle red)

### Key rules applied
- **No future dates**: all date helpers cap at or before the most recent 28th-processed month; periods whose natural end extends past `today` are trimmed to the hard ceiling
- **Overall period**: bounded at the 28th of the last completed month — current (partial) month is excluded
- **No duplicates on re-run**: `firstOrCreate` on the service's business key guarantees safe re-runs; shortfall is accumulated, not reset
- **Office filter**: new `#da-office-select` dropdown in the filter bar — choosing an office only reveals offices in debt for that branch; the sub-row breakdown (per debt month) is filtered accordingly
