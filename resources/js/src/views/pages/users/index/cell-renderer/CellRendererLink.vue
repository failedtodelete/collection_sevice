<template>
  <div class="flex items-center">
    <vs-avatar :src="params.data.avatar" class="flex-shrink-0 mr-2" size="30px" @click="$router.push(url)" />
    <router-link :to="url" @click.stop.prevent class="text-inherit hover:text-primary">{{ params.value }}</router-link>
  </div>
</template>

<script>
    export default {
        name: 'CellRendererLink',
        computed: {
          url() {
            return "/users/" + this.params.data.id
          }
        }
    }
</script>
