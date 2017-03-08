<?php 

include './../../config.php';
include './../../mysqlconfig.php';

/************** User task Individual Listing *****************/
$sql = $sqlQueries['user-task-time-spent-individual']['query'];

if(isset($_REQUEST['from'],$_REQUEST['to'])){
    $addsqlFormtoFilter = "and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";//"and (A.created, A.created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
    $sql = sprintf($sql,$_REQUEST['username'],$addsqlFormtoFilter);
    
    $sqlTRemaining_worklogDFilter = " to_char(w.startdate::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(w.startdate::date, '2YYY-MM-DD')::DATE < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE "
            . "and w.author='".$_REQUEST['username']."'";
    $sqlTRemaining_jiraDFilter = " to_char(i.created::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(i.created::date, '2YYY-MM-DD')::DATE < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE "
            . "and i.assignee='".$_REQUEST['username']."'";        
}else{
    $sql = sprintf($sql,$_REQUEST['username'],'');
    
    //not required
    $sqlTRemaining_worklogDFilter = " Extract(month from to_char(w.startdate::date, '2YYY-MM-DD')::DATE) = Extract(month from CURRENT_DATE::date) and Extract(year from to_char(w.startdate::date, '2YYY-MM-DD')::DATE) = Extract(year from CURRENT_DATE::date)";
    $sqlTRemaining_jiraDFilter = " Extract(month from to_char(i.created::date, '2YYY-MM-DD')::DATE) = Extract(month from CURRENT_DATE::date) and Extract(year from to_char(i.created::date, '2YYY-MM-DD')::DATE) = Extract(year from CURRENT_DATE::date)";
}

#echo $sql;exit;
$retData = pg_query($db, $sql);
	
if(!$retData){
   echo pg_last_error($db);
} else {
   //error_log( "Query successfully\n" );
}

// timeremaining calculation
    $sql2 = $sqlQueries['user-task-time-spent-individual']['query_timeremaining'];
    $sqlquery_timeremaining = sprintf($sql2,$sqlTRemaining_worklogDFilter,$sqlTRemaining_jiraDFilter);

    $retData2 = pg_query($db, $sqlquery_timeremaining);

    $timeremainData = array();
    while($rowretData2 = pg_fetch_object($retData2)) {
        //echo $rowretData2->project."  ".$rowretData2->timeestimateremaining."\n";
        if($rowretData2->project != '' && $rowretData2->timeestimateremaining != '00:00'){
            $timeremainData["$rowretData2->project"] = $rowretData2->timeestimateremaining;
        }
    }
    #print_r($timeremainData);exit;
