<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'config.php';
include 'mysqlconfig.php';

$sql = $sqlQueries['jiraworklogdata']['query'];
#$sql = "select * from jiraissue where assignee like 'peter.pawan' AND project = 11901";
$retData = pg_query($db, $sql);
	
if(!$retData){
   echo pg_last_error($db);
} else {
   //error_log( "Query successfully\n" );
}

/*echo "<pre>";
           while($rowretData = pg_fetch_object($retData)) {
                print_r($rowretData);
           }exit;*/

$sql = "INSERT INTO `jira_worklog_issue` (
                `id` ,
                `w_issueid` ,
                `w_author` ,
                `w_timeworked` ,
                `w_created` ,
                `w_updated` ,
                `i_projectid` ,
                `i_timeorgestimate`,
                `i_timeremaining`,
                `created_at`
                ) VALUES";

$issueIds = array();

while($fetch = pg_fetch_assoc($retData))
{
    
    /*if(isset($issueIds[$fetch['issueid']]) && in_array($fetch['author'], $issueIds[$fetch['issueid']])){
        $fetch['estimate']='';
        $fetch['remaining']='';
    }
    $issueIds[$fetch['issueid']][] = $fetch['author'];*/
    
    
    $sql .="(
            NULL , '".$fetch['issueid']."', '".$fetch['author']."', '".$fetch['timeworked']."', '".date('Y-m-d H:i:s',strtotime($fetch['created']))."','".date('20y-m-d H:i:s',strtotime($fetch['updated']))."', '".$fetch['projectid']."','".$fetch['estimate']."','".$fetch['remaining']."', '".date('Y-m-d H:i:s')."'
            ),";
    
    
}
//echo in_array('satyendra.kumar', $issueIds['22076']);
//echo "<pre>";print_r($issueIds);exit;
$sql = substr($sql, 0,-1).';';



$result = mysql_query('TRUNCATE TABLE `jira_worklog_issue`') or die(mysql_error());

$result1 = mysql_query($sql) or die(mysql_error());

if($result1){
    echo "Data Imported Successfully";
}else{
    echo "Data Imported Failed";
}
