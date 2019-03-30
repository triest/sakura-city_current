<template>
    <div>
        <h3 v-if="isOpen">У вас открыт доступ к приватной информации!</h3>
        <button class="btn-primary" v-if="showSendRegButton" v-on:click="sendRequwest()">Отправит запрос на открытие
            анкеты
        </button>
        <br>
        <h3 v-if="regStatus=='notreaded'">Заявка не рассмотрена</h3>
        <h3 v-if="regStatus=='acept'">Заявка принята</h3>
        <h3 v-if="regStatus=='denide'">Заявка отклонена</h3>
        <br>
        <button class="btn-default" v-on:click="showMessageWindow()">Написать сообщение</button>
        <br>
        <modal v-if="showModal===true" :id="id" v-on:close="showModal = false">></modal>
        <button class="btn-success" v-on:click="showPresentModal=true">Отправить подарок</button>
        <br>
        <present v-if="showPresentModal" :id="user_id" @closeRequest='close()'></present>
    </div>
</template>

<script>
    import modal from './ModalComponent.vue';
    import present from './PresentModal.vue'

    export default {
        props: {
            id: {
                type: '',
                required: true
            },
            user_id: {
                type: '',
                required: true
            },
        },
        components: {
            modal, present
        },
        mounted() {
            console.log('private');
            console.log(this.id);
            this.getuserid();
            this.khowHasPrivateOrNot();

        },
        data() {
            return {
                isOpen: false,
                showSendRegButton: false,
                regStatus: null,
                isModalVisible: true,
                showModal: false,
                showPresentModal: false,
                userId: '',
            }
        },
        methods: {
            khowHasPrivateOrNot() {
                console.log('reg ' + this.id);
                axios.get('/getisprivaterrnot', {
                    params: {
                        id: this.user_id
                    }
                })
                    .then((response) => {
                        if (response.data == "open true") {
                            this.isOpen = true;     //открыта анкета
                        }
                        else {
                            this.isOpen = false;
                            console.log("open false"); //закрыта
                            this.khowSendRequwestOrNot();
                        }
                    })
            },
            getuserid() {
                axios.get('/getuserid', {
                    params: {
                        id: this.id
                    }
                })
                    .then((response) => {
                        this.user_id = response.data;
                    })
            },

            khowSendRequwestOrNot() { //узнаёт, отправлен запрос или нет
                console.log("sendor not");
                var data_response = null;
                axios.get('/getsendregornot', {
                    params: {
                        id: this.id
                    }
                })
                    .then((response) => {
                        data_response = response.data;
                        if (data_response == "not") {
                            this.showSendRegButton = true;
                            console.log("not")
                        }
                        else {
                            console.log("true");

                            this.showSendRegButton = false;
                            console.log();
                            //если отправлен, то надо статус показать
                            if (response.data['readed'] == 1) {
                                // this.regStatus = "notreaded"
                                console.log("readed");
                                if (response.data['status'] == 'confirmed') {
                                    this.regStatus = "acept";
                                    this.showSendRegButton = false;
                                }
                                else {
                                    this.regStatus = "denide";
                                    this.showSendRegButton = false;
                                }
                                //this.showSendRegButton=false;
                            }
                            else {
                                console.log("not readed")
                                this.regStatus = "notreaded";
                            }
                        }

                    })
            },//отправляет запрос на открытие анкеты
            sendRequwest() {
                axios.get('/sendreg', {
                    params: {
                        id: this.id
                    }
                })
                    .then((response) => {
                            this.khowSendRequwestOrNot();
                        }
                    )
            },
            itemClicked: function () {
                console.log("modal");
                $("#my-modal").modal('show');
            },
            showModal() {
                this.isModalVisible = true;
            },
            closeModal() {
                this.isModalVisible = false;
            },
            showMessageWindow() {
                this.showModal = true
            },
            close() {
                console.log("false");
                this.showPresentModal = false;
            }
        }
    }
</script>

<style scoped>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: table;
        transition: opacity .3s ease;

    }


</style>