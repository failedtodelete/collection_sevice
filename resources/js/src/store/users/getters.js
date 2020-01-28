/*=========================================================================================
  File Name: moduleCalendarGetters.js
  Description: Calendar Module Getters
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/


export default {
    // Получение ролей
    ROLES_EXIST_USERS: (state) => {
        let e_roles = [];
        return state.users.map(u => {
            if (!$e_roles.find(r => r.id === u.role.id)) {
                e_roles.push(u.role);
                return u.role
            }
        })
    }
    // Roles: (state, users) => {
    //     return users.Role
    // }
}
