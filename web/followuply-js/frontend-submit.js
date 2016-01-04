$(document).ready(function() {
    $('#submit-form').submit(function() {
        var pageA = $('#pageA').val();
        var pageB = $('#pageB').val();
        var timeFrame = $('#timeframe').val();

        $.post(
            '/api/path/add',
            {
                pageA: pageA,
                pageB: pageB,
                timeFrame: timeFrame
            }
        );
    });
});
