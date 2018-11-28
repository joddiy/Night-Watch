$(window).on('load', function () {
    $(".clickable_panel").click(function () {
        let cluster = $(this).find("input")[0].value;
        window.location.href = "/site/gpu?cluster=" + cluster;
    });

    let current_cluster_name = $('#current_cluster_name').attr("value");
    if (current_cluster_name) {
        $.ajax({
            type: 'GET',
            url: '/api/get-gpu-history?cluster=' + current_cluster_name,
            data: {},
            success: function (data) {
                if (data.code === 200) {
                    for (var i = 0; i < data.data.length; i++) {
                        let tmp_data = data.data[i];
                        var chart = Morris.Area({
                            element: 'morris-chart-' + current_cluster_name + i,
                            data: tmp_data,
                            axes: false,
                            xkey: 'add_time',
                            ykeys: ['memory_rate', 'power_rate'],
                            ymax: 100,
                            labels: ['Memory Usage', 'Utilization'],
                            yLabelFormat: function (y) {
                                return y.toString() + ' %';
                            },
                            gridEnabled: false,
                            gridLineColor: 'transparent',
                            lineColors: ['#1b72bc', '#8eb5e3'],
                            lineWidth: 0,
                            pointSize: 0,
                            pointFillColors: ['#3e80bd'],
                            pointStrokeColors: '#3e80bd',
                            fillOpacity: .7,
                            gridTextColor: '#999',
                            parseTime: false,
                            resize: true,
                            behaveLikeLine: true,
                            hideHover: 'auto'
                        });
                    }
                }
            }
        });
    }


});