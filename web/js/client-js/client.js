if (typeof __followuplyopts == "undefined") {
    __followuplyopts = {}
}

$(document).ready(function () {
    var a = encodeURIComponent(window.location);
    console.log("Current URL is: " + a);
    __followuplyopts.url = a;
    $.get("http://followuply.dev/api/event/submit", __followuplyopts)
});