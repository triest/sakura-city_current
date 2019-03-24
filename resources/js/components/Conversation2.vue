<template>
    <div class="conversation2">
        <h1>{{ contact ? contact.name : 'Select a Contact' }}</h1>
        <MessagesFeed2 :contact="contact" :messages="messages"/>
        <MessageComposer2 @send="sendMessage"/>
    </div>
</template>

<script>
    import MessagesFeed2 from './MessagesFeed2';
    import MessageComposer2 from './MessageComposer2';

    export default {
        props: {
            contact: {
                type: Object,
                default: null
            },
            messages: {
                type: Array,
                default: []
            }
        },
        methods: {
            sendMessage(text) {
                if (!this.contact) {
                    return;
                }

                axios.post('/conversation/send', {
                    contact_id: this.contact.id,
                    text: text
                }).then((response) => {
                    this.$emit('new', response.data);
                })
            }
        },
        components: { MessageComposer2,MessagesFeed2}
    }
</script>

<style lang="scss" scoped>
    .conversation {
        flex: 5;
        display: flex;
        flex-direction: column;
        justify-content: space-between;

        h1 {
            font-size: 20px;
            padding: 10px;
            margin: 0;
            border-bottom: 1px dashed lightgray;
        }
    }
</style>
