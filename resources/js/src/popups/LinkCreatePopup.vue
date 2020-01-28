<template>
    <vs-popup title="Создание ссылки" :active.sync="active">
        <form name="linkCreateForm">
            <div class="vx-row">
                <div class="vx-col w-full">
                    <vs-input class="w-full mt-4" label="Ссылка" v-model="url" v-validate="'required|url'" name="url" />
                    <span class="text-danger text-sm" v-show="errors.has('url')">{{ errors.first('url') }}</span>
                </div>
            </div>
            <div class="vx-row">
                <div class="vx-col w-full">
                    <div class="mt-8 flex flex-wrap items-center justify-end">
                        <vs-button class="ml-auto mt-2" @click.prevent="submit" :disabled="!validation">Создать</vs-button>
                    </div>
                </div>
            </div>
        </form>
    </vs-popup>
</template>

<script>

    export default {
        data() {
            return {
                url: '',
                type: ''
            }
        },
        computed: {
            active: {
                get() {return this.$store.state.popup && this.$store.state.popup.name === 'link-create'},
                set() {this.$store.dispatch('showPopup', null);}
            },
            validation() {
                return !this.errors.any()
            }
        },
        methods: {
            submit() {
                this.$validator.validateAll().then(result => {
                    if (result) {

                        this.$vs.loading()
                        let payload = {
                            name: this.url,
                            type_id: this.type.id
                        };
                        this.$store.dispatch('sites/create', payload).then(link => {
                            this.$vs.loading.close()
                            this.$vs.notify({
                                title: 'Успешно',
                                text: "Ссылка отправлена на модерацию",
                                iconPack: 'feather',
                                icon: 'icon-alert-circle',
                                color: 'success'
                            })
                            this.$store.dispatch('showPopup', null)

                        }).catch(err => {
                            this.$vs.loading.close()
                            this.$vs.notify({
                                title: 'Ошибка',
                                text: err.message,
                                iconPack: 'feather',
                                icon: 'icon-alert-circle',
                                color: 'danger'
                            })
                        });
                    }
                });
            }
        }
    }
</script>

<style scoped>

</style>
