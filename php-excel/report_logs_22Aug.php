<?php

// load library
require 'php-excel.class.php';

// create a simple 2-dimensional array
$xlsArray = array(
            0 => array ('firstCol'=>"",'assignee'=>'', 'timespent'=>'','holiday'=>'','leave'=>'','missing'=>''),
            1 => array ('firstCol'=>"S.No",'assignee'=>'Assignee', 'timespent'=>'Time Spent (Hrs)','holiday'=>'Holiday (days)','leave'=>'Leave (days)','missing'=>'Missing days(Hours)'),
        //array('Schwarz', 'Oliver'),
        //array('Test', 'Peter')
        );

	/******************************************* include Config *************************************/
        include './../config.php';
	include './../mysqlconfig.php';
$action = 'total-time-spent-assignee-last-month-leave-holiday';
if (in_array($action,$actionArr)) {

	$sql = $sqlQueries[$action]['query'];
	
	$from = $_GET['from'];
    	$to = $_GET['to'];

        if(isset($from,$to) && !empty($from) && !empty($to)){
	    
	    $fromDateWD = date("d-m-Y",strtotime($from));
	    $toDateWD = date("d-m-Y",strtotime($to));

            $sqlStrAddTotalTimeSpentAssignee = " and  to_char(startdate::date, '2YYY-MM-DD')::DATE >= '".date("Y-m-d",strtotime($fromDateWD))."'::DATE and to_char(startdate::date, '2YYY-MM-DD')::DATE < '".date("Y-m-d",strtotime($toDateWD.' +1 days'))."'::DATE ";
            
	    $totalDaysWkHr = 8 * datediffExWkDays(date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',strtotime($toDateWD).' -1 days'));


	    $Objholiday = mysql_query('SELECT count(*) as holiday_days from tbl_holidays WHERE date BETWEEN "'.date("Y-m-d",strtotime($fromDateWD)).'" AND "'.date("Y-m-d",strtotime($toDateWD)).'"');
        }else{

	    //$fromDateWD = date('d-');
	    //$toDateWD = "01-$month-$year";
            $sqlStrAddTotalTimeSpentAssignee = " and  Extract(month from to_char(startdate::date, '2YYY-MM-DD')::DATE) = Extract(month from CURRENT_DATE::date) and Extract(year from to_char(startdate::date, '2YYY-MM-DD')::DATE) = Extract(year from CURRENT_DATE::date)";//"and (created, created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime(date('Y-m-d').' - 30 days'))."'::DATE, '".date("Y-m-d 23:59:59",strtotime(date('Y-m-d')))."'::DATE) ";
            $totalDaysWkHr = 8 * datediffExWkDays(date('Y-m-01'),date('Y-m-d'));
	    
	    $Objholiday = mysql_query('SELECT count(*) as holiday_days from tbl_holidays WHERE date BETWEEN "'.date("Y-m-01").'" AND "'.date("Y-m-d").'"');
        }

        $xlsArray[0]['timespent'] = "Total Hours :".$totalDaysWkHr;
        
        $sqlPostSql = sprintf($sql,$sqlStrAddTotalTimeSpentAssignee);
	
	$retPostData = pg_query($db, $sqlPostSql);

	if(!$retPostData){
	   echo pg_last_error($db);
	} else {
	   //error_log( "Project Name Query successfully\n") ;
	}
	
        //holiday
        
        
        
        
        while($rowretData = mysql_fetch_object($Objholiday)){ 
            
            $holidays = $rowretData->holiday_days ;
	
	}
        
        $i=1;
	if($fromDateWD == 0 && $toDateWD == 0){
	    	$toDateWD = date('Y-m-d');
		$fromDateWD = date('Y-m-01');
	}

        while($retPostData2 = pg_fetch_object($retPostData)){
            //print_r($retPostData2);exit;
            $rowData['firstCol'] = $i;
            $rowData['assignee'] = $retPostData2->assignee;
            $rowData['timespent'] = $retPostData2->timespent;
            $rowData['holiday'] = $holidays;

	    
	    //echo $rowData['assignee'].'---'.GetLeaveUserName($retPostData2->assignee,date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',strtotime($toDateWD)));
            $rowData['leave'] = GetLeaveUserName($retPostData2->assignee,date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',strtotime($toDateWD))) > 0 ? (GetLeaveUserName($retPostData2->assignee,date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',strtotime($toDateWD)))/8) ."(".GetLeaveUserName($retPostData2->assignee,date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',strtotime($toDateWD)))." Hrs )":"";
	    $missing = $totalDaysWkHr - $rowData['timespent'] - ($holidays * 8) - GetLeaveUserName($retPostData2->assignee,date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',strtotime($toDateWD)));
            $rowData['missing'] = $missing > 0 ? ($missing/8)."(".$missing." Hrs)" :"";
            $xlsArray[] = $rowData;
	    $i++;
	}
	//echo "<pre>";print_r($xlsArray);exit;
    $xlsArray[] = array ('firstCol'=>"-------",'assignee'=>"-------", 'timespent'=>'-------','holiday'=>"-------",'leave'=>"-------",'missing'=>"-------");
    $xlsArray[] = array ('firstCol'=>"-------",'assignee'=>"-------", 'timespent'=>'Defaulters','holiday'=>"-------",'leave'=>"-------",'missing'=>"-------");

	$sql = "select * from cwd_membership c, cwd_user u where c.parent_name= 'jira-users' and u.active=1 and c.child_name=u.user_name";
	$retDaysPlanData = pg_query($db, $sql);
	$k = 0;
	while($rowProject = pg_fetch_object($retDaysPlanData)){
	    $modelAdminTitleIndex = array_search($rowProject->child_name, array_column($xlsArray, 'assignee'));
	    if($modelAdminTitleIndex === false){
		$k++;
		    //$AllempUN[]=$rowProject->user_name;
		    $xlsArray[] = array ('firstCol'=>$k,'assignee'=>"-------", 'timespent'=>$rowProject->child_name,'holiday'=>"-------",'leave'=>"-------",'missing'=>"-------");
	    }
	}
    

    // generate file (constructor parameters are optional)
    $xls = new Excel_XML('UTF-8', false, date('M')." - ". date('Y'));
    $xls->addArray($xlsArray);
    $xls->generateXML(date("d M",strtotime($fromDateWD))." - ".date("d M",strtotime($toDateWD))." - ". date('Y'));
	
} 


?>
