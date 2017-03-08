<?php 
       
	/******************************************* include Config *************************************/
	include 'config.php';
	$sqlActiveUser = $sqlQueries['no_active_users']['query'];
	$retDataActiveUser = pg_query($db, $sqlActiveUser);
	
	if(!$retDataActiveUser){
	   echo pg_last_error($db);
	} else {
	   //error_log( "ActiveUser Query successfully\n" );
	}

	while($fetchActiveUser = pg_fetch_assoc($retDataActiveUser))
	{
		$ActiveUser = $fetchActiveUser['no_active_users']; 
	}

	//Active Projects 
	$sqlactive_projects = $sqlQueries['active_projects']['query'];
	$retDataactive_projects = pg_query($db, $sqlactive_projects);
	
	if(!$retDataactive_projects){
	   echo pg_last_error($db);
	} else {
	   //error_log( "active_projects Query successfully\n" );
	}

	while($fetchactive_projects = pg_fetch_assoc($retDataactive_projects))
	{
		$ActiveProjects = $fetchactive_projects['project']; 
	}
        
        //Active Projects 
	$sqlusersNoTask = $sqlQueries['users-no-task-today']['count_query'];
	$retDatausersNoTask = pg_query($db, $sqlusersNoTask);
	
	if(!$retDatausersNoTask){
	   echo pg_last_error($db);
	} else {
	   //error_log( "Users No Task Query successfully\n" );
	}

	while($fetchusersNoTask = pg_fetch_assoc($retDatausersNoTask))
	{
		$UserNoTaskToday = $fetchusersNoTask['user_no_task']; 
	}
        

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Jira | Dashboard</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Morris chart -->
        <link href="css/morris/morris.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
        <link href="css/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
        <!-- Date Picker -->
        <link href="css/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
        <!-- Daterange picker -->
        <link href="css/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" />
        <style>
            body > .header .navbar .nav > li > a > .label{ width: auto;}
            form.sidebar-form {
                    display: none;
                }
                .breadcrumb li {margin-left: 2px;padding-top: 10px;}
        </style>
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
                            <img src="img/avatar3.png" class="img-circle" alt="User Image" />
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
                                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <?php include 'widgets-siderbar.php'; ?>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-breadcrumb">
                    <ol class="breadcrumb" style="margin:0 auto;">
                        <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
                        
                    </ol>
                </section>
                
                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-green">

                                <div class="inner">
				<a href="pages/tables/top-menudata.php?view=task-in-progess" style="color: #f9f9f9 !important;">
                                    <h3 class="taskinprogress">
					0
                                    </h3>
                                    <p>
                                        Tasks in Progress
                                    </p>
				</a>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
				
                                <a href="#" class="small-box-footer">

                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
				<a href="pages/tables/data.php?view=active-projects" style="color: #f9f9f9 !important;">
                                    <h3>
                                        <?php echo $ActiveProjects; ?>
                                    </h3>
                                    <p>
                                        Active Projects
                                    </p>
				</a>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="#" class="small-box-footer">

                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
				<a href="pages/tables/top-menudata.php?view=active-users" style="color: #f9f9f9 !important;">
                                    <h3>
                                        <?php echo $ActiveUser; ?>
                                    </h3>
                                    <p>
                                        Active Users
                                    </p>
				</a>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="#" class="small-box-footer">

                                </a>
                            </div>
                        </div><!-- ./col -->
			<div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
				<a href="pages/tables/top-menudata.php?view=users-no-task-today" style="color: #f9f9f9 !important;">
                                    <h3 class="user_no_task_today">
                                        <?php echo $UserNoTaskToday; ?>
                                    </h3>
                                    <p>
                                        Users without task
                                    </p>
				</a>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a href="#" class="small-box-footer">
                                    
                                </a>
                            </div>
                        </div><!-- ./col -->
                    </div><!-- /.row -->

		    <!-- Main row -->
                    <div class="row">
			<div class="box box-success">
		        	<div class="box-header">
		                    <h3 class="box-title"> Time Spent - Projects (Past 7 days)</h3>
		                </div>
		                <div class="box-body chart-responsive">
		                    <div class="chart" id="bar-chart" style="height: 300px;"></div>
		                </div><!-- /.box-body -->
		       </div><!-- /.box -->
		    </div>

			<?php 
			
			$sql_graph_table = $sqlQueries['this_weekchart_dashboard']['query'];
			$retData_graph_table = pg_query($db, $sql_graph_table);
	
			if(!$retData_graph_table){
			   echo pg_last_error($db);
			} else {
			   //error_log( "graph_table Query successfully\n" );
			}
                        #echo pg_num_rows($retData_graph_table);exit;
			?>
                    <div class="row">
			     <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title"> Time Spent - Projects (Past 7 days)</h3>
                                    <div style="float: right;padding: 10px 20px;"><button class="btn btn-success export-timespent" type="button">Export</button></div>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
					    <?php foreach($sqlQueries['this_weekchart_dashboard']['columns'] as $keycol => $columns) { ?>
                                                <th><?php echo $columns; ?></th>
                                             <?php } ?>   
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($rowretData_graph_table = pg_fetch_object($retData_graph_table)){ 
                                                
                                                #if($rowretData_graph_table->timespent == 0){ continue;}
                                            ?>
						
					      	<tr>
						<?php foreach($sqlQueries['this_weekchart_dashboard']['columns'] as $keycol => $columns) { ?>
						    <td>
                                                        <?php echo $keycol == "pnames" ? "<a href='pages/tables/data.php?view=assignee-total-est-project-selection&id=".$rowretData_graph_table->pid."&from=".date('F d,Y',  strtotime(date('Y-m-d').'-6 days'))."&to=".date('F d,Y')."'>" : ""; ?>
                                                        <?php echo $rowretData_graph_table->{$keycol} === null || $rowretData_graph_table->{$keycol} == ' hrs' ?"-": $rowretData_graph_table->{$keycol} ;  ?>
                                                        <?php echo $keycol == "pnames" ?"</a>" :""; ?>
                                                    </td>
						<?php } ?>
						</tr>
						
					   <?php }?>
                                        </tbody>
                                        
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
    		    </div>
                    <a name="unestimated"></a>
			<!-- Unestimated Tasks -->
		   <?php 
			/*
			$sql_unestimated_task = $sqlQueries['unestimated_task']['query'];
			$retData_unestimated_task = pg_query($db, $sql_unestimated_task);
	
			if(!$retData_unestimated_task){
			   echo pg_last_error($db);
			} else {
			   //error_log( "unestimated_task Query successfully\n" );
			}

						
			?>
		    <div class="row">
			     <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Unestimated Tasks (Past 7 days)</h3>
                                    <div style="float: right;padding: 10px 20px;"><button class="btn btn-success export-unestimate" type="button">Export</button></div>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example3" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
					    <?php foreach($sqlQueries['unestimated_task']['columns'] as $keycol => $columns) { ?>
                                                <th><?php echo $columns; ?></th>
                                             <?php } ?>   
                                            </tr>
                                        </thead>
                                        <tbody>
					
                                            <?php while($rowretData_unestimated_task = pg_fetch_object($retData_unestimated_task)){ #print_r($sqlQueries[$action]['columns']);exit;?>
						
					      	<tr>
						<?php foreach($sqlQueries['unestimated_task']['columns'] as $keycol => $columns) { ?>
						    <td><?php echo $rowretData_unestimated_task->{$keycol} === null ?"-": $rowretData_unestimated_task->{$keycol} ;  ?></td>
						<?php } ?>
						</tr>
						
					   <?php }?>
                                           
                                        </tbody>
                                        
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
    		    </div>
                    <?php */?>
                        <?php /*?>
                    <!-- Main row -->
                    <div class="row">
                        <!-- Left col -->
                        <section class="col-lg-7 connectedSortable">                            


                            <!-- Custom tabs (Charts with tabs)-->
                            <div class="nav-tabs-custom">
                                <!-- Tabs within a box -->
                                <ul class="nav nav-tabs pull-right">
                                    <li class="active"><a href="#revenue-chart" data-toggle="tab">Area</a></li>
                                    <li><a href="#sales-chart" data-toggle="tab">Donut</a></li>
                                    <li class="pull-left header"><i class="fa fa-inbox"></i> Sales</li>
                                </ul>
                                <div class="tab-content no-padding">
                                    <!-- Morris chart - Sales -->
                                    <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>
                                    <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;"></div>
                                </div>
                            </div><!-- /.nav-tabs-custom -->

                            <!-- Chat box -->
                            <div class="box box-success">
                                <div class="box-header">
                                    <i class="fa fa-comments-o"></i>
                                    <h3 class="box-title">Chat</h3>
                                    <div class="box-tools pull-right" data-toggle="tooltip" title="Status">
                                        <div class="btn-group" data-toggle="btn-toggle" >
                                            <button type="button" class="btn btn-default btn-sm active"><i class="fa fa-square text-green"></i></button>
                                            <button type="button" class="btn btn-default btn-sm"><i class="fa fa-square text-red"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-body chat" id="chat-box">
                                    <!-- chat item -->
                                    <div class="item">
                                        <img src="img/avatar.png" alt="user image" class="online"/>
                                        <p class="message">
                                            <a href="#" class="name">
                                                <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 2:15</small>
                                                Mike Doe
                                            </a>
                                            I would like to meet you to discuss the latest news about
                                            the arrival of the new theme. They say it is going to be one the
                                            best themes on the market
                                        </p>
                                        <div class="attachment">
                                            <h4>Attachments:</h4>
                                            <p class="filename">
                                                Theme-thumbnail-image.jpg
                                            </p>
                                            <div class="pull-right">
                                                <button class="btn btn-primary btn-sm btn-flat">Open</button>
                                            </div>
                                        </div><!-- /.attachment -->
                                    </div><!-- /.item -->
                                    <!-- chat item -->
                                    <div class="item">
                                        <img src="img/avatar2.png" alt="user image" class="offline"/>
                                        <p class="message">
                                            <a href="#" class="name">
                                                <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 5:15</small>
                                                Jane Doe
                                            </a>
                                            I would like to meet you to discuss the latest news about
                                            the arrival of the new theme. They say it is going to be one the
                                            best themes on the market
                                        </p>
                                    </div><!-- /.item -->
                                    <!-- chat item -->
                                    <div class="item">
                                        <img src="img/avatar3.png" alt="user image" class="offline"/>
                                        <p class="message">
                                            <a href="#" class="name">
                                                <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 5:30</small>
                                                Susan Doe
                                            </a>
                                            I would like to meet you to discuss the latest news about
                                            the arrival of the new theme. They say it is going to be one the
                                            best themes on the market
                                        </p>
                                    </div><!-- /.item -->
                                </div><!-- /.chat -->
                                <div class="box-footer">
                                    <div class="input-group">
                                        <input class="form-control" placeholder="Type message..."/>
                                        <div class="input-group-btn">
                                            <button class="btn btn-success"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.box (chat box) -->                                                        

                            <!-- TO DO List -->
                            <div class="box box-primary">
                                <div class="box-header">
                                    <i class="ion ion-clipboard"></i>
                                    <h3 class="box-title">To Do List</h3>
                                    <div class="box-tools pull-right">
                                        <ul class="pagination pagination-sm inline">
                                            <li><a href="#">&laquo;</a></li>
                                            <li><a href="#">1</a></li>
                                            <li><a href="#">2</a></li>
                                            <li><a href="#">3</a></li>
                                            <li><a href="#">&raquo;</a></li>
                                        </ul>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <ul class="todo-list">
                                        <li>
                                            <!-- drag handle -->
                                            <span class="handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <!-- checkbox -->
                                            <input type="checkbox" value="" name=""/>
                                            <!-- todo text -->
                                            <span class="text">Design a nice theme</span>
                                            <!-- Emphasis label -->
                                            <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
                                            <!-- General tools such as edit or delete-->
                                            <div class="tools">
                                                <i class="fa fa-edit"></i>
                                                <i class="fa fa-trash-o"></i>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <input type="checkbox" value="" name=""/>
                                            <span class="text">Make the theme responsive</span>
                                            <small class="label label-info"><i class="fa fa-clock-o"></i> 4 hours</small>
                                            <div class="tools">
                                                <i class="fa fa-edit"></i>
                                                <i class="fa fa-trash-o"></i>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <input type="checkbox" value="" name=""/>
                                            <span class="text">Let theme shine like a star</span>
                                            <small class="label label-warning"><i class="fa fa-clock-o"></i> 1 day</small>
                                            <div class="tools">
                                                <i class="fa fa-edit"></i>
                                                <i class="fa fa-trash-o"></i>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <input type="checkbox" value="" name=""/>
                                            <span class="text">Let theme shine like a star</span>
                                            <small class="label label-success"><i class="fa fa-clock-o"></i> 3 days</small>
                                            <div class="tools">
                                                <i class="fa fa-edit"></i>
                                                <i class="fa fa-trash-o"></i>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <input type="checkbox" value="" name=""/>
                                            <span class="text">Check your messages and notifications</span>
                                            <small class="label label-primary"><i class="fa fa-clock-o"></i> 1 week</small>
                                            <div class="tools">
                                                <i class="fa fa-edit"></i>
                                                <i class="fa fa-trash-o"></i>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <input type="checkbox" value="" name=""/>
                                            <span class="text">Let theme shine like a star</span>
                                            <small class="label label-default"><i class="fa fa-clock-o"></i> 1 month</small>
                                            <div class="tools">
                                                <i class="fa fa-edit"></i>
                                                <i class="fa fa-trash-o"></i>
                                            </div>
                                        </li>
                                    </ul>
                                </div><!-- /.box-body -->
                                <div class="box-footer clearfix no-border">
                                    <button class="btn btn-default pull-right"><i class="fa fa-plus"></i> Add item</button>
                                </div>
                            </div><!-- /.box -->

                            <!-- quick email widget -->
                            <div class="box box-info">
                                <div class="box-header">
                                    <i class="fa fa-envelope"></i>
                                    <h3 class="box-title">Quick Email</h3>
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
                                    </div><!-- /. tools -->
                                </div>
                                <div class="box-body">
                                    <form action="#" method="post">
                                        <div class="form-group">
                                            <input type="email" class="form-control" name="emailto" placeholder="Email to:"/>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="subject" placeholder="Subject"/>
                                        </div>
                                        <div>
                                            <textarea class="textarea" placeholder="Message" style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="box-footer clearfix">
                                    <button class="pull-right btn btn-default" id="sendEmail">Send <i class="fa fa-arrow-circle-right"></i></button>
                                </div>
                            </div>

                        </section><!-- /.Left col -->
                        <!-- right col (We are only adding the ID to make the widgets sortable)-->
                        <section class="col-lg-5 connectedSortable"> 

                            <!-- Map box -->
                            <div class="box box-solid bg-light-blue-gradient">
                                <div class="box-header">
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button class="btn btn-primary btn-sm daterange pull-right" data-toggle="tooltip" title="Date range"><i class="fa fa-calendar"></i></button>
                                        <button class="btn btn-primary btn-sm pull-right" data-widget='collapse' data-toggle="tooltip" title="Collapse" style="margin-right: 5px;"><i class="fa fa-minus"></i></button>
                                    </div><!-- /. tools -->

                                    <i class="fa fa-map-marker"></i>
                                    <h3 class="box-title">
                                        Visitors
                                    </h3>
                                </div>
                                <div class="box-body">
                                    <div id="world-map" style="height: 250px;"></div>
                                </div><!-- /.box-body-->
                                <div class="box-footer no-border">
                                    <div class="row">
                                        <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <div id="sparkline-1"></div>
                                            <div class="knob-label">Visitors</div>
                                        </div><!-- ./col -->
                                        <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                           <div id="sparkline-2"></div>
                                            <div class="knob-label">Online</div>
                                        </div><!-- ./col -->
                                        <div class="col-xs-4 text-center">
                                            <div id="sparkline-3"></div>
                                            <div class="knob-label">Exists</div>
                                        </div><!-- ./col -->
                                    </div><!-- /.row -->
                                </div>
                            </div>
                            <!-- /.box -->

                            <!-- solid sales graph -->
                            <div class="box box-solid bg-teal-gradient">
                                <div class="box-header">
                                    <i class="fa fa-th"></i>
                                    <h3 class="box-title">Sales Graph</h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        <button class="btn bg-teal btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                                <div class="box-body border-radius-none">
                                    <div class="chart" id="line-chart" style="height: 250px;"></div>                                    
                                </div><!-- /.box-body -->
                                <div class="box-footer no-border">
                                    <div class="row">
                                        <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <input type="text" class="knob" data-readonly="true" value="20" data-width="60" data-height="60" data-fgColor="#39CCCC"/>
                                            <div class="knob-label">Mail-Orders</div>
                                        </div><!-- ./col -->
                                        <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <input type="text" class="knob" data-readonly="true" value="50" data-width="60" data-height="60" data-fgColor="#39CCCC"/>
                                            <div class="knob-label">Online</div>
                                        </div><!-- ./col -->
                                        <div class="col-xs-4 text-center">
                                            <input type="text" class="knob" data-readonly="true" value="30" data-width="60" data-height="60" data-fgColor="#39CCCC"/>
                                            <div class="knob-label">In-Store</div>
                                        </div><!-- ./col -->
                                    </div><!-- /.row -->
                                </div><!-- /.box-footer -->
                            </div><!-- /.box -->                            

                            <!-- Calendar -->
                            <div class="box box-solid bg-green-gradient">
                                <div class="box-header">
                                    <i class="fa fa-calendar"></i>
                                    <h3 class="box-title">Calendar</h3>
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <!-- button with a dropdown -->
                                        <div class="btn-group">
                                            <button class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                                            <ul class="dropdown-menu pull-right" role="menu">
                                                <li><a href="#">Add new event</a></li>
                                                <li><a href="#">Clear events</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#">View calendar</a></li>
                                            </ul>
                                        </div>
                                        <button class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        <button class="btn btn-success btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>                                        
                                    </div><!-- /. tools -->
                                </div><!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <!--The calendar -->
                                    <div id="calendar" style="width: 100%"></div>
                                </div><!-- /.box-body -->  
                                <div class="box-footer text-black">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <!-- Progress bars -->
                                            <div class="clearfix">
                                                <span class="pull-left">Task #1</span>
                                                <small class="pull-right">90%</small>
                                            </div>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-green" style="width: 90%;"></div>
                                            </div>

                                            <div class="clearfix">
                                                <span class="pull-left">Task #2</span>
                                                <small class="pull-right">70%</small>
                                            </div>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-green" style="width: 70%;"></div>
                                            </div>
                                        </div><!-- /.col -->
                                        <div class="col-sm-6">
                                            <div class="clearfix">
                                                <span class="pull-left">Task #3</span>
                                                <small class="pull-right">60%</small>
                                            </div>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-green" style="width: 60%;"></div>
                                            </div>

                                            <div class="clearfix">
                                                <span class="pull-left">Task #4</span>
                                                <small class="pull-right">40%</small>
                                            </div>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-green" style="width: 40%;"></div>
                                            </div>
                                        </div><!-- /.col -->
                                    </div><!-- /.row -->                                                                        
                                </div>
                            </div><!-- /.box -->                            

                        </section><!-- right col -->
                    </div><!-- /.row (main row) -->
                    <?php */ ?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
	

        <!-- add new calendar event modal -->


        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
        <!-- Morris.js charts -->
        <script src="js/raphael-min.js"></script>
        <script src="js/plugins/morris/morris.min.js" type="text/javascript"></script>
        <!-- Sparkline -->
        <script src="js/plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
        <!-- jvectormap -->
        <script src="js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
        <script src="js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
        <!-- jQuery Knob Chart -->
        <script src="js/plugins/jqueryKnob/jquery.knob.js" type="text/javascript"></script>
	<!-- DATA TABES SCRIPT -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/jquery.dataTables.columnFilter.js" type="text/javascript"></script>
        <!-- daterangepicker -->
        <script src="js/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
        <!-- datepicker -->
        <script src="js/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
        <!-- iCheck -->
        <script src="js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>

        <!-- AdminLTE App -->
        <script src="js/AdminLTE/app.js" type="text/javascript"></script>

        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        <script src="js/AdminLTE/dashboard.js" type="text/javascript"></script>

        <!-- AdminLTE for demo purposes -->
        <script src="js/AdminLTE/demo.js" type="text/javascript"></script>
	<script>
		$(function(){

			function datagraph() {
			    $.ajax({
				type : 'POST',
				url : 'graph-data.php',
				success : function(data){
				    var jsonData = $.parseJSON(data);
				    
				    //BAR CHART
					var bar = new Morris.Bar({
					    element: 'bar-chart',
					    resize: false,//jsonData,
					    data: jsonData,/*[
						{y: 'Monday', a: 40},
						{y: 'Tuesday', a: 35},
						{y: 'Wednesday', a: 20},
						{y: 'Thursday', a: 15},
						{y: 'Friday', a: 10},
					       
					    ],*/
                                            //xLabelMargin: 20,
                                            xLabelAngle: 60,
					    barColors: ['#00a65a'],
					    xkey: 'y',
					    ykeys: ['a'],
					    labels: ['Time Spent'],
					    hideHover: 'auto'
					});
                                        
                                        $('#bar-chart').css('height','400px');
                                        $('#bar-chart svg').attr('height','400px');
				},
			    });
			};
			datagraph();
		});
		
		$(function() {

			//update no.of task inprogress
			
			function dataupdate() {
			    $.ajax({
				type : 'POST',
				url : 'ajax-data.php',
				success : function(data){
				    var json = $.parseJSON(data);
				    //alert(json.user_no_task_today);
                                    $('.inner a > h3.user_no_task_today').html(json.user_no_task_today);
				    $('.inner a > h3.taskinprogress').html(json.task_inprogress);
                                    
				},
			    });
			};
			dataupdate();	
		
			var refInterval = setInterval(function() {
				      dataupdate();
				}, 30000); // 30 seconds
                                
                        function datatopbarupdate() {
			    $.ajax({
				type : 'POST',
				url : 'ajax-topbardata.php',
				success : function(data){
				    var json = $.parseJSON(data);
				    //alert(json.user_no_task_today);
                                    $('.unestimated_task span.label').html(json.unestimted_task_count);
				    $('.project span.label').html(json.project);
                                    $('.task_inprogress span.label').html(json.task_inprogress);
                                    
				},
			    });
			};
			datatopbarupdate();	
		
			var refInterval = setInterval(function() {
				      datatopbarupdate();
				}, 30000); // 30 seconds

                        $.datepicker.regional[""].dateFormat = 'dd/mm/yy';
                        $.datepicker.setDefaults($.datepicker.regional['']);
                
			$('#example1').dataTable();
		        $('#example2').dataTable({
		            "bPaginate": true,
		            "bLengthChange": false,
		            "bFilter": false,
		            "bSort": true,
		            "bInfo": true,
		            "bAutoWidth": false
		        });
			$('#example3').dataTable({
                            "aaSorting": [[5,'asc']],
                            "iDisplayLength": 25
                        });/*.columnFilter({ 	sPlaceHolder: "head:before",
					aoColumns: [ 	{ type: "text" },
                                                        { type: "text" },
                                                        { type: "text" },
                                                        { type: "text" },
                                                        { type: "text" },
                                    			{ type: "date-range" }
						]
                            });*/
                        
                        $('.export-unestimate').click(function(){
                            //alert(view);
                            window.location = "php-excel/topmenuexportxls.php?view=unestimated_task";

                        });

                        $('.export-timespent').click(function(){
                            //alert(view);
                            window.location = "php-excel/topmenuexportxls.php?view=this_weekchart_dashboard";

                        });
                        
		});
	</script>
        <script type="text/javascript">
            $('aside.left-side.sidebar-offcanvas').addClass('collapse-left');
            $('aside.right-side').addClass('strech');
        </script>
    </body>
</html>
