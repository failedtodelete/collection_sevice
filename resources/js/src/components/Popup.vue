<template>
    <div class="popups">
        <UserCreatePopup/>
        <LinkCreatePopup/>
    </div>
</template>

<script>
    import UserCreatePopup from '@/popups/UserCreatePopup'
    import LinkCreatePopup from '@/popups/LinkCreatePopup'

    export default {
        name: "Popup",
        components: {
            UserCreatePopup,
            LinkCreatePopup,
        }
    }
</script>

<style scoped>

</style>
