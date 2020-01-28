/*=========================================================================================
  File Name: moduleCalendarMutations.js
  Description: Calendar Module Mutations
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/


export default {


    SET_USERS(state, users) {
        state.users = users
    },

    ADD_USER(state, user) {
        state.users.push(user);
    },
    SET_ROLES(state, roles) {
        state.roles = roles
    },





    REMOVE_RECORD(state, itemId) {
        const userIndex = state.users.findIndex((u) => u.id == itemId)
        state.users.splice(userIndex, 1)
    },
}
