import Vue from "vue";

const getters = {

    /**
     * Получение (boolean) значения аутентификации пользователя.
     * Получеине времени окончания длительности токена из хранилища.
     * Пользовать аутентицифрован только есть текущее время меньше чем время смерти токена.
     * @returns {boolean|boolean}
     */
    isAuthenticated: state => {
        // Получение токена
        let exp = localStorage.getItem('tokenExpDate')
        if (!exp || exp && !exp.length) return false
        // Сравнение с текущим временем.
        let tokenExp = parseInt(exp)
        let nowExp = Vue.moment(new Date()).unix()
        return nowExp < tokenExp
    }
}

export default getters
