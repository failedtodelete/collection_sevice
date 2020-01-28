<template>
    <div id="app" :class="vueAppClasses">

        <!-- Отображение корневой страницы. Используется проброс класса к корневому элементу. -->
        <router-view @setAppClasses="setAppClasses"/>

        <!-- Всплывающие окна -->
        <popup/>

    </div>
</template>

<script>
    import Popup from '@/components/Popup';
    import themeConfig from '../themeConfig.js'

    export default {
        components: {
            Popup
        },
        data() {
            return {

                // Класса, присваиваемые корневому элементу.
                vueAppClasses: [],
            }
        },
        watch: {

            /**
             * Прослушивание значения установленной темы в хранилище.
             * Переключение темы.
             */
            '$store.state.theme'(val) {
                this.toggleClassInBody(val)
            },

            /**
             * Переключение значения режима отображения страницы.
             */
            '$vs.rtl'(val) {
                document.documentElement.setAttribute("dir", val ? "rtl" : "ltr")
            }
        },
        methods: {

            /**
             * Переключение активного класса темы у родительского body.
             */
            toggleClassInBody(className) {
                if (className == 'dark') {
                    if (document.body.className.match('theme-semi-dark')) document.body.classList.remove('theme-semi-dark')
                    document.body.classList.add('theme-dark')
                } else if (className == 'semi-dark') {
                    if (document.body.className.match('theme-dark')) document.body.classList.remove('theme-dark')
                    document.body.classList.add('theme-semi-dark')
                } else {
                    if (document.body.className.match('theme-dark')) document.body.classList.remove('theme-dark')
                    if (document.body.className.match('theme-semi-dark')) document.body.classList.remove('theme-semi-dark')
                }
            },

            /**
             * Добавление класса к корневому #app элементу.
             * @param classesStr
             */
            setAppClasses(classesStr) {
                this.vueAppClasses.push(classesStr)
            },

            /**
             * Обработчик события изменения ширины экрана.
             * Обновление значения в хранилище.
             */
            handleWindowResize() {
                this.$store.commit('UPDATE_WINDOW_WIDTH', window.innerWidth)
                document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
            },

            /**
             * Обработчик события изменения позиции прокручивания (скрола страницы)
             */
            handleScroll() {
                this.$store.commit('UPDATE_WINDOW_SCROLL_Y', window.scrollY)
            }
        },

        /**
         * Внедрение основной темы как
         * класса для родительского элемента верстки.
         * После первоначальной загрузки приложения происходит фиксация ширины страницы в хранилище.
         */
        mounted() {
            this.toggleClassInBody(themeConfig.theme)
            this.$store.commit('UPDATE_WINDOW_WIDTH', window.innerWidth)

            let vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        },

        /**
         * Установка конфуигураций темы и запуск обработчиков событий
         * изменения ширины экрана и вертикального скрола
         * @returns {Promise<void>}
         */
        async created() {

            let dir = this.$vs.rtl ? "rtl" : "ltr"
            document.documentElement.setAttribute("dir", dir)
            window.addEventListener('resize', this.handleWindowResize)
            window.addEventListener('scroll', this.handleScroll)

        },

        /**
         * При смерти текущего экземпляра удаляются запущенные в created обработчики
         * прослушивающие изменения ширины экрана и вертикального скрола
         */
        destroyed() {
            window.removeEventListener('resize', this.handleWindowResize)
            window.removeEventListener('scroll', this.handleScroll)
        },
    }

</script>
