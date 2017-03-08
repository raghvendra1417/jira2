<?php 

require_once('config.php');

// Unestimated Task

$sql = $sqlQueries['unestimated_task']['count_query'];
$retData = pg_query($db, $sql);
	
if(!$retData){
   echo pg_last_error($db);
} else {
   //error_log( "Query successfully\n" );
}

while($fetch = pg_fetch_assoc($retData))
{
	$output['unestimted_task_count'] = $fetch['unestimted_task_count']; 
}

//No of Active Projects
$sql3 = $sqlQueries['task_in_progress']['query'];
$retData3 = pg_query($db, $sql3);
	
if(!$retData3){
   echo pg_last_error($db);
} else {
   //error_log( "Query successfully\n" );
}

while($fetch3 = pg_fetch_assoc($retData3))
{
	$output['task_inprogress'] = $fetch3['task_inprogress']; 
}

//No of Active Projects
$sql2 = $sqlQueries['active_projects']['query'];
$retData2 = pg_query($db, $sql2);
	
if(!$retData2){
   echo pg_last_error($db);
} else {
   //error_log( "Query successfully\n" );
}

while($fetch2 = pg_fetch_assoc($retData2))
{
	$output['project'] = $fetch2['project']; 
}

echo json_encode($output);exit;


?>
