Run in Server SSH
---Poilies Page Update
php artisan migrate --path=database/migrations/2025_11_19_144018_create_user_policy_responses_table.php

// if (in_array($user->role->role_id, [3,4,6])) {
//     $offices = Office::where('id', $user->office_id)->get();
// } 

// elseif ($user->role->role_id == 6) {
//     $offices = Office::where('province_id', $user->province_id)->get();
// } else {
//     $offices = collect();
// }