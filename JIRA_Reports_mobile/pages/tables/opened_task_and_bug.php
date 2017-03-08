<?php
/* * ***************************************** include Config ************************************ */
    include './../../config.php';
    
   
    
    $sql = $sqlQueries['epic-user-issues']['query'];
    $sql = sprintf($sql, $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'],$_REQUEST['id'],$_REQUEST['user_id']);
    $retData = pg_query($db, $sql);    
    $datas = pg_fetch_object($retData);
    if (!$retData) {
        echo pg_last_error($db);
    }      
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
                        
                        <li class=""><a href="./../../pages/tables/epic_data.php?view=epic-dashboard&id=<?php echo $_REQUEST['id']; ?>"><i class="fa fa-table"></i> Epic Dashboard</a></li> 
                        <li class="active"></li>
                    </ol>
                </section>
                <section class="content-header">
                    <h1>
                        <?php                        
                            echo 'Epics in '.$datas->project;                       
                        ?>
                        <small></small>
                    </h1>
                </section>

                <!-- Main content -->

                <section class="content">
                    
                        <div class="row">

                            <div class="col-xs-12">

                                <div class="box">
                                    

                                    <div class="box-body table-responsive">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Assignee</th>
                                                    <th>Summary</th>
                                                    <th>Due date</th>
                                                    <th>Time Spent</th>                                                   
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php 
                                            $sql1 = $sqlQueries['epic-user-issues']['query'];
                                            $sql1 = sprintf($sql, $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'],$_REQUEST['id'],$_REQUEST['user_id']);
                                            $retData1 = pg_query($db, $sql1);    
                                            while($data = pg_fetch_object($retData1)){ ?>
                                                <tr>
                                                    <td><?php echo $data->assignee; ?></td>
                                                    <td><?php echo $data->summary; ?></td>
                                                    <td><?php echo $data->duedate; ?></td>
                                                    <td><?php echo $data->timespent; ?></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>

                                        </table>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->
                            </div>
                        </div> 
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

                                                  

                            $('#example1').dataTable({
                                "aoColumns": [
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "date-uk" },
                                    { "sType": "numeric" },
                                   
                                    
                                ],
                                "aaSorting": [[ 3, "desc" ],[0,"asc"]],
	                        "iDisplayLength": 10
                            });
                            $('#example2').dataTable({
                                "bPaginate": true,
                                "bLengthChange": false,
                                "bFilter": false,
                                "bSort": true,
                                "bInfo": true,
                                "bAutoWidth": false
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
            $('aside.left-side.sidebar-offcanvas').addClass('collapse-left');
            $('aside.right-side').addClass('strech');
        </script>
    </body>
</html>
