import axios from "@/axios"
import Vue from 'vue'

const mutations = {

    // Updates user info in state and localstorage
    UPDATE_USER_INFO(state, payload) {

        // Get Data localStorage
        let userInfo = JSON.parse(localStorage.getItem("auth")) || state.user
        for (const property of Object.keys(payload)) {
            if (payload[property] != null) {

                // If some of user property is null - user default property defined in state.
                state.user[property] = payload[property]

                // Update key in localStorage
                userInfo[property] = payload[property]
            }
        }
        // Store data in localStorage
        localStorage.setItem("auth", JSON.stringify(userInfo))
    },

    SET_BEARER(state, data) {
        var t = new Date();
        t.setSeconds(t.getSeconds() + data.exp);
        const tokenExpDate = Vue.moment(t).unix();
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + data.accessToken;
        localStorage.setItem('tokenAccess', data.accessToken)
        localStorage.setItem('tokenExpDate', tokenExpDate)
    },

    CLEAR_TOKEN_BROWSER() {
        localStorage.removeItem('auth')
        localStorage.removeItem('tokenExpDate')
        localStorage.removeItem('tokenAccess')
    }

}
export default mutations
