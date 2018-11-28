$(window).on('load', function () {
    $.ajax({
        type: 'GET',
        url: '/api/get-server-status',
        data: {},
        success: function (data) {
            if (data.code === 200) {
                let gpu = data.data.gpu;
                if (gpu) {
                    $("#gpu_span_0").text(gpu + "%");
                    $("#gpu_span_1").text(gpu + "%");
                    $("#gpu_progress").width(gpu + "%");
                }
            }
        }
    });
});