while($fetch = pg_fetch_assoc($retData))
{
    if(!isset($output[$fetch['project']]['total_timespent'])){
        $output[$fetch['project']]['total_timespent'] = 0;
    }
    #echo "<pre>";print_r($fetch);
  #      continue;
    $output[$fetch['project']]['pid']= $fetch['project'];
    $output[$fetch['project']]['pname']= $fetch['pname'];
    $output[$fetch['project']]['from'][]= $fetch['startdate'];
    $output[$fetch['project']]['to'][]= $fetch['resolutiondate'];
    $output[$fetch['project']]['total_timespent'] += $fetch['timespent'];
    $output[$fetch['project']]['timeoriginalestimate'] += $fetch['timeoriginalestimate'];
    #$response[] = $fetch;
    
}#exit;
#echo "<pre>";print_r($retData);exit;
$columns= array(
            'pname'=>'Project',
            //'from'=>'From',
            //'to'=>'To',
           // 'total_days'=>'Total Days',
	   // 'timeoriginalestimate'=>'Estimate Given',
            'total_timespent'=>'Total Timespent',
        );

        $finalArray = array();
        $total_days = 0;
        $total_time_Spent = 0;
        foreach ($output as $projectId => $projectDeatails) {
            
            $finalArray[$projectId]['pid'] = $projectDeatails['pid'];
            $finalArray[$projectId]['pname'] = $projectDeatails['pname'];
            
            $min = date('Y-m-d H:i:s',  strtotime($projectDeatails['from'][0]));
            $max = date('Y-m-d H:i:s',  strtotime($projectDeatails['to'][0]));
            
            foreach ($projectDeatails['from'] as $fromvalue) {
                
                if(date('Y-m-d H:i:s',  strtotime($fromvalue)) <= $min ) {
                    $min =  date('Y-m-d H:i:s',  strtotime($fromvalue)) ;
                }
                //echo "min:$min  max:$max"."<br>";
            }
            
            foreach ($projectDeatails['to'] as $tovalue) {
                if(date('Y-m-d H:i:s',  strtotime($tovalue)) > $max ) {
                    $max =  date('Y-m-d H:i:s',  strtotime($tovalue)) ;
                }
                //echo "min:$min  max:$max"."<br>";
            } 
            //echo "<pre>";print_r($projectDeatails['fromto']);exit;
            
            $finalArray[$projectId]['from'] = date('d-m-Y',  strtotime($min));
            $finalArray[$projectId]['to'] = date('d-m-Y',  strtotime($max));
           // $finalArray[$projectId]['total_days'] = round(abs(strtotime($max)-strtotime($min))/86400).' days';
            $total_days += round(abs(strtotime($max)-strtotime($min))/86400);
            $finalArray[$projectId]['total_timespent'] =  floor($projectDeatails['total_timespent'] / 3600).( floor(($projectDeatails['total_timespent'] / 60) % 60)==0 ?'':':'.sprintf("%02d",floor(($projectDeatails['total_timespent'] / 60) % 60) )). ' hrs';
	    $finalArray[$projectId]['timeoriginalestimate'] =  floor($projectDeatails['timeoriginalestimate'] / 3600).( floor(($projectDeatails['timeoriginalestimate'] / 60) % 60)==0 ?'':':'.sprintf("%02d",floor(($projectDeatails['timeoriginalestimate'] / 60) % 60) )). ' hrs';
            $total_time_Spent +=  floor($projectDeatails['total_timespent'] / 3600);
            
        }
        
        //total result
        //$finalArray[0]['pname']=' ';
        //$finalArray[0]['from']=' ';
        //$finalArray[0]['to']=' ';
        //$finalArray[0]['total_days']= "Total : ".$total_days.' days';
        //$finalArray[0]['total_timespent']=' ';
        
    /******************* Chart Data ************************/
        
        
        $sqlChart7 = $sqlQueries['user-time-spent-cat-bug']['query'];

        
        $sqlBaarChart = sprintf(
                        $sqlChart7
                        ,$_REQUEST['username']
                        ,date("Y-m-d",strtotime($_REQUEST['from']))
                        ,date("Y-m-d",strtotime($_REQUEST['to']))
                        
                    );
    
    #echo $sqlBaarChart;exit;
    $retDataBaarChart = pg_query($db, $sqlBaarChart);
    
    $rowretDataBaarChart = pg_fetch_object($retDataBaarChart);
    
    #print_r($rowretDataBaarChart);exit;
    
    $filterFromTo = '';
    if(isset($_REQUEST['from'] ,$_REQUEST['to'])){
        $filterFromTo = '&from='.$_REQUEST['from'].'&to='.$_REQUEST['to'];
    }elseif ($action == 'total-time-spent-assignee-last-month') {
        $filterFromTo = '&from='.date('M d,Y',  strtotime(date('Y-m-d').'-31 days')).'&to='.date('M d,Y');    
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
        
        
        <!-- fullCalendar -->
        <link href="../../css/fullcalendar.css" rel="stylesheet" type="text/css" />
        <link href="../../css/fullcalendar.print.css" rel="stylesheet" type="text/css" media='print' />
        
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
            .box .todo-list > li{ list-style: outside none none;}
            .box .todo-list > li .text{display: inline;}
            .box .todo-list > li .label{ color: #444;font-size: 12px;background-color: #F3F4F5;}
            .box .todo-list > li > input[type="checkbox"]{margin: 0 2px;}
            .fc-event-time{display: none;}
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
        <style>
            .box .todo-list .handle{display: none;}
        </style>
        <script src="../../js/jquery.min.js"></script>
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
                        <li><a href="<?php echo $_SESSION['highest_role'] ==1 ?'./../../index.php':'#' ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                        <li><a href="<?php echo $_SESSION['highest_role'] ==1 ?'./../../pages/tables/top-menudata.php?view=total-time-spent-assignee-last-month'.$filterFromTo:'#' ?>"><i class="fa fa-table"></i> User Worklogs</a></li>
                        <li class="active">User Time Spent</li>
                    </ol>
                </section>
                <section class="content-header">
                    <div class="box-title" style="font-size: 20px; display: inline-block; width: 65%;">
                        <?php echo "User Worklogs - ".$_REQUEST['username']; ?>
                        <img width="" title="<?php echo $_REQUEST['username']; ?>" src="<?php echo $GLOBALS['jira_url']."/secure/useravatar?size=small&ownerId=".$_REQUEST['username'];?>"/>
                    </div>
                    
                    <div style="display: inline-block; width: 30%;">
                        <div style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;" class="pull-right" id="reportrange">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span>Feb 01,2015 - Feb 28,2015</span> <b class="caret"></b>
                        </div>
                    </div>
                </section>
                <section class="content">
                <?php if(isset($finalArray) && !empty($finalArray)){?>

                    <div class="row">

                        <div class="col-xs-12">
                            <div class="boxed" style="margin: 10px; ">
                                <!--<div style="width:33%;display: inline-block;">
                                    <div id="time-chart-all-print" style=''></div>
                                </div>-->
                                <div style="width:33%;display: inline-block;">
                                    <?php 
                                        $sqlBaarChart_sprint = $sqlQueries['user-time-spent-task-est-spent']['query'];
                                        $sqlBaarChart_sprint = sprintf(
                                                                            $sqlBaarChart_sprint,
                                                                            // bugs_est others
                                                                            $_REQUEST['username'],date('Y-m-d',strtotime($_REQUEST['from'])),date('Y-m-d',strtotime($_REQUEST['to'])),
                                                                            // bugs_est me
                                                                            $_REQUEST['username'],$_REQUEST['username'],date('Y-m-d',strtotime($_REQUEST['from'])),date('Y-m-d',strtotime($_REQUEST['to'])),
                                                                            // bugs_spent others
                                                                            $_REQUEST['username'],date('Y-m-d',strtotime($_REQUEST['from'])),date('Y-m-d',strtotime($_REQUEST['to'])),
                                                                            // bugs_spent me
                                                                            $_REQUEST['username'],$_REQUEST['username'],date('Y-m-d',strtotime($_REQUEST['from'])),date('Y-m-d',strtotime($_REQUEST['to'])),
                                                                            // others_est others
                                                                            $_REQUEST['username'],date('Y-m-d',strtotime($_REQUEST['from'])),date('Y-m-d',strtotime($_REQUEST['to'])),
                                                                            // others_est me
                                                                            $_REQUEST['username'],$_REQUEST['username'],date('Y-m-d',strtotime($_REQUEST['from'])),date('Y-m-d',strtotime($_REQUEST['to'])),
                                                                            // others_spent others
                                                                            $_REQUEST['username'],date('Y-m-d',strtotime($_REQUEST['from'])),date('Y-m-d',strtotime($_REQUEST['to'])),
                                                                            // others_spent me
                                                                            $_REQUEST['username'],$_REQUEST['username'],date('Y-m-d',strtotime($_REQUEST['from'])),date('Y-m-d',strtotime($_REQUEST['to']))
                                                                        );
                                        $retDataBaarChart_sprint = pg_query($db, $sqlBaarChart_sprint);
                                        
                                    ?>
       
                                    <div id="container8" style=''></div>                             <?php /*
                                    <table id="datatable8" style="display: none;">
                                        <thead>
                                            <tr>
                                            <th></th>
                                            <th>Estimate</th>
                                            <th>Spent</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                while($rowProject_sprint = pg_fetch_object($retDataBaarChart_sprint)){ 
                                                    print_r($rowProject_sprint);exit;
                                                    foreach(array('bugs'=>'bugs','tasks'=>'others') as $ktype => $type){
                                                        $varest = $type.'_est';
                                                        $varspent = $type.'_spent';
                                                        $colr = $type =='bugs'?'Bug':'Task';
                                            ?>
                                            <tr>
                                                <th><?php echo ucfirst($ktype); ?></th>
                                                <td><?php echo $rowProject_sprint->$varest !==null ? (float) str_replace(':', '.', $rowProject_sprint->$varest):0; ?></td>
                                                <td><?php echo $rowProject_sprint->$varspent !== null ? (float) str_replace(':', '.', $rowProject_sprint->$varspent):0; ?></td>
                                            </tr>
                                            <?php   }
                                                }?>
                                        </tbody>
                                    </table>
                                     */ ?>
                                </div>
                                <div style="width:33%;;display: inline-block;">
                                    <div id="container7" style=""></div>
                                </div>
                                <div style="width: 33%; display: inline-block; overflow-y: auto; max-height: 400px;">
                                    <div class="box" style="min-height: 400px; margin: 0px auto;">
                                        <div class="box-header">

                                        </div><!-- /.box-header -->

                                        <div class="box-body table-responsive">
                                            <table id="example5" style="text-align:center;" class="table table-bordered table-striped">
                                                <thead style="color:#f57304;">
                                                    <tr>
                                                        <!--<th><?php #echo "Assignee"; ?></th>-->
                                                    <?php foreach($columns as $keycol => $columnret) { ?>
                                                        <th style="text-align: center;"><?php echo $columnret; ?></th>
                                                     <?php } ?>   
                                                        <th style="text-align: center;">Estimate Remaining</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                <?php 

                                                    $i=0;
                                                    foreach($finalArray as $projectID => $retData){ $i++;
                                                    #print_r($retData);exit;
                                                ?>

                                                        <tr class="<?php echo $retData['pid'];?>">
                                                            <?php /*if($i == 1){ ?>
                                                            <td><?php echo $_REQUEST['username']; ?></td>
                                                            <?php }else{ ?>
                                                                <td></td>
                                                            <?php }*/?>

                                                            <?php foreach($columns as $keycol => $retcolumns) { ?>
                                                                <td>
                                                                    <?php if($keycol == 'pname'){ ?>
                                                                    <a href="./../../pages/tables/data-project-assignee-task.php?pid=<?php echo $retData['pid'];?>&uid=<?php echo $_REQUEST['username'].$filterFromTo; ?>">
                                                                    <?php } ?>

                                                                        <?php echo $retData[$keycol] === null || $retData[$keycol] == '' ?"-": $retData[$keycol] ;  ?>

                                                                    <?php if($keycol == 'pname'){ ?>
                                                                    </a>
                                                                    <?php } ?>
                                                                </td>
                                                            <?php } ?>
                                                                <td><?php echo isset($timeremainData[$retData['pid']]) ? $timeremainData[$retData['pid']]." hrs" :"00:00 hrs";?></td>
                                                        </tr>

                                                <?php }?>

                                                </tbody>

                                            </table>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                    <div id="container12" style="clear: both;min-width: 310px; height: 400px; margin: 0 auto"></div>
                    
                    <div style="margin-top: 1%;">
                        <div class="col-md-8">
                            <div class="box box-primary">
                                <div class="box-body no-padding">
                                    <!-- THE CALENDAR -->
                                    <div id="calendar"></div>
                                </div><!-- /.box-body -->
                            </div><!-- /. box -->
                        </div>
                        <div>
                            <div class="fix0 connectedSortable ui-sortable" style="display: inline-block; margin: 10px; width: 30%;">
                                <?php 
                                    $gsql = "select username,g.id as id,g.status as status,g.goal as goal,g.added_at as added_at from tbl_goals g,tbl_users u where type='2' and archived='0' and referenceid='".$_REQUEST['username']."' group by g.id order by g.added_at asc";
                                    $gretData = mysql_query($gsql);

                                ?>
                                <!-- TO DO List -->
                                <div class="box box-primary" style="">
                                    <div class="box-header">
                                        <i class="ion ion-clipboard"></i>
                                        <h3 class="box-title">Goals</h3>

                                    </div><!-- /.box-header -->
                                    <div class="box-body goals-boxs" style="max-height: 250px; overflow-x: auto;">
                                        <ul class="todo-list goals-b">
                                            <?php while($GoalV = mysql_fetch_object($gretData)) { ?>

                                            <li class="li-data-<?php echo $GoalV->id; ?>" data-id="<?php echo $GoalV->id; ?>">
                                                <!-- drag handle -->
                                                <span class="handle">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </span>
                                                <!-- checkbox -->
                                                <input type="checkbox" <?php echo $GoalV->status ==1?"checked":""; ?> class="goal-list" value="<?php echo $GoalV->id; ?>" name=""/>
                                                <!-- todo text -->
                                                <span class="text"><?php echo $GoalV->goal; ?></span>
                                                <br>
                                                <!-- Emphasis label -->
                                                <small class="label label-primary"><i class="fa fa-clock-o"></i> <?php echo ucfirst($GoalV->username)." ".date('d/m/Y H:i',  strtotime($GoalV->added_at));?></small>
                                                <!-- General tools such as edit or delete-->
                                                <?php if(isset($_SESSION['highest_role']) && $_SESSION['highest_role'] != 5){?>
                                                <div class="tools">
                                                    <i class="fa fa-edit"></i>
                                                    <i class="fa fa-trash-o"></i>
                                                </div>
                                                <?php } ?>
                                            </li>

                                            <?php } ?>
                                        </ul>
                                    </div><!-- /.box-body -->
                                    <div class="box-footer clearfix no-border">
                                        <div class="col-sm-6">
                                            <div class="clearfix">
                                                <span class="pull-left">Progress :</span>
                                                <small class="pull-right percTile">70%</small>
                                            </div>

                                            <div class="progress xs">
                                                <div style="width: 70%;" class="percTile progress-bar progress-bar-green"></div>
                                            </div>
                                        </div>
                                        <div class="pull-right">
                                            <?php if(isset($_SESSION['highest_role']) && $_SESSION['highest_role'] != 5){?>
                                            <button class="modelPop btn btn-default"><i class="fa fa-plus"></i> Add Goal</button>
                                            <button class="archive-goals btn btn-default"><i class="fa fa-plus"></i> Archive Goal(s)</button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div><!-- /.box -->

                            </div>


                            <div class="fix0 connectedSortable ui-sortable" style="display: inline-block; margin: 10px; float: none; width: 30%;">
                                <?php 
                                    $csql = "select c.added_at as added_at,username,message from tbl_comments c,tbl_users u where c.added_by =u.id and c.type='2' and c.reference_id='".$_REQUEST['username']."' order by c.added_at asc";
                                    $cretData = mysql_query($csql);

                                ?>
                                <div class="box box-success" style="">
                                    <div class="box-header">
                                        <i class="fa fa-comments-o"></i>
                                        <h3 class="box-title">Comment</h3>

                                    </div>
                                    <div class="box-body chat" id="chat-box" style="overflow-x: auto;max-height: 250px;">
                                        <?php while($commentV = mysql_fetch_object($cretData)) { ?>
                                        <!-- chat item -->
                                        <div class="item">
                                            <img src="<?php echo HOST_NAME; ?>img/avatar.png" alt="user image" class="online"/>
                                            <p class="message">
                                                <a href="javascript:void(0);" class="name">
                                                    <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> <?php echo date('d/m/Y H:i',strtotime($commentV->added_at)); ?></small>
                                                    <?php echo ucfirst($commentV->username); ?>
                                                </a>
                                                <?php echo $commentV->message; ?>
                                            </p>

                                        </div><!-- /.item -->
                                        <?php } ?>
                                    </div><!-- /.chat -->
                                    <div class="box-footer">
                                        <div class="input-group" style="width:85%;">
                                            <?php if(isset($_SESSION['highest_role']) && $_SESSION['highest_role'] != 5){?>
                                            <form action="manage-goals.php?action=5">
                                                <textarea rows="3" style="height: 83px; padding: 4px; margin: 0px 5px;" class="form-control" id="comment-id-txt" placeholder="Type message..."/></textarea>
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn-comment btn btn-success"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </form>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div><!-- /.box (chat box) -->                                                        

                            </div>


                            <div id="arch-goal" class="fix0 connectedSortable ui-sortable" style="display: inline-block; width: 30%; float: left;margin: 10px;">
                                <?php 
                                    $gsql = "select username,g.id as id,g.status as status,g.goal as goal,g.added_at as added_at,archived_date from tbl_goals g,tbl_users u where type='2' and archived='1' and referenceid='".$_REQUEST['username']."' group by g.id order by g.archived_date asc";
                                    $gretData = mysql_query($gsql);

                                ?>
                                <!-- TO DO List -->
                                <div class="box box-primary" style="">
                                    <div class="box-header">
                                        <i class="ion ion-clipboard"></i>
                                        <h3 class="box-title">Archived Goals</h3>

                                    </div><!-- /.box-header -->
                                    <div class="box-body goal-boxs-archive" style="max-height: 250px; overflow-x: auto;">
                                        <ul class="todo-list">
                                            <?php 

                                                $olddate = '';
                                                $i=0;
                                                $num_rows = mysql_num_rows($gretData);

                                                while($GoalV = mysql_fetch_object($gretData)) { 
                                                    $i++;
                                            ?>

                                                <?php 

                                                    if($olddate != date('dmYhiA',  strtotime($GoalV->archived_date)) ){
                                                        if(isset($total, $checked) && $total!=0 && $i!=1){
                                                            $percent = round((($checked/$total)*100),2)."%";

                                                ?>
                                            <script> $('.dropdown-header.<?php echo $olddate; ?> span').html('<?php echo $percent; ?>');</script>
                                                 <?php } //error_log("$GoalV->archived_date:$olddate"); ?>
                                                    <li class="dropdown-header <?php echo date('dmYhiA',  strtotime($GoalV->archived_date)); ?>" style="margin-left: 0px; font-size: 16px; font-weight: bold;"> <?php echo date('d/m/Y h:i A',  strtotime($GoalV->archived_date)); ?> <span style="padding-left: 25%;"></span></li>
                                                <?php $total = 0; $checked=0;?>
                                                <?php } 

                                                    $olddate = date('dmYhiA',  strtotime($GoalV->archived_date)); 

                                                ?>

                                                <li class="li-data-<?php echo $GoalV->id; ?> <?php echo date('dmYhiA',  strtotime($GoalV->archived_date)); ?>" data-id="<?php echo $GoalV->id; ?>">
                                                    <i class="fa fa-angle-double-right"></i>
                                                    <!-- checkbox -->
                                                    <input type="checkbox" disabled <?php if($GoalV->status ==1){ echo "checked";$checked++;} $total++; ?> class="goal-list-archived" value="<?php echo $GoalV->id; ?>" name=""/>
                                                    <!-- todo text -->
                                                    <span class="text"><?php echo $GoalV->goal; ?></span>
                                                    <br>
                                                    <!-- Emphasis label -->
                                                    <small class="label label-primary"><i class="fa fa-clock-o"></i> <?php echo ucfirst($GoalV->username)." ".date('d/m/Y H:i',  strtotime($GoalV->added_at));?></small>
                                                    <!-- General tools such as edit or delete-->
                                                    <!--div class="tools">
                                                        <i class="fa fa-edit"></i>
                                                        <i class="fa fa-trash-o"></i>
                                                    </div-->
                                                </li>
                                                <?php if(isset($total, $checked) && $total!=0 && $i==$num_rows){
                                                            $percent = round((($checked/$total)*100),2)."%";

                                                ?>
                                                <script> $('.dropdown-header.<?php echo $olddate; ?> span').html('<?php echo $percent; ?>');</script>
                                                <?php } ?>
                                            <?php } ?>
                                        </ul>
                                    </div><!-- /.box-body -->
                                    <div class="box-footer clearfix no-border">
                                        <div class="col-sm-6">
                                            <div class="clearfix">
                                                <span class="pull-left">Progress :</span>
                                                <small class="pull-right percTileArch">0%</small>
                                            </div>

                                            <div class="progress xs">
                                                <div style="width: 0%;" class="percTileArch progress-bar progress-bar-green"></div>
                                            </div>
                                            <script>
                                                var sum=0;
                                                count =0;
                                                $('.todo-list .dropdown-header span').each(function(){
                                                  sum += parseInt($(this).html().replace('%',''));
                                                  count++;
                                                });

                                                if(count !== 0){
                                                    percentT = (sum/(count*100)) * 100;
                                                    if(isNaN(percentT)){ percentT = "0%";}
                                                    $('.percTileArch.progress-bar').css('width',percentT.toFixed(2).replace('.00','')+"%");
                                                    $('.pull-right.percTileArch').html(percentT.toFixed(2).replace('.00','')+"%");
                                                }
                                            </script>
                                        </div>
                                        <!--button class="modelPop btn btn-default pull-right"><i class="fa fa-plus"></i> Add Goal</button-->
                                    </div>
                                </div><!-- /.box -->

                            </div>
                        </div>
                    </div>
                    
                <?php }else{?>
                    <div style="text-align: center; font-size: 30px; margin: 0px auto; padding: 100px;">No Worklog Submitted! </div>
                <?php } ?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        <?php if(isset($finalArray) && !empty($finalArray)){?>
        <!-- COMPOSE MESSAGE MODAL -->
        <div class="modal fade" id="compose-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" style="display: block; padding-top: 18%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"><i class="fa  fa-angle-right"></i> Goal </h4>
                    </div>
                    <form action="#" method="post" action="manage-goals.php">
                        <div class="modal-body">
                            <div class="form-group">
                                <div class="input-group">
                                    <input name="goal" id="goal-text" class="form-control input-lg" placeholder="Sprint Goal">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer clearfix">
                            <input type="hidden" name="action" id="actionP" value="1">
                            <input type="hidden" name="g-id" id ="g-id">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>

                            <button type="button" class="btn btn-primary pull-left cuPGoal"><i class="fa  fa-angle-right"></i> <span class="opt-r">Save</span> </button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <?php } ?>
        
        <script src="../../js/bootstrap.min.js" type="text/javascript"></script>
        <!-- DATA TABES SCRIPT -->
        <script src="../../js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="../../js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <!--<script src="../../js/AdminLTE/app.js" type="text/javascript"></script>-->
        <!-- AdminLTE for demo purposes -->
        <script src="../../js/AdminLTE/demo.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui.min.js" type="text/javascript"></script>
        <!-- page script -->
         
        <script type="text/javascript">
             
            $(function() {
                
                /*classes ='';
                old_classes='';
                total = 0;
                checked = 0;
                lastClass = $('.goal-boxs-archive ul.todo-list li:last').attr('class');

                $('.goal-boxs-archive ul.todo-list li').not('li.dropdown-header').each(function(){

                  classes = $(this).attr('class').split(/\s+/);
                  console.log(classes);

                  if(classes[1] != old_classes || old_classes !='' || classes[0] == lastClass[0]){
                    percent = (checked/total)*100;
                    if(isNaN(percent)){ percent =0; }

                    console.log(old_classes);
                    $('.dropdown-header.'+old_classes).html(percent);

                    total = 0;
                    checked = 0;
                    if($(this).find('input.goal-list').is(':checked')){
                      checked++;
                    }
                    total++;
                  }else{

                    if($(this).find('input.goal-list').is(':checked')){
                      checked++;
                    }
                    total++;

                  }

                  old_classes = classes[1];
                });*/

                function generatePercentile(){

                    total = 0;
                    checked = 0;
                    $('.goals-boxs ul.todo-list li').each(function(){
                        if($(this).find('input.goal-list').is(':checked')){
                            checked++;
                        }
                        total++;
                    });
                    percent = (checked/total)*100;
                    if(isNaN(percent)){ percent =0; }
                    
                    $('.pull-right.percTile').html(percent.toFixed(2).replace('.00','')+"%");
                    $('.percTile.progress-bar').css('width',percent.toFixed(2).replace('.00','')+"%");
                    
                    //console.log($('.goals-boxs').height()+$('#chat-box').height());
                    if($('.goals-boxs').height()+$('#chat-box').height() < 424 ){
                        $('#arch-goal').css('margin','10px');
                    }else{
                        $('#arch-goal').css('margin','-16% 2%');
                    }
                }
                generatePercentile();
                
                $(document.body).on('click','.modelPop',function(){

                    $('#goal-text').val('');
                    $('#actionP').val('1');
                    $('.opt-r').html('Create');

                    $( "#compose-modal" ).modal('show');
                });
                
                $(document.body).on('click','.archive-goals',function(){
                    r = confirm('Want to Archive all Goals ?');
                    if(r){
                        var str='';
                        $('div.goals-boxs li').each(function(){
                            str += $(this).data('id')+',';
                        });
                        
                        ids = str.substring(0,str.length -1);
                        
                        $.ajax({
                            url:'manage-goals.php',
                            type:'POST',
                            data:'action=6&gid='+ids,
                            success:function(){
                                $('div.goals-boxs li').each(function(){
                                    $(this).remove();
                                });
                                generatePercentile();
                                location.reload();
                            } 
                        });
                    }
                });
                
                $(document.body).on('click','.cuPGoal',function(){
                    if($.trim($('#goal-text').val()) != ''){
                        $.ajax({
                            url:'manage-goals.php',
                            type:'POST',
                            data:'action='+$('#actionP').val()+'&uid='+'<?php echo $_REQUEST['username']; ?>'+'&goal='+$('#goal-text').val()+'&gid='+$('#g-id').val(),
                            success:function(response){

                                data = $.parseJSON(response);

                                $( "#compose-modal" ).modal('hide');

                                //location.reload();
                                $('.todo-list.goals-b').append(data.html);

                                if($('#actionP').val() == 2){
                                    console.log($('#g-id').val()+"  "+$('#goal-text').val());
                                    $('.li-data-'+$('#g-id').val()).find('span.text').html($('#goal-text').val());
                                }

                                $('#goal-text').val('');
                                $('.goals-boxs').animate({ scrollTop: 10000 }, 1000);
                                
                                generatePercentile();
                            } 
                        });
                    }else{
                        $('#goal-text').css('border','1px dotted #ff0000');
                    }
                });

                $(document.body).on('change','.goal-list',function(){
                    if($(this).is(':checked')){
                        status = 1;
                    }else{
                        status = 2;
                    }

                    $.ajax({
                        url:'manage-goals.php',
                        type:'POST',
                        data:'action=3&gid='+$(this).val()+'&status='+status,
                        success:function(){
                            generatePercentile();
                        } 
                    });
                });

                $(document.body).on('click','.fa-edit,.fa-trash-o',function(){

                    id = $(this).closest('li').data('id');
                    text = $(this).closest('li').find('span.text').html();
                    li= $(this).closest('li');

                    if($(this).hasClass('fa-trash-o')){
                        r = confirm('Want to Delete Goal : "'+text+'"');
                        if(r){
                            $.ajax({
                                url:'manage-goals.php',
                                type:'POST',
                                data:'action=4&gid='+id,
                                success:function(){
                                    li.remove();
                                } 
                            });
                        }
                    }else if($(this).hasClass('fa-edit')){
                        $('#goal-text').val(text);
                        $('#actionP').val('2');
                        $('#g-id').val(id);
                        $('.opt-r').html('Update');

                        $( "#compose-modal" ).modal('show');

                    }
                    generatePercentile();
                });

                $(document.body).on('click','.btn-comment',function(){
                    if($.trim( $('#comment-id-txt').val()) != ''){
                        $.ajax({
                            url:'manage-goals.php',
                            type:'POST',
                            data:'action=5'+'&uid='+'<?php echo $_REQUEST['username']; ?>'+'&message='+$('#comment-id-txt').val(),
                            success:function(response){

                                data = $.parseJSON(response);
                                $('#chat-box').append(data.html);
                                $('#comment-id-txt').val('');
                                $('#chat-box').animate({ scrollTop: 10000 }, 1000);
                            } 
                        });
                    }else{

                        $('#comment-id-txt').css('border','1px dotted #ff0000');
                    }
                });

                $('#chat-box').animate({ scrollTop: 10000 }, 1000);
                $('.goals-boxs').animate({ scrollTop: 10000 }, 1000);
                
                
                
                
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
                }
                
                datatopbarupdate();	

                var refInterval = setInterval(function() {
                              datatopbarupdate();
                        }, 30000); // 30 seconds
                                
                                
                view = "<?php echo $_REQUEST['view']; ?>";
                
                if( view == 'task-in-progess' ) {
                    $('#example1').dataTable({
                        "aaSorting": [[0,'desc']],
                        "iDisplayLength": 25
                    });
                }else if( view == 'active-projects' ) {
                    $('#example1').dataTable({
                        "aaSorting": [[2,'desc']],
                        "iDisplayLength": 25
                    });
                }else if( view == 'active-users' ) {
                    $('#example1').dataTable({
                        "iDisplayLength": 50
                    });
                }else if( view == 'task-no-duedate' ) {
                    $('#example1').dataTable({
                        "aaSorting": [[4,'asc']],
                        "iDisplayLength": 50
                    });
                } else {
                    $('#example1').dataTable({"aaSorting": [[0,'desc']],"iDisplayLength": 500});
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
                    redirectUrl = "./../../php-excel/topmenuexportxls.php?view="+view;
                    
                    if(<?php echo isset($_REQUEST['from'], $_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to']) ? 1:0; ?>){
                        redirectUrl += '&from='+'<?php echo isset($_REQUEST['from'])&& !empty($_REQUEST['from'])?$_REQUEST['from']:'0'; ?>'+'&to='+'<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])?$_REQUEST['to']:'0'; ?>';
                    }
		    			
                    window.location = redirectUrl;
		});

		//On Change Of Project Dropdown
		$('#project-select').change(function(){
			//alert($('#project-select').val());
			window.location = "./../../pages/tables/data.php?view="+view+"&id="+$('#project-select').val();
			///jiraadmin/pages/tables/data.php?view=<?php echo $action; ?>&id=
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
        
        <script src="../../js/highcharts.js"></script>
        <script src="../../js/modules/exporting.js"></script>
        
        <script src="../../js/fullcalendar.min.js"></script>
        
        <script type="text/javascript">
                $(document).ready(function() {

                   var cb = function(start, end, label) {
                     console.log(start.toISOString(), end.toISOString(), label);
                     $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                     //alert("Callback has fired: [" + start.format('MMMM D, YYYY') + " to " + end.format('MMMM D, YYYY') + ", label = " + label + "]");
                   };

                   fromDate = '<?php echo isset($_REQUEST['from']) && !empty($_REQUEST['from'])? date('m/d/Y',strtotime($_REQUEST['from'])):''; ?>';
                   startDatePic = typeof(fromDate) == "undefined" || fromDate==''? moment().subtract(3, 'years'): fromDate;
                   endDate = '<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])? date('m/d/Y',strtotime($_REQUEST['to'])):''; ?>';
                   endDatePic = typeof(endDate) == "undefined" || endDate=='' ? moment(): endDate;

                   
                   if(<?php echo $action =='total-time-spent-assignee-last-month' && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0 ?>){
                       startDatePic = moment().subtract(31, 'days');//.format('MMMM D, YYYY');
                   } else if(<?php echo $action =='active-projects' && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0; ?>){
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
                        startDatePic = typeof(fromDate) == "undefined" || fromDate =='' ? moment().subtract(3, 'years').format('MMMM D, YYYY'): fromDate;
                        endDate = '<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])? date('M d,Y',  strtotime($_REQUEST['to'])):''; ?>';
                        endDatePic = typeof(endDate) == "undefined" || endDate =='' ? moment().format('MMMM D, YYYY'): endDate;
                        
                        if(<?php echo $action =='total-time-spent-assignee-last-month' && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0 ?>){
                            startDatePic = moment().subtract(31, 'days').format('MMMM D, YYYY');
                        }else if(<?php echo $action =='active-projects' && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0; ?>){
                            startDatePic = moment().subtract(7, 'days').format('MMMM D, YYYY');
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

                            window.location= "./../../pages/tables/user-time-spent.php?username=<?php echo $_REQUEST['username'] ?>&id=<?php echo $_REQUEST['id'] ?>&from="+picker.startDate.format('MMMM D, YYYY')+"&to="+picker.endDate.format('MMMM D, YYYY');

                        });
                        
                        Highcharts.visualize = function(table, options) {
                            // the categories
                            options.xAxis.categories = [];
                            $('tbody th', table).each( function(i) {
                                options.xAxis.categories.push(this.innerHTML);
                            });
                            // the data series
                            options.series = [];
                            $('tr', table).each( function(i) {
                                var tr = this;
                                $('th, td', tr).each( function(j) {
                                    if (j > 0) { // skip first column
                                        if (i == 0) { // get the name and init the series
                                            options.series[j - 1] = {
                                                name: this.innerHTML,
                                                data: []
                                            };
                                        } else { // add values
                                            options.series[j - 1].data.push(parseFloat(this.innerHTML));
                                        }
                                    }
                                });
                            });
                            var chart = new Highcharts.Chart(options);
                        }

                        Highcharts.theme = {
                                colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],


                                xAxis: {
                                   gridLineWidth: 1,
                                   lineColor: '#000',
                                   tickColor: '#000',
                                   labels: {
                                      style: {
                                         color: '#000',
                                         font: '11px Trebuchet MS, Verdana, sans-serif'
                                      }
                                   },
                                   title: {
                                      style: {
                                         color: '#333',
                                         fontWeight: 'bold',
                                         fontSize: '12px',
                                         fontFamily: 'Trebuchet MS, Verdana, sans-serif'

                                      }
                                   }
                                },
                                yAxis: {
                                   minorTickInterval: 'auto',
                                   lineColor: '#000',
                                   lineWidth: 1,
                                   tickWidth: 1,
                                   tickColor: '#000',
                                   labels: {
                                      style: {
                                         color: '#000',
                                         font: '11px Trebuchet MS, Verdana, sans-serif'
                                      }
                                   },
                                   title: {
                                      style: {
                                         color: '#333',
                                         fontWeight: 'bold',
                                         fontSize: '12px',
                                         fontFamily: 'Trebuchet MS, Verdana, sans-serif'
                                      }
                                   }
                                },
                                legend: {
                                   itemStyle: {
                                      font: '9pt Trebuchet MS, Verdana, sans-serif',
                                      color: 'black'

                                   },
                                   itemHoverStyle: {
                                      color: '#039'
                                   },
                                   itemHiddenStyle: {
                                      color: 'gray'
                                   }
                                },
                                labels: {
                                   style: {
                                      color: '#99b'
                                   }
                                },

                                navigation: {
                                   buttonOptions: {
                                      theme: {
                                         stroke: '#CCCCCC'
                                      }
                                   }
                                }
                             };

                             // Apply the theme
                             var highchartsOptions = Highcharts.setOptions(Highcharts.theme);
                             
                            /***************** 1st chart ***************/ 
                            <?php /*                             
                                var datasets1= [];
                                <?php 
                                    
                                    $sqlBaarChart_sprint = $sqlQueries['user-time-spent-est-spent']['query'];
                                    $sqlBaarChart_sprint = sprintf(
                                                                            $sqlBaarChart_sprint
                                                                            ,$_REQUEST['username'],date('Y-m-d',  strtotime($_REQUEST['from'])),date('Y-m-d',  strtotime($_REQUEST['to']))
                                                                            ,$_REQUEST['username'],date('Y-m-d',  strtotime($_REQUEST['from'])),date('Y-m-d',  strtotime($_REQUEST['to']))
                                                                        );
                                        $retDataBaarChart_sprint = pg_query($db, $sqlBaarChart_sprint);

                                        $data = '[';
                                        while($rowProject_sprint = pg_fetch_object($retDataBaarChart_sprint)){ 

                                            if($rowProject_sprint->estimate == null && $rowProject_sprint->spent== null){
                                                continue;                            
                                            }
                                ?>
                                            obj = {};
                                            obj['name'] = '<?php echo $rowProject_sprint->pname; ?>';
                                            obj['color'] = '<?php echo $IssueTypeColors[$rowProject_sprint->pname]; ?>';
                                            obj['data'] = [<?php echo (float) ($rowProject_sprint->estimate==null || $rowProject_sprint->estimate=='00:00' ?0:str_replace(':', '.', $rowProject_sprint->estimate)); ?>,<?php echo (float) ($rowProject_sprint->spent==null || $rowProject_sprint->spent=='00:00' ?0:str_replace(':', '.', $rowProject_sprint->spent)); ?>];
                                            datasets1.push(obj);


                                        <?php } ?>
                                
                                $('#time-chart-all-print').highcharts({
                                        chart: {
                                            type: 'column'
                                        },
                                        title: {
                                            text: 'User Time Spent'
                                        },
                                        xAxis: {
                                            categories: ['Estimate', 'Spent']
                                        },
                                        yAxis: {
                                            min: 0,
                                            title: {
                                                text: 'Total Time (Hrs)'
                                            },
                                            stackLabels: {
                                                enabled: true,
                                                style: {

                                                    fontWeight: 'bold',
                                                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                                                }
                                            }
                                        },

                                        tooltip: {
                                            formatter: function () {
                                                return '<b>' + this.x + '</b><br/>' +
                                                    this.series.name + ': ' + this.y + '<br/>' +
                                                    'Total: ' + this.point.stackTotal;
                                            }
                                        },
                                        plotOptions: {
                                            column: {
                                                stacking: 'normal',
                                                dataLabels: {
                                                    enabled: true,
                                                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'black',
                                                    style: {
                                                        //textShadow: '0 0 3px black'
                                                        fontweight:'bold'
                                                    }
                                                }
                                            }
                                        },
                                        series: datasets1,
                                    });
                             
                             <?php */ ?>
                        /***************** 2nd Chart **************************/
                        
                        /*var table = document.getElementById('datatable8'),
                            options = {
                                chart: {
                                    renderTo: 'container8',
                                    defaultSeriesType: 'column'
                                },
                                title: {
                                    text: 'User Worklog'
                                },
                                xAxis: {
                                },
                                yAxis: {
                                    min:0,
                                    allowDecimals: true,
                                    title: {
                                        text: 'Hrs'
                                    }
                                },
                                tooltip: {
                                    formatter: function() {
                                        return '<b>'+ this.series.name +'</b><br/>'+
                                        this.y +' Hrs '+ this.x;
                                    }
                                },
                                legend:false,
                                colors:['#89afd7','#ec8e00']
                            };
                        Highcharts.visualize(table, options);*/
                        
                        var datasets10 = [];
                        var types = ['Bugs','Tasks'];
                        
                    <?php 
                        while($rowProject_sprint = pg_fetch_object($retDataBaarChart_sprint)){ 
                            #print_r($rowProject_sprint);exit;
                        ?>
                            obj = {};
                            obj['name'] = 'Assigned to Me';
                            obj['color'] = '#f57304';
                            obj['data'] = [<?php echo isset($rowProject_sprint->bugs_est_me) ? (float) $rowProject_sprint->bugs_est_me : 0;?>,<?php echo isset($rowProject_sprint->others_est_me) ? (float) $rowProject_sprint->others_est_me :0;?>];
                            obj['stack'] = 'Estimate'
                            datasets10.push(obj);

                            obj = {};
                            obj['name'] = 'Assigned to Others';
                            obj['color'] = '#ffa500';
                            obj['data'] = [<?php echo isset($rowProject_sprint->bugs_est_others) ? (float) $rowProject_sprint->bugs_est_others : 0;?>,<?php echo isset($rowProject_sprint->others_est_others) ? (float) $rowProject_sprint->others_est_others :0;?>];
                            obj['stack'] = 'Estimate'
                            datasets10.push(obj);

                            obj = {};
                            obj['name'] = 'Assigned to Me';
                            obj['color'] = '#058DC7';
                            obj['data'] = [<?php echo isset($rowProject_sprint->bugs_spent_me) ? (float) $rowProject_sprint->bugs_spent_me : 0;?>,<?php echo isset($rowProject_sprint->others_spent_me) ? (float) $rowProject_sprint->others_spent_me :0;?>];
                            obj['stack'] = 'Spent'
                            datasets10.push(obj);

                            obj = {};
                            obj['name'] = 'Assigned to Others';
                            obj['color'] = '#A2c8f0';
                            obj['data'] = [<?php echo isset($rowProject_sprint->bugs_spent_others) ? (float) $rowProject_sprint->bugs_spent_others : 0;?>,<?php echo isset($rowProject_sprint->others_spent_others) ? (float) $rowProject_sprint->others_spent_others :0;?>];
                            obj['stack'] = 'Spent'
                            datasets10.push(obj);
                    <?php 
                        }
                        ?>
                        $('#container8').highcharts({

                            chart: {
                                type: 'column',
                                inverted: false
                            },

                            title: {
                                text: 'User Worklog'
                            },

                            yAxis: {
                                allowDecimals: false,
                                min: 0,
                                title: {
                                    text: 'No. of Hrs'
                                }
                            },

                            xAxis: {
                                categories: types

                            },

                            tooltip: {
                                formatter: function () {

                                    return '<b>' + this.x + '</b><br/>' +//this.series.options.stack+' '+
                                        this.series.name + ': ' + this.y + '<br/>' +
                                        'Total '+this.series.options.stack+': ' + this.point.stackTotal;
                                }
                            },
                            legend:false,
                            plotOptions: {
                                column: {
                                    stacking: 'normal'
                                },

                            },

                            series: datasets10
                        });
                    
                        
                        /***************** 3rd Chart **************************/
                        $('#container7').highcharts({
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false
                            },
                            title: {
                                text: 'Work Breakup'
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
                                    name: 'Total',
                                    data: [
                                        ['Bug', <?php echo $rowretDataBaarChart->bug; ?>],
                                        ['Tasks', <?php echo $rowretDataBaarChart->others; ?>]

                                    ],
                                    colors:['#ff4444','#A2c8f0']
                                }]
                        });

                        /**** 4th Beelow per day/month/year Chart ****/
                        var category = [];
                        var catval = [];
                        var bugcatval = [];
                        var leaves = [];
                        var holiday = [];
                        
                        <?php 
                        
                            $fromDate = date('d-m-Y',strtotime($_REQUEST['from']));
                            $toDate = date('d-m-Y',strtotime($_REQUEST['to']));
                            $nodays=(strtotime($toDate) - strtotime($fromDate))/ (60 * 60 * 24);
                            
                            
                            if($nodays <= 30){

                                    //date format
                                    $dateFormat = "DD-MM-2YYY";
                                    $phpdateFormat = "d-m-Y";
                                    $incTime = ' +1 days';
                                    $categFormat = 'd D M';
                                    
                            }elseif($nodays <= 365){
                                
                                    //month format
                                    $dateFormat = "MM-2YYY";
                                    $phpdateFormat = "m-Y";
                                    $incTime = ' +1 month';
                                    $categFormat = 'M Y';
                                    
                            }else{

                                    //year format
                                    $dateFormat = "2YYY";
                                    $phpdateFormat = "Y";
                                    $incTime = ' +1 year';
                                    $categFormat = 'Y';
                                    
                            }
                            
                            
                            //get data from database
                            $utsQl = $sqlQueries['worklog-user-timespent-filter-graph']['query'];
                            
                            // Query substitution
                            $utsQl = sprintf($utsQl,
                                                    $dateFormat,
                                                    date('Y-m-d',strtotime($fromDate)),
                                                    date('Y-m-d',strtotime($toDate)),
                                                    $_REQUEST['username'],
                                                    $dateFormat,
                                                    $dateFormat
                                            );

                            $retDataWorklogd = pg_query($db, $utsQl);

                            $datetimeWorkedAll = array();
                            while($retDataWorklogAllv = pg_fetch_object($retDataWorklogd)){ 
                                $datetimeWorkedAll[$retDataWorklogAllv->datemonthyear] = $retDataWorklogAllv->worked;
                            }
                            
                            //bug category Query 
                            
                            $utsQ2 = $sqlQueries['worklog-user-timespent-filter-graph']['bugquery'];
                            
                            // Query substitution
                            $utsQ2 = sprintf($utsQ2,
                                                    $dateFormat,
                                                    date('Y-m-d',strtotime($fromDate)),
                                                    date('Y-m-d',strtotime($toDate)),
                                                    $_REQUEST['username'],
                                                    $dateFormat,
                                                    $dateFormat
                                            );

                            $retDataWorklogBug = pg_query($db, $utsQ2);
                            
                            
                            $datetimeWorkedBug = array();
                            while($retDataWorklogBugv = pg_fetch_object($retDataWorklogBug)){ 
                                $datetimeWorkedBug[$retDataWorklogBugv->datemonthyear] = $retDataWorklogBugv->worked;
                            }

                            //Leaves Query
			    $mapped_username = array('rajesh.e'=>'rajesh','buvaneswaran'=>'buvaneswaran.m','nagraj'=>'nagraj.h','taufique.ammanagi'=>'taufique.a');
			    $leave_username = in_array($_REQUEST['username'],array_keys($mapped_username)) ? $mapped_username[$_REQUEST['username']]: $_REQUEST['username'];

                            $gsql = "select id,username,start_date,end_date,nb_hrs from tbl_leaves where username='".$leave_username."' order by added_at asc";
                            $gretData = mysql_query($gsql);
                            $datetimeLeaves = array();
                            #print_r($datetimeWorkedAll);exit;
                            while($Leave = mysql_fetch_object($gretData)) {
                                if(date('Y-m-d',strtotime($Leave->start_date)) == date('Y-m-d',strtotime($Leave->end_date))){
                                    //same day leave
                                    $datetimeLeaves[date('d-m-Y',strtotime($Leave->start_date))] = str_replace(':', '.', $Leave->nb_hrs);
                                }else{
                                    //group leave
                                    $startData = date('Y-m-d',  strtotime($Leave->start_date));
                                    while($startData <= date('Y-m-d',strtotime($Leave->end_date))){
					if( date('N',strtotime($startData)) < 6 ){
		                                if( $startData != date('Y-m-d',strtotime($Leave->end_date)) ){
		                                    $datetimeLeaves[date('d-m-Y',strtotime($startData))] = 8;
		                                }else{
		                                    $datetimeLeaves[date('d-m-Y',strtotime($startData))] = date('H',strtotime($Leave->end_date)) <= 14 ? 8-(18-date('H',strtotime($Leave->end_date)))+1 : 8-(18-date('H',strtotime($Leave->end_date)));
		                                }
					}
                                        $startData = date('Y-m-d',  strtotime($startData.' +1 days'));
                                    }
                                }
                            }
                            //Holiday query
                            $holidaysql=mysql_query('SELECT date from tbl_holidays WHERE date BETWEEN "'.date("Y-m-d",strtotime($_REQUEST['from'])).'" AND "'.date("Y-m-d",strtotime($_REQUEST['to'])).'"');
                            $datetimeHolidays = array();
                            while($holiday = mysql_fetch_object($holidaysql)) {
                                     $datetimeHolidays[date('d-m-Y',strtotime($holiday->date))]=8;                            
                             }
                            //Graph categories and values
                            
                            for (;strtotime($fromDate) <= strtotime($toDate); ) { 

                                ?>

                                category.push('<?php echo date($categFormat,  strtotime($fromDate)); ?>');
                    <?php
                                if(isset($datetimeWorkedAll[date($phpdateFormat,  strtotime($fromDate))])){ ?>
                                    catval.push(<?php echo (float) $datetimeWorkedAll[date($phpdateFormat,  strtotime($fromDate))]; ?>);
                    <?php      
                                }else{ ?>
                                    catval.push(0);
                    <?php
                                } ?>
                                
                    <?php
                                if(isset($datetimeLeaves[date($phpdateFormat,  strtotime($fromDate))])){ ?>
                                    leaves.push(<?php echo (float) $datetimeLeaves[date($phpdateFormat,  strtotime($fromDate))]; ?>);
                    <?php      
                                }else{ ?>
                                    leaves.push(0);
                    <?php
                                } ?>
                                        
                     <?php
                                if(isset($datetimeHolidays[date($phpdateFormat,  strtotime($fromDate))])){ ?>
                                    holiday.push(<?php echo (float) $datetimeHolidays[date($phpdateFormat,  strtotime($fromDate))]; ?>);
                    <?php      
                                }else{ ?>
                                    holiday.push(0);
                    <?php
                                } ?>
                                        
                    
                    <?php
                                if(isset($datetimeWorkedBug[date($phpdateFormat,  strtotime($fromDate))])){ ?>
                                    bugcatval.push(<?php echo (float) $datetimeWorkedBug[date($phpdateFormat,  strtotime($fromDate))]; ?>);
                    <?php      
                                }else{ ?>
                                    bugcatval.push(0);
                    <?php
                                }


                                $fromDate = date('d-m-Y', strtotime($fromDate.$incTime));
                            }
                        ?>
                        
                        $('#container12').highcharts({
                            title: {
                                text: 'User Worklog Time',
                                x: -20 //center
                            },
                            
                            xAxis: {
                                categories: category
                            },
                            yAxis: {
                                title: {
                                    text: 'Number of Hrs'
                                },
                                plotLines: [{
                                    value: 0,
                                    width: 1,
                                    color: '#808080'
                                }]
                            },
                            tooltip: {
                                valueSuffix: ' Hrs'
                            },
                            
                            series: [
                                {
                                    name: '<?php echo "All Tasks"; ?>',
                                    data: catval
                                },
                                {
                                    name: '<?php echo "Bugs"; ?>',
                                    data: bugcatval,
                                    color:'#ff4444'
                                },
                                {
                                    name: '<?php echo "Leave"; ?>',
                                    data: leaves,
                                    color:'#EE82EE'
                                },
                                {
                                    name: '<?php echo "Holiday"; ?>',
                                    data: holiday,
                                    color:'#FFA500'
                                }]
                        });
                        
                   var calendarEvents = [];
                   <?php 
                   
                    $sqlCalendar = $sqlQueries['worklog-user-timespent-calendar']['query'];
                    $addsqlCaldrDateFilter = "and to_char(w.startdate::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(w.startdate::date, '2YYY-MM-DD')::DATE < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
                    $sqlCalendar = sprintf($sqlCalendar,$_REQUEST['username'],$addsqlCaldrDateFilter);
                    
                    $retDataCalendar = pg_query($db, $sqlCalendar);
                    
                    while ($retDataC = pg_fetch_object($retDataCalendar)){ ?>
                        
                        obj = {};
                        obj['title'] = "<?php echo addslashes($retDataC->summary)." - ".$retDataC->timeworked.' Hrs'; ?>";
                        obj['start'] = new Date(<?php echo date('Y',strtotime($retDataC->wstartdate)); ?>, <?php echo date('m',strtotime($retDataC->wstartdate))-1; ?>, <?php echo date('d',strtotime($retDataC->wstartdate)); ?>);
                        obj['url'] = '<?php echo $GLOBALS['jira_url']; ?>'+'/browse/'+'<?php echo $retDataC->pkey; ?>'+'-'+'<?php echo $retDataC->issuenum; ?>',
                        obj['backgroundColor'] = '#3c8dbc';
                        obj['borderColor'] = '#3c8dbc';
                        calendarEvents.push(obj);
             <?php }
                    
                    //now Leave
          			    $mapped_username = array('rajesh.e'=>'rajesh','buvaneswaran'=>'buvaneswaran.m','nagraj'=>'nagraj.h','taufique.ammanagi'=>'taufique.a');
			              $leave_username = in_array($_REQUEST['username'],array_keys($mapped_username)) ? $mapped_username[$_REQUEST['username']]: $_REQUEST['username'];
                    $gsql = "select id,username,start_date,end_date from tbl_leaves where username='".$leave_username."' and DATE(start_date) >= DATE('".date('Y-m-d',strtotime($_REQUEST['from']))."') and DATE(start_date) <= DATE('".date('Y-m-d',strtotime($_REQUEST['to']))."') order by added_at asc";
                    $gretData = mysql_query($gsql);

                    while($Leave = mysql_fetch_object($gretData)) { ?>
                        
                        obj = {};
                        obj['title'] = "<?php echo 'Leave'; ?>";
                        obj['start'] = new Date(<?php echo date('Y',strtotime($Leave->start_date)); ?>, <?php echo date('m',strtotime($Leave->start_date))-1; ?>, <?php echo date('d',strtotime($Leave->start_date)); ?>);
                        obj['end'] = new Date(<?php echo date('Y',strtotime($Leave->end_date)); ?>, <?php echo date('m',strtotime($Leave->end_date))-1; ?>, <?php echo date('d',strtotime($Leave->end_date))+1; ?>);
                        obj['backgroundColor'] = '#00c0ef';
                        obj['borderColor'] = '#00c0ef';
                        calendarEvents.push(obj);
             <?php }
                   ?>     
                    /* initialize the calendar
                    -----------------------------------------------------------------*/
                   //Date for the calendar events (dummy data)
                   var date = new Date();
                   var d = <?php echo date('d',strtotime($_REQUEST['from'])); ?>,//date.getDate(),
                       m = <?php echo date('m',strtotime($_REQUEST['from']))-1; ?>,//date.getMonth(),
                       y = <?php echo date('Y',strtotime($_REQUEST['from'])); ?>;//date.getFullYear();
                       //alert(date+'   '+d+m+y);
                   $('#calendar').fullCalendar({
                       defaultDate: new Date(y, m, d),
                       header: {
                           left: 'prev,next today',
                           center: 'title',
                           right: 'month'
                       },
                       buttonText: {
                           today: 'today',
                           month: 'month',
                           week: 'week',
                           day: 'day'
                       },
                       //Random default events
                       events: calendarEvents,
                       eventClick: function(event) {
                            if (event.url) {
                                window.open(event.url);
                                return false;
                            }
                       },
                       //editable: true,
                       //droppable: true, // this allows things to be dropped onto the calendar !!!
                       /*drop: function(date, allDay) { // this function is called when something is dropped

                           // retrieve the dropped element's stored Event Object
                           var originalEventObject = $(this).data('eventObject');

                           // we need to copy it, so that multiple events don't have a reference to the same object
                           var copiedEventObject = $.extend({}, originalEventObject);

                           // assign it the date that was reported
                           copiedEventObject.start = date;
                           copiedEventObject.allDay = allDay;
                           copiedEventObject.backgroundColor = $(this).css("background-color");
                           copiedEventObject.borderColor = $(this).css("border-color");

                           // render the event on the calendar
                           // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                           $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

                           // is the "remove after drop" checkbox checked?
                           if ($('#drop-remove').is(':checked')) {
                               // if so, remove the element from the "Draggable Events" list
                               $(this).remove();
                           }

                       }*/
                   });

                   /* ADDING EVENTS */
                   var currColor = "#f56954"; //Red by default
                   //Color chooser button
                   var colorChooser = $("#color-chooser-btn");
                   $("#color-chooser > li > a").click(function(e) {
                       e.preventDefault();
                       //Save color
                       currColor = $(this).css("color");
                       //Add color effect to button
                       colorChooser
                               .css({"background-color": currColor, "border-color": currColor})
                               .html($(this).text()+' <span class="caret"></span>');
                   });
                   $("#add-new-event").click(function(e) {
                       e.preventDefault();
                       //Get value and make sure it is not null
                       var val = $("#new-event").val();
                       if (val.length == 0) {
                           return;
                       }

                       //Create events
                       var event = $("<div />");
                       event.css({"background-color": currColor, "border-color": currColor, "color": "#fff"}).addClass("external-event");
                       event.html(val);
                       $('#external-events').prepend(event);

                       //Add draggable funtionality
                       ini_events(event);

                       //Remove event from text input
                       $("#new-event").val("");
                   });

                   
                        
               });
        </script>
        <script type="text/javascript">
            $('aside.left-side.sidebar-offcanvas').addClass('collapse-left');
            $('aside.right-side').addClass('strech');
        </script>
    </body>
</html>
