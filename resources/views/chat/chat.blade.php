@extends('layouts.blog3', ['title' => 'Чат'])

<?php
use \App\Http\Controllers\ChatController;
?>

@section('title', 'Page Title')

@section('content')
    <
    <style>
        .left-sidebar, .righ-sidebar {
            background-color: #ffff;

        }

        .textMsgArea textarea {

        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.18/vue.min.js"></script>


    <div id="usersApp">

        <div class="col-lg-4 col-md-3 col-sm-5 col-xs-9 ">

            <div v-for="post in posts">
                <li v-on:click="getUserMessages(post.id)">

                    <b>@{{post.name}}</b>
                    <img height="50" width="50" :src="'<?php echo asset("public/images/upload/' + post.main_image")?>">
                </li>
            </div>
        </div>
        <div class="col-lg-9 col-md-3 col-sm-5 col-xs-9 ">

            <div v-for="post in current_user">
                <div v-if="post.name==<?php echo Auth::user()->id ?>">
                    <div style="float:left; background-color:#fa000f; padding: 5px">
                        <b>@{{ post.name }}</b>
                        @{{ post.created_at }} <br>
                        <b>@{{post.msg}}</b>
                    </div>
                </div>
                <div v-else>
                    <div style="float:right; background-color:rgba(255,255,255,0.33); padding: 5px">
                        <b>@{{ post.name }}</b>
                        @{{ post.created_at }} <br>
                        <b>@{{post.msg}}</b>
                    </div>
                </div>
                <br>
                <br>
                <br>
            </div>
            <div class="textMsgArea" @keydown="inputHandle">
                <textarea class="col-md-12 form-controll" v-model="textForSend"></textarea>
            </div>
            <button v-on:click="sendButton">Отправить</button>
        </div>
    </div>

    <script src="/js/app.js"></script>


    <script type="text/javascript">
        new Vue({
            el: '#usersApp',
            data: {
                users: [],
                searchLogin: "",
                searchName: "",
                searchFalily: "",
                searchPatronymic: "",
                searchEmail: "",
                currentSort: 'name',
                currentSortDir: 'asc',
                sortKey: 'username',
                sortOrder: 1,
                vuevariable: "s",
                msg: "updateNewPost",
                posts: "",
                current_user: "",
                msgFrom: "",
                msgForSend: "",
                textForSend: "",
                senfTo: ""
            },
            ready:
                function () {
                    this.getMessages();
                    console.log("reade");
                },

            methods: {

                getMessages: function () {
                    axios.get('/getAllMyMessages')
                        .then(
                            response => {
                                //this.users = response.data;
                                console.log(response.data)
                                this.posts = response.data;
                            }
                        )
                        .catch(
                            error => console.log(error)
                        )
                },
                getUserMessages: function (id) {
                    console.log(id),
                        this.sendTo = id;
                    axios.get('/getUserMessages/' + id)
                        .then(
                            response => {
                                //this.users = response.data;
                                console.log(response.data)
                                this.current_user = response.data;
                            }
                        )
                        .catch(
                            error => console.log(error)
                        )
                },
                sendButton: function () {
                    console.log("sendbutton");
                //    alert(this.textForSend);
                    console.log(this.sendTo);
                    this.sendMsg()
                },
                //отправлят сообщение
                sendMsg() {
                    console.log(this.msgForSend);
                    if (!this.msgForSend) {
                        axios.post('/sendChatMessage', {
                            msg: this.textForSend,
                            to: this.sendTo
                        })
                            .then(function (response) {
                                console.log(response.data)
                                this.posts=null;
                                this.getUserMessages(this.sendTo)
                            })
                    }
                }

            },
            beforeMount() {
                this.getMessages()
            },
            inputHandle(e) {        //сочитание клавиш для отправки
                if (e.keyCode === 13 && !e.keyCode) {
                    e.preventDefault();
                    this.sendMsg();
                }
            },


        })

    </script>
@endsection



