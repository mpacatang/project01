import ErrorCallback from './error-callback'

export default {
    getNotifications(data, cb, errorCb) {
        axios.get('/api/notifications', {
            params: data
        })
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            if (!ErrorCallback.defaultError(error)) {
                errorCb(error)
            }
        })
    },
    getProfile(cb, errorCb) {
        axios.get('/api/profile')
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
        	if (!ErrorCallback.defaultError(error)) {
        		errorCb(error)
        	}
        })
    },
    getSingleUser(data, cb, errorCb) {
        axios.get('/api/users/' + data.username)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            if (!ErrorCallback.defaultError(error)) {
                errorCb(error)
            }
        })
    },
    getUsers(data, cb, errorCb) {
        axios.get('/api/users', {
            params: data
        })
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            if (!ErrorCallback.defaultError(error)) {
                errorCb(error)
            }
        })
    },
    follow(data, cb, errorCb) {
        axios.patch('/api/users/' + data.userId + '/follow')
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            if (!ErrorCallback.defaultError(error)) {
                errorCb(error)
            }
        })
    },
    getFollowers(data, cb, errorCb) {
        axios.post('/api/users/' + data.userId + '/followers', data.params)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            if (!ErrorCallback.defaultError(error)) {
                errorCb(error)
            }
        })
    },
    getFollowing(data, cb, errorCb) {
        axios.post('/api/users/' + data.userId + '/following', data.params)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            if (!ErrorCallback.defaultError(error)) {
                errorCb(error)
            }
        })
    },
    getSuggestedPeople(cb, errorCb) {
        axios.get('/api/suggested-people')
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            if (!ErrorCallback.defaultError(error)) {
                errorCb(error)
            }
        })
    },
    updateProfile(data, cb, errorCb) {
        axios.patch('/api/profile', data)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    }
}