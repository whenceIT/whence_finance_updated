role == 1 (Admin sees all)
role == 3 (loan officers - sees only data(clients and loans etc) under their office (user->office_id))
role == 4 (Branch Manager sees only all data(clients and loans etc) under his office (user->office_id))

role == 12 (DM Manager sees only all data(clients->office->district_id and loan_transactions->office->district_id loans->office->district_id etc) under his district_id (user->office->district_id))
role == 6 (Provincial Manager sees only all data(clients and loans etc) under his provice  (user->province_id) )

