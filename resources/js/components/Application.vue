<template>
    <div>
        <ul class="nav nav-tabs">
            <li role="presentation" @click="currentTab = 'main'"><a href="#"><b>Просьбы предоставить доступ</b></a></li>
            <li role="presentation" @click="currentTab = 'application'"><a href="#"><b>Мои запросы на открытие
                анкеты</b></a></li>
            <li role="presentation" @click="currentTab = 'who'"><a href="#"><b>Кто может смотреть мою анкету</b></a>
            </li>
        </ul>


        <div class="applicationClass">
            <div class="tab-content">
                <div v-if="currentTab == 'main'">
                    <b>
                        Просьбы предоставить доступ:
                    </b>
                    <div v-for="application in applications">
                        <div class="avatar">
                            <img :src="'images/upload/'+application.main_image" :alt="application.name" height="150">
                        </div>
                        <div class="contact">
                            <p class="name">{{ application.name }}</p>
                        </div>
                        <button v-on:click="getNewApplication(application.id)">Предоставить доступ</button>
                        <button v-on:click="denideNewApplication(application.id)">Закрыть доступ</button>
                    </div>
                </div>

                <div v-if="currentTab == 'application'">
                    <b>
                        Мои запросы на открытие анкеты:
                    </b>
                    <div v-for="application in myapplications">

                        <div class="avatar">
                            <img :src="'images/upload/'+application.image" :alt="application.who_name" height="150">
                            <br> {{application.who_name}}
                            <div v-if="application.status==null">Не рассмотрен</div>
                            <div v-if="application.status=='confirmed'">Подтвержден</div>
                            <div v-if="application.status=='denide'">Отклонен</div>
                        </div>
                        <div class="contact">
                            <p class="name">{{ application.name }}</p>

                        </div>
                    </div>
                </div>
                <b>
                    <div v-if="currentTab == 'who'">
                        Кому предоставил доступ:
                        <div v-for="application in whocansee">
                            <div class="avatar">
                                <img :src="'images/upload/'+application.main_image" :alt="application.name"
                                     height="150">
                            </div>
                            <div class="contact">
                                <p class="name">{{ application.name }}</p>
                            </div>
                            <button v-on:click="clouseAccess(application.id)">Закрыть доступ к приватной
                                информации
                            </button>
                        </div>
                    </div>
                </b>

            </div>
        </div>
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
                applications: [],
                myapplications: [],
                whocansee: [],
                currentTab: 'main'
            };
        },
        mounted() {
            this.getApplications(),
                this.getMyApplications(),
                this.getWhoHavwAccessToMyAnket()

        },
        methods:
            {
                getApplications() {
                    axios.get('/getapplication')
                        .then((response) => {
                            this.applications = null;
                            this.applications = response.data;
                        })
                },
                getMyApplications() {
                    axios.get('/getmyapplication')
                        .then((response) => {
                            this.myapplications = null;
                            this.myapplications = response.data;
                        })
                },
                denideNewApplication(girl_id) {
                    console.log(girl_id)
                    axios.get('/denideaccess', {
                        params: {
                            id: girl_id,

                        }
                    })
                        .then((response) => {
                            //this.applications = response.data;
                            // console.log(response.data)
                            if (response.data == 'ok') {
                                this.getApplications();
                            }
                        })
                    this.getApplications();
                },
                getNewApplication(user_id) {
                    axios.get('/geteaccess', {
                        params: {
                            id: user_id
                        }
                    })
                        .then((response) => {
                            if (response.data == 'ok') {
                                this.getApplications();
                            }
                        })
                    this.getApplications();
                    this.getWhoHavwAccessToMyAnket()
                },
                getWhoHavwAccessToMyAnket() {
                    axios.get('/whohaveaccesstomyanket')
                        .then((response) => {
                            this.whocansee = null
                            this.whocansee = response.data;
                        })
                },
                clouseAccess(id) {
                    console.log(id);
                    axios.get('/clouseaccess', {
                        params: {
                            id: id,
                        }
                    })
                        .then((response) => {
                            if (response.data == 'ok') {
                                this.getWhoHavwAccessToMyAnket();
                            }
                        })
                }


            }
    }
</script>

<style scoped>

</style>