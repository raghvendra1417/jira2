<?php

// load library
require 'php-excel.class.php';



	/******************************************* include Config *************************************/
	include './../config.php';

if (in_array($action,$actionArr)) {
    
	$sql = $sqlQueries[$action]['query'];
	//$sql = sprintf($sql,$_REQUEST['id']);

        /*if(isset($_REQUEST['from'],$_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to'])){
            $sqlStrAddTotalTimeSpentAssignee = "and (resolutiondate, resolutiondate) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
            $sqlStrAddActiveProjects = "(updated, updated) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
            $sqlStrAddTaskInProgess = "and (A.created, A.created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
            $sqlStrAddTaskNoDues = "and (A.created, A.created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
            $sqlUnestimated = "(A.created, A.created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
        }else{
            $sqlStrAddTotalTimeSpentAssignee = "and resolutiondate::date BETWEEN (now() - '31 days'::interval)::timestamp AND now()";
            $sqlStrAddActiveProjects = "updated::date > (CURRENT_DATE::date -  '7 days'::INTERVAL )";
            $sqlStrAddTaskInProgess = "";
            $sqlStrAddTaskNoDues = "and created::date BETWEEN (now() - '31 days'::interval)::timestamp AND now()";
            $sqlUnestimated = "A.created > (CURRENT_DATE::date - INTERVAL '7 days')";
        }*/
        
        if(isset($_REQUEST['from'],$_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to'])){
            /*$sqlStrAddTotalTimeSpentAssignee = " startdate::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and startdate::date < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
            $sqlStrAddActiveProjects = "W.startdate::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and W.startdate::date <'".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
            $sqlStrAddTaskInProgess = "and A.created::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and created::date < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
            $sqlStrAddTaskNoDues = "and A.created::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and created::date < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
            $sqlUnestimated = "A.created::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and created::date < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
            $sqlStrProjectTasks = " and A.updated::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and A.updated::date < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
            */
            
            $sqlStrAddTotalTimeSpentAssignee = " to_char(startdate::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(startdate::date, '2YYY-MM-DD')::DATE < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
            $sqlStrAddActiveProjects = "to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
            $sqlStrAddTaskInProgess = "and A.created::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and created::date < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
            $sqlStrAddTaskNoDues = "and A.created::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and created::date < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
            $sqlUnestimated = "A.created::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and created::date < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
            $sqlStrProjectTasks = " and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
           /* $sqlStrAddTotalTimeSpentAssignee = "and (created, created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
            $sqlStrAddActiveProjects = "(updated, updated) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
            $sqlStrAddTaskInProgess = "and (A.created, A.created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
            $sqlStrAddTaskNoDues = "and (A.created, A.created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
            $sqlUnestimated = "(A.created, A.created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."'::DATE) ";
           */

        }else{
            //$sqlStrAddTotalTimeSpentAssignee = " created::date >= (CURRENT_DATE::date - INTERVAL '31 days')";//"and (created, created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime(date('Y-m-d').' - 30 days'))."'::DATE, '".date("Y-m-d 23:59:59",strtotime(date('Y-m-d')))."'::DATE) ";
            $sqlStrAddTotalTimeSpentAssignee = " Extract(month from to_char(startdate::date, '2YYY-MM-DD')::DATE) = Extract(month from CURRENT_DATE::date) and Extract(year from to_char(startdate::date, '2YYY-MM-DD')::DATE) = Extract(year from CURRENT_DATE::date)";//"and (created, created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime(date('Y-m-d').' - 30 days'))."'::DATE, '".date("Y-m-d 23:59:59",strtotime(date('Y-m-d')))."'::DATE) ";
            $sqlStrAddActiveProjects = "to_char(W.startdate::date, '2YYY-MM-DD')::DATE > (CURRENT_DATE::date -  '7 days'::INTERVAL )";
            $sqlStrAddTaskInProgess = "";
            $sqlStrAddTaskNoDues = "and created::date > (CURRENT_DATE::date - INTERVAL '31 days')";//"and created::date BETWEEN (now() - '31 days'::interval)::timestamp AND now()";
            $sqlUnestimated = "A.created::date > (CURRENT_DATE::date - INTERVAL '7 days')";
            $sqlStrProjectTasks = " and to_char(W.startdate::date, '2YYY-MM-DD')::DATE > (CURRENT_DATE::date -  '7 days'::INTERVAL )";
        }

        if($action == 'total-time-spent-assignee-last-month'){
            $sql = sprintf($sql,$sqlStrAddTotalTimeSpentAssignee);
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
        }elseif ($action == 'epic-data') {
            $sql = sprintf($sql, $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id'], $_REQUEST['id']);
        }elseif($action == 'projects-sprint'){
            $sql = sprintf($sql,$_REQUEST['id']);
        }elseif ($action == 'data-project-assignee-task') {
            $addsqlFormtoFilter = "and E.startdate::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and E.startdate::date < '".date("Y-m-d",strtotime($_REQUEST['to'].' +1 days'))."'::DATE ";
            $sql = sprintf($sql,$_REQUEST['pid'],$_REQUEST['uid'],$addsqlFormtoFilter);
        }
        
        
        #echo '2'.$sql;exit;
	$retData = pg_query($db, $sql);

	if(!$retData){
	   echo pg_last_error($db);
	} else {
	   //error_log( "Query successfully\n" );
	}

        foreach($sqlQueries[$action]['columns'] as $keycol => $columns) { 
            if($keycol == 'gotojira' || $keycol == 'profile_pic'){continue;}
            
            $columss[]= $columns;
        }
        // create a simple 2-dimensional array
        $xlsArray = array(
            1 => $columss,//array ('Assignee', 'Total Task','Original Time Estimate','Time Spent'),
            //array('Schwarz', 'Oliver'),
            //array('Test', 'Peter')
        );

	while($rowretData = pg_fetch_object($retData)){ 
		$ArrData = array();
		foreach($sqlQueries[$action]['columns'] as $keycol => $columns) { 
                    if($keycol == 'gotojira' || $keycol == 'profile_pic'){continue;}
                    
                    $ArrData[]= $rowretData->{$keycol} === null || $rowretData->{$keycol} == ' hrs' ?"-": $rowretData->{$keycol};
		}
		$xlsArray[] = $ArrData;
	
	}
//echo "<pre>";print_r($xlsArray);exit;

	#echo $sql;exit;

	// generate file (constructor parameters are optional)
        $xls = new Excel_XML('UTF-8', false, $action." - ". date('d M Y'));
        $xls->addArray($xlsArray);
        $xls->generateXML($action." - ". date('d M Y'));
} 



?>
