const userDefaults = {
    // id          : 0,
    // name        : " Undefined",
    // email       : "undefined@net.com",
    // balance     : 0,
    photoURL    : require("@assets/images/portrait/small/avatar-s-11.jpg")
};
const userInfoLocalStorage = JSON.parse(localStorage.getItem("auth")) || {};

const getUserInfo = () => {
    let userInfo = {}

    // Update property in user
    Object.keys(userDefaults).forEach((key) => {
        // If property is defined in localStorage => Use that
        userInfo[key] = userInfoLocalStorage[key] ?  userInfoLocalStorage[key] : userDefaults[key]
    })

    // Include properties from localStorage
    Object.keys(userInfoLocalStorage).forEach((key) => {
        if(userInfo[key] === undefined && userInfoLocalStorage[key] != null)
            userInfo[key] = userInfoLocalStorage[key]
    })

    return userInfo
}

const state = {
    user: getUserInfo()
}
export default state
