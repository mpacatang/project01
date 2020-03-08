export default {
    login(data, cb, errorCb) {
        axios.post('/oauth/token', data)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    register(data, cb, errorCb) {
        axios.post('/api/register', data)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    activateAccount(data, cb, errorCb) {
        axios.post('/api/activate-account', data)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    resendActivation(data, cb, errorCb) {
        axios.post('/api/resend-activation', data)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    changePassword(data, cb, errorCb) {
        axios.patch('/api/change-password', data)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    forgotPassword(data, cb, errorCb) {
        axios.post('/api/forgot-password', data)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    resetPassword(data, cb, errorCb) {
        axios.post('/api/reset-password', data)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    getAuthenticatedUser(cb, errorCb) {
        axios.post('/get-authenticated-user')
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    }
}