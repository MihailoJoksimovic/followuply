/**
 * JavaScript snippet to be pasted on client's website
 */

__followuplyopts = __followuplyopts || {};

(function(window, document, undefined) {

    // This is a cross platform AJAX lib

    /**
     * IE 5.5+, Firefox, Opera, Chrome, Safari XHR object
     *
     * @param string url
     * @param object callback
     * @param mixed data
     * @param null x
     */
    var ajax = function(url, callback, data, cache) {

        // Must encode data
        if(data && typeof(data) === 'object') {
            var y = '', e = encodeURIComponent;
            for (x in data) {
                y += '&' + e(x) + '=' + e(data[x]);
            }
            data = y.slice(1) + (! cache ? '&_t=' + new Date : '');
        }

        try {
            var x = new(this.XMLHttpRequest || ActiveXObject)('MSXML2.XMLHTTP.3.0');
            x.open(data ? 'POST' : 'GET', url, 1);
            x.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            x.onreadystatechange = function () {
                x.readyState > 3 && callback && callback(x.responseText, x);
            };
            x.send(data)
        } catch (e) {
            window.console && console.log(e);
        }
    };

    var generateUid = function (separator) {
        /// <summary>
        ///    Creates a unique id for identification purposes.
        /// </summary>
        /// <param name="separator" type="String" optional="true">
        /// The optional separator for grouping the generated segmants: default "-".
        /// </param>

        var delim = separator || "-";

        function S4() {
            return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
        }

        return (S4() + S4() + delim + S4() + delim + S4() + delim + S4() + delim + S4() + S4() + S4());
    };

    function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
    }

    //
    // Real followuply code starts here
    //

    var email = __followuplyopts.email;
    var callbackFn = function() {};
    var uid = getCookie('fuplyuid') || generateUid();

    document.cookie = 'fuplyuid='+uid;

    var submitData = {
        email: email,
        url: document.location.href,
        uid: uid
    };

    ajax("http://192.168.33.11/api/pageview/submit", callbackFn, submitData);

}(window, document));

