<template>
    <div id="page-users">
        <!-- Фильтр пользователей-->
        <vx-card ref="filterCard" title="Фильтр" class="user-list-filters mb-8" actionButtons @refresh="resetColFilters" @remove="resetColFilters">
            <div class="vx-row">
                <div class="vx-col md:w-1/4 sm:w-1/2 w-full">
                    <label class="text-sm opacity-75">Роль</label>
                    <v-select :options="roleOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="roleFilter" class="mb-4 md:mb-0" />
                </div>
                <div class="vx-col md:w-1/4 sm:w-1/2 w-full">
                    <label class="text-sm opacity-75">Status</label>
                    <v-select :options="statusOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="statusFilter" class="mb-4 md:mb-0" />
                </div>
            </div>
        </vx-card>
        <!-- Таблицы пользователей-->
        <div class="vx-card p-6">
            <div class="flex flex-wrap items-center justify-between ag-grid-table-actions-right">
                <vs-input class="mb-4 md:mb-0 mr-4" v-model="searchQuery" @input="updateSearchQuery" placeholder="Search..." />
                <vs-button class="mb-4 md:mb-0" @click="$showPopup('user-create')">Создать</vs-button>
            </div>

            <ag-grid-vue
                ref="agGridTable"
                :components="components"
                :gridOptions="gridOptions"
                class="ag-theme-material w-100 my-4 ag-grid-table"
                :columnDefs="columnDefs"
                :defaultColDef="defaultColDef"
                :rowData="users"
                rowSelection="multiple"
                colResizeDefault="shift"
                :animateRows="true"
                :floatingFilter="false"
                :pagination="true"
                :paginationPageSize="paginationPageSize"
                :suppressPaginationPanel="true"
                :enableRtl="$vs.rtl">
            </ag-grid-vue>

            <!-- Пагинация-->
            <vs-pagination :total="totalPages" :max="7" v-model="currentPage" />

        </div>
    </div>
</template>

<script>

    import { AgGridVue } from "ag-grid-vue"
    import CellRendererLink from "./cell-renderer/CellRendererLink.vue"
    import CellRendererStatus from "./cell-renderer/CellRendererStatus.vue"
    import '@sass/vuexy/extraComponents/agGridStyleOverride.scss'

    import vSelect from 'vue-select'
    import storageUsersModule from '@/store/users'

    export default {
        components: {
            vSelect,
            CellRendererLink,
            CellRendererStatus,
            AgGridVue,
        },
        data() {
            return {

                // Фильтр ролей.
                roleFilter: { label: 'Все',     value: null },
                roleOptions: [
                    { label: 'Все',             value: null},
                    { label: 'Администратор',   value: 'Администратор'},
                    { label: 'Агент',           value: 'Агент'},
                ],

                // Фильтр по статусу.
                statusFilter: { label: 'Все',   value: null },
                statusOptions: [
                    { label: 'Все',             value: null},
                    { label: 'Активен',         value: 'Активен'},
                    { label: 'Не активен',      value: 'Не активен'},
                    { label: 'Заблокирован',    value: 'Заблокирован'},
                ],

                // Поисковой запрос.
                searchQuery: "",

                // Конфигурация таблицы.
                gridApi: null,
                gridOptions: {},
                defaultColDef: {
                    sortable: true,
                    resizable: true,
                    suppressMenu: true
                },
                columnDefs: [
                    { headerName: 'ID', field: 'id', width: 100 },
                    { headerName: 'Имя', field: 'name', cellRendererFramework: 'CellRendererLink' },
                    { headerName: 'Email', field: 'email' },
                    { headerName: 'Роль', field: 'role.display_name' },
                    { headerName: 'Баланс', field: 'balance', cellRendererFramework: 'CellRendererStatus' },
                    { headerName: 'Выплачено', field: 'paid_out', cellRendererFramework: 'CellRendererStatus'},
                    { headerName: 'Статус', field: 'status.display_name' }
                ],

                // Компоненты.
                components: {
                    CellRendererLink,
                    CellRendererStatus
                }
            }
        },
        watch: {
            roleFilter(obj) {this.setColumnFilter("role.display_name", obj.value)},
            statusFilter(obj) {this.setColumnFilter("status.display_name", obj.value)}
        },
        computed: {
            popup() {
                return this.$store.state.popup === 'user-create'
            },
            users() {
                return JSON.parse(JSON.stringify(this.$store.state.users.users)).reverse()
            },
            paginationPageSize() {
                if(this.gridApi) return this.gridApi.paginationGetPageSize()
                else return 10
            },
            totalPages() {
                if(this.gridApi) return this.gridApi.paginationGetTotalPages()
                else return 0
            },
            currentPage: {
                get() {
                    if(this.gridApi) return this.gridApi.paginationGetCurrentPage() + 1
                    else return 1
                },
                set(val) {
                    this.gridApi.paginationGoToPage(val - 1)
                }
            }
        },
        methods: {

            /**
             * Применение сортировки по полю и значению.
             */
            setColumnFilter(column, val) {
                const filter = this.gridApi.getFilterInstance(column)
                let modelObj = null
                if(val !== null) modelObj = { type: "equals", filter: val }
                filter.setModel(modelObj)
                this.gridApi.onFilterChanged()
            },

            /**
             * Сброс фильтра.
             */
            resetColFilters() {
                this.gridApi.setFilterModel(null)
                this.gridApi.onFilterChanged()
                this.roleFilter = this.statusFilter = this.isVerifiedFilter = this.departmentFilter = { label: 'Все', value: null}
                this.$refs.filterCard.removeRefreshAnimation()
            },

            /**
             * Применение поиска по строке.
             */
            updateSearchQuery(val) {
                this.gridApi.setQuickFilter(val)
            }
        },

        /**
         * Инициализация и конфигурация таблицы, ее формата и стиля отображения.
         */
        mounted() {

            this.gridApi = this.gridOptions.api
            this.gridApi.sizeColumnsToFit()

            /* =================================================================
              NOTE:
              Header is not aligned properly in RTL version of agGrid table.
              However, we given fix to this issue. If you want more robust solution please contact them at gitHub
            ================================================================= */
            if(this.$vs.rtl) {
                const header = this.$refs.agGridTable.$el.querySelector(".ag-header-container")
                header.style.left = "-" + String(Number(header.style.transform.slice(11,-3)) + 9) + "px"
            }
        },

        /**
         * Регистрация модуля хранилища для пользователей.
         * Загрузка всех пользователей системы в хранилище.
         */
        created() {
            if(!storageUsersModule.isRegistered) {
                this.$store.registerModule('users', storageUsersModule)
                storageUsersModule.isRegistered = true
            }

            this.$store.dispatch("users/fetchUsers").catch(err => {console.error(err)})
        }
    }

</script>

<style lang="scss">
    #page-user {
        .user-list-filters {
            .vs__actions {
                position: absolute;
                right: 0;
                top: 50%;
                transform: translateY(-58%);
            }
        }
    }
</style>
