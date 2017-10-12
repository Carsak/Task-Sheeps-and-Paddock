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
    <script src="/js/jquery-3.1.1.js"></script>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            {{--<form action="#">--}}
            <fieldset>
                <legend>Ферма</legend>
                <span>День:</span>
                <p id="day">0</p>

            <?$index = 1?>
            @foreach($paddock as $key => $list)
                <div class="col-md-6">
                    <h3>Загон №{{ $key  }}</h3>

                    <div id="paddock{{ $key  }}" class="zagon">
                        {{--@for($i = 1; $i <= $value; $i++, $index++ )--}}
                        @foreach($list as $sheepId)
                            <div id="sheep{{ $sheepId }}" class="name">Sheep{{ $sheepId }}</div>
                        {{--@endfor--}}
                        @endforeach
                    </div>
                </div>
            @endforeach
            </fieldset>



                <div class="col-md-6">
                    <form>
                        <div class="form-group">
                            <button id="reset" class="btn btn-danger">Reset</button>
                            {{--<button id="sleep" class="btn btn-warning">Sleep</button>--}}
                        </div>
                        <div class="form-group">
                            {{--<input type="text" placeholder="Enter command">--}}
                            <select name="command" id="">
                                <option value="add">Add</option>
                                <option value="sleep">Sleep</option>
                            </select>
                            <input type="submit" name="send" value="Send Command">
                        </div>
                    </form>
                </div>

            <div class="col-md-7 info-block">
                <p><a href="/stat/">Общая статистика</a></p>
                <p class="bg-danger">Reset = Очищает таблицу овец и историю</p>
                <p class="bg-success">Add = Добавляет одну овцу</p>
                <p class="bg-info">Sleep = Убирает одну овечку. Если в каком то загоне осталось одна овечка, переводит из самой насленной</p>
            </div>
            </div>


            {{--</form>--}}
</div>
</div>

<style>

    .zagon {
        border: 1px dashed black;
        /*width: 20%;*/
        overflow-y: scroll;
        height: 12em;
    }

    form {
        margin: 2em auto;
    }

    form select {
        padding: 0.1em 1em;
    }

    .info-block p {
        padding: 1em;
    }

</style>

<script>
    $(function () {

        function add() {
            $.ajax({
                url: '/reproduce/',
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    $('#paddock' + data.paddock).append('<div class="name">Sheep' + data.sheep_id + '</div>');
                }
            });
        }

        function sleep() {
            $.ajax({
                url: '/sleep/',
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    $('#sheep' + data.sleep.id).hide();

                    $('#sheep' + data.moved.id).appendTo('#paddock' + data.moved.to);
                }
            });
        }

        $('input[type=submit]').on('click', function () {
            var cmd = $('select').val();

            if (cmd == 'add') {
                add();
            } else if (cmd == 'sleep') {
                sleep();
            }

            return false;
        });

        $('#reset').on('click', function () {
            $.ajax({
                url: '/reset',
                success: function () {

                    window.location.reload();
                }
            });

            setDay(0);
            clearInterval(timer);
        });

        var timer = setInterval(function () {
            var day = localStorage.getItem('day') ? localStorage.getItem('day') : 0;
            day = parseInt(day) + 1;
            setDay(day);

            if (day % 10 == 0 && day > 0) {
                add();
            }

//            if(day % 100 == 0 && day > 0){
            if (day % 20 == 0 && day > 0) {
                sleep();
            }
        }, 1000);

        function setDay(day) {
            localStorage.setItem('day', day);
            $('#day').html(day);
        }
    });
</script>
	
</body>
</html>