<?php

 

	/******************************************* include Config *************************************/
	include './../../config.php';
        include './../../mysqlconfig.php';

         if(!in_array($_REQUEST['view'],$_SESSION['role_permission']) && !in_array($_REQUEST['view'],array('data-project-tasks'))){
	     echo "<script>window.location.href='".HOST_NAME."no_permission.php'</script>";
	     exit;
	}

	$actionsData = array(
                'task-in-progess'=>'Tasks in Progress'
                ,'active-projects'=>'Active Projects'
                ,'active-users'=>'Active Users'
                ,'users-no-task-today'=>'Users without task'
                ,'total-time-spent-assignee-last-month'=>'User Worklogs'
                ,'task-no-duedate'=>'Tasks without Due Date'
                ,'unestimated_task'=>'Unestimated Tasks <span style="font-size: 14px;">(default: Past 7 days)</span>'
                ,'data-project-tasks'=>'Project Tasks'
            );
	$action = $_REQUEST['view'];

	  if (array_key_exists($action,$actionsData)) {
	
		$sql = $sqlQueries[$action]['query'];
		
                if(isset($_REQUEST['from'],$_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to'])){
                    $sqlStrAddTotalTimeSpentAssignee = " to_char(startdate::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(startdate::date, '2YYY-MM-DD')::DATE < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
                    $sqlTRemaining_worklogDFilter = " to_char(w.startdate::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(w.startdate::date, '2YYY-MM-DD')::DATE < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
                    $sqlTRemaining_jiraDFilter = " to_char(i.created::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(i.created::date, '2YYY-MM-DD')::DATE < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE";
                    
                    
                    $sqlStrAddActiveProjects = "to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
                    $sqlStrAddTaskInProgess = "and A.created::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and created::date < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
                    $sqlStrAddTaskNoDues = "and A.created::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and created::date < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
                    $sqlUnestimated = "A.created::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and created::date < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
                    if( !in_array(1, $_SESSION['user_role']) ){
                        $mapped_username = array('shivang'=>'shivang.ahuja','buvaneswaran'=>'buvaneswaran.m','nagraj'=>'nagraj.h');
                        $username = in_array($_SESSION['username'], $mapped_username) ? array_search($_SESSION['username'], $mapped_username) : $_SESSION['username'];
                        $sqlStrAddTaskInProgess .= " and (A.reporter='".$username."' or A.assignee= '".$username."') ";
                        $sqlStrAddTaskNoDues .= " and (A.reporter='".$username."' or A.assignee= '".$username."') ";
                        $sqlUnestimated .= " and (A.reporter='".$username."' or A.assignee= '".$username."') ";                        
                    }
                    $sqlStrProjectTasks = " and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
                    
                   /* $sqlStrAddTotalTimeSpentAssignee = "and (created, created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
                    $sqlStrAddActiveProjects = "(updated, updated) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
                    $sqlStrAddTaskInProgess = "and (A.created, A.created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
                    $sqlStrAddTaskNoDues = "and (A.created, A.created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
                    $sqlUnestimated = "(A.created, A.created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
                   */
                    
                }else{
                    $sqlStrAddTotalTimeSpentAssignee = " Extract(month from to_char(startdate::date, '2YYY-MM-DD')::DATE) = Extract(month from CURRENT_DATE::date) and Extract(year from to_char(startdate::date, '2YYY-MM-DD')::DATE) = Extract(year from CURRENT_DATE::date)";//"and (created, created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime(date('Y-m-d').' - 30 days'))."'::DATE, '".date("Y-m-d 23:59:59",strtotime(date('Y-m-d')))."'::DATE) ";
                    $sqlTRemaining_worklogDFilter = " Extract(month from to_char(w.startdate::date, '2YYY-MM-DD')::DATE) = Extract(month from CURRENT_DATE::date) and Extract(year from to_char(w.startdate::date, '2YYY-MM-DD')::DATE) = Extract(year from CURRENT_DATE::date)";
                    $sqlTRemaining_jiraDFilter = " Extract(month from to_char(i.created::date, '2YYY-MM-DD')::DATE) = Extract(month from CURRENT_DATE::date) and Extract(year from to_char(i.created::date, '2YYY-MM-DD')::DATE) = Extract(year from CURRENT_DATE::date)";
                    
                    
                    $sqlStrAddActiveProjects = "to_char(W.startdate::date, '2YYY-MM-DD')::DATE > (CURRENT_DATE::date -  '7 days'::INTERVAL )";
                    $sqlStrAddTaskInProgess = "";
                    $sqlStrAddTaskNoDues = "and created::date > (CURRENT_DATE::date - INTERVAL '31 days')";//"and created::date BETWEEN (now() - '31 days'::interval)::timestamp AND now()";
                    $sqlUnestimated = "A.created::date > (CURRENT_DATE::date - INTERVAL '7 days')";
                    if( !in_array(1, $_SESSION['user_role']) ){
                        $mapped_username = array('shivang'=>'shivang.ahuja','buvaneswaran'=>'buvaneswaran.m','nagraj'=>'nagraj.h');
                        $username = in_array($_SESSION['username'], $mapped_username) ? array_search($_SESSION['username'], $mapped_username) : $_SESSION['username'];
                        $sqlStrAddTaskInProgess .= " and (A.reporter='".$username."' or A.assignee= '".$username."') ";
                        $sqlStrAddTaskNoDues .= " and (A.reporter='".$username."' or A.assignee= '".$username."') ";
                        $sqlUnestimated .= " and (A.reporter='".$username."' or A.assignee= '".$username."') ";
                    }                
                    $sqlStrProjectTasks = " and to_char(W.startdate::date, '2YYY-MM-DD')::DATE > (CURRENT_DATE::date -  '7 days'::INTERVAL )";
                }
                
                if($action == 'total-time-spent-assignee-last-month'){
                    $sql = sprintf($sql,$sqlStrAddTotalTimeSpentAssignee);
                    
                    $sql2 = $sqlQueries['total-time-spent-assignee-last-month']['query_timeremaining'];
                    $sqlquery_timeremaining = sprintf($sql2,$sqlTRemaining_worklogDFilter,$sqlTRemaining_jiraDFilter);
                            
                    $retData2 = pg_query($db, $sqlquery_timeremaining);
                    
                    $timeremainData = array();
                    while($rowretData2 = pg_fetch_object($retData2)) {
                        if($rowretData2->assignee != '' && $rowretData2->timeestimateremaining != '00:00'){
                            $timeremainData["$rowretData2->assignee"] = $rowretData2->timeestimateremaining;
                        }
                    }
                    
                    $fromDateWD = isset($_REQUEST['from']) ? date('Y-m-d',strtotime($_REQUEST['from'])) : date('Y-m-01');
                    $toDateWD = isset($_REQUEST['to']) ? date('Y-m-d',strtotime($_REQUEST['to'])) : date('Y-m-t');
                    #print_r($timeremainData);exit;
                }elseif ($action == 'active-projects') {
                    $sql = sprintf($sql,$sqlStrAddActiveProjects);
                }elseif ($action =='task-in-progess') {
                    $sql = sprintf($sql,$sqlStrAddTaskInProgess);
                }elseif ($action == 'task-no-duedate') {
                    $sql = sprintf($sql,$sqlStrAddTaskNoDues);
                }elseif ($action == 'unestimated_task') {
                    $sql = sprintf($sql,$sqlUnestimated);
                }elseif ($action == 'data-project-tasks') {
                    $sql = sprintf($sql,$_REQUEST['id'],$sqlStrProjectTasks);
                }
                
                #echo $sql;exit;
		$retData = pg_query($db, $sql);
	
		if(!$retData){
		   echo pg_last_error($db);
		} else {
		   //error_log( "Query successfully\n" );
		}

		#echo $sql;exit;
	   } else {
		echo "request Not Possible";
	   }
/*echo "<pre>";
           while($rowretData = pg_fetch_object($retData)) {
                print_r($rowretData);
           }exit;*/
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
        
        <link href="../../css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <link href="../../css/iCheck/minimal/blue.css" rel="stylesheet" type="text/css" />
        
        
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
                #loading-indicator {
                position: absolute;
                left: 10px;
                top: 10px;
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
        <?php include 'widget-topbar.php'; ?>
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
                        <?php if($_SESSION['highest_role']==1){ ?>
                        <li><a href="./../../index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                        <?php } ?>
                        <li class="active"><?php echo $actionsData[$action]; ?></li>
                    </ol>
                </section>
                
                <!-- Main content -->
                <section class="content">
                    <div class="row">
			
                        <div class="col-xs-12">
			    
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title"><?php echo $actionsData[$action]; ?></h3>
                                    <?php $dontRequireDateRange = array('active-users','users-no-task-today');
                                    if(!in_array($action, $dontRequireDateRange)){ ?>
                                    <div>
                                        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;margin: 10px">
                                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                            <span></span> <b class="caret"></b>
                                        </div>
                                    </div>
                                    <?php }?>
                                       
                                    <div style="float: right;padding: 10px 20px;">
                                        <?php if($action == 'users-no-task-today'){ ?>
                                            <button class="btn btn-success btn-primary sendMailPOP" type="button"><i class="fa fa-pencil"></i> Message</button>
                                        <?php } ?>
                                        <?php if($action == 'total-time-spent-assignee-last-month'){ ?>
					<button class="btn btn-success exportReport" type="button">Summary</button>
                                        <?php } ?>
                                        <button class="btn btn-success export" type="button">Export</button>
                                    </div>
                                    
                                </div><!-- /.box-header -->
				
                                <div class="box-body table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                            <?php if($action == 'users-no-task-today'){ ?>
                                                <th><input id="chkAll" type="checkbox" /></th>
                                            <?php } ?>
                                                
					    <?php foreach($sqlQueries[$action]['columns'] as $keycol => $columns) { ?>
                                                <th><?php echo $columns; ?></th>
                                             <?php } ?>   
                                                
                                            <?php if($action == 'total-time-spent-assignee-last-month'){ ?>
                                                <th>Estimate remaining</th>
                                                <th style="width:22%;">Compliances</th>
                                            <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            if($action == 'total-time-spent-assignee-last-month'){ 
                                                    $Objholiday = mysql_query('SELECT count(*) as holiday_days from tbl_holidays WHERE date BETWEEN "'.date("Y-m-01",strtotime($fromDateWD)).'" AND "'.date("Y-m-d",strtotime($toDateWD)).'"');
						    while($rowretData = mysql_fetch_object($Objholiday)){ 
							$holidays = $rowretData->holiday_days ;
						    }
                                                    $totalDaysWkHr = 8 * datediffExWkDays(date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',strtotime($toDateWD).' -1 days'));
                                                    echo "Total Work Hours : ".($totalDaysWkHr - $holidays * 8);
                                                    if(date('Y-m-d',strtotime($fromDateWD)) < date('Y-m-d') && date('Y-m-d',strtotime($toDateWD)) > date('Y-m-d')){
                                                        //Then 3 datas (Future Estimate,current Estimate and user spent)
                                                        
                                                        $totalFutureEstimate = 100; //Which will be 100%
                                                        
                                                        $currentHrs = 8 * datediffExWkDays(date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',  strtotime('-1 days')));
                                                        
                                                        $currentEstPer = ($currentHrs / $totalDaysWkHr) * 100;
                                                        
                                                        
                                                    }elseif(date('Y-m-d',strtotime($toDateWD)) < date('Y-m-d')){
                                                        //2 datas (date Estimate and user spent)
                                                        //goes on 
                                                        $currentHrs = 8 * datediffExWkDays(date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',  strtotime($toDateWD)));
                                                    }else{
                                                        //2 datas (date Estimate and user spent)
                                                        //goes on 
                                                        $currentHrs = 8 * datediffExWkDays(date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',  strtotime($toDateWD.'-1 days')));
                                                    }
                                                    
                                                    $totalRemaingHr = 8 * datediffExWkDays(date('Y-m-d'),date('Y-m-d',strtotime($toDateWD)));
                                            }
                                        ?>
                                        
					<?php while($rowretData = pg_fetch_object($retData)){ 
                                            
					#print_r($rowretData);exit;?>
						
					      	<tr>
                                                    <?php if($action == 'users-no-task-today'){ ?>
                                                        <td><input class="chkthis" type="checkbox" name="sendmail[]" value="<?php echo $rowretData->email_address; ?>" id="<?php echo $rowretData->user_name;?>"/></td>
                                                    <?php } ?>    
						<?php foreach($sqlQueries[$action]['columns'] as $keycol => $columns) { ?>
                                                    <td>
                                                        
                                                        <?php 
                                                            
                                                            $filterFromTo = '';
                                                            if(isset($_REQUEST['from'] ,$_REQUEST['to'])){
                                                                $filterFromTo = '&from='.$_REQUEST['from'].'&to='.$_REQUEST['to'];
                                                                $holiday = mysql_query('SELECT count(*) from tbl_holidays WHERE date BETWEEN "'.date("Y-m-d",strtotime($_REQUEST['from'])).'" AND "'.date("Y-m-d",strtotime($_REQUEST['to'])).'"');
                                                                $NoofHoliday = mysql_fetch_row($holiday);
                                                                $HolidayHrs= $NoofHoliday[0]*8;
                                                                
                                                            }elseif ($action == 'total-time-spent-assignee-last-month') {
                                                                $filterFromTo = '&from='.date('M d,Y',  strtotime(date('d-m-Y', strtotime('first day of this month')))).'&to='.date('M d,Y',  strtotime(date('d-m-Y', strtotime('last day of this month'))));   
                                                                $holiday = mysql_query("SELECT count(*) FROM tbl_holidays WHERE YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW()) AND DAY(date) < DAY(NOW())");
                                                                $NoofHoliday = mysql_fetch_row($holiday);
                                                                $HolidayHrs= $NoofHoliday[0]*8;
                                                            }
                                                        ?>
                                                        
                                                        <?php if($action == 'total-time-spent-assignee-last-month' && $keycol=='assignee' && $rowretData->assignee != ''){?>
                                                            <img width="" title="<?php echo $rowretData->assignee; ?>" src="<?php echo $GLOBALS['jira_url']."/secure/useravatar?size=small&ownerId=".$rowretData->assignee;?>"/>
                                                            <a href="./../../pages/tables/user-time-spent.php?username=<?php echo $rowretData->assignee.$filterFromTo; ?>">
                                                        <?php }elseif ($action == 'active-projects' && $keycol=='pname') { ?>
                                                            <a href="./../../pages/tables/data.php?view=assignee-total-est-project-selection&id=<?php echo $rowretData->project.$filterFromTo;?>">            
                                                        <?php }elseif ( ($action=='task-in-progess' || $action=='task-no-duedate' || $action=='unestimated_task') && $keycol == 'gotojira') { ?>
                                                                <a target="_blank"href="<?php echo $GLOBALS['jira_url']."/browse/".$rowretData->pkey."-".$rowretData->issuenum;?>">
                                                                    <img width="" title="Go to Jira" src="<?php echo $GLOBALS['jira_url']."/secure/projectavatar?pid=".$rowretData->pid;?>"/>
                                                                    <?= $rowretData->pkey."-".$rowretData->issuenum; ?>
                                                                </a>
                                                        <?php 
                                                                    continue;
                                                               }elseif ( ($action=='task-in-progess' || $action=='task-no-duedate' || $action=='unestimated_task' ) && $keycol == 'assignee') { ?>
                                                                
                                                                <img width="" title="<?php echo $rowretData->assignee; ?>" src="<?php echo $GLOBALS['jira_url']."/secure/useravatar?size=small&ownerId=".$rowretData->assignee;?>"/>
                                                                
                                                        <?php }elseif ( ($action=='task-in-progess' || $action=='task-no-duedate' || $action=='unestimated_task') && $keycol == 'reporter') { ?>
                                                                
                                                                <img width="" title="<?php echo $rowretData->reporter; ?>" src="<?php echo $GLOBALS['jira_url']."/secure/useravatar?size=small&ownerId=".$rowretData->reporter;?>"/>
                                                                
                                                        <?php }elseif ($action=='active-users' && $keycol == 'profile_pic') { ?>
                                                                <a target="_blank"href="<?php echo $GLOBALS['jira_url']."/secure/ViewProfile.jspa?name=".$rowretData->user_name;?>">
                                                                    <img width="" title="Go to Jira" src="<?php echo $GLOBALS['jira_url']."/secure/useravatar?size=small&ownerId=".$rowretData->user_name;?>"/>
                                                                </a>
                                                        <?php 
                                                                    continue;
                                                               } ?>
                                                                
                                                            <?php 
                                                                if($action=='task-in-progess' && $keycol=='assignee' && $rowretData->assignee == ''){
                                                                    echo '-';
                                                                }elseif($keycol=='assignee' && $rowretData->assignee == ''){
                                                                    echo 'Unestimated Task';
                                                                }else{
                                                                    echo $rowretData->{$keycol} === null || $rowretData->{$keycol} == '' ?"": ($keycol == 'timespent'? $rowretData->{$keycol}." hrs":$rowretData->{$keycol}) ;  
                                                                }
                                                            ?>
                                                                
                                                        <?php if( 
                                                                     ( $action == 'total-time-spent-assignee-last-month' && $keycol=='assignee' && $rowretData->assignee != '') 
                                                                  || ( $action == 'active-projects' && $keycol=='pname' )
                                                                  
                                                                 ){?>
                                                            </a>
                                                        <?php }?>
                                                    </td>
						<?php } ?>
                                                    
                                                    <?php if($action == 'total-time-spent-assignee-last-month'){ ?>
                                                    <td><?php echo isset($timeremainData["$rowretData->assignee"]) ?$timeremainData["$rowretData->assignee"]." hrs":"00:00 hrs"; ?></td>
                                                    <td>
                                                        <?php
                                                            $userSpent = (($rowretData->timespent) / $totalDaysWkHr) * 100;
                                                            
						    	    $mapped_username = array('shivang'=>'shivang.ahuja','buvaneswaran'=>'buvaneswaran.m','nagraj'=>'nagraj.h');
	    						    $leave_username = in_array($rowretData->assignee,array_keys($mapped_username)) ? $mapped_username[$rowretData->assignee]: $rowretData->assignee;
                                                            $leaveHrs = GetLeaveUserName($leave_username,$fromDateWD,$toDateWD);
							    
                                                            $leavesHrsPer = ($leaveHrs / $totalDaysWkHr) * 100;
                                                            $HolidasysHrsPer = ($HolidayHrs / $totalDaysWkHr) * 100;
                                                            $currentEstPerFinal = $currentEstPer - $userSpent - $leavesHrsPer-$HolidasysHrsPer;
                                                            
                                                            if(isset($totalFutureEstimate) && !empty($totalFutureEstimate)){
                                                                $currentEstPerFinal = $currentEstPerFinal > 0 ? $currentEstPerFinal:0;
                                                                $totalFutureEstimateFinal = $totalFutureEstimate - $currentEstPerFinal - $userSpent - $leavesHrsPer-$HolidasysHrsPer;
                                                            }else{
                                                                $currentEstPerFinal = 100-$userSpent-$leavesHrsPer-$HolidasysHrsPer;
                                                            }
                                                            
                                                            $rowretDatatimespent = (int) str_replace(':', '.', $rowretData->timespent);
                                                            
                                                            $unlogedHrs = $currentHrs - $rowretDatatimespent -$leaveHrs -$HolidayHrs; //exclude today
                                                            
                                                            #echo "$unlogedHrs = $currentHrs - $rowretDatatimespent"."<br>";
                                                            #echo ($userSpent).",".($leavesHrsPer).",".($currentEstPerFinal).",".round($totalFutureEstimateFinal);
                                                        ?>
                                                        <div style="width:100%">
                                                            
                                                            <div style="width: <?php echo round($userSpent,1) < 0?0: (round($userSpent,1) > 100? 100:round($userSpent,1)); ?>%; background-color: #51a825; text-align: center; float: left; display: inline-block;">
                                                                <p title="Spent:<?php echo round($userSpent,1) < 0?0: round($userSpent,1); ?>% (<?php echo $rowretDatatimespent >=0? $rowretDatatimespent:0;?> Hrs)" style="margin:1px auto;"> &nbsp; </p>
                                                            </div>                                                            
                                                            <div style="width: <?php echo round($leavesHrsPer,1) < 0?0: round($leavesHrsPer,1); ?>%; background-color: #EE82EE; text-align: center; float: left; display: inline-block;">
                                                                <p title="Leave:<?php echo round($leavesHrsPer,1) < 0?0: round($leavesHrsPer,1); ?>% (<?php echo $leaveHrs >=0? $leaveHrs:0;?> Hrs)" data-bind="<?php echo ($leaveHrs.'-'.$leavesHrsPer);?>" style="margin:1px auto;"> &nbsp; </p>
                                                            </div>
                                                            <div style="width: <?php echo round($HolidasysHrsPer,1) < 0?0: round($HolidasysHrsPer,1); ?>%; background-color: #FFA500; text-align: center; float: left; display: inline-block;">
                                                                <p title="Holiday:<?php echo round($HolidasysHrsPer,1) < 0?0: round($HolidasysHrsPer,1); ?>% (<?php echo $HolidayHrs >=0? $HolidayHrs:0;?> Hrs)" data-bind="<?php echo ($HolidayHrs.'-'.$HolidasysHrsPer);?>" style="margin:1px auto;"> &nbsp; </p>
                                                            </div>
                                                            <div style="width: <?php echo round($currentEstPerFinal,1) < 0?0: round($currentEstPerFinal,1); ?>%; background-color: red; text-align: center; float: left; display: inline-block;">
                                                                <p title="Unlogged:<?php echo round($currentEstPerFinal,1) < 0?0: round($currentEstPerFinal,1); ?>% (<?php echo $unlogedHrs >=0? $unlogedHrs:0;?> Hrs)" data-bind="<?php echo ($currentHrs.'-'.$rowretDatatimespent);?>" style="margin:1px auto;"> &nbsp; </p>
                                                            </div>

                                                            <div style="width: <?php echo round($totalFutureEstimateFinal,1) < 0?0: round($totalFutureEstimateFinal,1); ?>%; background-color: #89afd7; text-align: center; display: inline-block;">
                                                                <p title="Upcoming:<?php echo round($totalFutureEstimateFinal,1) < 0?0: round($totalFutureEstimateFinal,1); ?>% (<?php echo $totalRemaingHr >=0? $totalRemaingHr:0;?> Hrs)" style="margin:1px auto;"> &nbsp; </p>
                                                            </div>
                                                        </div>
                                                        

                                                    </td>
                                                    <?php } ?>
						</tr>
						
					<?php }?>
                                            
                                        </tbody>
                                        
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div>

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        <!-- COMPOSE MESSAGE MODAL -->
        <div class="modal fade" id="compose-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-envelope-o"></i> Compose New Message</h4>
                    </div>
                    <form action="#" method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">TO:</span>
                                    <input name="email_to" id="sendtoaddress" class="form-control" placeholder="Email TO">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">CC:</span>
                                    <input name="email_to_cc" id="sendtocc" class="form-control" placeholder="Email CC">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">BCC:</span>
                                    <input name="email_to_bcc" class="form-control" placeholder="Email BCC">
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea name="message" id="email_message" class="form-control" placeholder="Message" style="height: 120px;"></textarea>
                            </div>
                            <div class="form-group" style="text-align: center;">
                                <img src="../../img/ajax-loader1.gif" style="display:none" id="loading-I"/>
                            </div>
                            

                        </div>
                        <div class="modal-footer clearfix">

                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>

                            <button type="submit" class="btn btn-primary pull-left sendMail"><i class="fa fa-envelope"></i> Send Message</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <script src="../../js/jquery.min.js"></script>
        <script src="../../js/bootstrap.min.js" type="text/javascript"></script>
        <!-- DATA TABES SCRIPT -->
        <script src="../../js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="../../js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        
        <script src="../../js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
        
        <!-- AdminLTE App -->
        <script src="../../js/AdminLTE/app.js" type="text/javascript"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="../../js/AdminLTE/demo.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui.min.js" type="text/javascript"></script>

<!--	<script src="../../js/date-euro.js" type="text/javascript"></script>	-->
        <script src="../../js/date-uk.js" type="text/javascript"></script>
        <!-- page script -->
         
        <script type="text/javascript">
             
            $(function() {
                //"use strict";
                function datatopbarupdate() {
			    $.ajax({
				type : 'POST',
				url : './../../ajax-topbardata.php',
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
                                
                                
                var view = "<?php echo $_REQUEST['view']; ?>";
                var pid = "<?php echo $_REQUEST['id']; ?>";
                
                if( view == 'task-in-progess' ) {
                    $('#example1').dataTable({
			"aoColumns": [
                                    { "sType": "number" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "date-uk" },
                                    { "sType": "date-uk" },
                                    
                                ],
                        "aaSorting": [[ 4, "desc" ]],
                        "iDisplayLength": 25
                    });
                }else if( view == 'active-projects' ) {
                    $('#example1').dataTable({
                        "aaSorting": [[2,'desc']],
                        "iDisplayLength": 25
                    });
                }else if( view == 'active-users' ) {
                    $('#example1').dataTable({
                        "aoColumns": [
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "date-uk" },
                                    
                                ],
                        "aaSorting": [[1,'asc']],
                        "iDisplayLength": 50
                    });
                }else if( view == 'users-no-task-today' ) {
                    $('#example1').dataTable({
                        "iDisplayLength": 500,
                        "multipleSelection": true
                    });
                }else if( view == 'task-no-duedate' ) {
                    $('#example1').dataTable({
                                "aoColumns": [
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "date-uk" },
                                    
                                ],
                                "aaSorting": [[ 4, "desc" ],[0,"asc"]],
	                        "iDisplayLength": 50
                    });
                }else if( view == 'unestimated_task' ) {
                    $('#example1').dataTable({
			"aoColumns": [
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "date-uk" },
                                    
                                ],
                        "aaSorting": [[5,'desc']],
                        "iDisplayLength": 50
                    });
                }else if( view == 'data-project-tasks' ) {
                    $('#example1').dataTable({
			"aoColumns": [
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "date-uk" },
                                    { "sType": "date-uk" },
                                    
                                    { "sType": "string" },
                                    { "sType": "string" },
                                ],
                        "aaSorting": [[5,'desc']],
                        "iDisplayLength": 50
                    });
                }else if( view == 'total-time-spent-assignee-last-month' ) {
                    
                    jQuery.fn.dataTableExt.oSort['timehrs-asc']  = function(x,y) {

                        x = parseFloat (x.replace(':','.'));
                        y = parseFloat (y.replace(':','.'));

                        return x<y?-1:x>y?1:0;

                      };

                      jQuery.fn.dataTableExt.oSort['timehrs-desc'] = function(x,y) {

                        x = parseFloat (x.replace(':','.'));
                        y = parseFloat (y.replace(':','.'));

                          return x<y?1:x>y?-1:0;
                      };
                    
                    $('#example1').dataTable({
			"aoColumns": [
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "timehrs" },
                                    { "sType": "timehrs" },
                                    { "sType": "string" },
                                ],
                        "aaSorting": [[0,'asc']],
                        "iDisplayLength": 25
                    });
                }else {
                    $('#example1').dataTable({"iDisplayLength": 25});
                }
                
                $('#example2').dataTable({
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });
		
		

		$('.export').click(function(){
		    //alert(view);
                    redirectUrl = "./../../php-excel/topmenuexportxls.php?view="+view+'&id='+pid;
                    
                    if(<?php echo isset($_REQUEST['from'], $_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to']) ? 1:0; ?>){
                        redirectUrl += '&from='+'<?php echo isset($_REQUEST['from'])&& !empty($_REQUEST['from'])?$_REQUEST['from']:'0'; ?>'+'&to='+'<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])?$_REQUEST['to']:'0'; ?>';
                    }
		    			
                    window.location = redirectUrl;
		});
		$('.exportReport').click(function(){
		    from = "<?php echo (isset($_REQUEST['from']) && !empty($_REQUEST['from'])) ? date('d-m-Y',strtotime($_REQUEST['from'])) :'0'; ?>";
		    to = "<?php echo isset($_REQUEST['to'])&& !empty($_REQUEST['to']) ? date('d-m-Y',strtotime($_REQUEST['to'])):'0'; ?>";
		    //alert(from);
		
                    redirectUrl = "./../../php-excel/report_logs.php?from="+from+"&to="+to;
		    //alert(redirectUrl);			
                    window.location = redirectUrl;
		});

		//On Change Of Project Dropdown
		$('#project-select').change(function(){
			//alert($('#project-select').val());
			window.location = "./../../pages/tables/data.php?view="+view+"&id="+$('#project-select').val();
			///jiraadmin/pages/tables/data.php?view=<?php echo $action; ?>&id=
		});
                
                $('#chkAll').on('ifClicked', function(){
                    if($('#chkAll').is(':checked')){
                        $('#chkAll,.chkthis').iCheck('uncheck');
                    }else{
                        $('#chkAll,.chkthis').iCheck('check');
                    }
                });
                
                $('.sendMailPOP').click(function(){
                    //First - Collect all the users emailid (comma seperated)
                    var emailids = '';
                    $.each($('.chkthis:checkbox:checked'),function(){
                        emailids += $(this).val()+',';
                    });
                    
                    $('#sendtoaddress').val(emailids);
                    //Clear form Errors
                    $('input[name="email_to"],.wysihtml5-sandbox').css('border-color','#ccc');
                    
                    //Fourthly -  Show Modal box
                    $( "#compose-modal" ).modal('show');
                    
                });
                
                
                function validateEmail(field) {
                    //var regex=/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i;
                    var regex=/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
                    return (regex.test((field))) ? true : false;
                }
                function validateMultipleEmailsCommaSeparated(value) {
                    var result = value.split(",");

                    //alert(result);
                    for(var i = 0;i < result.length;i++)
                    if(!validateEmail(result[i]))
                            return false;              
                    return true;
                }
                
                //By Default
                $('#chkAll,.chkthis').iCheck('uncheck');
                $('input[name="email_to"],.wysihtml5-sandbox').css('border-color','#ccc');
                //Initialize WYSIHTML5 - text editor
                $("#email_message").wysihtml5();
                
                $('.sendMail').click(function(){
                    var flag = 1;
                    
                    $.each($('input[name="email_to"],#email_message'),function(){
                        if($(this).val() == ''){
                            
                            if( $(this).attr('id') == 'email_message'){
                                $('.wysihtml5-sandbox').css('border-color','red');
                            }else{
                                $(this).css('border-color','red');
                            }
                            flag = 0;
                        }else{
                            $(this).css('border-color','#ccc');
                        }
                        
                        if(flag == 1 && $(this).attr('name') == 'email_to'){
                            k = $(this).val();
                            flag = validateMultipleEmailsCommaSeparated(k.substring(0,k.length -1)) ? 1 : 0;
                            
                            $(this).css('border-color','#ccc');
                        }
                    });
                    
                    if(flag){
                        
                        $('#loading-I').show();
                        
                        $.ajax({
                            url:'./../../sendmail.php',
                            type:'post',
                            data:'to='+k.substring(0,k.length -1)+'&message='+$('#email_message').val(),
                            success: function(response){
                                if(response == 'Message sent!'){
                                    alert('Message Sent!!');
                                    $('#compose-modal').modal('hide');
                                }else{
                                    alert('Message sending Failed');
                                    $('#loading-I').hide();
                                }
                            }
                        });
                    }
                    
                    return false;
                });
                
                
            });
        </script>
	  <script src="./../../chosen/chosen.jquery.js" type="text/javascript"></script>
	  <script src="./../../chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
	  <script type="text/javascript">
	    var config = {
	      '.chosen-select'           : {},
	      '.chosen-select-deselect'  : {allow_single_deselect:true},
	      '.chosen-select-no-single' : {disable_search_threshold:10},
	      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
	      '.chosen-select-width'     : {width:"95%"}
	    }
	    for (var selector in config) {
	      $(selector).chosen(config[selector]);
	    }
	  </script>
          
        <script type="text/javascript" src="./../../js/bootstrap-daterangepicker-master/moment.js"></script>
        <script type="text/javascript" src="./../../js/bootstrap-daterangepicker-master/daterangepicker.js"></script>
        
        <script type="text/javascript">
                $(document).ready(function() {

                   var cb = function(start, end, label) {
                     //console.log(start.toISOString(), end.toISOString(), label);
                     $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                     //alert("Callback has fired: [" + start.format('MMMM D, YYYY') + " to " + end.format('MMMM D, YYYY') + ", label = " + label + "]");
                   };

                   fromDate = '<?php echo isset($_REQUEST['from']) && !empty($_REQUEST['from'])? date('m/d/Y',strtotime($_REQUEST['from'])):''; ?>';
                   startDatePic = typeof(fromDate) == "undefined" || fromDate==''? moment().subtract(3, 'years'): fromDate;
                   endDate = '<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])? date('m/d/Y',strtotime($_REQUEST['to'])):''; ?>';
                   endDatePic = typeof(endDate) == "undefined" || endDate=='' ? moment(): endDate;

                   
                   if(<?php echo $action =='task-no-duedate' && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0 ?>){
                       startDatePic = moment().subtract(31, 'days');//.format('MMMM D, YYYY');
                   } else if(<?php echo $action =='total-time-spent-assignee-last-month' && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0 ?> ){
                       startDatePic = moment().startOf('month');
                       endDatePic = moment().endOf('month');
                   } else if(<?php echo ($action =='active-projects' || $action =='unestimated_task')&& !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0; ?>){
                       startDatePic = moment().subtract(7, 'days');//.format('MMMM D, YYYY');
                   }
                   
                   //alert('fromDate'+moment().subtract(3, 'days'));
                   //alert('First '+startDatePic+ '  '+endDatePic );
                   var optionSet1 = {
                            startDate: startDatePic,
                            endDate: endDatePic,
                           // minDate: '01/01/2012',
                            //maxDate: '12/31/2014',
                            //dateLimit: { days: 60 },
                            showDropdowns: true,
                            showWeekNumbers: true,
                            timePicker: false,
                            timePickerIncrement: 1,
                            timePicker12Hour: true,
                            ranges: {
                               'Today': [moment(), moment()],
                               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                               'This Month': [moment().startOf('month'), moment().endOf('month')],
                               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                            },
                            opens: 'left',
                            buttonClasses: ['btn btn-default'],
                            applyClass: 'btn-small btn-primary',
                            cancelClass: 'btn-small',
                            format: 'MM/DD/YYYY',
                            separator: ' to ',
                            locale: {
                                applyLabel: 'Submit',
                                cancelLabel: 'Clear',
                                fromLabel: 'From',
                                toLabel: 'To',
                                customRangeLabel: 'Custom',
                                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
                                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                                firstDay: 1
                            }
                        };
                        
                        fromDate = '<?php echo isset($_REQUEST['from']) && !empty($_REQUEST['from'])? date('M d,Y',  strtotime($_REQUEST['from'])):''; ?>';
                        startDatePic = typeof(fromDate) == "undefined" || fromDate =='' ? moment().subtract(3, 'years').format('MMM D, YYYY'): fromDate;
                        endDate = '<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])? date('M d,Y',  strtotime($_REQUEST['to'])):''; ?>';
                        endDatePic = typeof(endDate) == "undefined" || endDate =='' ? moment().format('MMM D, YYYY'): endDate;
                        
                        if(<?php echo $action =='task-no-duedate' && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0; ?>){
                            startDatePic = moment().subtract(31, 'days').format('MMM D, YYYY');
                        } else if(<?php echo $action =='total-time-spent-assignee-last-month' && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0 ?> ){
                            startDatePic = moment().startOf('month').format('MMM D, YYYY');
                            endDatePic = moment().endOf('month').format('MMM D, YYYY');
                        } else if(<?php echo ($action =='active-projects' || $action =='unestimated_task' ) && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0; ?>){
                            startDatePic = moment().subtract(7, 'days').format('MMM D, YYYY');
                        }
                        
                        
                        //alert('Second '+startDatePic+ '  '+endDatePic );
                        $('#reportrange span').html(startDatePic + ' - ' + endDatePic);

                        $('#reportrange').daterangepicker(optionSet1, cb);

                        $('#options1').click(function() {
                            $('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
                        });


                        $('#reportrange').on('apply.daterangepicker', function(ev, picker) { 
                            /*console.log("apply event fired, start/end dates are " 
                              + picker.startDate.format('MMMM D, YYYY') 
                              + " to " 
                              + picker.endDate.format('MMMM D, YYYY')
                            );*/ 

                            window.location= "./../../pages/tables/top-menudata.php?view=<?php echo $_REQUEST['view'] ?>&id=<?php echo $_REQUEST['id'] ?>&from="+picker.startDate.format('MMMM D, YYYY')+"&to="+picker.endDate.format('MMMM D, YYYY');

                        });
               });
        </script>
        <style>
            .modal-dialog{left: 0%;}
        </style>
        <script type="text/javascript">
            $('aside.left-side.sidebar-offcanvas').addClass('collapse-left');
            $('aside.right-side').addClass('strech');
        </script>
    </body>
</html>
