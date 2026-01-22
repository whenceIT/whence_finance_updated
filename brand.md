
1. Current Cash Balance (Closing Balance)
This is calculated as: opening_balance + net_change. The query below computes it directly.

SELECT 
    (gl.cash_balance + 
     COALESCE((SELECT SUM(at.amount_paid) 
               FROM advance_transactions at 
               JOIN advances a ON at.advance_id = a.id 
               WHERE a.office_id = gl.office_id 
               AND at.last_update_date BETWEEN @start_limit_date AND @todays_date), 0) +
     COALESCE((SELECT SUM(credit) 
               FROM loan_transactions 
               WHERE office_id = gl.office_id 
               AND transaction_type = 'repayment' 
               AND payment_apply_to = 'full_payment' 
               AND date BETWEEN @start_limit_date AND @todays_date), 0) +
     COALESCE((SELECT SUM(credit) 
               FROM loan_transactions 
               WHERE office_id = gl.office_id 
               AND payment_apply_to = 'reloan_payment' 
               AND date BETWEEN @start_limit_date AND @todays_date), 0) +
     COALESCE((SELECT SUM(credit) 
               FROM loan_transactions 
               WHERE office_id = gl.office_id 
               AND payment_apply_to = 'part_payment' 
               AND date BETWEEN @start_limit_date AND @todays_date), 0) +
     COALESCE(gl.total_income, 0) -
     COALESCE((SELECT SUM(amount) 
               FROM advances 
               WHERE office_id = gl.office_id 
               AND status IN ('approved', 'closed') 
               AND date_approved BETWEEN @start_limit_date AND @todays_date), 0) -
     COALESCE((SELECT SUM(amount) 
               FROM expenses 
               WHERE office_id = gl.office_id 
               AND date BETWEEN @start_limit_date AND @todays_date), 0) -
     COALESCE((SELECT SUM(debit) 
               FROM loan_transactions 
               WHERE office_id = gl.office_id 
               AND transaction_type = 'disbursement' 
               AND date BETWEEN @start_limit_date AND @todays_date), 0)
    ) AS closing_balance,
    o.name AS office_name
FROM general_ledgers gl
JOIN offices o ON gl.office_id = o.id
WHERE gl.office_id = @office_id
LIMIT 1;
2. Total Income
SELECT gl.total_income, o.name AS office_name
FROM general_ledgers gl
JOIN offices o ON gl.office_id = o.id
WHERE gl.office_id = @office_id
LIMIT 1;
3. Total Advances
SELECT a.id, a.first_name, a.last_name, a.amount, a.date_approved, a.last_update_date, o.name AS office_name
FROM advances a
JOIN offices o ON a.office_id = o.id
WHERE a.office_id = @office_id 
  AND a.status IN ('approved', 'closed') 
  AND a.last_update_date BETWEEN @start_date AND @end_date;
4. Advance Installments Paid
SELECT at.id, at.advance_id, at.amount_paid, at.last_update_date, a.first_name, a.last_name, o.name AS office_name
FROM advance_transactions at
JOIN advances a ON at.advance_id = a.id
JOIN offices o ON a.office_id = o.id
WHERE a.office_id = @office_id 
  AND at.last_update_date BETWEEN @start_date AND @end_date;
5. Total Expenses
SELECT e.id, e.expense_type, e.name, e.amount, e.date, o.name AS office_name
FROM expenses e
JOIN offices o ON e.office_id = o.id
WHERE e.office_id = @office_id 
  AND e.date BETWEEN @start_date AND @end_date;
6. Total Full Payments
SELECT lt.id, lt.loan_id, lt.client_id, lt.credit, lt.date, c.first_name, c.last_name, o.name AS office_name
FROM loan_transactions lt
JOIN clients c ON lt.client_id = c.id
JOIN offices o ON lt.office_id = o.id
WHERE lt.office_id = @office_id 
  AND lt.transaction_type = 'repayment' 
  AND lt.payment_apply_to = 'full_payment' 
  AND lt.date BETWEEN @start_date AND @end_date;
7. Total Reloan Payments
SELECT lt.id, lt.loan_id, lt.client_id, lt.credit, lt.date, c.first_name, c.last_name, o.name AS office_name
FROM loan_transactions lt
JOIN clients c ON lt.client_id = c.id
JOIN offices o ON lt.office_id = o.id
WHERE lt.office_id = @office_id 
  AND lt.payment_apply_to = 'reloan_payment' 
  AND lt.date BETWEEN @start_date AND @end_date;
8. Total Part Payments
SELECT lt.id, lt.loan_id, lt.client_id, lt.credit, lt.date, c.first_name, c.last_name, o.name AS office_name
FROM loan_transactions lt
JOIN clients c ON lt.client_id = c.id
JOIN offices o ON lt.office_id = o.id
WHERE lt.office_id = @office_id 
  AND lt.payment_apply_to = 'part_payment' 
  AND lt.date BETWEEN @start_date AND @end_date;
9. Total New Loans
SELECT lt.id, lt.loan_id, lt.client_id, lt.debit, lt.date, c.first_name, c.last_name, o.name AS office_name
FROM loan_transactions lt
JOIN clients c ON lt.client_id = c.id
JOIN offices o ON lt.office_id = o.id
WHERE lt.office_id = @office_id 
  AND lt.transaction_type = 'disbursement' 
  AND lt.date BETWEEN @start_date AND @end_date;