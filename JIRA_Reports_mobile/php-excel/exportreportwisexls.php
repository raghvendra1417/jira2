<?php

// load library
require 'php-excel.class.php';

// create a simple 2-dimensional array
$xlsArray = array(
        1 => array ('Assignee', 'Total Task','Original Time Estimate','Time Spent'),
        //array('Schwarz', 'Oliver'),
        //array('Test', 'Peter')
        );

	/******************************************* include Config *************************************/
        include './../config.php';
	include './../mysqlconfig.php';

if (in_array($action,$actionArr) && $_REQUEST['id']) {

	$sql = $sqlQueries[$action]['query'];
        
        if(isset($_REQUEST['from'],$_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to'])){
            $sqlStrAdd = "and w_created >= '".date("Y-m-d 00:00:00",strtotime($_REQUEST['from']))."' and w_created <= '".date("Y-m-d 23:59:59",strtotime($_REQUEST['to']))."' ";
        }else{
            $sqlStrAdd = '';
        }

        $sql = sprintf($sql,$_REQUEST['id'],$sqlStrAdd);
	//$sql = sprintf($sql,$_REQUEST['id']);
        
	$retData = mysql_query($sql);

	if(!$retData){
	   echo mysql_error($db);
	} else {
	   //error_log( "Query successfully\n" );
	}

	while($rowretData = mysql_fetch_object($retData)){ 
		$ArrData = array();
		foreach($sqlQueries[$action]['columns'] as $keycol => $columns) { 
			$ArrData[]= $rowretData->{$keycol} === null ?"-": $rowretData->{$keycol};
		}
		$xlsArray[] = $ArrData;
	
	}
//echo "<pre>";print_r($xlsArray);exit;

	$sqlProjectNameSql = $sqlQueries['getProjectName']['query'];
	$sqlProjectNameSql = sprintf($sqlProjectNameSql,$_REQUEST['id']);

	$retProjectName = pg_query($db, $sqlProjectNameSql);

	if(!$retProjectName){
	   echo pg_last_error($db);
	} else {
	   //error_log( "Project Name Query successfully\n") ;
	}
	while($rowProjectName = pg_fetch_object($retProjectName)){
		$projectName = $rowProjectName->pname;
	}
	#echo $sql;exit;
	
        
    // generate file (constructor parameters are optional)
    $xls = new Excel_XML('UTF-8', false, $projectName." - ". date('d M Y'));
    $xls->addArray($xlsArray);
    $xls->generateXML($projectName." - ". date('d M Y'));
	
} 


?>
