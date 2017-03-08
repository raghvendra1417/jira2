<?php
/* * ***************************************** include Config ************************************ */
include './../../config.php';
include './../../mysqlconfig.php';

$actionsData = array(
    'projects-sprint' => array(
        'cpage'=>'Project Sprint Report'
        ,'pbreadcrumb'=>array(
            'Sprint Wise Report'=>'pages/tables/sprint-all-projects.php?view=sprint-all-projects'
        )
    )
    ,'sprint-dashboard' => array(
        'cpage'=>'Sprint Dashboard'
        ,'pbreadcrumb'=>array(
            'Sprint Wise Report'=>'pages/tables/sprint-all-projects.php?view=sprint-all-projects'
            ,'Sprint Project Report'=>'pages/tables/sprint-data.php?view=projects-sprint&id=%s'
        )
    )
);

$roles = array(
        'est_developer'=>'Dev',
        'est_tester'=>'Testing',
        'est_design'=>'Design',
        'est_pm'=>'PM',
        
        's_developer'=>'Dev',
        's_tester'=>'Testing',
        's_design'=>'Design',
        's_pm'=>'PM',
        );


$filterFromTo = '';

if (array_key_exists($action, $actionsData) && $action == 'projects-sprint') {
    $ismysqlQuery = 0;
    $sql = $sqlQueries[$action]['query'];

    $sql = sprintf($sql, $_REQUEST['id'],$_REQUEST['id']);
    //echo $sql;exit;
    $retData = pg_query($db, $sql);
    $retDataP = pg_query($db, $sql);
    
    if (!$retData) {
        echo pg_last_error($db);
    }
    
    while ($rowretData = pg_fetch_object($retDataP)) { 
        $pName = $rowretData->pname;
        break;
    }

    $sqlProject = $sqlQueries['sprint-all-projects']['query'];
    $sqlProject = sprintf($sqlProject, '');
    $retProject = pg_query($db, $sqlProject);

    if (!$retProject) {
        echo pg_last_error($db);
    } else {
        //error_log( "Project Query successfully\n") ;
    }

    //Issue distribution Type Based
    $sqlProject_typebased = $sqlQueries['issue-piechart-type-based']['query'];
    $sqlProject_typebased = sprintf($sqlProject_typebased, $_REQUEST['id'],$_REQUEST['id']);
    
    $retProject_typebased = pg_query($db, $sqlProject_typebased);
    
    /*while ($rowretDataPage = pg_fetch_object($retProject_typebased)) {
        print_r($rowretDataPage);
    }exit;*/
    //Burn up chart.
    $sqlLine_chart = $sqlQueries['projects-sprint']['query_chart'];
    $sqlLine_charts = sprintf($sqlLine_chart, $_REQUEST['id'],$_REQUEST['id']);

    $retLine_chart = pg_query($db, $sqlLine_charts);
    
} elseif (array_key_exists($action, $actionsData) && $action == 'sprint-dashboard') {
    

    
    // @todo Project Id needed
    
    
    $sqlStackedBar = $sqlQueries['sprint-dashboard-gyr']['query'];
    $sqlStackedBar = sprintf($sqlStackedBar, $_REQUEST['id']);
    $retDataStackedBar = pg_query($db, $sqlStackedBar);
    
    #echo $sqlBaarChart;exit;
    $retDatapageContent = pg_query($db, $sqlStackedBar);
    
    while ($rowretDataPage = pg_fetch_object($retDatapageContent)) {
        #print_r($rowretDataPage);exit;
        $pName = $rowretDataPage->pname;
        $sprintName = $rowretDataPage->sprintname;
        
        $actionsData['sprint-dashboard']['pbreadcrumb']['Sprint Project Report'] = sprintf($actionsData['sprint-dashboard']['pbreadcrumb']['Sprint Project Report'],$rowretDataPage->project);
        break;
    }

    #echo "<pre>";print_r($dataBarChart);exit;
        
    
    
    $sqlDaysPlan = $sqlQueries['sprint-project-days-status']['query'];
    $sqlDaysPlan = sprintf($sqlDaysPlan, $_REQUEST['id']);
    $retDaysPlanData = pg_query($db, $sqlDaysPlan);
    $dataDaysPlan = pg_fetch_object($retDaysPlanData);
    
    #echo "<pre>";print_r($sqlDaysPlan);exit;
    
}
    
    //Effort Estimate
    $sqlaction = $action == 'projects-sprint'?'sprint-timespent-rolewise-all':'sprint-timespent-rolewise'; //else sprint-dashboard
    $sqlBaarChart = $sqlQueries[$sqlaction]['query'];
    
    if($action == 'sprint-dashboard'){
        $sqlBaarChart = sprintf(
                        $sqlBaarChart,
                        $_REQUEST['id'],$_REQUEST['pid']
                        ,$_REQUEST['id'],$_REQUEST['pid']
                        ,$_REQUEST['id'],$_REQUEST['pid']
                        ,$_REQUEST['id'],$_REQUEST['pid']
                        ,$_REQUEST['id'],$_REQUEST['pid']
                        ,$_REQUEST['id'],$_REQUEST['pid']
                        ,$_REQUEST['id'],$_REQUEST['pid']
                        ,$_REQUEST['id'],$_REQUEST['pid']
                        ,$_REQUEST['id'],$_REQUEST['pid']
                        ,$_REQUEST['id'],$_REQUEST['pid']
                    );
    }elseif($action == 'projects-sprint'){
        $sqlBaarChart = sprintf(
                        $sqlBaarChart,
                        $_REQUEST['id']
                        ,$_REQUEST['id']
                        ,$_REQUEST['id']
                        ,$_REQUEST['id']
                        ,$_REQUEST['id']
                        ,$_REQUEST['id']
                        ,$_REQUEST['id']
                        ,$_REQUEST['id']
                        ,$_REQUEST['id']
                        ,$_REQUEST['id']
                    );
    }
    #echo $sqlBaarChart;exit;
    $retDataBaarChart = pg_query($db, $sqlBaarChart);
    
    $rowretDataBaarChart = pg_fetch_object($retDataBaarChart);
    
    
    foreach ($rowretDataBaarChart as $role => $svalue) {

        if(!array_key_exists($role, $roles)){continue;}
        
        $operation = explode('_', $role);
        $opt = $operation[0]=='s'?'timespent':'timeest';
        
        $dataBarChart[$roles[$role]][$opt] = (float) str_replace(':', '.', $svalue);
        //$dataBC[] = $dataBarChart;
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
            .widthminmax{ width: 45%;/*min-width: 400px; max-width: 450px;*/ margin: 20px;}
            .fleft{ float: left;} 
            .fright{ float: right;}
            .wefull { width:100%;clear: both;padding: 20px 0px;}
            .wefull.ffull{ min-width: 450px; max-width: 600px; margin: 20px;}
            .box .todo-list > li{ list-style: outside none none;}
            .box .todo-list > li .text{display: inline;}
            .col-lg-5.connectedSortable.ui-sortable{ min-height: 235px;}
            //.widthminmax.fright { min-width: 450px !important;}
            .fix0{ display: inline-block;margin: 5px;width: 30%;}
            .box .todo-list > li .label{ color: #444;font-size: 12px;background-color: #F3F4F5;}
            .box .todo-list > li > input[type="checkbox"]{margin: 0 2px;}
            
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
        <style>
            .box .todo-list .handle{display: none;}
        </style>
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
                    <?php
                        if ($action == 'sprint-dashboard') {
                            echo "<h1>".$sprintName . ' - ' . $pName."</h1>";
                        } else{ ?>
                    <div style="text-align: center;">
                            <div style="width:10%;display: inline-block;">&nbsp;</div>
                            <div style="width:64%;display: inline-block;">
                                <span style="font-size: 16px;"><?php echo $actionsData[$action]['cpage'];?> <?php echo isset($projectName)?"- ".$projectName:""; ?> : </span>
                                <select id="project-select" name="id" data-placeholder="Choose a Project..." class="chosen-select" style="width:280px;float:right;" tabindex="1">
                                        <option value="">Select Project</option>
                                <?php #$action = $_REQUEST['view'];?>
                                <?php while($rowProject = pg_fetch_object($retProject)){ ?>    
                                        <option value="<?php echo $rowProject->pid; ?>" <?php echo isset($_REQUEST['id']) && $rowProject->pid == $_REQUEST['id']?"selected":""; ?>><?php echo $rowProject->pname; ?></option>
                                <?php } ?>
                                </select>
                            </div>
                            <div style="display: inline-block;width: 5%;margin-left: 15%;">
                                <button class="btn btn-success export" type="button">Export</button>
                            </div>
                            
                    </div>
                    <?php }
                        ?>
                </section>

                <!-- Main content -->

                <section class="content">
                    <?php if (array_key_exists($action, $actionsData) && $action == 'projects-sprint') : ?>
                        <div class="row">

                            <div class="col-xs-12">
                                <div class="boxed" style="margin: 10px; ">
                                    <div style="width:33%;display: inline-block;">
                                        <div id="time-chart-all-print" style=''></div>
                                    </div>
                                    <div style="width:32%;display: inline-block;">
                                        <div id="container7" style=""></div>
                                        <table id="datatable7" style="display: none;">
                                        <thead>
                                            <tr>
                                            <th></th>
                                            <th>Estimate</th>
                                            <th>Spent</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($dataBarChart as $key => $valueBC) { ?>
                                            <tr>
                                                <th><?php echo $key; ?></th>
                                                <td><?php echo $valueBC['timeest']; ?></td>
                                                <td><?php echo $valueBC['timespent']; ?></td>
                                            </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                    </div>
                                    <div style="width:33%;;display: inline-block;">
                                        <div id="container8" style=''></div>
                                    </div>
                                </div>
                                <div class="boxed" style="margin: 30px;">
                                    
                                    <div style="width:100%;;display: inline-block;">
                                        <div id="container11" ></div>
                                    </div>
                                </div>
                                <div class="box" style="min-height: 300px;">
                                    <div class="box-header">
                                        <div style="width:33%;float: left;">
                                            <h3 class="box-title" style="min-width:240px;"><?php echo isset($projectName) ? $projectName : ""; ?></h3>
                                            <?php if ($action == 'assignee-total-est-project-selection') { ?>
                                                <div style="float: left;padding: 5px 20px;"><a class="btn btn-success back" style="color:white" href="./../../pages/tables/data.php?view=active-projects&id=<?php echo $_REQUEST['pid'] . $filterFromTo; ?>"> Back</a></div>
                                            <?php } ?>
                                        </div>


                                    </div><!-- /.box-header -->

                                    <div class="box-body table-responsive">
                                        <table id="example10" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <?php foreach ($sqlQueries[$action]['columns'] as $keycol => $columns) { 
                                                        if($keycol == 'totalestimate'){
                                                                                continue;
                                                                }elseif($keycol == 'timespent'){
                                                                    
                                                        ?>
                                                    <th>Time Estimate / Spent</th>
                                                        <?php  }else{ ?>
                                                                <th><?php echo $columns; ?></th>
                                                    <?php      }
                                                           }?>   
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php
                                                    $queryObj = $ismysqlQuery == 1 ? 'mysql_fetch_object' : 'pg_fetch_object';
                                                    while ($rowretData = pg_fetch_object($retData)) { #echo "<pre>";print_r($rowretData);continue;
                                                    
                                                    ?>
                                                    <tr>
                                                        <?php
                                                            foreach ($sqlQueries[$action]['columns'] as $keycol => $columns) {
                                                                if($keycol == 'totalestimate'){
                                                                                continue;
                                                                }elseif($keycol == 'timespent'){
                                                                    $est_time = (float) str_replace(':', '.', $rowretData->totalestimate);
                                                                    $spent_time = (float) str_replace(':', '.', $rowretData->timespent);
                                                                    $total_time = $est_time + $spent_time;
                                                                    $so_perc_est = ($est_time/$total_time) * 100;
                                                                    $so_perc_spent = ($spent_time/$total_time) * 100;
                                                                    $colr = $spent_time <= $est_time ?"#51a825":"#ec8e00";
                                                                    
                                                        ?>
                                                            <td>
                                                                <div style="width: <?php echo $so_perc_est; ?>%; background-color: #89afd7; padding: 2px; margin: 2px auto; text-align: center; float: left;">
                                                                    <p style="margin:1px auto;min-width: 100px;"><?php echo $rowretData->totalestimate != ' hrs'? $rowretData->totalestimate:'00:00 hrs'; ?></p>
                                                                </div>
                                                                
                                                                <div style="width: <?php echo $so_perc_spent; ?>%; background-color: <?php echo $colr; ?>; padding: 2px; margin: 2px auto; text-align: center; float: left; clear: both;">
                                                                    <p style="margin:1px auto;min-width: 100px;"><?php echo $rowretData->timespent != ' hrs'? $rowretData->timespent:'00:00 hrs'; ?></p>
                                                                </div>
                                                                
                                                            </td>
                                                        <?php  }else{
                                                            ?>

                                                            <td>
                                                                <?php if ($action == 'projects-sprint' && $keycol == 'sname') { ?>

                                                                    <a href="./../../pages/tables/sprint-data.php?view=sprint-dashboard&id=<?php echo $rowretData->sid; ?>&pid=<?php echo $_REQUEST['id']; ?>">
                                                                    <?php } ?>
                                                                    <?php
                                                                        if($keycol == 'start_date' || $keycol == 'end_date' || $keycol == 'complete_date'){
                                                                            echo $rowretData->{$keycol} === null || $rowretData->{$keycol} == ' hrs' ? "-" : get_unixtimestamp_date($rowretData->{$keycol});
                                                                        }else{
                                                                            echo $rowretData->{$keycol} === null || $rowretData->{$keycol} == ' hrs' ? "-" : $rowretData->{$keycol};
                                                                        }
                                                                    ?>
                                                                    <?php if ($action == 'projects-sprint' && $keycol == 'sname') { ?>
                                                                    </a>            
                                                                <?php } ?>

                                                            </td>

                                                        <?php }
                                                            }   ?>
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
                        
                        
                        <!--<div class="width:100%;clear: both;">
                            <div id="task-status" class="widthminmax fleft"></div>
                            <div id="time-chart" class="widthminmax fright" ></div>
                        </div>
                        
                        <div class="width:100%;clear: both;">
                            <div id="task-plan" class="widthminmax fleft"></div>
                            <div id="container5" class="widthminmax fright" ></div>
                        </div>-->
                        
                        <div style="width:100%;clear: both;">
                            <div id="container6" class="fix0" style="width: 362px; min-height: 422px;"></div>
                            <table id="datatable6" style="display: none;">
                                <thead>
                                    <tr>
                                    <th></th>
                                    <th>Est.</th>
                                    <th>Spent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dataBarChart as $key => $valueBC) { ?>
                                    <tr>
                                        <th><?php echo $key; ?></th>
                                        <td><?php echo $valueBC['timeest']; ?></td>
                                        <td><?php echo $valueBC['timespent']; ?></td>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                            <?php if($dataDaysPlan){
     
                            //state => 1 - days pending, 2 - days are over due date
                            //closed => 1 - Sprint Closed 2 - Sprint Not Closed
                            
                            if($dataDaysPlan->state == 1){
                                //days are pending
                                if($dataDaysPlan->closed == 1){
                                    $upperText = 'Completed within';
                                    $underText = 'planned days. Closed Sprint';
                                    $color = 'green';
                                }elseif($dataDaysPlan->closed == 2){
                                    $upperText = 'In-Progress... ';
                                    $underText = 'days remaining. Active Sprint';
                                    $color = 'green';
                                }
                            }elseif($dataDaysPlan->state == 2){
                                //days are over
                                if($dataDaysPlan->closed == 1){
                                    $upperText = 'Over due by ';
                                    $underText = 'days. Closed Sprint';
                                    $color = 'red';
                                }elseif($dataDaysPlan->closed == 2){
                                    $upperText = 'Over due by';
                                    $underText = 'days. Sprint Not Closed';
                                    $color = 'red';
                                }
                            }
                            
                            #print_r($dataDaysPlan);
                                /*$dataDaysPlan->closed
                                        state
                                        countdays_no_weekend*/
                        ?>
                            <div id="dataDaysPlan" class="fix0" style="border:1px;height: 380px;" >
                                <div style="width:100%">
                                    <!-- Success box -->
                                    <div class="box box-solid bg-<?php echo $color; ?>">
                                        
                                        <div class="box-body" style="height: 380px;text-align: center; margin: 0% auto;">
                                            <div style="font-size: 23px;margin: 10% 0 0;"><?php echo $upperText; ?></div>
                                            <div style="width: 100%; margin: 2% 0 0;">
                                                <div style=" font-size: 70px;"><?php echo $dataDaysPlan->countdays_no_weekend >= 0 ? $dataDaysPlan->countdays_no_weekend :"-"; ?></div>
                                            </div>
                                            <div style="font-size: 23px;"><?php echo $underText; ?></div>
                                            <div style="font-size: 19px; padding: 0px 8%;">
                                                Started : <?php echo $dataDaysPlan->startdate === null ?"-":date('d/m/Y h:i A',strtotime($dataDaysPlan->startdate)); ?>
                                               <br>
                                               Due Date : <?php echo $dataDaysPlan->enddate === null ?"-":date('d/m/Y h:i A',strtotime($dataDaysPlan->enddate)); ?>
                                               <br>
                                               Completed : <?php echo $dataDaysPlan->completed_at === null ?"-":date('d/m/Y h:i A',strtotime($dataDaysPlan->completed_at)); ?>
                                            </div>
                                            <span>Note: Days are excluding Sat and Sun.</span>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary" style="margin-left: 20%;background: none repeat scroll 0% 0% #00a65a; "> </button> 
                                    <span style="padding-right: 4%;"> Within due date </span>
                                    <!--<button type="submit" class="btn btn-primary" style="background: none repeat scroll 0% 0% #f39c12; padding-left: 1%;"> </button> 
                                    <span style=""> Completed (&gt; Due date)</span>-->
                                    <button type="submit" class="btn btn-primary" style="background: none repeat scroll 0% 0% #f56954; "> </button> 
                                    <span style=""> Overdue </span>
                                    
                                </div>
                            </div>
                            <?php } ?>
                            
                            <div class="fix0 connectedSortable ui-sortable" style="width: 33.8%; margin: 0px auto; min-height: 396px;">
                                <?php 
                                    $gsql = "select username,g.id as id,g.status as status,g.goal as goal,g.added_at as added_at from tbl_goals g,tbl_users u where type='1' and referenceid='".$_REQUEST['id']."' group by g.id order by g.added_at asc";
                                    $gretData = mysql_query($gsql);
                                    
                                ?>
                                <!-- TO DO List -->
                                <div class="box box-primary" style="float: left;min-height: 376px;">
                                    <div class="box-header">
                                        <i class="ion ion-clipboard"></i>
                                        <h3 class="box-title">Goals</h3>
                                        
                                    </div><!-- /.box-header -->
                                    <div class="box-body goals-boxs" style="max-height: 300px; overflow-x: auto;">
                                        <ul class="todo-list">
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
                                                <div class="tools">
                                                    <i class="fa fa-edit"></i>
                                                    <i class="fa fa-trash-o"></i>
                                                </div>
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
                                        <button class="modelPop btn btn-default pull-right"><i class="fa fa-plus"></i> Add Goal</button>
                                    </div>
                                </div><!-- /.box -->
                                
                            </div>
                        </div>
                        
                        <!-- Chat box -->
                        <div class="row" style="width: 100%; margin: 20px 0px 0px; clear: both;">
                            
                            <div id="container1" style="width: 650px; max-width: 750px; display: inline-block;" ></div>
                            
                            <div class="col-lg-5 connectedSortable ui-sortable" style="display: inline-block; float: right;">
                                <?php 
                                    $csql = "select c.added_at as added_at,username,message from tbl_comments c,tbl_users u where c.added_by =u.id and c.type='1' and c.reference_id='".$_REQUEST['id']."' order by c.added_at asc";
                                    $cretData = mysql_query($csql);
                                    
                                ?>
                                <div class="box box-success" style="">
                                <div class="box-header">
                                    <i class="fa fa-comments-o"></i>
                                    <h3 class="box-title">Comment</h3>
                                    
                                </div>
                                    <div class="box-body chat" id="chat-box" style="overflow-x: auto; max-height: 250px; min-height: 200px;">
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
                                    <div class="input-group">
                                        <form action="manage-goals.php?action=5">
                                            <textarea rows="3" style="height: 83px; width: 390px; padding: 4px; margin: 0px 5px;" class="form-control" id="comment-id-txt" placeholder="Type message..."/></textarea>
                                            <div class="input-group-btn">
                                                <button type="button" class="btn-comment btn btn-success"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div><!-- /.box (chat box) -->                                                        
                            
                            </div>
                        </div>
                        
                        
                        <div class="wefull">
                            
                            
                        </div>
                        
                    <?php endif; ?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

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
        
        <script src="../../js/jquery.min.js"></script>

        <script src="../../js/highcharts.js"></script>
        <script src="../../js/modules/exporting.js"></script>
        


        <script src="../../js/bootstrap.min.js" type="text/javascript"></script>
        <!-- DATA TABES SCRIPT -->
        <script src="../../js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="../../js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <!--<script src="../../js/AdminLTE/app.js" type="text/javascript"></script>-->
        <!-- AdminLTE for demo purposes -->
        <script src="../../js/AdminLTE/demo.js" type="text/javascript"></script>

        <script src="../../js/Chart.min.js"></script>

        
<!--        <script src="../../js/date-euro.js" type="text/javascript"></script>	-->
        <script src="../../js/date-uk.js" type="text/javascript"></script>

        <!-- page script -->
        <script type="text/javascript">
                        $(function () {

                            $('#example1').dataTable({
                                "aoColumns": [
                                            { "sType": "string" },
                                            { "sType": "date-uk" },
                                            { "sType": "date-uk" },
                                            { "sType": "date-uk" },
                                            { "sType": "string" },
                                            { "sType": "string","sWidth": "20%","bSortable": false },
                                            { "sType": "string" },
                                            
                                        ],
                                
                                "aaSorting": [[2,'desc'],[6,'desc']],
                                "iDisplayLength": 50
                            });
                            
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
                            
                            datatopbarupdate();

                            var refInterval = setInterval(function () {
                                datatopbarupdate();
                            }, 30000); // 30 seconds

                            $('#project-select').change(function(){
                                    //alert($('#project-select').val());
                                if($('#project-select').val() != ''){

                                    window.location = "./../../pages/tables/sprint-data.php?view=projects-sprint&id="+$('#project-select').val();
                                }else{
                                    window.location = "./../../pages/tables/epic-all-projects.php?view=all-projects";
                                }
                            });
                            
                            view = "<?php echo $_REQUEST['view']; ?>";
                            
                            $('.export').click(function () {
                                //alert(view);
                                if (view == 'projects-sprint' && $('#project-select').val() != '') {
                                    redirectUrl = "./../../php-excel/topmenuexportxls.php?view="+view+"&id="+$('#project-select').val();

                                    if (<?php echo isset($_REQUEST['from'], $_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to']) ? 1 : 0; ?>) {
                                        redirectUrl += '&from=' + '<?php echo isset($_REQUEST['from']) && !empty($_REQUEST['from']) ? $_REQUEST['from'] : '0'; ?>' + '&to=' + '<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to']) ? $_REQUEST['to'] : '0'; ?>';
                                    }

                                    window.location = redirectUrl;
                                }
                            });
                            
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
                            }
                            generatePercentile();
                            
                            
                            $(document.body).on('click','.modelPop',function(){
                                
                                $('#goal-text').val('');
                                $('#actionP').val('1');
                                $('.opt-r').html('Create');
                                
                                $( "#compose-modal" ).modal('show');
                            });
                            
                            var sprintId = <?php echo isset($_REQUEST['id']) ? $_REQUEST['id']:0; ?>;
                            var pId = <?php echo isset($_REQUEST['pid'])?$_REQUEST['pid']:0; ?>;
                            
                            $(document.body).on('click','.cuPGoal',function(){
                                if($.trim($('#goal-text').val()) != ''){
                                    $.ajax({
                                        url:'manage-goals.php',
                                        type:'POST',
                                        data:'action='+$('#actionP').val()+'&goal='+$('#goal-text').val()+'&pid='+pId+'&sid='+sprintId+'&gid='+$('#g-id').val(),
                                        success:function(response){

                                            data = $.parseJSON(response);

                                            $( "#compose-modal" ).modal('hide');

                                            //location.reload();
                                            $('.todo-list').append(data.html);

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
                                                generatePercentile();
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
                                
                            });
                            
                            $(document.body).on('click','.btn-comment',function(){
                                if($.trim( $('#comment-id-txt').val()) != ''){
                                    $.ajax({
                                        url:'manage-goals.php',
                                        type:'POST',
                                        data:'action=5&pid='+pId+'&sid='+sprintId+'&message='+$('#comment-id-txt').val(),
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
            
            $('.navbar-btn.sidebar-toggle').click(function(){
                if($('aside.left-side.sidebar-offcanvas').hasClass('collapse-left')){
                  $('aside.left-side.sidebar-offcanvas').removeClass('collapse-left');
                  $('aside.right-side').removeClass('strech');
                }else{
                  $('aside.left-side.sidebar-offcanvas').addClass('collapse-left');
                  $('aside.right-side').addClass('strech');
                }
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
                     
                     var datasets4 = [];
                     var assignees = [];
           
<?php 
        if($action == 'sprint-dashboard'){
            
            $includeData= array(
                                'open'
                                ,'in_review'
                                ,'done'
                                ,'to_do'
                                ,'in_progress'
                                ,'reopened'
                                ,'resolved'
                                ,'closed'
                            );
            $graphBugData = array();
            $graphOtherData = array();
            
            for( $i=1;$i<=2;$i++ ){
                foreach ($includeData as $ikey => $ivalue) {
                    if($i == 1){
                        $graphBugData[$ivalue]['name']= ucwords(str_replace('_', ' ', $ivalue));
                        $graphBugData[$ivalue]['stack']='Bugs';
                        $colorData = issuestatusColor(ucwords(str_replace('_', ' ', $ivalue)));
                        $graphBugData[$ivalue]['color'] = $colorData['cstack'];
                        $graphBugData[$ivalue]['data'] = array();
                    }else{
                        $graphOtherData[$ivalue]['name']= ucwords(str_replace('_', ' ', $ivalue));
                        $graphOtherData[$ivalue]['stack']='Others';
                        $colorData = issuestatusColor(ucwords(str_replace('_', ' ', $ivalue)));
                        $graphOtherData[$ivalue]['color'] = $colorData['cstack'];
                        $graphOtherData[$ivalue]['data'] = array();
                    }
                }
            }
            
            while ($rowretDataStackedBar = pg_fetch_object($retDataStackedBar)) {  
                
            ?>
                assignees.push("<?php echo $rowretDataStackedBar->assignees != null ? $rowretDataStackedBar->assignees : "Unassigned"; ?>");
            <?php
                    
                foreach ($rowretDataStackedBar as $rowretDataStackedBarKey => $rowretDataStackedBarvalue) {
                    $compare_key = str_replace('b_','',str_replace('nb_', '', $rowretDataStackedBarKey));
                    
                    if(!in_array($compare_key, $includeData)){
                        continue;    
                    }
                    
                    //set Data
                    if(strpos($rowretDataStackedBarKey, 'nb_') !== false){ 
                        $graphOtherData[$compare_key]['data'][] = $rowretDataStackedBarvalue;
                            
                    }else{
                        $graphBugData[$compare_key]['data'][] = $rowretDataStackedBarvalue;
                    }
                    
                }
            }
                
            for( $i = 1;$i <= 2;$i++ ){
                $graphArrayName = $i==1?'graphOtherData':'graphBugData';
                    foreach ($$graphArrayName as $keyygr => $valueygr) {
                        #print_r($valueygr);exit;
                ?>
                            
                            obj = {};
                            obj['name'] = '<?php echo $valueygr['name']; ?>';
                            obj['color'] = '<?php echo $valueygr['color']; ?>';
                            obj['data'] = [<?php echo implode(',', $valueygr['data'])?>];
                            obj['stack'] = '<?php echo $valueygr['stack']; ?>'
                            datasets4.push(obj);
                    <?php  }
            } ?>
        

            $(function () {

                //red green yellow chart
                /*$('#container1').highcharts({
                    chart: {
                        type: 'bar'
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
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'top',
                        x: -40,
                        y: 100,
                        floating: true,
                        borderWidth: 1,
                        backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
                        shadow: true
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
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
                });*/
        
                $('#container1').highcharts({

                    chart: {
                        type: 'column',
                        //inverted: false
                    },

                    title: {
                        text: 'Assignee Report'
                    },

                    yAxis: {
                        allowDecimals: false,
                        min: 0,
                        title: {
                            text: 'Total No. Task'
                        }
                    },

                    xAxis: {
                        categories: assignees

                    },

                    tooltip: {
                        formatter: function () {
                            
                            return '<b>' + this.x + '</b><br/>' +this.series.options.stack+' '+
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
                    
                    series: datasets4
                });
            });
            
            //dashboard table based role based Data
            var table = document.getElementById('datatable6'),
                options = {
                    chart: {
                        renderTo: 'container6',
                        defaultSeriesType: 'column'
                    },
                    title: {
                        text: 'Effort Estimate'
                    },
                    xAxis: {
                    },
                    yAxis: {
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
                    }
                };
                Highcharts.visualize(table, options);
        <?php } ?>
            
            var datasets1 = [];
<?php if($action=='projects-sprint'){
            $sqlBaarChart_sprint = $sqlQueries['project-allsprint-time-management']['query'];
            $sqlBaarChart_sprint = sprintf(
                                                $sqlBaarChart_sprint,
                                                $_REQUEST['id']
                                            );
            $retDataBaarChart_sprint = pg_query($db, $sqlBaarChart_sprint);

            //$data = '[';
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
            $(function(){
                
                
                //time estimate/spent All Sprints
                $('#time-chart-all-print').highcharts({
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Project Sprint Time'
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
                    
                    //pie Chart issue disstribution typewise
                    $('#container8').highcharts({
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false
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
                                name: 'Issues',
                                data: [
                                    <?php while ($rowretData_typebased = pg_fetch_object($retProject_typebased)) { 
                                            $pieColor .= "'".$IssueTypeColors[$rowretData_typebased->pname]."',";
                                            $countIssues += $rowretData_typebased->count;
                                     ?>
                                        ['<?php echo $rowretData_typebased->pname; ?>', <?php echo $rowretData_typebased->count; ?>],
                                    <?php }?>
                                ],
                                colors: [
                                    <?php echo $pieColor; ?>
                                ],
                            }],
                        title: {
                            text: '<?php echo $countIssues; ?> Issues'
                        },
                    });
                
                
                // role based all sprint data ie. dev,tester,pm,design
                
                var table = document.getElementById('datatable7'),
                    options = {
                        chart: {
                            renderTo: 'container7',
                            defaultSeriesType: 'column'
                        },
                        title: {
                            text: 'Effort Estimate'
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
                    Highcharts.visualize(table, options);
                    
                    //$('.left-side.sidebar-offcanvas').addClass('collapse-left');
                    //$('.right-side').addClass('strech');
            });
            var datasets11 = [];
            var datasets12 = [];
            var datasets13 = [];
            <?php
             while($rowProject_sprintLine_chart = pg_fetch_object($retLine_chart)){          
             ?>                   
                datasets11.push(<?php echo $rowProject_sprintLine_chart->nb_task; ?>);
                datasets12.push(<?php echo $rowProject_sprintLine_chart->resolved_issues; ?>);
                datasets13.push('<?php echo $rowProject_sprintLine_chart->sname; ?>');
                             
        <?php } ?>
            $(function () {
    $('#container11').highcharts({
        chart: {
            type: 'line'
        },
        title: {
            text: 'Burn Up'
        },
        
        xAxis: {
           categories: datasets13,
        },
        yAxis: {
            min:0,            
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            }
        },
        series: [{
            name: 'Total Work',
            data: datasets11,
        }, {
            name: 'No of Task Completed',
            data: datasets12,
        }]
    });
});
        <?php } ?>
            
        </script>
        <script type="text/javascript">
            $('aside.left-side.sidebar-offcanvas').addClass('collapse-left');
            $('aside.right-side').addClass('strech');
        </script>
        <style>
            #project_select_chosen{text-align: left;}
        </style>
    </body>
</html>
