<?php 


	/******************************************* include Config *************************************/
	include './../../config.php';
        include './../../mysqlconfig.php';
        if(!in_array("project_worklogs",$_SESSION['role_permission'])){
	     echo "<script>window.location.href='".HOST_NAME."no_permission.php'</script>";
	     exit;
	 }
        $actionsData = array(
                'active-projects'=>array('cpage'=>'Project Worklogs','pbreadcrumb'=>array())
                ,'assignee-total-est-project-selection'=>array('cpage'=>'Project Worklogs','pbreadcrumb'=>array('Project Worklogs <span style="font-size: 10px;">(Active Projects)</span>'=>'pages/tables/data.php?view=active-projects'))
                
            );
        
        //Get User Project.
        $projectIds = array();
        $sql_project = "select *,tbl_users.id as user_id from  tbl_users inner join tbl_user_projects on tbl_users.id = tbl_user_projects.user_id where tbl_users.id='".$_SESSION['uid']."' and tbl_users.status='1'";
        $retDataMY = mysql_query($sql_project);  
        while($rowretDataMY = mysql_fetch_object($retDataMY)){
             $projectIds[]= $rowretDataMY->project_id;
        }
        
	if (array_key_exists($action,$actionsData) && $action == 'assignee-total-est-project-selection') {
                
            include './../../mysqlconfig.php';
            $mysqlQuery = $sqlQueries[$action]['query'];


            if(isset($_REQUEST['from'],$_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to'])){
                //here w_created is startdate of worklog.
                $sqlStrAdd = "and w_created >= '".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."' and w_created <= '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."' ";
            }else{
                $sqlStrAdd = '';
            }

            $mysqlQuery = sprintf($mysqlQuery,$_REQUEST['id'],$sqlStrAdd);
            #echo $mysqlQuery;exit;
            $retData = mysql_query($mysqlQuery);

            if(!$retData){
               echo mysql_error($db);
            } else {
               //error_log( "Query successfully\n" );
            }

            $sqlProjectName = $sqlQueries['getProjectName']['query'];
            $sqlProjectName = sprintf($sqlProjectName,$_REQUEST['id']);

            $retProjectName = pg_query($db, $sqlProjectName);

            if(!$retProjectName){
               echo pg_last_error($db);
            } else {
               //error_log( "Project Name Query successfully\n") ;
            }
            while($rowProjectName = pg_fetch_object($retProjectName)){
                    $projectName = $rowProjectName->pname;
            }
            $ismysqlQuery = 1;
            #echo $mysqlQuery;exit;
        } elseif(array_key_exists($action,$actionsData) && $action == 'active-projects') {
               
            $ismysqlQuery = 0; 
            $sql = $sqlQueries[$action]['query'];
		
            if(isset($_REQUEST['from'],$_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to'])){

                $sqlStrAddActiveProjects = "to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <'".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";

            }else{

                $sqlStrAddActiveProjects = "to_char(W.startdate::date, '2YYY-MM-DD')::DATE > (CURRENT_DATE::date - INTERVAL '7 days')";
            }

            if($_SESSION['highest_role'] !=1 ){
                if(!empty($projectIds)){
                    $sqlStrAddActiveProjects .= ' and B.id in ('.trim(implode(",", $projectIds)).') ';
                }else{
                    $sqlStrAddActiveProjects .= ' and B.id in (0)';
            }
            }

            $sql = sprintf($sql,$sqlStrAddActiveProjects);

            #echo $sql;exit;
            $retData = pg_query($db, $sql);

            if(!$retData){
               echo pg_last_error($db);
            } else {
               //error_log( "Query successfully\n" );
            }
                
           }
	
	//project data
		
		$sqlProject = $sqlQueries['allprojects']['query'];
                $sqlStrAllProjects = '';
                if($_SESSION['highest_role'] !=1){
                    if(!empty($projectIds)){
                        $sqlStrAllProjects .= ' where id in ('.trim(implode(",", $projectIds)).') ';
                    }else{
                        $sqlStrAllProjects .= ' where id in (0)';
                    }
                }

                $sqlProject = sprintf($sqlProject,$sqlStrAllProjects);
		$retProject = pg_query($db, $sqlProject);
	
		if(!$retProject){
		   echo pg_last_error($db);
		} else {
		   //error_log( "Project Query successfully\n") ;
		}

		
		#print_r($sqlQueries);exit;
		#while($rowProject = pg_fetch_object($retProject)){ print_r($rowProject);exit;}
                
                $filterFromTo = '';
                if(isset($_REQUEST['from'] ,$_REQUEST['to'])){
                    $filterFromTo = '&from='.$_REQUEST['from'].'&to='.$_REQUEST['to'];
                }elseif ($action == 'total-time-spent-assignee-last-month') {
                    $filterFromTo = '&from='.date('M d,Y',  strtotime(date('Y-m-d').'-31 days')).'&to='.date('M d,Y');    
                }elseif ($action == 'active-projects') {
                    $filterFromTo = '&from='.date('M d,Y',  strtotime(date('Y-m-d').'-6 days')).'&to='.date('M d,Y');
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
                .box .todo-list > li{ list-style: outside none none;}
                .box .todo-list > li a{color: #fff;}
                .box .todo-list > li .text{display: inline;}
                .box .todo-list > li .custom{ color: #444;font-size: 12px;background-color: #F3F4F5;}
                .box .todo-list > li > input[type="checkbox"]{margin: 0 2px;}
                
	  </style>
          
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
                        <?php if($_SESSION['highest_role']==1){ ?>
                        <li><a href="./../../index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                        <?php } ?>
                        <?php 
                            foreach($actionsData[$action]['pbreadcrumb'] as $bText =>$bLink ){?>
                        <li class=""><a href="./../../<?php echo $bLink.$filterFromTo; ?>"><i class="fa fa-table"></i> <?php echo $bText;?></a></li>        
                  <?php    }
                        ?>
                        <li class="active"><?php echo $actionsData[$action]['cpage'];?></li>
                    </ol>
                </section>
                <section class="content-header">
                    
                    <div style="text-align: center;">
                            <div style="width:15%;display: inline-block;"><?php //echo isset($projectName)?$projectName:""; ?>&nbsp;</div>
                            <div style="width:54%;display: inline-block;">
                                <span style="font-size: 16px;"><?php echo $actionsData[$action]['cpage'];?> : </span>
                                <select id="project-select" name="id" data-placeholder="Choose a Project..." class="chosen-select" style="width: 280px;float:right;" tabindex="1">
                                        <option value="">Select Project</option>
                                <?php #$action = $_REQUEST['view'];?>
                                <?php while($rowProject = pg_fetch_object($retProject)){ ?>    
                                        <option value="<?php echo $rowProject->id; ?>" <?php echo isset($_REQUEST['id']) && $rowProject->id == $_REQUEST['id']?"selected":""; ?>><?php echo $rowProject->pname; ?></option>
                                <?php } ?>
                                </select>
                            </div>
                            <div style="display: inline-block;width: 5%;margin-left: 15%;">
                              <?php if($_SESSION['highest_role']==1){ ?>
                                <button class="btn btn-success export" type="button">Export</button>
                                <?php } ?>
                            </div>
                            
                    </div>

                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
			
                        <div class="col-xs-12">
			    
                            <div class="box">
                                <div class="box-header">
                                    
                                        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;margin: 10px">
                                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                            <span></span> <b class="caret"></b>
                                        </div>
                                    
                                </div><!-- /.box-header -->
				
                                <div class="box-body table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
					    <?php foreach($sqlQueries[$action]['columns'] as $keycol => $columns) { ?>
                                                <th><?php echo $columns; ?></th>
                                             <?php } ?>   
                                            </tr>
                                        </thead>
                                        <tbody>
					
					<?php $queryObj = $ismysqlQuery ==1?'mysql_fetch_object':'pg_fetch_object';
                                        
                                                while($rowretData = $queryObj($retData)){ #print_r($sqlQueries[$action]['columns']);exit;?>
						
					      	<tr>
						<?php foreach($sqlQueries[$action]['columns'] as $keycol => $columns) { ?>
                                                    
						    <td>
                                                        <?php if ($action == 'active-projects' && $keycol=='pname') { ?>
                                                            <a href="./../../pages/tables/data.php?view=assignee-total-est-project-selection&id=<?php echo $rowretData->project.$filterFromTo;?>">            
                                                        <?php }elseif ($action == 'active-projects' && $keycol=='task_count') { ?>
                                                            <a href="./../../pages/tables/top-menudata.php?view=data-project-tasks&id=<?php echo $rowretData->project.$filterFromTo;?>">            
                                                        <?php }?>
                                                        <?php echo $keycol == "assignee" && $rowretData->assignee != '' ? "<a href='./../../pages/tables/data-project-assignee-task.php?pid=".$_REQUEST['id']."&uid=".$rowretData->assignee.$filterFromTo."'>" : ""; ?>
                                                        <?php //echo $rowretData->{$keycol} === null ?"-": $rowretData->{$keycol} ;  
                                                                if($keycol=='assignee' && $rowretData->assignee == ''){
                                                                    echo 'Unestimated Task';
                                                                }else{
                                                                    echo $rowretData->{$keycol} === null || $rowretData->{$keycol} == ' hrs' ?"-": $rowretData->{$keycol} ;
                                                                }
                                                        ?>
                                                        <?php //echo $rowretData->{$keycol} === null || $rowretData->{$keycol} == ' hrs' ?"-": $rowretData->{$keycol} ;  ?>
                                                        <?php echo $keycol == "assignee" && $rowretData->assignee != '' ?"</a>" :""; ?>
                                                        <?php if ($action == 'active-projects' && $keycol=='pname') { ?>
                                                            </a>            
                                                        <?php }elseif ($action == 'active-projects' && $keycol=='task_count') { ?>
                                                            </a>            
                                                        <?php }?>
                                                    
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
                    <?php if($action == 'assignee-total-est-project-selection'){ ?>                 
                    <div>
                        <div class="fix0 connectedSortable ui-sortable" style="display: inline-block; margin: 20px; width: 44%;">
                            <?php 
                                $gsql = "select username,g.id as id,g.status as status,g.goal as goal,g.added_at as added_at,referenceid,type,project_id 
                                                from tbl_goals g,tbl_users u 
                                                where (project_id ='".$_REQUEST['id']."' or referenceid='".$_REQUEST['id']."') "
                                        . "     group by g.id "
                                        . "     order by g.added_at asc";
                                $gretData = mysql_query($gsql);

                            ?>
                            <!-- TO DO List -->
                            <div class="box box-primary" style="float: left;min-height: 390px;">
                                <div class="box-header">
                                    <i class="ion ion-clipboard"></i>
                                    <h3 class="box-title">Goals</h3>

                                </div><!-- /.box-header -->
                                <div class="box-body goals-boxs" style="max-height: 300px; overflow-x: auto;">
                                    <ul class="todo-list">
                                        <?php while($GoalV = mysql_fetch_object($gretData)) { ?>

                                        <li class="li-data-<?php echo $GoalV->id; ?>" data-id="<?php echo $GoalV->id; ?>" <?php if($GoalV->type == '1'){ ?> data-sprintid="<?php echo $GoalV->referenceid; ?>" <?php } ?> >
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
                                            <small class="label custom label-primary"><i class="fa fa-clock-o"></i> <?php echo ucfirst($GoalV->username)." ".date('d/m/Y H:i',  strtotime($GoalV->added_at));?></small>
                                            <?php if($GoalV->type == '1'){ ?>
                                            <small class="label label-success" style="font-size:10px;"><a href="sprint-data.php?view=sprint-dashboard&id=<?php echo $GoalV->referenceid; ?>&pid=<?php echo $GoalV->project_id; ?>">SPRINT</a></small>
                                            <?php } ?>
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


                        <div class="col-lg-5 connectedSortable ui-sortable" style="display: inline-block; margin: 20px; float: right; width: 44%;">
                            <?php 
                                $csql = "select c.added_at as added_at,username,message from tbl_comments c,tbl_users u where c.added_by =u.id and c.type='3' and c.reference_id='".$_REQUEST['id']."' order by c.added_at asc";
                                $cretData = mysql_query($csql);

                            ?>
                            <div class="box box-success" style="">
                                <div class="box-header">
                                    <i class="fa fa-comments-o"></i>
                                    <h3 class="box-title">Comment</h3>

                                </div>
                                <div class="box-body chat" id="chat-box" style="overflow-x: auto; max-height: 250px;">
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
                    <div style="width:70%;margin:0 auto;">
                        <?php 
                            $sub_query = $sqlQueries['projects-sprint']['small_query'];
                            $sub_query = sprintf($sub_query,$_REQUEST['id']);
                            $sub_queryretData = pg_query($db, $sub_query);
                                         
                            
                        ?>
                        <table class="table table-bordered table-striped dataTable" id="example1" aria-describedby="example1_info">
                            <thead>
                                <tr role="row">
                                    <th> Sprint </th>
                                    <th> Goal Progress </th>
                                </tr>
                            </thead>
                            <tbody role="alert" aria-live="polite" aria-relevant="all">
                                <?php while($subretData = pg_fetch_object($sub_queryretData)){ ?>
                                <tr class="odd sprintsall">
                                    <td class="  sorting_1">
                                        <a href="sprint-data.php?view=sprint-dashboard&id=<?php echo $subretData->id; ?>&pid=<?php echo $subretData->pid; ?>">
                                            <?php echo $subretData->sname; ?>
                                        </a>
                                    </td>
                                    <td class="pg" data-siid="<?php echo $subretData->id; ?>">
                                        <div class="col-sm-12">
                                            <div class="progress xs">
                                                <div style="width: 70%;" class="TpercTile<?php echo $subretData->id; ?> progress-bar progress-bar-green"></div>
                                            </div>
                                            <small class="pull-right TpercTile<?php echo $subretData->id; ?>">70%</small>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } 
                        
                        if($action=='assignee-total-est-project-selection'){ ?>
                    <div class="box" >
                        <div id="chartrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;margin: 10px">
                                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                            <span></span> <b class="caret"></b>
                                        </div>
                        <div style="margin:10 auto;float:right;padding:10px">
                            <!--<input type="radio" name="report" value="project" onclick="showProject()" checked> Project &nbsp;&nbsp;-->
                            <input type="radio" name="report" value="sprint" onclick="showSprint()" checked> Sprint&nbsp;&nbsp;
                            <?php 
                            $sub_query1 = $sqlQueries['projects-sprint']['small_query'];
                            $sub_query1 = sprintf($sub_query1,$_REQUEST['id']);
                            $sub_queryretData1 = pg_query($db, $sub_query1);
                                         
                            
                          ?>
                            <div id="sprint" style="display:none">  
                                <select onchange="javascript:initSprintChart(this);" id="sprint_val">
                                    <!-- <option value="">Select any</option> -->
                                 <?php  while($subretDatas = pg_fetch_object($sub_queryretData1)){
                                     ?>
                                       <option value="<?php echo $subretDatas->id; ?>"><?php echo $subretDatas->sname; ?></option>                                    
                    <?php } ?>
                                </select>
                            </div>
                        </div>
                                    
                                    <div style="width:100%;;display: inline-block;">
                                        <div id="container1" ></div>
                                    </div>
                    </div> 
                    <?php } ?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        <?php if($action=='assignee-total-est-project-selection'){ ?>
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
        
        <script src="../../js/jquery.min.js"></script>
        <script src="../../js/highcharts.js"></script>
         <script src="../../js/Chart.min.js"></script>
         
        <script type="text/javascript" src="../../js/bootstrap.min.js"></script>
        <!-- DATA TABES SCRIPT -->
        <script src="../../js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="../../js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <!--script src="../../js/AdminLTE/app.js" type="text/javascript"></script-->
        <!-- AdminLTE for demo purposes -->
        <script src="../../js/AdminLTE/demo.js" type="text/javascript"></script>
        <!-- page script -->
        <script type="text/javascript">
            
            //Burn Down Chart.
            function initSprintChart(a){
                
                $.ajax({
                    url:'sprintData.php',
                    type:'POST',
                    data:'sprint_id='+$(a).val()+'&project_id='+"<?php echo $_REQUEST['id']; ?>",
                    success:function(response){

                        data = $.parseJSON(response);
                        //console.log(data);
                        e = [];
                        w=[];
                        sprint_date =[];
                        
                        $.each(data,function(i,index){
                            //console.log(i+' , '+index);
                            sprint_date.push(i);
                            $.each(index,function(i2,index2){
                                //console.log(i2+' , '+index2);
                                if(i2 == 'e'){
                                    e.push(parseFloat(index2));
                                }
                                
                                if(i2 == 's'){
                                    w.push(parseFloat(index2));
                                }
                            });
                        });
                        //return false;
                        
                        
                        $('#container1').highcharts({
                            chart: {
                                type: 'line'
                            },
                            title: {
                                text: 'Burn Down'
                            },

                            xAxis: {
                               categories: sprint_date,
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
                                name: 'Work Planned',
                                data: e,
                            }, {
                                name: 'Actual Work',
                                data: w
                            }]
                        });
                    } 
                });
            }
            
            $(function() {
                
                
                //w = [7.0, 6.9, 9.5, 14.5, 18.4, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6];
                //aw = [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8];
                //initSprintChart(w,aw);
                
                
                
                function allSprintPercentileGoal(){
                    
                    $('tr.sprintsall td.pg').each(function(){
                        
                        $sprintId = $(this).data('siid');
                        stotal = 0;
                        schecked = 0;
                        $('ul.todo-list li').each(function(){
                          if($(this).data('sprintid') == $sprintId){
                            if($(this).find('input.goal-list').is(':checked')){
                              schecked++;
                            }
                            stotal++;
                          }
                        });
                        spercent = (schecked/stotal)*100;
                        if(isNaN(spercent)){ spercent =0; }
                        
                        $('.pull-right.TpercTile'+$sprintId).html(spercent.toFixed(2).replace('.00','')+"%");
                        $('.TpercTile'+$sprintId+'.progress-bar').css('width',spercent.toFixed(2).replace('.00','')+"%");
                    });
                    
                }
                
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
                    
                    //alert(percent +"= ("+checked+"/"+total+")*100");
                    
                    $('.pull-right.percTile').html(percent.toFixed(2).replace('.00','')+"%");
                    $('.percTile.progress-bar').css('width',percent.toFixed(2).replace('.00','')+"%");
                    
                    allSprintPercentileGoal();
                }
                
                generatePercentile();
                
                $(document.body).on('click','.modelPop',function(){

                    $('#goal-text').val('');
                    $('#actionP').val('1');
                    $('.opt-r').html('Create');

                    $( "#compose-modal" ).modal('show');
                });
                
                $(document.body).on('click','.cuPGoal',function(){
                    if($.trim($('#goal-text').val()) != ''){
                        $.ajax({
                            url:'manage-goals.php',
                            type:'POST',
                            data:'action='+$('#actionP').val()+'&project=1&goal='+$('#goal-text').val()+'&pid='+'<?php echo $_REQUEST['id'];?>'+'&gid='+$('#g-id').val(),
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
                            data:'action=5&project=1&pid='+'<?php echo $_REQUEST['id']; ?>'+'&message='+$('#comment-id-txt').val(),
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
			};
			datatopbarupdate();	
		
			var refInterval = setInterval(function() {
				      datatopbarupdate();
				}, 30000); // 30 seconds
                                
		$('#example1').dataTable({"iDisplayLength": 25});
                $('#example2').dataTable({
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });
		
		view = "<?php echo $_REQUEST['view']; ?>";

		$('.export').click(function(){
                    //alert(view);
		    if(view == 'assignee-total-est-project-selection' && $('#project-select').val() != ''){
                        redirectUrl = "./../../php-excel/exportreportwisexls.php?view="+view+"&id="+$('#project-select').val();
                        
                        if(<?php echo isset($_REQUEST['from'], $_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to']) ? 1:0; ?>){
                            redirectUrl += '&from='+'<?php echo isset($_REQUEST['from'])&& !empty($_REQUEST['from'])?$_REQUEST['from']:'0'; ?>'+'&to='+'<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])?$_REQUEST['to']:'0'; ?>';
                        }
                        
		    }else if(view == 'active-projects'){			
                        redirectUrl = "./../../php-excel/topmenuexportxls.php?view="+view;
                        if(<?php echo isset($_REQUEST['from'], $_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to']) ? 1:0; ?>){
                            redirectUrl += '&from='+'<?php echo isset($_REQUEST['from'])&& !empty($_REQUEST['from'])?$_REQUEST['from']:'0'; ?>'+'&to='+'<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])?$_REQUEST['to']:'0'; ?>';
                        }
                    }
                    
                    window.location = redirectUrl;
		});

		//On Change Of Project Dropdown
		$('#project-select').change(function(){
			//alert($('#project-select').val());
                    if($('#project-select').val() != ''){
                        Url = '&from='+'<?php echo isset($_REQUEST['from']) && !empty($_REQUEST['from'])?$_REQUEST['from']:date('M d,Y',  strtotime(date('Y-m-d').'-6 days')); ?>'+'&to='+'<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])?$_REQUEST['to']:date('M d,Y'); ?>';
                        window.location = "./../../pages/tables/data.php?view=assignee-total-est-project-selection&id="+$('#project-select').val()+Url;
                    }else{
                        window.location = "./../../pages/tables/data.php?view=assignee-total-est-project-selection";
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
                   
                   if(<?php echo ($action =='active-projects') && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0; ?>){
                       startDatePic = moment().subtract(6, 'days');//.format('MMMM D, YYYY');
                   }
                   
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
                        
                        if(<?php echo ($action =='active-projects' ) && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0; ?>){
                            startDatePic = moment().subtract(6, 'days').format('MMMM D, YYYY');
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

                            window.location= "./../../pages/tables/data.php?view=<?php echo $_REQUEST['view'] ?>&id=<?php echo $_REQUEST['id'] ?>&from="+picker.startDate.format('MMMM D, YYYY')+"&to="+picker.endDate.format('MMMM D, YYYY');

                        });
                        
                    //Line Chart Date picker.
                    
                    var cb = function(start, end, label) {                     
                     $('#chartrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                     };  
                   
                   fromDate = '<?php echo isset($_REQUEST['from']) && !empty($_REQUEST['from'])? date('m/d/Y',strtotime($_REQUEST['from'])):''; ?>';
                   startDatePic = typeof(fromDate) == "undefined" || fromDate==''? moment().subtract(3, 'years'): fromDate;
                   endDate = '<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])? date('m/d/Y',strtotime($_REQUEST['to'])):''; ?>';
                   endDatePic = typeof(endDate) == "undefined" || endDate=='' ? moment(): endDate;
                   
                   if(<?php echo ($action =='active-projects') && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0; ?>){
                       startDatePic = moment().subtract(6, 'days');//.format('MMMM D, YYYY');
                   }
                   
                   var optionSet1 = {
                            startDate: startDatePic,
                            endDate: endDatePic,
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
                        
                        if(<?php echo ($action =='active-projects' ) && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0; ?>){
                            startDatePic = moment().subtract(6, 'days').format('MMMM D, YYYY');
                        }
                        
                        $('#chartrange span').html(startDatePic + ' - ' + endDatePic);

                        $('#chartrange').daterangepicker(optionSet1, cb);

                        $('#options1').click(function() {
                            $('#chartrange').data('daterangepicker').setOptions(optionSet1, cb);
               });


                        $('#chartrange').on('apply.daterangepicker', function(ev, picker) { 
                                                      
                        });
               });
               
               function showSprint(){
                            document.getElementById('sprint').style.display = "inline";
                            document.getElementById('chartrange').style.display = "none";
                }
                function showProject(){
                            document.getElementById('sprint').style.display = "none";
                            document.getElementById('chartrange').style.display = "inline";
                }
                
                showSprint();                
                initSprintChart($("#sprint_val option:first"));
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
