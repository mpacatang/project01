export default {
    defaultError(error) {
        var response = error.response !== undefined ? error.response.data : error
    	if (response.message == 'Unauthenticated.') {
            localStorage.removeItem('token')
			window.location.href = '/'
    	}

        return false
    }
}