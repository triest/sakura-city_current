<template>
    <div>
        <carousel :per-page="1" :mouse-drag="false" :autoplay="true" :loop="true" :centerMode="true"
                  :navigationEnabled="true">

            <slide v-for="image in imagesList" :key="image[0].id">
                <div class="flex-center2 position-ref">
                    <a :href="'/anket/'+image[0].id">
                        <img :src="'/images/small/'+image[0].main_image" height="100">
                    </a>
                    <b>{{image[0].name}},</b>
                    <b>Возраст:{{image[0].age}}</b>
                </div>
            </slide>
            <slide>
                <div class="flex-center2 position-ref">
                    <a :href="'/power'">
                        <img :src="'/images/anketa.jpeg'" height="100">
                    </a>
                </div>
            </slide>

        </carousel>
    </div>
</template>

<script>

    export default {
        data() {
            return {
                imagesList: []
            };
        },
        mounted() {
            this.getmainImage()
        },
        methods:
            {
                getmainImage() {
                    axios.get('/getdataforcarousel')
                        .then((response) => {
                            this.imagesList = response.data;
                        });
                }
            }
    };
</script>

<style scoped>


    .carousel {
        text-align: center; /* Родительскому задать выравнивание по центру */
    }

    .flex-center2 {
        align-items: center;
        display: flex;
        justify-content: center;
    }

    .position-ref {
        position: relative;
    }
</style>