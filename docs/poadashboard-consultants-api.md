# POA Dashboard — Consultants Performance API

**View:** `resources/views/user/poadashboard.blade.php`

---

## Endpoint

```
GET https://lms2backend.whencefinancesystem.com/consultants-performance-by-office
```

### Query Parameters

| Parameter    | Type   | Required | Description                                 |
|-------------|--------|----------|---------------------------------------------|
| `office_id`  | int    | Yes      | The branch/office ID to fetch consultants for |
| `start_date` | string | Yes      | Period start date — format `YYYY-MM-DD`     |
| `end_date`   | string | Yes      | Period end date — format `YYYY-MM-DD`       |

### Example Request

```
GET https://lms2backend.whencefinancesystem.com/consultants-performance-by-office?office_id=5&start_date=2026-10-01&end_date=2026-10-31
```

---

## When It's Called

The request is fired client-side (via `fetch`) when the user clicks the yellow **expand** button on a branch row in the Provincial Performance table. Consultants are loaded lazily — only once per branch, on first expand.

---

## Response Structure

```json
{
    "data": [
        {
            "user_id": 42,
            "name": "Jane Doe",
            "total_uncollected": 15000.00,
            "total_collected": 12000.00,
            "still_uncollected": 3000.00,
            "given_out": 25000.00,
            "carry_over": 2000.00,
            "pdua": 0.12,
            "target_met_current": 1,
            "target_history": [1, 0, 1, 1, 0],
            "cycle_end_on": "2026-10-31"
        }
    ]
}
```

### Top-Level

| Field  | Type  | Description                          |
|--------|-------|--------------------------------------|
| `data` | array | Array of consultant performance objects |

### Consultant Object (`data[]`)

| Field                | Type         | Description                                                                 |
|---------------------|--------------|-----------------------------------------------------------------------------|
| `user_id`            | int          | Internal user ID — used to link to `/user/{id}/staff_info`                 |
| `name`               | string       | Consultant's full name                                                       |
| `total_uncollected`  | float        | Opening uncollected balance at cycle start                                  |
| `total_collected`    | float        | Total amount collected during the cycle                                     |
| `still_uncollected`  | float        | Remaining uncollected (`total_uncollected - total_collected`)               |
| `given_out`          | float        | Total loans given out during the cycle                                      |
| `carry_over`         | float        | Amount carried over from previous cycle                                     |
| `pdua`               | float        | Past Due Amount ratio as a decimal — multiply by 100 for percentage display |
| `target_met_current` | int          | `1` = target met for current cycle, `0` = not met                          |
| `target_history`     | array of int | Historical target results — newest first. `1` = met, `0` = missed          |
| `cycle_end_on`       | string       | Cycle end date — format `YYYY-MM-DD`                                        |

---

## How the View Uses the Response

### Banding by `given_out`

Consultants are grouped into bands based on their `given_out` value:

| Band Label    | Range                    |
|--------------|--------------------------|
| Below 10,000 | 0 – 9,999.99             |
| 10,000       | 10,000 – 19,999.99       |
| 20,000       | 20,000 – 29,999.99       |
| 30,000       | 30,000 – 39,999.99       |
| 40,000+      | 40,000 and above         |

Bands with no consultants are skipped.

### Target History Circles

Each value in `target_history` is rendered as a coloured dot:

| Value | Colour  | Meaning     |
|-------|---------|-------------|
| `1`   | Green   | Target met  |
| `0`   | Red     | Target missed |
| other | Grey    | No data     |

If the history array has fewer than 5 entries, the remaining dots are padded with grey.

### Target Met Badge

| `target_met_current` | Badge             |
|---------------------|-------------------|
| `1`                 | `Met` (green)     |
| `0`                 | `Not Met` (grey)  |

---

## Error Handling

If the request fails or the network throws an error, the consultant panel shows:

```
Failed to load consultant data.
```

The full error is logged to the browser console via `console.error`.
