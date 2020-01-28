<!-- =========================================================================================
    File Name: Login.vue
    Description: Login Page
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <div class="h-screen flex w-full bg-img vx-row no-gutter items-center justify-center" id="page-login">
        <div class="vx-col sm:w-1/2 md:w-1/2 lg:w-3/4 xl:w-3/5 sm:m-0 m-4">
            <vx-card>
                <div slot="no-body" class="full-page-bg-color">
                    <div class="vx-row no-gutter justify-center items-center">
                        <div class="vx-col hidden lg:block lg:w-1/2">
                            <img src="@assets/images/pages/login.png" alt="login" class="mx-auto">
                        </div>
                        <div class="vx-col sm:w-full md:w-full lg:w-1/2 d-theme-dark-bg">
                            <div class="p-8 login-tabs-container">

                                <div class="vx-card__title mb-4">
                                    <h4 class="mb-4">Вход</h4>
                                    <p class="mb-8">Добро пожаловать, вы можете войти в систему используя почту и пароль</p>
                                </div>

                                <div>
                                    <form name="loginForm">

                                        <vs-input
                                            name="email"
                                            icon-no-border
                                            icon="icon icon-user"
                                            icon-pack="feather"
                                            label-placeholder="Почта"
                                            v-model="email"
                                            class="w-full"
                                            v-validate="'required|email'"/>
                                        <span class="text-danger text-sm">{{ errors.first('email') }}</span>

                                        <vs-input
                                            type="password"
                                            name="password"
                                            icon-no-border
                                            icon="icon icon-lock"
                                            icon-pack="feather"
                                            label-placeholder="Пароль"
                                            v-model="password"
                                            class="w-full mt-8 mb-0"
                                            v-validate="'required|min:6'"/>
                                        <span class="text-danger text-sm">{{ errors.first('password') }}</span>

                                        <div class="mt-6">
                                            <vs-button type="filled" @click.prevent="submitForm">Вход</vs-button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </vx-card>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                email: "",
                password: "",
                checkbox_remember_me: false,
            }
        },
        methods: {
            submitForm() {
                this.$validator.validateAll().then(result => {
                    if (result) {

                        const payload = {
                            checkbox_remember_me: this.checkbox_remember_me,
                            userDetails: {
                                email: this.email,
                                password: this.password
                            },
                            notify: this.$vs.notify,
                            closeAnimation: this.$vs.loading.close
                        }

                        // Loading
                        this.$vs.loading()
                        this.$store.dispatch('auth/login', payload)
                            .then((res) => {
                                this.$vs.loading.close()
                                this.$router.push({ name: 'home'});
                            })
                            .catch(err => {
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

<style lang="scss">
</style>
