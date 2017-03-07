<?php 

require_once('config.php');

//No of task Inprogress

$sql = $sqlQueries['task_in_progress']['query'];
$retData = pg_query($db, $sql);
	
if(!$retData){
   echo pg_last_error($db);
} else {
   //error_log( "Query successfully\n" );
}

while($fetch = pg_fetch_assoc($retData))
{
	$output['task_inprogress'] = $fetch['task_inprogress']; 
}

//No Task Users
$sql2 = $sqlQueries['users-no-task-today']['count_query'];
$retData2 = pg_query($db, $sql2);
	
if(!$retData2){
   echo pg_last_error($db);
} else {
   //error_log( "Query successfully\n" );
}

while($fetch2 = pg_fetch_assoc($retData2))
{
	$output['user_no_task_today'] = $fetch2['user_no_task']; 
}

echo json_encode($output);exit;


?>
