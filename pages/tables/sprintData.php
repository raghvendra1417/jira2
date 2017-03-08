<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('max_execution_time', 300);

include './../../config.php';
//include './../../common-functions.php';

$sPrintId = $_POST['sprint_id'];
$project_id = $_POST['project_id'];

$sqlSprintData = 'select '
                    . 'TO_CHAR(TO_TIMESTAMP(s."START_DATE"/1000),\'2YYY-MM-DD HH24:MI:SS\') as start_date,'
                    . 'TO_CHAR(TO_TIMESTAMP(s."END_DATE"/1000),\'2YYY-MM-DD HH24:MI:SS\') as end_date,'
                    . 'TO_CHAR(TO_TIMESTAMP(s."COMPLETE_DATE"/1000),\'2YYY-MM-DD HH24:MI:SS\') as complete_date,'
                    . '(CASE WHEN(s."CLOSED") THEN 1 ELSE 0 END) as status '
                    . 'from "AO_60DB71_SPRINT" s where s."ID" = '.$sPrintId;

$retDaysPlanData = pg_query($db, $sqlSprintData);

while($rowProject = pg_fetch_object($retDaysPlanData)){ 
    
    $startDate = $rowProject->start_date ? date('Y-m-d',  strtotime($rowProject->start_date)):'';
    $endDate = $rowProject->end_date ? date('Y-m-d',  strtotime($rowProject->end_date)):'';
    $completeDate = $rowProject->complete_date ? date('Y-m-d',  strtotime($rowProject->complete_date)):'';
    
    if($rowProject->status){
        //Closed
        $finalStartDate = $startDate;
        $finalEndDate = $completeDate;
        
    }else{
        //Open
        $finalStartDate = $startDate;
        $finalEndDate = $endDate;
    }
    
}


function task_subtask($db,$project_id,$sPrintId)
{

    $jiraissueSQL = 'select 
                        i.id as issues,
			i.timeoriginalestimate as jira_timeestimate,
			i.issuetype as issuetype,
			i.issuenum as issuenum
                    from
                        "jiraissue"  as i
                        inner join "customfieldvalue" as c on c.customfield = 10002
                        inner join "project" as p on p.id = i.project
                        inner join "AO_60DB71_SPRINT" as s on CAST(c.stringvalue AS BIGINT) = s."ID"
                        
                    where 
                        p.id = '.$project_id.' and s."ID" = '.$sPrintId.' and c.issue = i.id';

    $retIssues = pg_query($db, $jiraissueSQL);

    $issues = array();
    $subtaskiSprint = array();
    while($rowIssue = pg_fetch_object($retIssues)){ 
        if($rowIssue->issuetype != 5){
		$issues['ids'][]=$rowIssue->issues;
		//$issues['jira_timeestimate'] += $rowIssue->jira_timeestimate;
		$issues['jira_issue_est'][$rowIssue->issuenum] = $rowIssue->jira_timeestimate;///(3600);
        }else{
		$subtaskiSprint['ids'][]=$rowIssue->issues;
	}
    }
    //print_r($issues);  echo "subtaskiSprint";  print_r($subtaskiSprint);exit;
    if(isset($issues['ids']) && !empty($issues['ids'])){
	    $subQ = "";
	    if(!empty($subtaskiSprint['ids'])){
		$subQ= "(i.destination in (".trim(implode(',', $subtaskiSprint['ids']),',').")) or ";
            }

	    $subtaskIssues = "select 
		                i.destination as issues
		              from issuelink as i 
                              inner join jiraissue as jis on jis.id = i.source
                              inner join customfieldvalue as c on (c.customfield = 10002 and i.source = c.issue and CAST(c.stringvalue AS BIGINT) = '".$sPrintId."')
		              where ( $subQ (i.source in (".trim(implode(',', $issues['ids']),',').")))
                              and i.linktype='10100'";

	    $retOIssues = pg_query($db, $subtaskIssues);

	    $subtasks = array();
	    while($rowOIssue = pg_fetch_object($retOIssues)){ 
		$subtasks[] = $rowOIssue->issues;
	    }

	    if(!empty($subtasks)){
	       $jiraissueSubSQL = 'select 
		                i.id as issues,
				i.timeoriginalestimate as jira_timeestimate,
				i.issuenum as issuenum
		            from
		                "jiraissue"  as i
		               
		            where 
		                i.id in ('.trim(implode(',', $subtasks),',').')';

		$retIssues2 = pg_query($db, $jiraissueSubSQL);
	
		while($rowIssue2 = pg_fetch_object($retIssues2)){ 
		   $issues['ids'][]=$rowIssue2->issues;
		   //$issues['jira_timeestimate'] += $rowIssue2->jira_timeestimate;
		   $issues['jira_issue_est'][$rowIssue2->issuenum] = $rowIssue2->jira_timeestimate;///(3600);
	    	}
	    }
    }

    return $issues;
}

