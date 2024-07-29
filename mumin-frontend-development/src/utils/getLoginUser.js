

export const getLoginUser = () => {
    const user = JSON.parse(localStorage.getItem('user'))
    return user
}