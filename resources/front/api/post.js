export default {
    getExplorePosts(data, cb, errorCb) {
        axios.get('/api/posts/explore', {
            params: data
        })
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    getSinglePost(data, cb, errorCb) {
        axios.get('/api/posts/' + data.slug)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    getUserPosts(data, cb, errorCb) {
        axios.get('/api/users/' + data.username + '/posts', {
            params: data.params
        })
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    getSavedPosts(data, cb, errorCb) {
        axios.get('/api/saved-posts', {
            params: data
        })
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    getPosts(data, cb, errorCb) {
        axios.get('/api/posts', {
            params: data
        })
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    getLikes(data, cb, errorCb) {
        axios.get('/api/posts/' + data.postId + '/likes', {
            params: data.params
        })
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    createPost(data, cb, errorCb) {
        axios.post('/api/posts', data)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    deletePost(data, cb, errorCb) {
        axios.delete('/api/posts/' + data.postId)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    postComment(data, cb, errorCb) {
        axios.post('/api/posts/' + data.postId + '/comments', data.params)
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    like(data, cb, errorCb) {
        axios.patch('/api/posts/' + data.postId + '/likes')
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    },
    savePost(data, cb, errorCb) {
        axios.patch('/api/posts/' + data.postId + '/save')
        .then(function (response) {
            cb(response.data)
        })
        .catch(function (error) {
            errorCb(error)
        })
    }
}