<link rel="stylesheet" href="<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/css/style_charts.css'; ?>" type="text/css">
<script src="<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/js/amcharts/amcharts.js'; ?>" type="text/javascript"></script>
<script src="<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/js/amcharts/serial.js'; ?>" type="text/javascript"></script>
<script src="<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/js/amcharts/themes/light.js'; ?>" type="text/javascript"></script>

<div class="store_locator_statistics_div">
    <div class="wrap">
        <div class="metabox-holder">

            <div class="postbox" >
                <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Stores Statistics", 'store_locator'); ?></span></h3>
                <div class="inside">
                    <p>
                        <?php if ($transactions): ?>
                            <div id="chartAllStoresdiv" style="width: 100%; height: 400px;"></div>
                        <?php else: ?>
                            <div class="store_locator_chartNoData">
                                <?php echo __("No Data Found.", 'store_locator'); ?>
                            </div>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            
            <div class="postbox" >
                <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Statistics per store", 'store_locator'); ?></span></h3>
                <div class="inside">
                    <p>
                        <select id="store_location_selected_store">
                            <option value=""><?php echo __(" - - Select Store - -", 'store_locator'); ?> </option>
                            <?php foreach ($stores as $store): ?>
                                <option value="<?php echo $store->ID ?>"><?php echo $store->post_title; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="chartdivResults">
                            <div class="store_locator_chartNoData"><?php echo __("Data will Displayed here!", "store_locator"); ?></div>
                        </div>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function (jQuery) {
        jQuery("#store_location_selected_store").on("change", '', showStoreDetails);
        
    });

    function showStoreDetails() {
        jQuery('#chartdivResults').html('<div id="map_loader" style="z-index: 9;width: 100%; height: 200px;position: absolute;background-color: #fff;"><div class="uil-ripple-css" style="transform: scale(0.6); margin-left: auto; margin-right: auto;"><div></div><div></div></div></div>');
        jQuery.ajax({
            url: ajax_url,
            data: 'action=show_store_statistics' + '&store_id=' + jQuery("#store_location_selected_store").val(),
            type: 'post',
            success: function (html) {
                jQuery('#chartdivResults').html(html);
            }
        });
    }
</script>


<?php if ($transactions): ?>
<script>
    // chart for all stores
    var chartAllStores;
    var dataProvider = <?php echo json_encode($transactions);?>;
    makeCharts("light");

    // Theme can only be applied when creating chart instance - this means
    // that if you need to change theme at run time, youhave to create whole
    // chart object once again.

    function makeCharts(theme) {

        if (chartAllStores) {
            chartAllStores.clear();
        }

        // column chart
        chartAllStores = AmCharts.makeChart("chartAllStoresdiv", {
            type: "serial",
            theme: theme,
            dataProvider: dataProvider,
            categoryField: "store",
            startDuration: 1,
            categoryAxis: {
                gridPosition: "start"
            },
            valueAxes: [{
                    title: "Total number of searches"
                }],
            graphs: [{
                    type: "column",
                    title: "Searches",
                    valueField: "total_count",
                    lineAlpha: 0,
                    fillAlphas: 0.7,
                    balloonText: "<b>[[value]]</b> search for <b>[[category]]</b>"
                }],
            legend: {
                useGraphSettings: true
            }

        });
    }
</script>
 <?php endif; ?> 