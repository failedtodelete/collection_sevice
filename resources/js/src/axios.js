import axios from 'axios'
import store from '@/store'

// Token Refresh
let isAlreadyFetchingAccessToken = false
let subscribers = []

function onAccessTokenFetched(access_token) {
    subscribers = subscribers.filter(callback => callback(access_token))
}
function addSubscriber(callback) {
    subscribers.push(callback)
}


axios.defaults.baseURL = '/api/'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': 'Bearer ' + localStorage.getItem("tokenAccess"),
};
axios.interceptors.response.use(function (response) {

    if (response.data.status === false) {
        return Promise.reject(response.data);
    }
    return response.data;
}, async function (error) {


    // Do redirect if response error 401 (Unauthorized)
    const { config, response } = error
    const originalRequest = config

    if (response && response.status === 401) {
        if (!isAlreadyFetchingAccessToken) {
            isAlreadyFetchingAccessToken = true

            store.dispatch("auth/refresh")
                .then(access_token => {
                    isAlreadyFetchingAccessToken = false
                    onAccessTokenFetched(access_token)
                })
                .catch(err => {
                    store.commit('auth/CLEAR_TOKEN_BROWSER')
                });
        }

        const retryOriginalRequest = new Promise((resolve) => {
            addSubscriber(access_token => {
                originalRequest.headers.Authorization = 'Bearer ' + access_token
                resolve(axios(originalRequest))
            })
        })
        return retryOriginalRequest
    }
    return Promise.reject(error)

});

export default axios
