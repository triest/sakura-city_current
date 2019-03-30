<template>
    <div>
        <ul class="nav nav-tabs">
            <li role="presentation" @click="currentTab = 'new'"><a href="#"><b>Новые подарки</b></a></li>
            <li role="presentation" @click="currentTab = 'history'"><a href="#"><b>История подарков</b></a></li>
            <li role="presentation" @click="currentTab = 'fromMe'"><a href="#"><b>Подарки от меня</b></a></li>
        </ul>

        <div class="tab-content">
            <div v-if="currentTab == 'new'">
                <div v-for="present in presents">
                    <b>От:{{present.who_name}}</b><br>
                    <img :src="'presents/upload/'+present.pres_image" height="200">
                    <button v-on:click="mark_as_readed(present.act_id)">Получен</button>
                </div>
            </div>
            <div v-if="currentTab == 'history'">
                <div v-for="present in presentsHistoryFoME">
                    <b>От:{{present.who_name}}</b> <br>
                    {{present.sended}}<br>
                    <img :src="'presents/upload/'+present.pres_image" height="200">
                </div>
            </div>
            <div v-if="currentTab == 'fromMe'">
                <div v-for="present in presentsHistoryFromMe">
                    <b>От:{{present.who_name}}</b> <br>
                    {{present.sended}}<br>
                    <img :src="'presents/upload/'+present.pres_image" height="200">
                    <div v-if="present.resded=1">
                        Просмотрен
                    </div>
                    <div v-else>
                        Не просмотрен
                    </div>


                </div>
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
            this.getNewPresentsForMe(),
                this.getNewPresentsHistory(),
                this.getNewPresentsFromMe(),
                this.currentTab='new';
        },
        data() {
            return {
                presents: [],
                presentsHistoryFoME: [],
                presentsHistoryFromMe: [],
                name: '',
                price: '',
                file1: '',
                isDisabled: true,
                showModal: false,
                //currentTab: 'foMe',
                currentTab: 'new'
            }
        },
        methods: {
            getNewPresentsForMe() {
                this.presents = null;
                axios.get('/getpresentsforMe')
                    .then((response) => {
                        this.presents = response.data[0];
                    });
            },
            getNewPresentsFromMe() {
                this.presents = null;
                axios.get('/getpresentsFromMe')
                    .then((response) => {
                        this.presentsHistoryFromMe = response.data[0];
                    });
            },

            getNewPresentsHistory() {
                this.presents = null;
                axios.get('/getpresentsHistoryforMe')
                    .then((response) => {
                        this.presentsHistoryFoME = response.data[0];
                    });
            },

            handleFileUpload() {
                this.file = this.$refs.file.files[0];
            },
            mark_as_readed(id) {
                let formData = new FormData();
                formData.append('present_id', id);
                axios.post('/markpresentasreaded', formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                )
                    .then()
                    .catch();
                this.getNewPresentsForMe();
            },
            submitPresent() {
                var that = this;
                let formData = new FormData();
                formData.append('file', this.file);
                formData.append('name', this.name);
                formData.append('price', this.price);
                axios.post('/createpresent', formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then((response) => {

                        if (response.data == "ok") {
                            this.name = "";
                            this.price = "";
                            this.file = "";
                        }
                    })
                    .catch(function () {
                    });
                this.getPresents();
            },
            deleWindow(item) {
                this.id = item;
                console.log(item);
                this.showModal = true;
                axios.post('/delpresent',
                    {id: item}
                )
                    .then((response) => {

                    })
                    .catch(function () {
                    });

                this.getPresents();
            },
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