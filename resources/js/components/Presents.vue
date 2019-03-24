<template>
    <div>

        <h2>Создать подарок</h2><br>
        <div class="form-group">
            <label for="name">Название подарка</label> <br>
            <input type="name" name="name" id="name" v-model="name"><br>
        </div>
        <div class="form-group">
            <label for="price">Цена подарка:</label><br>
            <input type="number" id="price" name="price" v-model="price">
        </div>

        <div class="form-group">
            <label for="file">Файл подарка:</label><br>
            <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/>
        </div>
        <button v-on:click="submitPresent">Создать</button>


        <br><br>
        <b>Список подрков:</b>
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
                <td><img :src="'presents/upload/'+present.image" height="200"></td>
                <td>
                    <button class="btn" @click="deleWindow(present.id)"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
            </tbody>
        </table>


    </div>


</template>

<script>

    //'./components/delModal.vue'

    export default {
        props: {},
        components: {},
        mounted() {
            this.getPresents()
        },
        data() {
            return {
                presents: [],
                name: '',
                price: '',
                file1: '',
                isDisabled: true,
                showModal: false
            }
        },
        methods: {
            getPresents() {
                this.presents = {};
                axios.get('/getpresents')
                    .then((response) => {
                        this.presents = response.data;
                    });
            },
            handleFileUpload() {
                this.file = this.$refs.file.files[0];
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