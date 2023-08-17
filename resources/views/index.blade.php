<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Title</title>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <div class="chat">
        <div class="top">
            <img src="https://images.pexels.com/photos/220453/pexels-photo-220453.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" width="100px" height="100px" alt="">
            <div>
                <p>Ross edlie</p>
                <small>Online</small>
            </div>
        </div>

        <div class="messages">
            @include('receive', ['message'=>"Hey! What's up.."])
        </div>

        <div class="bottom">
            <form>
                <input type="text" id="message" name="message" placeholder="Enter message.. " autocomplete="off">
                <button type="submit"></button>
            </form>
        </div>
    </div>
</body>
<script>
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: 'ap2'
    });

    const channel = pusher.subscribe('public');

    channel.bind('chat', function(data) {
        $.post("/receive", {
                _token: '{{ csrf_token() }}',
                message: data.message,
            })
            .done(function(res) {
                $(".messages > .message").last().after(res);
                $(document).scrollTop($(document).height());
            });
    });

    // Broadcast messages

    $("form").submit(function(e) {
        e.preventDefault();

        $.ajax({
                url: "/broadcast",
                method: 'POST',
                headers: {
                    'X-Socket-Id': pusher.connection.socket_id
                },
                data: {
                    _token: '{{ csrf_token() }}',
                    message: $("form #message").val(),
                },
            })
            .done(function(res) {
                $(".messages > .message").last().after(res);
                $("form #message").val('');
                $(document).scrollTop($(document).height());
            });
    });
</script>

</html>
