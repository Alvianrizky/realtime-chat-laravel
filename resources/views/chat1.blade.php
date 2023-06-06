<x-app-layout>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.1.2/socket.io.js"></script>
    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Chat Message Module</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <div id="messages"
                                    style="border: 1px solid #121212; margin-bottom: 5px; height: 250px; padding: 2px; overflow: scroll;">
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <form action="sendmessage" method="POST">
                                    @csrf
                                    <input type="hidden" name="user" value="{{ Auth::user()->name }}">
                                    <textarea class="form-control message"></textarea>
                                    <br />
                                    <input type="button" value="Send" class="btn btn-success" id="send-message">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script>
        var socket = io.connect('http://localhost:8890');
        socket.on('new-message', function (data) {
            // data = jQuery.parseJSON(data);
            console.log(data);
            $( "#messages" ).append( "<strong>"+data.user+":</strong><p>"+data.message+"</p>" );
        });
        $("#send-message").click(function(e){
            e.preventDefault();
            var _token = '{{ csrf_token() }}';
            var user = $("input[name='user']").val();
            var message = $(".message").val();
            if(message != ''){
                $.ajax({
                    type: "POST",
                    url: '{{ route("chat") }}',
                    dataType: "json",
                    data: {
                        '_token':_token,
                        'message':message,
                        'user':user
                    },
                    success:function(data) {
                        socket.emit('message', data)
                        // $("#messages").append(data.content);
                        $(".message").val('');
                    }
                });
            }
        })
    </script>
</x-app-layout>
