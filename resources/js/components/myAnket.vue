<template>
    <div>
        <div class="col-sm-4">
            <img :src="'/images/upload/'+anket.anket.main_image" height="150">
            <p>{{anket.anket.status}}</p>
            <a href="/mypresents"> <img :src="'/images/gift.png'" height="100"></a>
        </div>
        <div class="col-sm-4">
            <h5><b>{{anket.anket.name}}</b></h5>   <a type="button" class="btn btn-primary" href="/edit" role="link">Редактировать</a>
            <p>{{anket.status}}</p>
          
            <div v-if="anket.anket.sex=='famele'">
                Женщина
            </div>
            <div v-else-if="anket.anket.sex=='male'">
                Mужчина
            </div>
            <br>

            <b>Хочу встретиться с:</b>
            <div v-if="anket.anket.meet=='famele'">женщиной</div>
            <div v-else-if="anket.anket.meet=='male'">мужчиной</div>
            <br>

            Возраст: {{anket.anket.age}} <br>
            Рост: {{anket.anket.height}}<br>
            Вес: {{anket.anket.weight}}<br>


            <li v-for="target in allTarget ">
                <div v-if="target.id in anketTarget.id">
                    {{target.name}}
                </div>
            </li>
            <b><a href="/editimages">Фотографии</a> </b><br>
            <div v-for="photo in topPhotos">
                <a href="/editimages"><img :src="'/images/upload/'+photo.photo_name" height="150"></a>
            </div>

            <b>Текст анкеты:</b><br>
            {{anket.anket.description}}
            <br><br><br>
            <b>Приватный текст:</b><br>
            {{anket.anket.private}}
            <br><br><br>
        </div>
        <edit v-if="showEditModal" @closeRequest='close()'></edit>
    </div>

</template>

<script>
    import edit from './Edit.vue'

    export default {
        name: "myAnket",
        mounted() {
            console.log("mounted");
            this.getMyAnketData();
            this.getTopPhotos();
        },
        data() {
            return {
                applications: [],
                myapplications: [],
                whocansee: [],
                currentTab: 'main',
                anket: "",
                allTarget: "",
                anketTarget: "",
                topPhotos: "",
                showEditModal: false
            };
        },
        comments: {
            edit
        },
        methods:
            {
                getMyAnketData() {
                    axios.get('/getMyAnketData')
                        .then((response) => {
                            this.anket = response.data;
                            console.log(this.anket)
                        })
                },
                getTopPhotos() {
                    axios.get('/getTopPhotos')
                        .then((response) => {
                            this.topPhotos = response.data[0];
                        })
                }
            }
    }
</script>

<style scoped>

</style>