export default {
    getRealtime(data, cb, errorCb) {
        axios.post('/api/realtime', data)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    }
}