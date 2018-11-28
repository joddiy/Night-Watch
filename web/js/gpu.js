$(window).on('load', function () {
    $(".clickable_panel").click(function () {
        let cluster = $(this).find("input")[0].value;
        window.location.href = "/site/gpu?cluster="+cluster;
    });

    var day_data = [
        {"elapsed": "2013 - 01", "value": 24, b: 2},
        {"elapsed": "2013 - 02", "value": 34, b: 22},
        {"elapsed": "2013 - 03", "value": 33, b: 7},
        {"elapsed": "2013 - 04", "value": 22, b: 6},
        {"elapsed": "2013 - 05", "value": 28, b: 17},
        {"elapsed": "2013 - 06", "value": 60, b: 15},
        {"elapsed": "2013 - 07", "value": 60, b: 17},
        {"elapsed": "2013 - 08", "value": 70, b: 7},
        {"elapsed": "2013 - 09", "value": 67, b: 18},
        {"elapsed": "2013 - 10", "value": 86, b: 18},
        {"elapsed": "2013 - 11", "value": 86, b: 18},
        {"elapsed": "2013 - 12", "value": 113, b: 29},
        {"elapsed": "2014 - 01", "value": 130, b: 23},
        {"elapsed": "2014 - 02", "value": 114, b: 10},
        {"elapsed": "2014 - 03", "value": 80, b: 22},
        {"elapsed": "2014 - 04", "value": 109, b: 7},
        {"elapsed": "2014 - 05", "value": 100, b: 6},
        {"elapsed": "2014 - 06", "value": 105, b: 17},
        {"elapsed": "2014 - 07", "value": 110, b: 15},
        {"elapsed": "2014 - 08", "value": 102, b: 17},
        {"elapsed": "2014 - 09", "value": 107, b: 7},
        {"elapsed": "2014 - 10", "value": 60, b: 18},
        {"elapsed": "2014 - 11", "value": 67, b: 18},
        {"elapsed": "2014 - 12", "value": 76, b: 18},
        {"elapsed": "2015 - 01", "value": 73, b: 29},
        {"elapsed": "2015 - 02", "value": 94, b: 13},
        {"elapsed": "2015 - 03", "value": 79, b: 24}
    ];

    var chart = Morris.Area({
        element: 'morris-chart-ncra0',
        data: day_data,
        axes: false,
        xkey: 'elapsed',
        ykeys: ['value', 'b'],
        labels: ['Download Speed', 'Upload Speed'],
        yLabelFormat: function (y) {
            return y.toString() + ' Mb/s';
        },
        gridEnabled: false,
        gridLineColor: 'transparent',
        lineColors: ['#8eb5e3', '#1b72bc'],
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
});