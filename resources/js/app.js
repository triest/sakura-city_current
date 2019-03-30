/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');


window.Vue = require('vue');

import VueCarousel from 'vue-carousel';

Vue.use(VueCarousel);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ChatApp.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('chat-app', require('./components/ChatApp.vue').default);
Vue.component('chat-app2', require('./components/ChatApp2.vue').default);
Vue.component('conversation', require('./components/Conversation.vue').default);
Vue.component('conversation2', require('./components/Conversation2.vue').default);
Vue.component('ContactsList', require('./components/ContactsList.vue').default);
Vue.component('ContactsList2', require('./components/ContactsList2.vue').default);
Vue.component('MessagesFeed', require('./components/MessagesFeed.vue').default);
Vue.component('MessagesFeed2', require('./components/MessagesFeed2.vue').default);

Vue.component('side-panel', require('./components/SidePanele.vue').default);
//Vue.component('header', require('./components/Carousel.vue').default);
Vue.component('privatepanel', require('./components/Private.vue').default);

//компонент с заявками
Vue.component('application', require('./components/Application.vue').default);

Vue.component('editimages', require('./components/Editimages.vue').default);

Vue.component('modal-template', require('./components/modal-template.vue').default);

Vue.component('power', require('./components/Power.vue').default);

Vue.component('carouseltemp', require('./components/CarouselTemp.vue').default);

Vue.component('presents', require('./components/Presents.vue').default);

Vue.component('mypresents', require('./components/MyPresents.vue').default);

//модальное окно для отправки сообщения
//Vue.component('modal', require('./components/ModalComponent.vue'));
Vue.component('delmodal', require('./components/delModal.vue').default);


Vue.component('myanket', require('./components/myAnket.vue').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('edit', require('./components/Edit.vue').default);

Vue.component('phonecomponent', require('./components/phoneComponent.vue').default);

const app = new Vue({
    el: '#app',
    data: {
        showModal: false
    }
});

const app2 = new Vue({
    el: '#app2',
    data: {
        showModal: false
    }
});

const app3 = new Vue({
    el: '#app3',
    data: {
        showModal: false
    }
});

const app4 = new Vue({
    el: '#app4',
    data: {
        showModal: false
    }
});

const privateApp = new Vue({
    el: '#privateApp',
    data: {
        showModal: false
    }
});

const app7 = new Vue({
    el: '#app7',
    data: {
        showModal: false
    }
});

const applicationClass = new Vue({
    el: '#applicationClass',
    data: {
        showModal: false
    }
});

const powerApp = new Vue({
    el: '#powerApp',
    data: {
        showModal: false
    }
});

const myAnket = new Vue({
    el: '#myAnketApp',
    data: {
        showModal: false
    }
});

const phoneApp = new Vue({
    el: '#phoneApp',
    data: {
        showModal: false
    }
});