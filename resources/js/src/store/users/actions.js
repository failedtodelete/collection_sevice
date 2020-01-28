/*=========================================================================================
  File Name: moduleCalendarActions.js
  Description: Calendar Module Actions
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

import axios from "@/axios"

export default {
    // addItem({ commit }, item) {
    //   return new Promise((resolve, reject) => {
    //     axios.post("/api/data-list/products/", {item: item})
    //       .then((response) => {
    //         commit('ADD_ITEM', Object.assign(item, {id: response.data.id}))
    //         resolve(response)
    //       })
    //       .catch((error) => { reject(error) })
    //   })
    // },


    async fetchUsers({ commit }) {
        await axios.get('/users')
            .then(users => {
                commit('SET_USERS', users.data)
            });
    },

    async fetchRoles({ commit }) {
        await axios.get('/users/roles')
            .then(roles => {
                commit('SET_ROLES', roles.data)
            })
    },

    async fetchUser({}, id) {
        return await axios.get('/users/' + id)
            .then(user => {
                return user;
            })
    },

    async store({ commit }, data) {
        return await axios.post('/users', data)
            .then(user => {
                console.log(user);
                commit('ADD_USER', user)
                return user;
            })
    },





    removeRecord({ commit }, userId) {
        return new Promise((resolve, reject) => {
            axios.delete(`/api/user-management/users/${userId}`)
                .then((response) => {
                    commit('REMOVE_RECORD', userId)
                    resolve(response)
                })
                .catch((error) => { reject(error) })
        })
    }
}
