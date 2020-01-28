<template>
    <div id="page-home">
        <div class="vx-row">
            <!-- Приветственная карточка пользователя. -->
            <div class="vx-col w-full lg:w-1/2 mb-base">
                <vx-card slot="no-body" class="text-center bg-primary-gradient greet-user">
                    <img src="@assets/images/elements/decore-left.png" class="decore-left" alt="Decore Left" width="200" >
                    <img src="@assets/images/elements/decore-right.png" class="decore-right" alt="Decore Right" width="175">
                    <feather-icon icon="AwardIcon" class="p-6 mb-8 bg-primary inline-flex rounded-full text-white shadow" svgClasses="h-8 w-8"></feather-icon>
                    <h1 class="mb-6 text-white">Привет, {{ profile.name }}</h1>
                    <p class="xl:w-3/4 lg:w-4/5 md:w-2/3 w-4/5 mx-auto text-white">Ты агент, а это значит что ты можешь продавать ссылки и создавать сайты</p>
                </vx-card>
            </div>
            <!-- Отображение сводных данных текущего пользователя -->
            <div class="vx-col w-full lg:w-1/2 mb-base">
                <div class="vx-row">
                    <div class="vx-col w-full">
                        <div class="vx-row">
                            <!-- Текущий баланс пользователя -->
                            <div class="vx-col w-full sm:w-1/2 md:w-1/1">
                                <statistics-card-line
                                    hideChart
                                    class="mb-base"
                                    icon="InboxIcon"
                                    icon-right
                                    :statistic="profile.balance + '₽'"
                                    color="info"
                                    statisticTitle="Баланс" />
                            </div>
                            <!-- Выплаченые деньги -->
                            <div class="vx-col w-full sm:w-1/2 md:w-1/1">
                                <statistics-card-line
                                    hideChart
                                    class="mb-base"
                                    icon="Trash2Icon"
                                    icon-right
                                    :statistic="profile.paid_out + '₽'"
                                    color="success"
                                    statisticTitle="Выплачено" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="vx-row">
                    <div class="vx-col w-full">
                        <div class="vx-row">
                            <!-- Проданные ссылки -->
                            <div class="vx-col w-full sm:w-1/2 md:w-1/1">
                                <statistics-card-line
                                    hideChart
                                    class="mb-base"
                                    icon="MapPinIcon"
                                    icon-right
                                    statistic="192"
                                    statisticTitle="Ссылок" />
                            </div>
                            <!-- Созданные сайты -->
                            <div class="vx-col w-full sm:w-1/2 md:w-1/1">
                                <statistics-card-line
                                    hideChart
                                    class="mb-base"
                                    icon="FileTextIcon"
                                    icon-right
                                    statistic="271"
                                    color="warning"
                                    statisticTitle="Сайтов" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="vx-row">
            <!-- Ссылки текущего пользователя -->
            <div class="vx-col w-full lg:w-1/2 mb-base">
                <div class="vx-col w-full">
                    <vx-card title="Мои ссылки">
                        <template slot="actions">
                            <vs-icon icon-pack="feather" icon="icon-plus-square" size="1.8rem" class="cursor-pointer" @click="$showPopup('link-create')"></vs-icon>

<!--                            <span class="text-grey">1 day ago</span>-->
<!--                            icon-plus-square-->
                        </template>


                        <div slot="no-body" class="mt-4">
                            <vs-table :data="links" class="table-dark-inverted">
                                <template slot="thead">
                                    <vs-th>ID</vs-th>
                                    <vs-th>ССЫЛКА</vs-th>
                                    <vs-th>СТАТУС</vs-th>
                                </template>

                                <template slot-scope="{data}">
                                    <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                                        <vs-td :data="data[indextr].orderNo">
                                            <span>#{{data[indextr].orderNo}}</span>
                                        </vs-td>
                                        <vs-td :data="data[indextr].status">
                                            <span class="flex items-center px-2 py-1 rounded"><div class="h-3 w-3 rounded-full mr-2" :class="'bg-' + data[indextr].statusColor"></div>{{data[indextr].status}}</span>
                                        </vs-td>
                                        <vs-td :data="data[indextr].orderNo">
                                            <ul class="users-liked user-list">
                                                <li v-for="(user, userIndex) in data[indextr].usersLiked" :key="userIndex">
                                                    <vx-tooltip :text="user.name" position="bottom">
                                                        <vs-avatar :src="user.img" size="30px" class="border-2 border-white border-solid -m-1"></vs-avatar>
                                                    </vx-tooltip>
                                                </li>
                                            </ul>
                                        </vs-td>
                                        <vs-td :data="data[indextr].orderNo">
                                            <span>{{data[indextr].location}}</span>
                                        </vs-td>
                                        <vs-td :data="data[indextr].orderNo">
                                            <span>{{data[indextr].distance}}</span>
                                            <vs-progress :percent="data[indextr].distPercent" :color="data[indextr].statusColor"></vs-progress>
                                        </vs-td>
                                        <vs-td :data="data[indextr].orderNo">
                                            <span>{{data[indextr].startDate}}</span>
                                        </vs-td>
                                        <vs-td :data="data[indextr].orderNo">
                                            <span>{{data[indextr].estDelDate}}</span>
                                        </vs-td>
                                    </vs-tr>
                                </template>
                            </vs-table>
                        </div>

                    </vx-card>
                </div>
            </div>
            <!-- Сайты текущего пользователя -->
            <div class="vx-col w-full lg:w-1/2 mb-base"></div>
        </div>
    </div>
</template>

<script>
    import StatisticsCardLine from '@/vendor/statistics-cards/StatisticsCardLine.vue'

    export default {
        components: {
            StatisticsCardLine
        },
        data() {
            return {
                links: []
            }
        },
        computed: {

            /**
             * Получение текущего пользователя
             * @returns auth user
             */
            profile() {
                return this.$store.state.auth.user
            }
        }
    }
</script>

<style lang="scss">
    #page-home {
        .greet-user{
            position: relative;

            .decore-left{
                position: absolute;
                left:0;
                top: 0;
            }
            .decore-right{
                position: absolute;
                right:0;
                top: 0;
            }
        }

        @media(max-width: 576px) {
            .decore-left, .decore-right{
                width: 140px;
            }
        }
    }
</style>
