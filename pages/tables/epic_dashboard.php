<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Dashboard - Ace Admin</title>

        <meta name="description" content="overview &amp; stats" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <!--basic styles-->

        <link href="../../template1/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link href="../../template1/assets/css/bootstrap-responsive.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="../../template1/assets/css/font-awesome.min.css" />

        <!--[if IE 7]>
          <link rel="stylesheet" href="assets/css/font-awesome-ie7.min.css" />
        <![endif]-->

        <!--page specific plugin styles-->

        <!--fonts-->

        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300" />

        <!--ace styles-->

        <link rel="stylesheet" href="../../template1/assets/css/ace.min.css" />
        <link rel="stylesheet" href="../../template1/assets/css/ace-responsive.min.css" />
        <link rel="stylesheet" href="../../template1/assets/css/ace-skins.min.css" />

        <!--[if lte IE 8]>
          <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
        <![endif]-->

        <!--inline styles related to this page-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>

    <body>




        <div class="space-6"></div>

        <div class="row-fluid">
            <div class="span7 infobox-container">
                <div class="infobox infobox-green  ">
                    <div class="infobox-icon">
                        <i class="icon-comments"></i>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-data-number">32</span>
                        <div class="infobox-content">comments + 2 reviews</div>
                    </div>
                    <div class="stat stat-success">8%</div>
                </div>

                <div class="infobox infobox-blue  ">
                    <div class="infobox-icon">
                        <i class="icon-twitter"></i>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-data-number">11</span>
                        <div class="infobox-content">new followers</div>
                    </div>

                    <div class="badge badge-success">
                        +32%
                        <i class="icon-arrow-up"></i>
                    </div>
                </div>

                <div class="infobox infobox-pink  ">
                    <div class="infobox-icon">
                        <i class="icon-shopping-cart"></i>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-data-number">8</span>
                        <div class="infobox-content">new orders</div>
                    </div>
                    <div class="stat stat-important">+4%</div>
                </div>

                <div class="infobox infobox-red  ">
                    <div class="infobox-icon">
                        <i class="icon-beaker"></i>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-data-number">7</span>
                        <div class="infobox-content">experiments</div>
                    </div>
                </div>

                <div class="infobox infobox-orange2  ">
                    <div class="infobox-chart">
                        <span class="sparkline" data-values="196,128,202,177,154,94,100,170,224"></span>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-data-number">6,251</span>
                        <div class="infobox-content">pageviews</div>
                    </div>

                    <div class="badge badge-success">
                        7.2%
                        <i class="icon-arrow-up"></i>
                    </div>
                </div>

                <div class="infobox infobox-blue2  ">
                    <div class="infobox-progress">
                        <div class="easy-pie-chart percentage" data-percent="42" data-size="46">
                            <span class="percent">42</span>
                            %
                        </div>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-text">traffic used</span>

                        <div class="infobox-content">
                            <span class="bigger-110">~</span>
                            58GB remaining
                        </div>
                    </div>
                </div>

                <div class="space-6"></div>

                <div class="infobox infobox-green infobox-small infobox-dark">
                    <div class="infobox-progress">
                        <div class="easy-pie-chart percentage" data-percent="61" data-size="39">
                            <span class="percent">61</span>
                            %
                        </div>
                    </div>

                    <div class="infobox-data">
                        <div class="infobox-content">Task</div>
                        <div class="infobox-content">Completion</div>
                    </div>
                </div>

                <div class="infobox infobox-blue infobox-small infobox-dark">
                    <div class="infobox-chart">
                        <span class="sparkline" data-values="3,4,2,3,4,4,2,2"></span>
                    </div>

                    <div class="infobox-data">
                        <div class="infobox-content">Earnings</div>
                        <div class="infobox-content">$32,000</div>
                    </div>
                </div>

                <div class="infobox infobox-grey infobox-small infobox-dark">
                    <div class="infobox-icon">
                        <i class="icon-download-alt"></i>
                    </div>

                    <div class="infobox-data">
                        <div class="infobox-content">Downloads</div>
                        <div class="infobox-content">1,205</div>
                    </div>
                </div>
            </div>

            <div class="vspace"></div>

            <div class="span5">
                <div class="widget-box">
                    <div class="widget-header widget-header-flat widget-header-small">
                        <h5>
                            <i class="icon-signal"></i>
                            Epic Report
                        </h5>


                    </div>

                    <div class="widget-body">
                        <div class="widget-main">
                            <div id="piechart-placeholder"></div>

                            <div class="hr hr8 hr-double"></div>

                            <div class="clearfix">
                                <div class="grid3">
                                    <span class="grey">
                                        <i class=""></i>
                                        &nbsp; Assignees
                                    </span>
                                    <h4 class="bigger pull-right"><?php echo $dataPie->total_assignees; ?></h4>
                                </div>

                                <div class="grid3">
                                    <span class="grey">
                                        <i class="purple"></i>
                                        &nbsp; Total Days
                                    </span>
                                    <h4 class="bigger pull-right"><?php echo round($dataPie->estimate / (3600 * 8)); ?></h4>
                                </div>

                                <div class="grid3">
                                    <span class="grey">
                                        <i class="red"></i>
                                        &nbsp; Spent Days
                                    </span>
                                    <h4 class="bigger pull-right"><?php echo round($dataPie->spent / (3600 * 8)); ?></h4>
                                </div>
                            </div>
                        </div><!--/widget-main-->
                    </div><!--/widget-body-->
                </div><!--/widget-box-->
            </div><!--/span-->
        </div><!--/row-->


        <!--basic scripts-->

        <!--[if !IE]>-->

        <script src="../../js/jquery.min.2.0.3.js"></script>
        <script type="text/javascript" src="../../js/bootstrap.min.js"></script>
        <!--<![endif]-->

        <!--[if IE]>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <![endif]-->

        <!--[if !IE]>-->

        <script type="text/javascript">
            window.jQuery || document.write("<script src='assets/js/jquery-2.0.3.min.js'>" + "<" + "/script>");
        </script>

        <!--<![endif]-->

        <!--[if IE]>
        <script type="text/javascript">
        window.jQuery || document.write("<script src='assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
        </script>
        <![endif]-->

        <script type="text/javascript">
            if ("ontouchend" in document)
                document.write("<script src='../../template1/assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
        </script>
        <script src="../../template1/assets/js/bootstrap.min.js"></script>

        <!--page specific plugin scripts-->

        <!--[if lte IE 8]>
          <script src="assets/js/excanvas.min.js"></script>
        <![endif]-->

        <script src="../../template1/assets/js/jquery-ui-1.10.3.custom.min.js"></script>
        <script src="../../template1/assets/js/jquery.ui.touch-punch.min.js"></script>
        <script src="../../template1/assets/js/jquery.slimscroll.min.js"></script>
        <script src="../../template1/assets/js/jquery.easy-pie-chart.min.js"></script>
        <script src="../../template1/assets/js/jquery.sparkline.min.js"></script>
        <script src="../../template1/assets/js/flot/jquery.flot.min.js"></script>
        <script src="../../template1/assets/js/flot/jquery.flot.pie.min.js"></script>
        <script src="../../template1/assets/js/flot/jquery.flot.resize.min.js"></script>

        <!--ace scripts-->

        <script src="../../template1/assets/js/ace-elements.min.js"></script>
        <script src="../../template1/assets/js/ace.min.js"></script>

        <!--inline scripts related to this page-->

        <script type="text/javascript">
            $(function () {
                $('.easy-pie-chart.percentage').each(function () {
                    var $box = $(this).closest('.infobox');
                    var barColor = $(this).data('color') || (!$box.hasClass('infobox-dark') ? $box.css('color') : 'rgba(255,255,255,0.95)');
                    var trackColor = barColor == 'rgba(255,255,255,0.95)' ? 'rgba(255,255,255,0.25)' : '#E2E2E2';
                    var size = parseInt($(this).data('size')) || 50;
                    $(this).easyPieChart({
                        barColor: barColor,
                        trackColor: trackColor,
                        scaleColor: false,
                        lineCap: 'butt',
                        lineWidth: parseInt(size / 10),
                        animate: /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase()) ? false : 1000,
                        size: size
                    });
                })

                $('.sparkline').each(function () {
                    var $box = $(this).closest('.infobox');
                    var barColor = !$box.hasClass('infobox-dark') ? $box.css('color') : '#FFF';
                    $(this).sparkline('html', {tagValuesAttribute: 'data-values', type: 'bar', barColor: barColor, chartRangeMin: $(this).data('min') || 0});
                });




                var placeholder = $('#piechart-placeholder').css({'width': '90%', 'min-height': '150px'});
                var data = [
                    {label: "Open Tasks", data: <?php echo $dataPie->tasks; ?>, color: "#68BC31"},
                    {label: "In ", data: <?php echo $dataPie->estimate / 3600; ?>, color: "#2091CF"},
                    {label: "Spent Hrs", data: <?php echo $dataPie->spent / 3600; ?>, color: "#AF4E96"},
                    {label: "Remaining Hrs", data: <?php echo ($dataPie->estimate - $dataPie->spent) / 3600; ?>, color: "#DA5430"}

                ]
                function drawPieChart(placeholder, data, position) {
                    $.plot(placeholder, data, {
                        series: {
                            pie: {
                                show: true,
                                tilt: 0.8,
                                highlight: {
                                    opacity: 0.25
                                },
                                stroke: {
                                    color: '#fff',
                                    width: 2
                                },
                                startAngle: 2
                            }
                        },
                        legend: {
                            show: true,
                            position: position || "ne",
                            labelBoxBorderColor: null,
                            margin: [-30, 15]
                        }
                        ,
                        grid: {
                            hoverable: true,
                            clickable: true
                        }
                    })
                }
                drawPieChart(placeholder, data);

                /**
                 we saved the drawing function and the data to redraw with different position later when switching to RTL mode dynamically
                 so that's not needed actually.
                 */
                placeholder.data('chart', data);
                placeholder.data('draw', drawPieChart);



                var $tooltip = $("<div class='tooltip top in hide'><div class='tooltip-inner'></div></div>").appendTo('body');
                var previousPoint = null;

                /*    placeholder.on('plothover', function (event, pos, item) {
                 
                 if (item) {
                 if (previousPoint != item.seriesIndex) {
                 previousPoint = item.seriesIndex;
                 var tip = item.series['label'] + " : ";
                 $tooltip.show().children(0).text(tip);
                 }
                 $tooltip.css({top: pos.pageY + 10, left: pos.pageX + 10});
                 } else {
                 $tooltip.hide();
                 previousPoint = null;
                 }
                 
                 });*/






                var d1 = [];
                for (var i = 0; i < Math.PI * 2; i += 0.5) {
                    d1.push([i, Math.sin(i)]);
                }

                var d2 = [];
                for (var i = 0; i < Math.PI * 2; i += 0.5) {
                    d2.push([i, Math.cos(i)]);
                }

                var d3 = [];
                for (var i = 0; i < Math.PI * 2; i += 0.2) {
                    d3.push([i, Math.tan(i)]);
                }


                var sales_charts = $('#sales-charts').css({'width': '100%', 'height': '220px'});
                $.plot("#sales-charts", [
                    {label: "Domains", data: d1},
                    {label: "Hosting", data: d2},
                    {label: "Services", data: d3}
                ], {
                    hoverable: true,
                    shadowSize: 0,
                    series: {
                        lines: {show: true},
                        points: {show: true}
                    },
                    xaxis: {
                        tickLength: 0
                    },
                    yaxis: {
                        ticks: 10,
                        min: -2,
                        max: 2,
                        tickDecimals: 3
                    },
                    grid: {
                        backgroundColor: {colors: ["#fff", "#fff"]},
                        borderWidth: 1,
                        borderColor: '#555'
                    }
                });


                $('#recent-box [data-rel="tooltip"]').tooltip({placement: tooltip_placement});
                function tooltip_placement(context, source) {
                    var $source = $(source);
                    var $parent = $source.closest('.tab-content')
                    var off1 = $parent.offset();
                    var w1 = $parent.width();

                    var off2 = $source.offset();
                    var w2 = $source.width();

                    if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2))
                        return 'right';
                    return 'left';
                }


                $('.dialogs,.comments').slimScroll({
                    height: '300px'
                });


                //Android's default browser somehow is confused when tapping on label which will lead to dragging the task
                //so disable dragging when clicking on label
                var agent = navigator.userAgent.toLowerCase();
                if ("ontouchstart" in document && /applewebkit/.test(agent) && /android/.test(agent))
                    $('#tasks').on('touchstart', function (e) {
                        var li = $(e.target).closest('#tasks li');
                        if (li.length == 0)
                            return;
                        var label = li.find('label.inline').get(0);
                        if (label == e.target || $.contains(label, e.target))
                            e.stopImmediatePropagation();
                    });

                $('#tasks').sortable({
                    opacity: 0.8,
                    revert: true,
                    forceHelperSize: true,
                    placeholder: 'draggable-placeholder',
                    forcePlaceholderSize: true,
                    tolerance: 'pointer',
                    stop: function (event, ui) {//just for Chrome!!!! so that dropdowns on items don't appear below other items after being moved
                        $(ui.item).css('z-index', 'auto');
                    }
                }
                );
                $('#tasks').disableSelection();
                $('#tasks input:checkbox').removeAttr('checked').on('click', function () {
                    if (this.checked)
                        $(this).closest('li').addClass('selected');
                    else
                        $(this).closest('li').removeClass('selected');
                });


            })
        </script>
        <script type="text/javascript">
            $('aside.left-side.sidebar-offcanvas').addClass('collapse-left');
            $('aside.right-side').addClass('strech');
        </script>
    </body>
</html>