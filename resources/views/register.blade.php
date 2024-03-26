<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>


    <form action="{{Route("register")}}" method="POST">
        @csrf
        <input type="text" name="first-name" placeholder="first-name">
        <input type="text" name="last-name" placeholder="last-name">
        <input type="email" name="email" placeholder="email">
        <input type="password" name="password" placeholder="password">
        <input type="submit">
    </form>
</body>
</html>
