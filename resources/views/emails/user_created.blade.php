<!DOCTYPE html>
<html>
<head>
    <title>Account Created</title>
</head>
<body>
    <p>Dear {{ $user->name }},</p>
    <p>Your account has been created successfully. Here are your login details:</p>
    <p>Email: {{ $user->email }}</p>
    <p>Password: {{ $password }}</p>
    <p>Please change your password after your first login.</p>
    <p>Regards,<br>Your Company Name</p>
</body>
</html>
