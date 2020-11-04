<link rel="stylesheet" href="<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/css/style_charts.css'; ?>" type="text/css">
<script src="<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/js/amcharts/amcharts.js'; ?>" type="text/javascript"></script>
<script src="<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/js/amcharts/serial.js'; ?>" type="text/javascript"></script>
<script src="<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/js/amcharts/pie.js'; ?>" type="text/javascript"></script>
<?php if ($transactions): ?>
<div id="chartStorediv" style="width:100%; height:400px;"></div>
<div id="chartStoredivPi" style="width: 100%; height: 400px;"></div>

<script>
    var chart;
    var graph;

    var chartData = <?php echo json_encode($transactions);?>;


        // SERIAL CHART
        chart = new AmCharts.AmSerialChart();

        chart.dataProvider = chartData;
        chart.marginLeft = 10;
        chart.categoryField = "date";

        // listen for "dataUpdated" event (fired when chart is inited) and call zoomChart method when it happens
        chart.addListener("dataUpdated", zoomChart);

        // AXES
        // category
        var categoryAxis = chart.categoryAxis;
        categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
        categoryAxis.minPeriod = "DD"; // our data is yearly, so we set minPeriod to YYYY
//        categoryAxis.dashLength = 3;
        categoryAxis.minorGridEnabled = true;
        categoryAxis.minorGridAlpha = 0.1;

        // value
        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.axisAlpha = 0;
        valueAxis.inside = true;
        valueAxis.dashLength = 3;
        valueAxis.precision = 0;
        chart.addValueAxis(valueAxis);

        // GRAPH
        graph = new AmCharts.AmGraph();
        graph.type = "line"; // this line makes the graph smoothed line.
        graph.lineColor = "#d1655d";
        graph.negativeLineColor = "#637bb6"; // this line makes the graph to change color when it drops below 0
        graph.bullet = "round";
        graph.bulletSize = 8;
        graph.bulletBorderColor = "#FFFFFF";
        graph.bulletBorderAlpha = 1;
        graph.bulletBorderThickness = 2;
        graph.lineThickness = 2;
        graph.valueField = "total_count";
        graph.balloonText = "[[category]]<br><b><span style='font-size:14px;'>[[total_count]] search</span></b>";
        chart.addGraph(graph);

        // CURSOR
        var chartCursor = new AmCharts.ChartCursor();
        chartCursor.cursorAlpha = 0;
        chartCursor.cursorPosition = "mouse";
//        chartCursor.categoryBalloonDateFormat = "YYYY";
        chart.addChartCursor(chartCursor);

        // SCROLLBAR
        var chartScrollbar = new AmCharts.ChartScrollbar();
        chart.addChartScrollbar(chartScrollbar);

        // WRITE
        chart.write("chartStorediv");

    // this method is called when chart is first inited as we listen for "dataUpdated" event
    function zoomChart() {
        // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
//        chart.zoomToDates(new Date(1972, 0), new Date(1984, 0));
    }
    
    // PIE CHART
    var chartPI;
    var chartDataPi= <?php echo json_encode($piData);?>;
    chartPI = new AmCharts.AmPieChart();
    chartPI.dataProvider = chartDataPi;
    chartPI.titleField = "user";
    chartPI.valueField = "total_count";
    chartPI.outlineColor = "#FFFFFF";
    chartPI.outlineAlpha = 0.8;
    chartPI.outlineThickness = 2;
    chartPI.balloonText = "[[title]]<br><span style='font-size:14px'><b>[[total_count]] search</b> ([[percents]]%)</span>";
    // this makes the chart 3D
    chartPI.depth3D = 15;
    chartPI.angle = 30;

    // WRITE
    chartPI.write("chartStoredivPi");
</script>

<?php else: ?>
<div class="store_locator_chartNoData">
    <?php echo __("No Data Found.", 'store_locator'); ?>
</div>
<?php endif; ?>