APIs
=======================
/create-deposit
Purpose: This endpoint creates a bank deposit record and optionally reduces outstanding office debt.

Key Components:

Input validation - Ensures required fields (deposit_type, office, amount, date) are present
Reference number check - Prevents duplicate reference numbers via database lookup
Debt determination - Sets debt=true if deposit date is in a past month OR if deposit_type==0 (likely "cash deposit" type)
Database insert - Creates record in deposits table
Debt adjustment - If debt=true, reduces outstanding_amount in office_debts table using GREATEST(0, ...) to prevent negative values