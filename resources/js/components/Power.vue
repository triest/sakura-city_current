<template>

    <div>
        <b>Текущее состояние счета: {{money.money}}</b>
        <br>
        <b> Поместить анкету в шапку сайта(сменяемое меню) на
            <input name="days" id="days" type="number" min="0"
                   :max="max2" :value="max2" ref="inputDaysNumber"></b>
        <div v-if="priceToTop<money">
            <button class="btn-primary" v-on:click="toTop()">Поднять</button>
        </div>
        <div v-else>
            Недостаточно денег. Пополните счет.
        </div>
        <b>Поднять анкету на первое место за {{priceToTop.price}} рублей</b>
        <div v-if="money.money>=priceToTop.price">
            <button v-on:click="toFirstPlase()">Поднять</button>
        </div>
        <div v-else>
            Недостаточно денег. Пополните счет.
        </div>
    </div>
</template>

<!--Модальное -->


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
                money: "",
                prices: "",
                priceToTop: "",
                inputDays: "",
                priceFirstPlase: ""
            };
        },
        computed: {
            max2: function () {
                //  return this.money.money / this.priceToTop[0][0].price
                return this.money.money / 1
            },


        },
        mounted() {
            this.getMoneut(),
                this.getPrices()
        },
        methods:
            {
                getMoneut() {
                    console.log("getMoney");
                    axios.get('/getMoney')
                        .then((response) => {
                            this.money = response.data;
                        });
                },
                getPrices() {
                    var returnedData = "";
                    axios.get('/getpricestotop')
                        .then((response) => {
                            this.priceToTop = response.data;
                        });

                },
                toFirstPlase() {
                    var that = this;
                    axios.get('/tofirstplaсe')
                        .then((response) => {
                            this.priceFirstPlase = response.data;
                        });
                    that.getMoneut()
                },
                toTop() {

                    axios.get('/totop', {
                        params: {
                            days: this.$refs.inputDaysNumber.value
                        }
                    })
                        .then((response) => {
                            if (!response.data) {

                            }
                            else {
                                this.isOpen = false;

                            }
                        });
                    this.getMoneut()

                }
            }
    }
</script>

<style scoped>

</style>