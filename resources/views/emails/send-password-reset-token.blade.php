<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reset password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body style="">
<div style="background-color: #f3f4f6; padding: 30px; position: relative; font-family: Rubik; color: black">
    <div style="padding: 40px; background-color: white; box-shadow: 0 0 8px 1px #e5e7eb; border-radius: 4px;">
        <h2>{{ config('app.name') }}</h2>
        <p>Hello! &#128075;</p>
        <p>There was a request to change your password!</p>
        <p>If you did not make this request then please ignore this email.</p>

        <br />
        <a style="background-color: #4ade80; padding: 12px 18px; border-radius: 4px; text-decoration: none; color: white;" href="{{ $callbackLink }}">Reset password</a>
        <p>
            Otherwise, please click this link to change your password:
            <a href="{{ $callbackLink }}">
                {{$callbackLink}}
            </a>
        </p>
    </div>
</div>
</body>

</html>
