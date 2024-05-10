<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <h2>welcome @if (getAuth('api-company'))
            {{ getAuth('api-company')->name }}
        @elseif (getAuth('web-company'))
            {{ getAuth('web-company')->name }}
        @endif
    </h2>
    <p>this is the verification code {{ $code }}</p>
    <a href="http://127.0.0.1:8000/customer/test" style="color: red ; padding:10px; background-color:aqua;">click</a>
</body>

</html>
