role == 1 (Admin sees all)
role == 3 (loan officers - sees only data(clients and loans etc) under their office (user->office_id))
role == 4 (Branch Manager sees only all data(clients and loans etc) under his office (user->office_id))
role == 6 (Provincial Manager sees only all data(clients and loans etc) under his provice  (user->province_id) )

Teachers are user->istrainer == 1

create a popup modal that does not close until



create a table called notifix (Model) this table like a notification table that store data in a json object

user_id, positions(object of ids), note (object column taking all details about the notification) (user should only have one record of the notifix, the object is the one thats update whenever there is a new notification)
the 'note' object:
-id(key)
-loan_id 
-from_id
-link_from
-link_to
-type
-message
-positions(array)
-created_date

create a Service i will be calling in the controllers to record into this notifix table call it NotifixService (it should have create, delete, get all, getMyNotix private functions)