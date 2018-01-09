<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>
        Home page
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Latest compiled and minified CSS -->
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">--}}

    <link rel="stylesheet" href="/css/bootstrap.css" >

</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <p class="bg-info">
                Всего овечек: {{ $all  }}
            </p>
            <p class="bg-success">Живых овечек: {{ $live  }}</p>
            <p class="bg-danger">Усыпленных овечек: {{ $sleep }}</p>
            <p class="bg-primary">Самый населеный загон №: {{ $max->paddock }}. Количество овец в загоне: {{ $max->total }}</p>
            <p class="bg-primary">Самый маленький загон №: {{ $min->paddock }}. Количество овец в загоне: {{ $min->total }}</p>
        </div>
    </div>
</div>

<style>
    p{
        padding: 2em;
    }
</style>
</body>
</html>