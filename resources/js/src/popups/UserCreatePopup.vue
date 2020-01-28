<template>
    <vs-popup class="" title="Создание пользователя" :active.sync="active">
        <form name="userCraeteForm">
            <div class="vx-row">
                <div class="vx-col md:w-1/2 w-full">
                    <vs-input class="w-full mt-4" label="Имя" v-model="name" v-validate="'required|alpha_num'" name="name" />
                    <span class="text-danger text-sm" v-show="errors.has('name')">{{ errors.first('name') }}</span>
                </div>
                <div class="vx-col md:w-1/2 w-full">
                    <vs-input class="w-full mt-4" label="Email" v-model="email" v-validate="'required|email'" name="email" />
                    <span class="text-danger text-sm" v-show="errors.has('email')">{{ errors.first('email') }}</span>
                </div>
                <div class="vx-col md:w-1/2 w-full">
                    <vs-input class="w-full mt-4" label="Пароль" v-model="password" v-validate="'required|min:6'" name="password" />
                    <span class="text-danger text-sm" v-show="errors.has('password')">{{ errors.first('password') }}</span>
                </div>
                <div class="vx-col md:w-1/2 w-full">
                    <div class="mt-4">
                        <label class="text-sm opacity-75">Роль</label>
                        <v-select :options="roleOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="role" class="mb-4 md:mb-0" />
                    </div>
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
    import vSelect from 'vue-select'
    export default {
        components: {
            vSelect
        },
        data() {
            return {

                name: '',
                email: '',
                password: '',

                // Фильтр ролей.
                role: { id: 1, label: 'Администратор',   value: 'Администратор'},
                roleOptions: [
                    { id: 1, label: 'Администратор',   value: 'Администратор'},
                    { id: 2, label: 'Агент',           value: 'Агент'},
                ],
            }
        },
        computed: {
            active: {
                get() {return this.$store.state.popup && this.$store.state.popup.name === 'user-create'},
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
                            name: this.name,
                            email: this.email,
                            password: this.password,
                            role_id: this.role.id
                        };
                        this.$store.dispatch('users/store', payload).then(user => {
                            this.$vs.loading.close()
                            this.$vs.notify({
                                title: 'Успешно',
                                text: "Пользователь успешно добавлен",
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
