<template>
    <div>
        <b><a href="/messages">Сообщения
            <div v-if="numberUnreaded>0">+{{numberUnreaded}}</div>
        </a>
        </b><br>
        <b><a href="/applications">Заявки на открытие анкеты
            <div v-if="numberApplication>0">+{{numberApplication}}</div>
        </a>
        </b>

        <b><a href="/editimages">Редактирование галлереи</a> </b><br>
        <b><a class="btn btn-primary" href="/power">Поднять анкету</a> </b><br><br>
        <b><a class="btn btn-info" href="/mypresents">Мои подарки</a> </b>
        <div v-if="numberApplicationPresents>0"><b>+{{numberApplicationPresents}}</b></div>
    </div>
</template>

<script>
    export default {
        props: {
            user: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                numberUnreaded: 0,
                numberApplication: 0,
                numberApplicationPresents: 0,
            };
        },
        mounted() {
            Echo.private(`messages.${this.user.id}`)
                .listen('NewMessage', (e) => {
                    console.log('NewMessage');
                    /*  axios.get('/getCountUnreaded')
                          .then((response) => {
                              this.numberUnreaded = response.data;
                          })*/
                    this.getNumberUnreadedMessages();
                });
            Echo.private(`requwests.${this.user.id}`)
                .listen('newApplication', (e) => {
                    console.log('NewRequwest');
                    axios.get('/getCountUnreadedRequwest')
                        .then((response) => {
                            this.numberApplication = response.data;
                        })
                });
            Echo.private(`gifs.${this.user.id}`)
                .listen('eventPreasent', (e) => {
                    console.log('NewPresent');
                });


            axios.get('/getCountUnreaded')
                .then((response) => {
                    this.numberUnreaded = response.data;
                });
            axios.get('/getCountUnreadedRequwest')
                .then((response) => {
                    this.numberApplication = response.data;
                }),
                this.getNumberUnreadedPresents()

        },
        methods:
            {
                getNumberUnreadedMessages() {
                    axios.get('/getCountUnreaded')
                        .then((response) => {
                            this.numberUnreaded = response.data;
                        })
                },
                chechAnketExist() {

                },
                getNumberUnreadedPresents() {
                    axios.get('/getCountUnreadedPresents')
                        .then((response) => {
                            this.numberApplicationPresents = response.data;
                        })
                },

            }
    }
</script>


<style scoped>

</style>