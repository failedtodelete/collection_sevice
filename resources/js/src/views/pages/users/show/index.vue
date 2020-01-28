<template>
    <div id="page-users-view">

        <!-- Отображение уведомления о том что пользователь не найден-->
        <vs-alert color="danger" title="User Not Found" :active.sync="user_not_found">
            <span>Пользователь: {{ $route.params.userId }} не найден. </span>
            <span><router-link :to="{name:'users'}" class="text-inherit underline">Все пользователи</router-link></span>
        </vs-alert>

        <!-- Отображение карточки пользователя-->
        <div id="user-data" v-if="user">
            <vx-card title="Account" class="mb-base">
                <div class="vx-row">

                    <!-- Аватар -->
                    <div class="vx-col" id="avatar-col">
                        <div class="img-container mb-4">
                            <img src="/images/avatar-s-11.jpg" class="rounded w-full" />
                        </div>
                    </div>

                    <!-- Основная информация 1 -->
                    <div class="vx-col flex-1" id="account-info-col-1">
                        <table>
                            <tr>
                                <td class="font-semibold">Имя</td>
                                <td>{{ user.name }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Email</td>
                                <td>{{ user.email }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Роль</td>
                                <td>{{ user.role.display_name }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Основная информация 2 -->
                    <div class="vx-col flex-1" id="account-info-col-2">
                        <table>
                            <tr>
                                <td class="font-semibold">Статус</td>
                                <td>{{ user.status.display_name }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Баланс</td>
                                <td>{{ user.balance }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Выплачено</td>
                                <td>{{ user.paid_out }}</td>
                            </tr>
                        </table>
                    </div>

                </div>
            </vx-card>
        </div>
    </div>
</template>

<script>
    import storageUsersModule from '@/store/users'
    export default {
        data() {
            return {
                user: null,
                user_not_found: false,
            }
        },

        /**
         * Регистрация модуля хранилища для пользователей.
         * Загрузка пользователя системы..
         * Пользователь НЕ хранится в хранилище.
         */
        created() {

            if(!storageUsersModule.isRegistered) {
                this.$store.registerModule('users', storageUsersModule)
                storageUsersModule.isRegistered = true
            }

            const userId = this.$route.params.userId
            this.$store.dispatch("users/fetchUser", userId)
                .then(user => { this.user = user })
                .catch(err => {
                    if (err.response && err.response.status === 404) {
                        this.user_not_found = true
                    }
                })
        }
    }

</script>

<style lang="scss">
    #avatar-col { width: 10rem }
    #page-users-view {
        table {
            td {
                vertical-align: top;
                min-width: 140px;
                padding-bottom: .8rem;
                word-break: break-all;
            }

            &:not(.permissions-table) {
                td {
                    @media screen and (max-width:370px) {
                        display: block;
                    }
                }
            }
        }
    }

    @media screen and (min-width:1201px) and (max-width:1211px),
    only screen and (min-width:636px) and (max-width:991px) {
        #account-info-col-1 {
            width: calc(100% - 12rem) !important;
        }
    }

</style>
