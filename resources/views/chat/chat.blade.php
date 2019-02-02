@extends('layouts.blog3', ['title' => 'Чат'])

@section('title', 'Page Title')

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.18/vue.min.js"></script>
    <div id="app">
        <example></example>
    </div>
    <div id="usersApp">
        <b>@{{currentSort}}</b>
        <b>@{{currentSortDir}}</b>
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
                vuevariable: "s"
            },
            methods: {
                getUsers: function () {
                    axios.get('/admin/default/get')
                        .then(
                            response => {
                                this.users = response.data;
                            }
                        )
                        .catch(
                            error => console.log(error)
                        )
                },
                searchNameFunction: function () {
                    return users;
                },

                sort: function (col) {
                    this.sortKey = col;
                    this.sortOrder = -this.sortOrder;
                },
            },
            computed: {
                filterName: function () {
                    var that = this;
                    return this.users.filter(post => {
                        return post.family.toLowerCase().includes(this.searchFalily.toLowerCase()) &&
                            post.username.toLowerCase().includes(this.searchLogin.toLowerCase()) &&
                            post.name.toLowerCase().includes(this.searchName.toLowerCase()) &&
                            post.patronymic.toLowerCase().includes(this.searchPatronymic.toLowerCase()) &&
                            post.email.toLowerCase().includes(this.searchEmail.toLowerCase())
                    })
                        .sort(function (a, b) {
                            a = a[that.sortKey].toLowerCase();
                            b = b[that.sortKey].toLowerCase();
                            if (that.sortOrder == 1) {
                                return a < b ? 1 : b < a ? -1 : 0;
                            }
                            else {
                                return a > b ? 1 : b > a ? -1 : 0;
                            }
                        });

                }
            },
            beforeMount() {
                this.getUsers()
            },

        })
    </script>
@endsection



