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

$sqlSprintT ='select wk.*,ji.timeestimate as jira_timeestimate from worklog wk inner join "jiraissue" as ji on wk.issueid = ji.id 
    where ji.id in (select 
                        i.id as issues
                    from
                        "jiraissue"  as i
                        inner join "customfieldvalue" as c on c.customfield = 10002
                        inner join "project" as p on p.id = i.project
                        inner join "AO_60DB71_SPRINT" as s on CAST(c.stringvalue AS BIGINT) = s."ID"

                    where 
                        p.id = %s and s."ID" = %s and c.issue = i.id) 
                    %s;
    ';
$shortquery = ' and to_char(wk.startdate::date, "2YYY-MM-DD")::DATE >= %s and to_char(wk.startdate::date, "2YYY-MM-DD")::DATE <= %s';
$shortquery = sprintf($sqlSprintT,$finalStartDate,$finalEndDate);
$sqlSprintT = sprintf($sqlSprintT,$project_id,$sPrintId,$shortquery);

$retDaysPlanData = pg_query($db, $sqlSprintT);

error_log('start:'.$finalStartDate.'  , end:'.$finalEndDate);

$issues = array();
$totalestimate = 0;
while($rowProject = pg_fetch_object($retDaysPlanData)){ 
    
    //echo $rowProject->status;
    //echo "<pre>";print_r((array) $rowProject);exit;
    $issues['estimate'][$rowProject->issueid] = $rowProject->jira_timeestimate;
    $totalestimate += $issues['estimate'][$rowProject->issueid];
    if(isset($issues['spent'][date('Y-m-d', strtotime($rowProject->startdate))])){
        $issues['spent'][date('Y-m-d', strtotime($rowProject->startdate))] += $rowProject->timeworked;
    }else{
        $issues['spent'][date('Y-m-d', strtotime($rowProject->startdate))] = $rowProject->timeworked;
    }
}

//echo count($issues);exit;
    $rst = $reportStartDate  = date('Y-m-d',  strtotime($finalStartDate));
    $rend = $reportEndDate  = date('Y-m-d',  strtotime($finalEndDate));

    $dataArr = array(0=>array('e'=>gmdate('H.i',$totalestimate),'s'=>gmdate('H.i',$totalestimate)));
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
        $totalestimate1 = $totalestimate1 - $totalestimate1/$nb_days_sprint;
        $dataArr[date('Y-m-d',  strtotime($reportStartDate))]['e']= gmdate('H.i',$totalestimate1);
        $dataArr[date('Y-m-d',  strtotime($reportStartDate))]['s']= gmdate('H.i',$issues['spent'][date('Y-m-d',  strtotime($reportStartDate))]);
        $nb_days_sprint = $nb_days_sprint-1;
        $reportStartDate = date('Y-m-d',  strtotime($reportStartDate.' + 1 day'));
    }

    echo json_encode($dataArr);
