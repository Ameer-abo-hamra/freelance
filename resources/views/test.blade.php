<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
@vite('resources/js/app.js')

<body>


    <script>

            window.onload = ()=> {
                Echo.channel("ameer").listen("ForTesting", (e) => {
                console.log(e);
                console.log("hi");
            })
            }
    </script>
</body>

</html>
