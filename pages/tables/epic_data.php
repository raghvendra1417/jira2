<?php
/* * ***************************************** include Config ************************************ */
include './../../config.php';

$actionsData = array(
    'epic-data' => array(
        'cpage'=>'Epic Wise Report'
        ,'pbreadcrumb'=>array(
            'Epic Wise Report <span style="font-size: 10px;">(Active Projects)</span>'=>'pages/tables/epic-all-projects.php?view=all-projects'
        )
    )
    ,'epic-dashboard' => array(
        'cpage'=>'Epic Dashboard'
        ,'pbreadcrumb'=>array(
            'Epic Wise Report <span style="font-size: 10px;">(Active Projects)</span>'=>'pages/tables/epic-all-projects.php?view=all-projects'
            ,'Epic Wise Report'=>'pages/tables/epic_data.php?view=epic-data&id=%s'
        )
    )
);

if (array_key_exists($action, $actionsData) && $action == 'epic-data') {
    $ismysqlQuery = 0;
    $sql = $sqlQueries[$action]['query'];

    $sql = sprintf($sql, $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id']);

    $retData = pg_query($db, $sql);
    $retData1 = pg_query($db, $sql);
    $data = pg_fetch_object($retData1);
    if (!$retData) {
        echo pg_last_error($db);
    }
} elseif (array_key_exists($action, $actionsData) && $action == 'epic-dashboard') {
    $sqlPie = $sqlQueries['epic-dashboard-pie']['query'];
    $sqlPie = sprintf($sqlPie, $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id']);
    $retDataPie = pg_query($db, $sqlPie);
    $dataPie = pg_fetch_object($retDataPie);

    $sql = $sqlQueries['epic-dashboard']['query'];
    $sql = sprintf($sql, $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'],$_REQUEST['id']);
    $retData = pg_query($db, $sql);
    $data = pg_fetch_object($retData);
    $actionsData['epic-dashboard']['pbreadcrumb']['Epic Wise Report'] = sprintf($actionsData['epic-dashboard']['pbreadcrumb']['Epic Wise Report'],$data->pid);
    
    $sqlStackedBar = $sqlQueries['epic-assignee-report-gyr']['query'];
    $sqlStackedBar = sprintf($sqlStackedBar, $_REQUEST['id']);
    $retDataStackedBar = pg_query($db, $sqlStackedBar);

    $sqlBaarChart = $sqlQueries['epic-task-assigned-to-assignee']['query'];
    $sqlBaarChart = sprintf($sqlBaarChart, $_REQUEST['id']);
    $retDataBaarChart = pg_query($db, $sqlBaarChart);

    $sqlPlan = $sqlQueries['task-plan']['query'];
    $sqlPlan = sprintf($sqlPlan, $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id']);
    $retPlanData = pg_query($db, $sqlPlan);
    $dataPlan = pg_fetch_object($retPlanData);


    if (!$retData) {
        echo pg_last_error($db);
    }
}

$sqlProject = $sqlQueries['all-projects']['query'];
$sqlProject = sprintf($sqlProject, '');
$retProject = pg_query($db, $sqlProject);

if (!$retProject) {
    echo pg_last_error($db);
} else {
    //error_log( "Project Query successfully\n") ;
}
#print_r($sqlQueries);exit;
#while($rowProject = pg_fetch_object($retProject)){ print_r($rowProject);exit;}
/*
  $filterFromTo = '';
  if (isset($_REQUEST['from'], $_REQUEST['to'])) {
  $filterFromTo = '&from=' . $_REQUEST['from'] . '&to=' . $_REQUEST['to'];
  } elseif ($action == 'total-time-spent-assignee-last-month') {
  $filterFromTo = '&from=' . date('M d,Y', strtotime(date('Y-m-d') . '-31 days')) . '&to=' . date('M d,Y');
  } elseif ($action == 'active-projects') {
  $filterFromTo = '&from=' . date('M d,Y', strtotime(date('Y-m-d') . '-6 days')) . '&to=' . date('M d,Y');
  } */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Jira | Reports</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../../css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="../../css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="../../css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="../../css/AdminLTE.css" rel="stylesheet" type="text/css" />

        <link rel="stylesheet" href="./../../chosen/docsupport/style.css">
        <link rel="stylesheet" href="./../../chosen/docsupport/prism.css">
        <link rel="stylesheet" href="./../../chosen/chosen.css">
        <style type="text/css" media="all">

            /* fix rtl for demo */
            .chosen-rtl .chosen-drop { left: -9000px; }
            .pagination li {
                margin-left: 0;
            }
            body > .header .navbar .nav > li > a > .label{ width: auto;}
            form.sidebar-form {
                display: none;
            }
            .breadcrumb li {margin-left: 2px;padding-top: 10px;}
            .widthminmax{ width: 45%;/*min-width: 400px; max-width: 450px;*/ margin: 10px;}
            .fleft{ float: left;} 
            .fright{ float: right;}
            .wefull { width:100%;clear: both;}
            .wefull.ffull{ min-width: 450px; max-width: 600px; margin: 20px;}
            //.widthminmax.fright { min-width: 450px !important;}
        </style>

        <link href="../../css/bootstrap.min.css" rel="stylesheet" />
        <link href="../../css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" media="all" href="./../../js/bootstrap-daterangepicker-master/daterangepicker-bs3.css" />
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-blue">
        <!-- header logo: style can be found in header.less -->
        <?php include 'widget-topbar.php'; ?>

        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="../../img/avatar3.png" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p>Hello, Vinutha</p>

                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>
                    <!-- search form -->
                    <form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search..."/>
                            <span class="input-group-btn">
                                <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <?php $datatables = true; ?>
                    <?php include 'widgets-siderbar.php'; ?>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-breadcrumb">
                    <ol class="breadcrumb" style="margin:0 auto;">
                        <li><a href="./../../index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                        <?php 
                            foreach($actionsData[$action]['pbreadcrumb'] as $bText =>$bLink ){?>
                        <li class=""><a href="./../../<?php echo $bLink.$filterFromTo; ?>"><i class="fa fa-table"></i> <?php echo $bText;?></a></li>        
                  <?php    }
                        ?>
                        <li class="active"><?php echo $actionsData[$action]['cpage'];?></li>
                    </ol>
                </section>
                <section class="content-header">
                    <h1>
                        <?php
                        if ($action == 'epic-dashboard') {
                            echo $data->project . ' - ' . $data->summary;
                        } else {
                            echo 'Epics in ' . $data->project;
                        }
                        ?>
                        <small></small>
                    </h1>
                </section>

                <!-- Main content -->

                <section class="content">
                    <?php if (array_key_exists($action, $actionsData) && $action == 'epic-data') : ?>
                        <div class="row">

                            <div class="col-xs-12">

                                <div class="box">
                                    <div class="box-header">
                                        <div style="width:33%;float: left;">
                                            <h3 class="box-title" style="min-width:240px;"><?php echo isset($projectName) ? $projectName : ""; ?></h3>
                                            <?php if ($action == 'assignee-total-est-project-selection') { ?>
                                                <div style="float: left;padding: 5px 20px;"><a class="btn btn-success back" style="color:white" href="./../../pages/tables/data.php?view=active-projects&id=<?php echo $_REQUEST['pid'] . $filterFromTo; ?>"> Back</a></div>
                                            <?php } ?>
                                        </div>

                                        <div style="width:33%;float: left;">
                                            <h5>Project : </h5>
                                            <select id="project-select" name="id" data-placeholder="Choose a Project..." class="chosen-select" style="width:350px;" tabindex="1">
                                                <option value="">Select Project</option>
                                                <?php #$action = $_REQUEST['view'];        ?>
                                                <?php while ($rowProject = pg_fetch_object($retProject)) { ?>    
                                                    <option value="<?php echo $rowProject->project; ?>" <?php echo $rowProject->project == $_REQUEST['id'] ? "selected" : ""; ?>><?php echo $rowProject->pname; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div style="width:33%;float: right;">
                                            
                                            <div style="float: right;padding: 10px 20px;"><button class="btn btn-success export" type="button">Export</button></div>
                                        </div>

                                    </div><!-- /.box-header -->

                                    <div class="box-body table-responsive">
                                        <table id="example10" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <?php foreach ($sqlQueries[$action]['columns'] as $keycol => $columns) { ?>
                                                        <th><?php echo $columns; ?></th>
                                                    <?php } ?>   
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php
                                                $queryObj = $ismysqlQuery == 1 ? 'mysql_fetch_object' : 'pg_fetch_object';
                                                while ($rowretData = $queryObj($retData)) { #print_r($sqlQueries[$action]['columns']);exit;
                                                    if ($rowretData->summary == '') {
                                                        continue;
                                                    }
                                                    ?>
                                                    <tr>
                                                        <?php
                                                        foreach ($sqlQueries[$action]['columns'] as $keycol => $columns) {
                                                            ?>

                                                            <td>
                                                                <?php if ($action == 'epic-data' && $keycol == 'summary') { ?>

                                                                    <a href="./../../pages/tables/epic_data.php?view=epic-dashboard&id=<?php echo $rowretData->id; ?>">
                                                                    <?php } ?>
                                                                    <?php
                                                                    //echo $rowretData->{$keycol} === null ?"-": $rowretData->{$keycol} ;  
                                                                    if ($keycol == 'assignee' && $rowretData->assignee == '') {
                                                                        echo 'Unestimated Task';
                                                                    } else if ($keycol == 'status') {
                                                                        echo '';
                                                                    } else {
                                                                        echo $rowretData->{$keycol} === null || $rowretData->{$keycol} == ' hrs' ? "" : $rowretData->{$keycol};
                                                                    }
                                                                    ?>
                                                                    <?php //echo $rowretData->{$keycol} === null || $rowretData->{$keycol} == ' hrs' ?"-": $rowretData->{$keycol} ;   ?>
                                                                    <?php if ($action == 'epic-data' && $keycol == 'summary') { ?>
                                                                    </a>            
                                                                <?php } ?>

                                                            </td>

                                                        <?php } ?>
                                                    </tr>

                                                <?php } ?>

                                            </tbody>

                                        </table>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->
                            </div>
                        </div>
                        <?php
                    else:
                        ?>
                        <b style="font-size:16px"> Total Tasks : <?php echo $dataPie->tasks; ?></b>
                        
                        <div class="width:100%;clear: both;">
                            <div id="task-status" class="widthminmax fleft"></div>
                            <div id="time-chart" class="widthminmax fright" ></div>
                        </div>
                        
                        <div class="width:100%;clear: both;">
                            <div id="task-plan" class="widthminmax fleft"></div>
                            <div id="container5" class="widthminmax fright" ></div>
                        </div>
                        
                        <div class="wefull">
                            <div id="container1" style="min-width: 550px; height: 400px; max-width: 750px; margin: 0 auto;" ></div>
                            
                        </div>
                    <?php endif; ?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        <script src="../../js/jquery.min.js"></script>
        <script src="../../js/bootstrap.min.js" type="text/javascript"></script>
        
        <script src="../../js/highcharts.js"></script>
        <script src="../../js/modules/exporting.js"></script>

        <!-- DATA TABES SCRIPT -->
        <script src="../../js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="../../js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <!--<script src="../../js/AdminLTE/app.js" type="text/javascript"></script>-->
        <!-- AdminLTE for demo purposes -->
        <script src="../../js/AdminLTE/demo.js" type="text/javascript"></script>

        <script src="../../js/Chart.min.js"></script>

        <script src="../../js/date-uk.js" type="text/javascript"></script>
        <!-- page script -->
        <script type="text/javascript">
                        $(function () {

                            function datatopbarupdate() {
                                $.ajax({
                                    type: 'POST',
                                    url: './../../ajax-topbardata.php',
                                    success: function (data) {
                                        var json = $.parseJSON(data);
                                        //alert(json.user_no_task_today);
                                        $('.unestimated_task span.label').html(json.unestimted_task_count);
                                        $('.project span.label').html(json.project);
                                        $('.task_inprogress span.label').html(json.task_inprogress);
                                    },
                                });
                            }
                            ;
                            datatopbarupdate();

                            var refInterval = setInterval(function () {
                                datatopbarupdate();
                            }, 30000); // 30 seconds

                            $('#example1').dataTable({
                                "aoColumns": [
                                    { "sType": "string" },
                                    { "sType": "date-uk" },
                                    { "sType": "date-uk" },
                                    { "sType": "numeric" },
                                    { "sType": "numeric" },
                                    
                                ],
                                "aaSorting": [[ 3, "desc" ],[0,"asc"]],
	                        "iDisplayLength": 50
                            });
                            $('#example2').dataTable({
                                "bPaginate": true,
                                "bLengthChange": false,
                                "bFilter": false,
                                "bSort": true,
                                "bInfo": true,
                                "bAutoWidth": false
                            });

                            view = "<?php echo $_REQUEST['view']; ?>";

                            $('.export').click(function () {
                                //alert(view);
                                if (view == 'epic-data' && $('#project-select').val() != '') {
                                    redirectUrl = "./../../php-excel/topmenuexportxls.php?view="+view+"&id="+$('#project-select').val();

                                    if (<?php echo isset($_REQUEST['from'], $_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to']) ? 1 : 0; ?>) {
                                        redirectUrl += '&from=' + '<?php echo isset($_REQUEST['from']) && !empty($_REQUEST['from']) ? $_REQUEST['from'] : '0'; ?>' + '&to=' + '<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to']) ? $_REQUEST['to'] : '0'; ?>';
                                    }

                                    window.location = redirectUrl;
                                }
                            });

                            //On Change Of Project Dropdown
                            $('#project-select').change(function () {
                                //alert($('#project-select').val());
                                if ($('#project-select').val() != '') {
                                    Url = '&from=' + '<?php echo isset($_REQUEST['from']) && !empty($_REQUEST['from']) ? $_REQUEST['from'] : date('M d,Y', strtotime(date('Y-m-d') . '-6 days')); ?>' + '&to=' + '<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to']) ? $_REQUEST['to'] : date('M d,Y'); ?>';
                                    window.location = "./../../pages/tables/epic_data.php?view=epic-data&id=" + $('#project-select').val() + Url;
                                } else {
                                    window.location = "./../../pages/tables/epic_data.php?view=epic-data";
                                }
                            });
                        });
        </script>
        <script src="./../../chosen/chosen.jquery.js" type="text/javascript"></script>
        <script src="./../../chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>

        <script type="text/javascript" src="./../../js/bootstrap-daterangepicker-master/moment.js"></script>
        <script type="text/javascript" src="./../../js/bootstrap-daterangepicker-master/daterangepicker.js"></script>

        <script type="text/javascript">
                        var config = {
                            '.chosen-select': {},
                            '.chosen-select-deselect': {allow_single_deselect: true},
                            '.chosen-select-no-single': {disable_search_threshold: 10},
                            '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
                            '.chosen-select-width': {width: "95%"}
                        }
                        for (var selector in config) {
                            $(selector).chosen(config[selector]);
                        }
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#task-status').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    title: {
                        text: 'Task Status Report'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false
                            },
                            showInLegend: true
                        }
                    },
                    series: [{
                            type: 'pie',
                            name: 'Tasks',
                            data: [
                                ['Open Tasks', <?php echo $dataPie->opentasks; ?>],
                                ['Tasks in Progress', <?php echo $dataPie->inprogress; ?>],
                                ['Re-Opened Tasks', <?php echo $dataPie->reopenedtasks; ?>],
                                ['Resolved Tasks', <?php echo $dataPie->resolvedtasks; ?>],
                                ['Closed Tasks', <?php echo $dataPie->closedtasks; ?>],
                                ['Tasks In Review', <?php echo $dataPie->inreviewtasks; ?>],
                                ['Tasks Done', <?php echo $dataPie->donetasks; ?>],
                                ['Tasks To Do', <?php echo $dataPie->todotasks; ?>],
                                ['Unassigned Tasks', <?php echo $dataPie->unassignedtasks; ?>]
                            ]
                        }]
                });


                $('#time-chart').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Work (in Hours)'
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        categories: ["Estimated", "Spent"],
                        type: 'Status',
                        labels: {
                            rotation: 0,
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Time (in Hrs)'
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    tooltip: {
                        pointFormat: 'Time: <b>{point.y:.1f} Hrs</b>'
                    },
                    series: [{
                            name: 'Status',
                            data: [
                                ['Estimated', <?php echo round($data->totalestimate / 3600); ?>],
                                ['Spent', <?php echo round($data->timespent / 3600); ?>]

                            ],
                            dataLabels: {
                                enabled: true,
                                rotation: -90,
                                color: '#000',
                                align: 'right',
                                x: 4,
                                y: 10,
                                style: {
                                    fontSize: '13px',
                                    fontFamily: 'Verdana, sans-serif',
                                    textShadow: '0 0 3px black'
                                }
                            }
                        }]
                });


                $('#task-plan').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    title: {
                        text: 'Task Plan Report'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false
                            },
                            showInLegend: true
                        }
                    },
                    series: [{
                            type: 'pie',
                            name: 'Tasks',
                            data: [
                                ['Estimated', <?php echo $dataPlan->estimated; ?>],
                                ['Unestimated', <?php echo $dataPlan->unestimated; ?>]

                            ]
                        }]
                });




            });
        </script>

        <script type="text/javascript">
            var assignees = [];
            var resolved = [];
            var pending = [];
            var overdue = [];

            var xassignne = [];
            var yassignne = [];

