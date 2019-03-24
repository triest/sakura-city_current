<template>

    <div>
        <ul class="nav nav-tabs">
            <li role="presentation" @click="currentTab = 'main'"><a href="#"><b>Главная фотография</b></a></li>
            <li role="presentation" @click="currentTab = 'galeray'"><a href="#"><b>Общие фотографии</b></a></li>
            <li role="presentation" @click="currentTab = 'private'"><a href="#"><b>Приватные фотографии</b></a></li>
        </ul>

        <div class="tab-content">
            <div v-if="currentTab == 'main'">
                <p class="h3">Главная фотография:</p>
                <p v-if="mainImage!=null">
                    <img :src="'images/upload/'+mainImage" height="150">
                </p>
                <div class="container">
                    <b>Обновить главныю фотографию в Вашей анкете всего за {{priceChangeMainImage}} рублей!</b>
                    <div v-if="userMoney>=priceChangeMainImage">
                        <div class="large-12 medium-12 small-12 cell">
                            <label>Выбирите изображение
                                <input type="file" id="file" ref="mainFileInput" v-on:change="handleFileMainUpload()"/>
                            </label>
                            <button v-on:click="submitFile()">Submit</button>
                        </div>


                    </div>
                    <div v-else>
                        <p>Недостаточно денег. <b><a class="btn btn-primary" href="/power">Пополните счет</a> </b></p>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div v-if="currentTab == 'galeray'">

                    <p class="h3">Галлерея:</p>
                    <div class="container">
                        <div class="large-12 medium-12 small-12 cell">
                            <label>File
                                <input type="file" id="galeray" ref="galerayFileInput"
                                       v-on:change="handleFileUploadGaleay()"/>
                            </label>
                            <button v-on:click="submitGaleray()">Загрузить</button>
                        </div>
                    </div>

                    <div v-for="image in images">
                        <p v-if="image!=null">
                            <img :src="'images/upload/'+image.photo_name" height="200">
                        </p>
                        <button class="btn btn-danger" v-on:click="deleteGelataiImage(image.photo_name)">Удалить
                        </button>
                    </div>
                </div>
            </div>
            <br>
            <div class="tab-content">
                <div v-if="currentTab == 'private'">
                    <p class="h3">Приватные фотографии:</p>
                    <div class="container">
                        <div class="large-12 medium-12 small-12 cell">
                            <label>File
                                <input type="file" id="privatefile" ref="privatefileInput"
                                       v-on:change="handlePrivateFileUpload()"/>
                            </label>
                            <button v-on:click="submitPrivateFile()">Загрузить</button>
                        </div>
                    </div>
                    <div v-for="image in privateimages">
                        <p v-if="image!=null">
                            <img :src="'images/upload/'+image.photo_name" height="200">
                        </p>
                        <button class="btn btn-danger" v-on:click="deletePrivateGelataiImage(image.photo_name)">
                            Удалить
                        </button>
                    </div>
                </div>
            </div>
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
                mainImage: null,
                file: '',
                galerayFile: '',
                showModal: false,
                images: [],
                privateimages: [],
                privatefile: '',
                privatefileInput: '',
                userMoney: '',
                priceChangeMainImage: '',
                mainFile: '',
                currentTab: 'main'
            };
        },
        computed: {
            getImageUrl: function () {
                return this.getmainImage();
            },
            getImagesUrls: function () {
                return this.getimages()
            }

        },
        mounted() {
            this.getmainImage(),
                this.getimages(),
                this.getprivateimages(),
                this.getDataForChangeMainImage()
        },
        methods:
            {
                getmainImage() {
                    axios.get('/getmainImage')
                        .then((response) => {
                            this.mainImage = response.data;
                        });
                },
                getimages() {

                    axios.get('/getImages')
                        .then((response) => {
                            this.images = response.data;
                        });
                },
                chechAnketExist() {

                },
                submitFile() {
                    /*
                            Initialize the form data
                        */
                    let formData = new FormData();
                    formData.append('file', this.mainFile);
                    axios.post('/updateMainImage',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }
                    ).then(function () {
                        console.log('SUCCESS!!');
                        getmainImage();
                    })
                        .catch(function () {
                            getmainImage();
                        });
                    getmainImage()
                },
                submitGaleray() {
                    /*
                            Initialize the form data
                        */
                    let formData = new FormData();
                    formData.append('file', this.galerayFile);
                    axios.post('/updateGalerayImage',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }
                    ).then(function () {
                    })
                        .catch(function () {
                        });
                    this.getimages();
                },
                submitPrivateFile() {
                    /*
                            Initialize the form data
                        */
                    let formData = new FormData();
                    formData.append('file', this.privatefile);
                    axios.post('/updatePrivateGalerayImage',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }
                    ).then(function () {
                    })
                        .catch(function () {
                        });
                    this.getimages();
                },

                handleFileMainUpload() {
                    this.mainFile = this.$refs.mainFileInput.files[0];
                },
                handleFileUploadGaleay() {
                    this.galerayFile = this.$refs.galerayFileInput.files[0];
                },
                handleFileUploadPrivateGaleay() {
                    this.privatefile = this.$refs.privatefileInput.files[0];
                },
                handlePrivateFileUpload() {
                    this.privatefile = this.$refs.privatefileInput.files[0];
                },

                deleteGelataiImage(image) {

                    axios.get('/deleteImage', {
                            params: {
                                imagename: image
                            }
                        }
                    )
                        .then((response) => {
                            this.getimages();
                        });
                    this.getimages();
                },
                deletePrivateGelataiImage(image) {

                    axios.get('/deletePrivateImage', {
                            params: {
                                imagename: image
                            }
                        }
                    )
                        .then((response) => {

                        });
                    this.getimages();
                },
                getprivateimages() {
                    console.log("get private images");
                    this.privateimages = null;
                    axios.get('/getPrivateImages')
                        .then((response) => {
                            this.privateimages = response.data;
                        });
                },
                getDataForChangeMainImage() {
                    axios.get('/getDataForChangeMainImage')
                        .then((response) => {
                                this.priceChangeMainImage = response.data.update_main_image[0].price
                                this.userMoney = response.data.user_money;


                            }
                        )
                }

            }
    }
</script>

<style scoped>

</style>