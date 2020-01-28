import axios from "@/axios"
export default {

    /**
     * Аутентификация пользователя.
     * Проверка, аутентифицирован ли пользователь -
     * Если пользователь уже аутентифицирован, закрытие анимации загрузки.
     * Отправка запроса на сервер для получения токена, установка значений в localstorage
     * Обновление профиля пользовтаеля в state.
     *
     * @param getters
     * @param dispatch
     * @param commit
     * @param payload
     * @returns {Promise<*>}
     */
    async login({ getters, dispatch, commit }, payload) {
        if (getters.isAuthenticated) {
            if(payload.closeAnimation) payload.closeAnimation()
            payload.notify({
                title: 'Ошибка входа',
                text: 'Вы уже аутентицированны',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'warning'
            })
            return;
        }

        return await axios.post('auth/login', payload.userDetails).then(async response => {

            // Занести данные в хранилище. (token)
            commit("SET_BEARER", {
                accessToken: response.access_token,
                exp: response.expires_in
            })

            // Аутентифицировать пользователя.
            commit('UPDATE_USER_INFO', await axios.get('profile'))

        }).catch(err => {
            throw new Error(err.message);
        })
    },

    /**
     * Выход из системы.
     * Отправка запроса на сервер, если выход успешен -
     * удаление пользователя, времени окончания и сам токен из хранилища
     * @param state
     * @param commit
     * @param payload
     * @returns {Promise<*>}
     */
    async logout({ state, commit }, payload) {
        return await axios.post('auth/logout').then(async response => {
            commit("CLEAR_TOKEN_BROWSER");
        });
    },


    async refresh({ commit }) {
        return await axios.post('auth/refresh')
            .then(async response => {

                // Занести данные в хранилище. (token)
                await commit("SET_BEARER", {accessToken: response.access_token, exp: response.expires_in});
                return response.access_token;

            }).catch(err => {
                commit("CLEAR_TOKEN_BROWSER")
                throw err;
            });
    },
    profile() {
        alert(123);
    }
}
