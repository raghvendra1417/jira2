<?php

include 'config.php';
include 'mysqlconfig.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Jira | Reports</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />

        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <link href="css/iCheck/minimal/blue.css" rel="stylesheet" type="text/css" />


        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" />

        <link rel="stylesheet" href="chosen/docsupport/style.css">
        <link rel="stylesheet" href="chosen/docsupport/prism.css">
        <link rel="stylesheet" href="chosen/chosen.css">
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
            #loading-indicator {
                position: absolute;
                left: 10px;
                top: 10px;
            }
            .error-page > .headline{
                float: left;
                font-size: 100px;
                font-weight: 300;
                margin: 33% 0 25% 33%;
                width: 100%;
            }
            .error-page > .error-content{
                display: block;
                margin: 10%;
                text-align: center;
            }
        </style>
        <link href="../../css/bootstrap.min.css" rel="stylesheet" />
        <link href="../../css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" media="all" href="./../../js/bootstrap-daterangepicker-master/daterangepicker-bs3.css" />

        <link rel="stylesheet" href="./../../css/jquery-ui.css">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-blue">
        
        <!-- header logo: style can be found in header.less -->

        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="./../../img/avatar3.png" class="img-circle" alt="User Image" />
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
 
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                
                

                <!-- Main content -->
                <section class="content">

                    <div class="error-page">
                        <div class="headline text-info"> 403 </div>
                        <div class="error-content">
                            <div><i class="fa fa-warning text-yellow"></i> Oops! you don't have permission to access this page.</div>
                            <p>
                                We could not find the page you were looking for.
                                Meanwhile, you may <a href='<?php if($_SESSION['user_role'][0] != 5){ echo HOST_NAME.'index.php'; }else{ echo HOST_NAME.'pages/tables/user-time-spent.php?username='.$_SESSION['alt_username'].'&from='.date('M 1,Y').'&to='.date('M t,Y'); } ?>'>return to dashboard</a> or try using the search form.
                            </p>
                            
                        </div><!-- /.error-content -->
                    </div><!-- /.error-page -->

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        

        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <!-- DATA TABES SCRIPT -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

        <script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>

        <!-- AdminLTE App -->
        <script src="js/AdminLTE/app.js" type="text/javascript"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="js/AdminLTE/demo.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>

<!--	<script src="../../js/date-euro.js" type="text/javascript"></script>	-->
        <script src="js/date-uk.js" type="text/javascript"></script>
        <!-- page script -->

        
        <script src="chosen/chosen.jquery.js" type="text/javascript"></script>
        <script src="chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
        

        <script type="text/javascript" src="js/bootstrap-daterangepicker-master/moment.js"></script>
        <script type="text/javascript" src="js/bootstrap-daterangepicker-master/daterangepicker.js"></script>

        <style>
            .modal-dialog{left: 0%;}
        </style>
        <script type="text/javascript">
            $('aside.left-side.sidebar-offcanvas').addClass('collapse-left');
            $('aside.right-side').addClass('strech');
        </script>
    </body>
</html>
