@extends('layouts.blog3', ['title' => 'Чат'])

<?php
use \App\Http\Controllers\ChatController;
?>

@section('title', 'Page Title')

@section('content')
    <style>
        .left-sidebar, .righ-sidebar {
            background-color: #ffff;
            min-height: 600px;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.18/vue.min.js"></script>


    <div id="usersApp">
        <div class="col-md-3 left-sidebar">
            <!--цикл с сообщениями-->
            <div v-for="post in posts">
                <li v-on:click="getUserMessages(post.user_from)">
                    <b>@{{post.id}}</b>
                    <b>@{{post.name}}</b>
                    <img :src="'<?php echo asset("public/images/upload/' + post.main_image")?>" height="100">
                </li>
            </div>
        </div>
        <div class="col-md-8 left-sidebar">
            <!--цикл с сообщениями-->
            <div v-for="post in current_user">
                <b>@{{post.msg}}</b>
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
                        console.log(id)
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
                    }
                },
                beforeMount() {
                    this.getMessages()
                },


            })
        </script>
@endsection



