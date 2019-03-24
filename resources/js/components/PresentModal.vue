<template>
    <transition name="modal" @close="showModal = false">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">

                    <div class="modal-header">
                        <slot name="header">
                            <b>Подарки</b>
                        </slot>
                    </div>

                    <div class="modal-body">
                        <slot name="body">
                            <table class="table table-condensed">
                                <thead>
                                <tr>
                                    <th>Подарок</th>
                                    <th>Цена</th>
                                    <th>Изображение</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="present in presents[0]">
                                    <td>{{present.name}}</td>
                                    <td>{{present.price}}</td>
                                    <td><img :src="'/presents/upload/'+present.image" height="200"></td>

                                    <td v-if="userMoney>=present.price">
                                        <button class="button btn-primary" @click="givePresent(present.id)">Подарить
                                        </button>
                                    </td>
                                    <td v-else>
                                        <b>Недостаточно денег. Пополните счет</b>
                                    </td>

                                </tr>
                                </tbody>
                            </table>
                        </slot>
                    </div>

                    <div class="modal-footer">
                        <slot name="footer">
                            <button class="modal-default-button" v-on:click="close()">
                                Закрыть
                            </button>
                        </slot>
                    </div>
                </div>

            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        props: {
            id: {
                type: '',
                required: true
            }
        },
        mounted() {
            this.getPresentsList(),
                console.log(this.id),
                this.getUserMoney();
        },
        data() {
            return {
                presents: [],
                currentAnket: '',
                userMoney: ''
            }
        },
        methods: {
            close() {
                this.$emit('closeRequest')
            },


            getPresentsList() {
                axios.get('/getpresents', {})
                    .then((response) => {
                            this.presents = response.data;
                        }
                    )
            },
            givePresent(id) {
                axios.post('/givepresent', {
                    user_id: this.id,
                    present_id: id
                }).then((response) => {
                });
                this.close();
            },
            getUserMoney() {
                axios.get('/getMoney'
                )
                    .then((response) => {
                        this.money = response.data.money
                        console.log(this.money)
                    })
            }
        }
    }
</script>

<style scoped>
    textarea {
        width: 90%; /* Ширина поля в процентах */
        height: 200px; /* Высота поля в пикселах */
        resize: none; /* Запрещаем изменять размер */
    }

    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
        transition: opacity .3s ease;
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }

    .modal-container {
        width: 600px;
        margin: 0px auto;
        padding: 20px 30px;
        background-color: #fff;
        border-radius: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
        transition: all .3s ease;
        font-family: Helvetica, Arial, sans-serif;
    }

    .modal-header h3 {
        margin-top: 0;
        color: #42b983;
    }

    .modal-body {
        margin: 20px 0;
    }

    .modal-default-button {
        float: right;
    }

    /*
     * The following styles are auto-applied to elements with
     * transition="modal" when their visibility is toggled
     * by Vue.js.
     *
     * You can easily play with the modal transition by editing
     * these styles.
     */

    .modal-enter {
        opacity: 0;
    }

    .modal-leave-active {
        opacity: 0;
    }

    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
    }
</style>