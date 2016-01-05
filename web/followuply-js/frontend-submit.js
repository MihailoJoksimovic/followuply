$(document).ready(function() {
    $('#submit-form').submit(function() {
        var pageA = $('#pageA').val();
        var pageB = $('#pageB').val();
        var timeFrame = $('#timeframe').val();

        var successFn = function(response) {
            if (!response.success) {
                failureFn(response);
                return;
            }

            alert("Path added successfully!");
        };

        var failureFn = function(response) {
            alert("An error has occurred. Please, try again.")
        };

        $.post(
            '/api/path/add',
            {
                pageA: pageA,
                pageB: pageB,
                timeFrame: timeFrame
            },
            successFn,
            'json'
        );
    });
});
