<template>
    <div>
        <div class="col-sm-10">
            <b>Для продолжения регистрации необходимо подтвердить Ваш телефонный номер. Именно этот номер будут видеть
                только
                пользователи, которым вы предоставите такую возможность.<br>
                Изменить номер нельзя.<br>
                <br>
                Первая цифра-код страны. Для россии это 7. Пример: 79001234567;
                Номер должен быть указан в формате 79001234567. <br></b>
            <input type="phone" name="phone" id="phone" v-model="phone" placeholder="79001234567" required>
            <p v-if="errors.length">
                <b>Пожалуйста исправьте указанные ошибки:</b>
            <ul>
                <li v-for="error in errors">{{ error }}</li>
            </ul>
            </p>
            <button class="btn btn-primary" v-on:click="sendSMS()">Подтвердить</button>
            <br><br>
            <div v-if="codeVisable">
                <label for="code"> Введите код:
                    <input type="number" name="code" id="code" v-model="code" required>
                </label>
                <button class="btn btn-primary" v-on:click="sendCode()">Введите код</button>
            </div>
            <div v-if="next">
                <a class="btn btn-primary" href="/createAnketPage">Создать анкету</a>
            </div>


        </div>
    </div>
</template>


<script>

    //'./components/delModal.vue'

    export default {
        props: {},
        components: {},
        mounted() {

        },
        data() {
            return {
                phone: "",
                code: "",
                errors: [],
                codeErors: [],
                codeVisable: false,
                nextButtonVisable:false,
                next:false
            }
        },
        methods: {
            sendSMS() {
                this.errors = [];
                console.log("sendSMS");
                if (!this.phone) {
                    this.errors.push('Введите телефон.');
                }
                else {
                    this.errors = [];
                    console.log(this.phone);
                    if (this.phone.match(/\d{11}/)) {
                        console.log("работает");
                        let formData = new FormData();
                        formData.append('phone', this.phone);
                        try {


                            axios.post('/inputPhone', formData,
                                {
                                    headers: {
                                        'Content-Type': 'multipart/form-data'
                                    }
                                }
                            )
                                .then((response) => {
                                    console.log(response.data)
                                    if (response.data.result == "ok") {
                                        console.log("ok");
                                        this.codeVisable = true;
                                        this.errors = [];
                                    } else {
                                        console.log("faik");
                                        this.errors.push("Этот телефон уже зарегистрирован");
                                    }
                                })
                                .catch(
                                    this.errors.push("Этот телефон уже зарегистрирован")
                                )
                        }
                        catch (e) {
                            this.errors.push("Этот телефон уже зарегистрирован")
                        }
                    } else {
                        this.errors.push('Введите телефон  указанном формате.');
                    }
                }

            },
            sendCode() {
                this.errors = [];
                console.log("sendCode");
                if (!this.code) {
                    this.codeErors.push('Введите телефон.');
                }
                else {
                    this.errors = [];
                    console.log(this.code);
                    if (this.code.match(/\d{4}/)) {
                        console.log("работает");
                        let formData = new FormData();
                        formData.append('code', this.code);
                        try {
                            axios.post('/inputCode', formData,
                                {
                                    headers: {
                                        'Content-Type': 'multipart/form-data'
                                    }
                                }
                            )
                                .then((response) => {
                                    if (response.data.result == "ok") {
                                        console.log("okConfurmPhone");
                                        this.next = true;
                                        this.nextButtonVisable = [];
                                    } else {
                                        this.codeErors.push("Неверный код");
                                    }
                                })
                                .catch(
                                    this.codeErors.push("Неверный код")
                                )
                        }
                        catch (e) {
                            this.codeErors.push("Неверный код")
                        }
                    } else {
                        this.nextButtonVisable.push('Неверный код');
                    }
                }
            }
        }
    }
</script>

<style scoped>
    .delmodal {
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