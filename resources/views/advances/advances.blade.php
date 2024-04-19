<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advances</title>
</head>
<body>
    <div>
        <h1>Salary Advances</h1>
        <ul>
            <li><a href="{{ route('advances.apply') }}">Apply for Advance</a></li>
            <li><a href="{{ route('advances.my_advances') }}">My Advances</a></li>
            <li><a href="{{ route('advances.pending_approvals') }}">Pending Approvals</a></li>
            <li><a href="{{ route('advances.closed_approvals') }}">Closed Advances</a></li>
        </ul>
    </div>
    <!----->
    <div>
        @yield('content')
    </div>
</body>
</html>