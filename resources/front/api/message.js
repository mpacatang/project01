export default {
    getMessages(data, cb, errorCb) {
        axios.get('/api/messages', {
            params: data
        })
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    getMessageDetails(data, cb, errorCb) {
        axios.get('/api/messages/' + data.username, {
            params: data.params
        })
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    postMessage(data, cb, errorCb) {
        axios.post('/api/messages/' + data.username, data.params)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    readMessages(data, cb, errorCb) {
        axios.patch('/api/messages/' + data.username + '/read')
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    }
}