<?php while ($rowretDataStackedBar = pg_fetch_object($retDataStackedBar)) { ?>

                assignees.push("<?php echo $rowretDataStackedBar->assignees != null ? $rowretDataStackedBar->assignees : "Unassigned"; ?>");
                resolved.push(<?php echo $rowretDataStackedBar->resolved; ?>);
                pending.push(<?php echo $rowretDataStackedBar->pending; ?>);
                overdue.push(<?php echo $rowretDataStackedBar->overdue; ?>);
    <?php
}
while ($rowretDataBaarChart = pg_fetch_object($retDataBaarChart)) { #print_r($rowretDataBaarChart);exit;
    ?>

                xassignne.push("<?php echo $rowretDataBaarChart->assignees != null ? $rowretDataBaarChart->assignees : "Unassigned"; ?>");
                yassignne.push(<?php echo $rowretDataBaarChart->total_timeestimate; ?>);

<?php }
?>

            $(function () {

                $('#container5').highcharts({
                    chart: {
                        type: 'column',
                    },
                    colors: [
                        '#41AE39'
                    ],
                    title: {
                        text: 'Assignee Estimate (Hrs)'
                    },
                    /*subtitle: {
                     text: 'Source: Wikipedia.org'
                     },*/
                    xAxis: {
                        categories: xassignne,
                        title: {
                            text: null
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Estimate (Hrs)',
                            align: 'high'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    tooltip: {
                        //valueSuffix: ' Tasks'
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'top',
                        x: -40,
                        y: 100,
                        floating: true,
                        borderWidth: 1,
                        backgroundColor: '#FFFFFF',
                        shadow: true
                    },
                    credits: {
                        enabled: false
                    },
                    series: [{
                            name: 'Estimate (Hrs)',
                            data: yassignne
                        }]
                });



                //red green yellow chart
                $('#container1').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Assignee Report'
                    },
                    colors: [
                        '#ff0000', '#ffff00', '#008000'
                    ],
                    xAxis: {
                        categories: assignees
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Total No. Task'
                        }
                    },
                    legend: {
                        reversed: false
                    },
                    plotOptions: {
                        series: {
                            stacking: 'normal',
                            cursor: 'pointer',
                            point: {
                                events: {
                                    click: function (e) {
                                        //console.log(e);
                                        var epic_id ='<?php echo $_REQUEST['id'] ?>';
                                        redirectUrl = "./../../pages/tables/opened_task_and_bug.php?id="+epic_id+"&user_id="+e.point.category;
                                        window.location = redirectUrl;
                                    }
                                }
                            }
                        }
                        
                    },
                    series: [{
                            name: 'Pending',
                            data: overdue
                        }, {
                            name: 'InProgress',
                            data: pending
                        }, {
                            name: 'Resolved',
                            data: resolved
                        }, ]
                });
            });
        </script>
        <script type="text/javascript">
            $('aside.left-side.sidebar-offcanvas').addClass('collapse-left');
            $('aside.right-side').addClass('strech');
        </script>
    </body>
</html>
