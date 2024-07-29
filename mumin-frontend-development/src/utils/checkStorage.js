

export const setLoginResponseCheck = () => {
    const accessToken = localStorage.getItem('access-token');
    const user = localStorage.getItem('user');
    const defaultCurrency = localStorage.getItem('defaultCurrency');
    const defaultLanguage = localStorage.getItem('defaultLanguage');
    if (!accessToken || !user || !defaultCurrency || !defaultLanguage) {
        localStorage.clear()
    }
    const data = { accessToken, user, defaultCurrency, defaultLanguage };

    return data
}


