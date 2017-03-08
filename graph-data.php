<?php 

require_once('config.php');

//No of task Inprogress

$sql = $sqlQueries['this_weekchart_dashboard']['query'];
//$sql = sprintf( $sql,date("Y-m-d",strtotime('monday this week')) );

$retData = pg_query($db, $sql);
	
if(!$retData){
   echo pg_last_error($db);
} else {
   //error_log( "Query successfully\n" );
}

while($fetch = pg_fetch_assoc($retData))
{
	if($fetch['timespent'] !== null && $fetch['timespent'] != 0 ) {
		$output['y'] = $fetch['pnames']; 
		$output['a'] = $fetch['timespent'];
	
		$response[] = $output;
	}
}
echo json_encode($response);exit;