$task_subtaskdata = task_subtask($db,$project_id,$sPrintId);
//print_r($task_subtaskdata);exit;
$issuesFinal = trim(implode(',',$task_subtaskdata['ids']),',');


$sqlSprintT ="select wk.*,ji.timeoriginalestimate as jira_timeestimate from worklog wk inner join jiraissue as ji on wk.issueid = ji.id 
    where ji.id in (".$issuesFinal.") and to_char(wk.startdate::date, '2YYY-MM-DD')::DATE >= '".$finalStartDate."' and to_char(wk.startdate::date, '2YYY-MM-DD')::DATE <= '".$finalEndDate."';";


$retDaysPlanData = pg_query($db, $sqlSprintT);

//error_log('start:'.$finalStartDate.'  , end:'.$finalEndDate);

$issues = array();

$totalestimate = array_sum($task_subtaskdata['jira_issue_est'])/3600;

while($rowProject = pg_fetch_object($retDaysPlanData)){ 
    

    //$issues['estimate'][$rowProject->issueid] = $rowProject->jira_timeestimate;
    //$totalestimate += $issues['estimate'][$rowProject->issueid];
    if(isset($issues['spent'][date('Y-m-d', strtotime($rowProject->startdate))])){
        $issues['spent'][date('Y-m-d', strtotime($rowProject->startdate))] += $rowProject->timeworked;
    }else{
        $issues['spent'][date('Y-m-d', strtotime($rowProject->startdate))] = $rowProject->timeworked;
    }
}



    $rst = $reportStartDate  = date('Y-m-d',  strtotime($finalStartDate));
    $rend = $reportEndDate  = date('Y-m-d',  strtotime($finalEndDate));

    $dataArr = array(0=>array('e'=>$totalestimate,'s'=>$totalestimate));
    $totalestimate1 = $totalestimate;
    $nb_days_sprint = 0;

    while(date('Y-m-d', strtotime($rst)) <= date('Y-m-d', strtotime($rend))){
        $nb_days_sprint += (date('N', strtotime($rst)) <= 6 ? 1 : 0);
        $rst = date('Y-m-d', strtotime($rst." +1 day"));
    }

    for ( ;date('Y-m-d',  strtotime($reportStartDate)) <= date('Y-m-d',  strtotime($reportEndDate)); ) {
        
        if(date('N', strtotime($reportStartDate)) == 7){
            $reportStartDate = date('Y-m-d',  strtotime($reportStartDate.' + 1 day'));
            continue;
        }
        $totalestimate1 = $totalestimate1 - ($totalestimate1/$nb_days_sprint);
        $dataArr[date('Y-m-d',  strtotime($reportStartDate))]['e']= round($totalestimate1,2);
        $dataArr[date('Y-m-d',  strtotime($reportStartDate))]['s']= gmdate('H.i',$issues['spent'][date('Y-m-d',  strtotime($reportStartDate))]);
        $nb_days_sprint = $nb_days_sprint-1;
        $reportStartDate = date('Y-m-d',  strtotime($reportStartDate.' + 1 day'));
    }

    echo json_encode($dataArr);
