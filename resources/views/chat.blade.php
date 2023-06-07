<x-app-layout>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.1.2/socket.io.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        textarea {
            resize: none;
            overflow: hidden;
            min-height: 20px;
            max-height: 100px;
        }
    </style>
    <div class="container my-5">
        <div class="row">
            <div class="col-4 p-0">
                <div class="card" style="height: 650px">
                    <div class="card-header bg-white">
                        <div class="row justify-content-between">
                            <div class="col-5">
                                <h5>{{ Auth::user()->name }}</h5>
                            </div>
                            <div class="col-7">
                                <button class="btn btn-primary btn-sm mr-2" type="button" data-bs-toggle="modal" data-bs-target="#userModal"><i class="fa-solid fa-plus"></i> User</button>
                                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#groupModal"><i class="fa-solid fa-plus"></i> Group</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="chat-user">
                        {{-- <div class="border-top border-bottom rounded text-white py-3 px-2 bg-primary bg-opacity-75">
                            dd
                        </div> --}}
                        {{-- <button class="text-start" style="width: 100%">
                            <div class="border-top border-bottom rounded py-3 px-2">
                                dd
                            </div>
                        </button> --}}
                    </div>
                </div>
            </div>
            <div class="col-8 p-0">
                <div class="card" style="height: 650px">
                    <div class="card-header bg-white">
                        <h5 id="title-chat">User</h5>
                    </div>
                    <div class="card-body d-flex flex-column" id="chat-message" style="overflow-y: auto; ">

                        {{-- <div style="display: inline;">
                            <div class="card p-2 mb-3 bg-primary text-white" style="max-width: 80%; float: left;">
                                <div class="fw-bold">Rizky</div>
                                <div>cek data aja masuk ga ya harusnya sih masuk iya ga sih masak enggak</div>
                            </div>
                        </div>

                        <div style="display: inline;">
                            <div class="card p-2 mb-3 bg-success text-white" style="max-width: 80%; float: right !important;">
                                <div>cek data aja</div>
                            </div>
                        </div> --}}

                    </div>
                    <div class="card-footer bg-white">
                        <div class="row my-1">
                            <div class="col-10">
                                <textarea oninput="auto_grow(this)" name="" id="" class="form-control message" rows="1"></textarea>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-success btn-sm" onclick="send()">Kirim</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="userModalLabel">User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="list-user">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="groupModalLabel">Group</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="list-group">
                    <div class="mb-3">
                        <label class="form-label">Nama Grup</label>
                        <input type="text" class="form-control" id="group-name" name="group-name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label col-12">Tambah Peserta</label>
                        <select class="select2-multiple form-control" id="group-user" name="user[]" multiple style="width: 100%;">
                            @foreach ($user as $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary" data-bs-dismiss="modal" onclick="saveGroupChat()">Buat</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var socket = io.connect('http://localhost:8890');
        var _token = '{{ csrf_token() }}';
        var user_id = '{{ Auth::user()->id }}';
        var group = '';

        $(document).ready(function() {
            $('.select2-multiple').select2({
                dropdownParent: $("#groupModal"),
                placeholder: "Select",
                allowClear: true
            });

        });

        socket.on('new-message', function (data) {
            if(data.group_id == group) {
                if(data.user_id == user_id) {
                    $( "#chat-message" ).append('<div style="display: inline;" id="child-chat">'+
                        '<div class="card p-2 mb-3 bg-success text-white" style="max-width: 80%; float: right !important;">'+
                            '<div>'+data.content+'</div>'+
                        '</div>'+
                    '</div>');
                } else {
                    $( "#chat-message" ).append('<div style="display: inline;" id="child-chat">'+
                        '<div class="card p-2 mb-3 bg-primary text-white" style="max-width: 80%; float: left;">'+
                            '<div class="fw-bold">'+data.user.name+'</div>'+
                            '<div>'+data.content+'</div>'+
                        '</div>'+
                    '</div>');
                }
            }

        });

        getUser();
        getUserChat();

        function chat(groupId, name) {
            document.getElementById("group_"+groupId+"").classList.add("text-white");
            document.getElementById("group_"+groupId+"").classList.add("bg-primary");
            document.getElementById("group_"+groupId+"").classList.add("bg-opacity-75");

            if(group != '') {
                document.getElementById("group_"+group+"").classList.remove("text-white");
                document.getElementById("group_"+group+"").classList.remove("bg-primary");
                document.getElementById("group_"+group+"").classList.remove("bg-opacity-75");
            }

            console.log(group);
            console.log(groupId);

            document.getElementById("title-chat").innerHTML = name;
            document.getElementById("chat-message").innerHTML = '';
            group = groupId;
            $.ajax({
                type: "GET",
                url: '{{ route("list-chat") }}',
                dataType: "json",
                data: {
                    '_token':_token,
                    'group_id':group
                },
                success:function(data) {
                    console.log(data);
                    $.each (data, function (k, v) {
                        if(v.user_id == user_id) {
                            $( "#chat-message" ).append('<div style="display: inline;" id="child-chat">'+
                                '<div class="card p-2 mb-3 bg-success text-white" style="max-width: 80%; float: right !important;">'+
                                    '<div>'+v.content+'</div>'+
                                    '</div>'+
                                '</div>');
                        } else {
                            $( "#chat-message" ).append('<div style="display: inline;" id="child-chat">'+
                                '<div class="card p-2 mb-3 bg-primary text-white" style="max-width: 80%; float: left;">'+
                                    '<div class="fw-bold">'+v.user.name+'</div>'+
                                    '<div>'+v.content+'</div>'+
                                    '</div>'+
                                '</div>');
                        }
                    });
                }
            });
        }

        function send() {
            var message = $(".message").val();
            if(message != '' && group != ''){
                $.ajax({
                    type: "POST",
                    url: '{{ route("chat") }}',
                    dataType: "json",
                    data: {
                        '_token':_token,
                        'content':message,
                        'group_id':group
                    },
                    success:function(data) {
                        socket.emit('message', data)
                        $(".message").val('');
                    }
                });
            }
        }

        function getUser() {
            $.ajax({
                type: "GET",
                url: '{{ route("list-user") }}',
                dataType: "json",
                success:function(data) {

                    $.each (data, function (k, v) {
                        $( "#list-user" ).append('<div class="row border-bottom py-2">'+
                            '<div class="col-10">'+v.name+'</div>'+
                            '<div class="col-2">'+
                                '<button class="btn btn-success btn-sm" data-bs-dismiss="modal" onclick="saveUserChat('+v.id+')">'+
                                    '<i class="fa-solid fa-plus"></i>'+
                                '</button>'+
                            '</div>'+
                            '</div>');
                    });
                }
            });
        }

        function getUserChat() {
            $.ajax({
                type: "GET",
                url: '{{ route("list-user-chat") }}',
                dataType: "json",
                success:function(data) {

                    $.each (data, function (k, v) {
                        if(v.group.type == 'private') {
                            $( "#chat-user" ).append('<button class="text-start" id="group_'+v.group_id+'" style="width: 100%" onClick="chat('+v.group_id+', \'' + v.user.name + '\')">'+
                            '<div class="border-top border-bottom rounded py-3 px-2" >'+
                                v.user.name+
                            '</div>'+
                            '</button>');
                        } else {
                            $( "#chat-user" ).append('<button class="text-start" id="group_'+v.group_id+'" style="width: 100%" onclick="chat('+v.group_id+', \'' + v.group.name + '\')">'+
                            '<div class="border-top border-bottom rounded py-3 px-2" >'+
                                v.group.name+
                            '</div>'+
                            '</button>');
                        }
                    });
                }
            });
        }

        function saveUserChat(user) {
            $.ajax({
                type: "POST",
                url: '{{ route("store-user") }}',
                dataType: "json",
                data: {
                    '_token':_token,
                    'user':user
                },
                success:function(data) {

                    $.each (data, function (k, v) {
                        if(v.group.type == 'private') {
                            $( "#chat-user" ).append('<button class="text-start" style="width: 100%" onClick="chat('+v.group_id+', \'' + v.user.name + '\')">'+
                            '<div class="border-top border-bottom rounded py-3 px-2" >'+
                                v.user.name+
                            '</div>'+
                            '</button>');
                        } else {
                            $( "#chat-user" ).append('<button class="text-start" style="width: 100%" onclick="chat('+v.group_id+', \'' + v.group.name + '\')">'+
                            '<div class="border-top border-bottom rounded py-3 px-2" >'+
                                v.group.name+
                            '</div>'+
                            '</button>');
                        }
                    });
                }
            });
        }

        function saveGroupChat() {
            var user = $("#group-user").val();
            var name = $("#group-name").val();
            console.log(user);
            $.ajax({
                type: "POST",
                url: '{{ route("store-group") }}',
                dataType: "json",
                data: {
                    '_token':_token,
                    'user':user,
                    'name':name,
                },
                success:function(data) {

                    $.each (data, function (k, v) {
                        if(v.group.type == 'private') {
                            $( "#chat-user" ).append('<button class="text-start" style="width: 100%" onClick="chat('+v.group_id+', \'' + v.user.name + '\')">'+
                            '<div class="border-top border-bottom rounded py-3 px-2" >'+
                                v.user.name+
                            '</div>'+
                            '</button>');
                        } else {
                            $( "#chat-user" ).append('<button class="text-start" style="width: 100%" onclick="chat('+v.group_id+', \'' + v.group.name + '\')">'+
                            '<div class="border-top border-bottom rounded py-3 px-2" >'+
                                v.group.name+
                            '</div>'+
                            '</button>');
                        }
                    });
                }
            });
        }

        function auto_grow(element) {
            element.style.height = "5px";
            element.style.height = (element.scrollHeight)+"px";
        }
    </script>
</x-app-layout>
