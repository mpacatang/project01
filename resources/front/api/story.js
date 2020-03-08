export default {
    getStories(data, cb, errorCb) {
        axios.get('/api/stories', {
            params: data
        })
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    createStory(data, cb, errorCb) {
        axios.post('/api/stories', data)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    viewStory(data, cb, errorCb) {
        axios.post('/api/stories/' + data.storyId + '/view', data)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    }
}