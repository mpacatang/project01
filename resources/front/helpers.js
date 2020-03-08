import moment from 'moment'

export default {
    cutText(text, length) {
        if (text.split(' ').length > 1) {
            var string = text.substring(0, length)
            var splitText = string.split(' ')
            splitText.pop()
            return splitText.join(' ') + '...'
        } else {
            return text
        }
    },
    formatDate(date, format) {
        return moment(date).format(format)
    },
    capitalizeFirstLetter(string) {
        if (string) {
            return string.charAt(0).toUpperCase() + string.slice(1)
        }
    },
    onlyNumber(number) {
        if (number) {
            return number.replace(/\D/g, '')
        } else {
            return ''
        }
    },
    formatCurrency(number) {
        if (number) {
            var number = number.toString().replace(/\D/g, '')
            var rest = number.length % 3
            var currency = number.substr(0, rest)
            var thousand = number.substr(rest).match(/\d{3}/g)
            var separator
            
            if (thousand) {
                separator = rest ? '.' : '';
                currency += separator + thousand.join('.')
            }

            return currency
        } else {
            return ''
        }
    },
    timeAgo(time) {
        var date = new Date((time || "").replace(/-/g,"/").replace(/[TZ]/g," ")),
            diff = (((new Date()).getTime() - date.getTime()) / 1000),
            dayDiff = Math.floor(diff / 86400);

        if (isNaN(dayDiff) || dayDiff < 0 || dayDiff >= 31)
            return moment(time).format("MMMM DD, YYYY");

        return dayDiff == 0 && (
                diff < 60 && "just now" ||
                diff < 120 && "1 minute ago" ||
                diff < 3600 && Math.floor( diff / 60 ) + " minutes ago" ||
                diff < 7200 && "1 hour ago" ||
                diff < 86400 && Math.floor( diff / 3600 ) + " hours ago") ||
            dayDiff == 1 && "Yesterday" ||
            dayDiff < 7 && dayDiff + " days ago" ||
            dayDiff < 31 && Math.ceil( dayDiff / 7 ) + " weeks ago";
    }
}