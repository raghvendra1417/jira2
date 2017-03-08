<?php

// load library
require 'php-excel.class.php';

// create a simple 2-dimensional array
    $xlsArray = array();
	
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

	        $toDateWD = date('Y-m-d');
            $fromDateWD = date('Y-m-01');
            $sqlStrAddTotalTimeSpentAssignee = " and  Extract(month from to_char(startdate::date, '2YYY-MM-DD')::DATE) = Extract(month from CURRENT_DATE::date) and Extract(year from to_char(startdate::date, '2YYY-MM-DD')::DATE) = Extract(year from CURRENT_DATE::date)";//"and (created, created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime(date('Y-m-d').' - 30 days'))."'::DATE, '".date("Y-m-d 23:59:59",strtotime(date('Y-m-d')))."'::DATE) ";
            $totalDaysWkHr = 8 * datediffExWkDays(date('Y-m-01'),date('Y-m-d'));
	    
	        $Objholiday = mysql_query('SELECT count(*) as holiday_days from tbl_holidays WHERE date BETWEEN "'.date("Y-m-01").'" AND "'.date("Y-m-d").'"');

        }


        
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
		$legal_assignee = array();
        $log_groups = array();
        while($retPostData2 = pg_fetch_object($retPostData)){
            //print_r($retPostData2);exit;
            if(!array_key_exists($retPostData2->parent_name, $xlsArray)){
                $log_groups[$retPostData2->parent_name] = array('name'=>$retPostData2->parent_name,'count'=>1);
                $xlsArray[$retPostData2->parent_name][] = array ('firstCol'=>"",'assignee'=>'', 'timespent'=>"Total Work Hours :".($totalDaysWkHr - $holidays * 8),'holiday'=>'','leave'=>'','missing'=>'','per_def'=>'');
                $xlsArray[$retPostData2->parent_name][] = array ('firstCol'=>"S.No",'assignee'=>'Associate Name', 'timespent'=>'Time Logged  (Hours)','holiday'=>'Holiday (Days)','leave'=>'Leave (Days)','missing'=>'Missing Log(Hours)','per_def'=>"% Of Deviation \n >10% dev is in red");
                
            }
                $rowData['firstCol'] = $log_groups[$retPostData2->parent_name]['count'];
                $rowData['assignee'] = $retPostData2->assignee;
                $rowData['timespent'] = $retPostData2->timespent;
                $rowData['holiday'] = $holidays;

        	    
    	        //$missing = $totalDaysWkHr - $rowData['timespent'] - ($holidays * 8) - GetLeaveUserName($retPostData2->assignee,date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',strtotime($toDateWD)));

                $fromDate = $fromDateWD;
                $toDate = $toDateWD;
                $username = $retPostData2->assignee;
    	        $nodays=(strtotime($toDate) - strtotime($fromDate))/ (60 * 60 * 24);

                $mapped_username = array('rajesh.e'=>'rajesh','buvaneswaran'=>'buvaneswaran.m','nagraj'=>'nagraj.h','taufique.ammanagi'=>'taufique.a');
    	        $leave_username = in_array($username,array_keys($mapped_username)) ? $mapped_username[$username]: $username;
	    
	            if(in_array($leave_username,$legal_assignee)){ continue; }

                $legal_assignee[] = $leave_username;
		
                /*$rowData['leave'] = GetLeaveUserName($leave_username,date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',strtotime($toDateWD))) > 0 ? (GetLeaveUserName($leave_username,date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',strtotime($toDateWD)))/8) ."(".GetLeaveUserName($leave_username,date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',strtotime($toDateWD)))." Hrs )":"";*/
	    
                $rowData['leave'] = GetLeaveUserName($leave_username,date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',strtotime($toDateWD))) > 0 ? GetLeaveUserName($leave_username,date('Y-m-d',strtotime($fromDateWD)),date('Y-m-d',strtotime($toDateWD)))." Hrs":"";

		
	              if($nodays <= 32){

                    //date format
                    $dateFormat = "DD-MM-2YYY";
                    $phpdateFormat = "d-m-Y";
                    $incTime = ' +1 days';
                    $categFormat = 'd D M';
                        
                }elseif($nodays <= 365){
                    
                        //month format
                        $dateFormat = "MM-2YYY";
                        $phpdateFormat = "m-Y";
                        $incTime = ' +1 month';
                        $categFormat = 'M Y';
                        
                }else{

                        //year format
                        $dateFormat = "2YYY";
                        $phpdateFormat = "Y";
                        $incTime = ' +1 year';
                        $categFormat = 'Y';
                        
                }


                //get data from database
                $utsQl = $sqlQueries['worklog-user-timespent-filter-graph']['query'];
                
                // Query substitution
                $utsQl = sprintf($utsQl,
                            $dateFormat,
                            date('Y-m-d',strtotime($fromDate)),
                            date('Y-m-d',strtotime($toDate)),
                            $username,
                            $dateFormat,
                            $dateFormat
                    );

                $retDataWorklogd = pg_query($db, $utsQl);

                $datetimeWorkedAll = array();
                while($retDataWorklogAllv = pg_fetch_object($retDataWorklogd)){ 
                    $datetimeWorkedAll[$retDataWorklogAllv->datemonthyear] = $retDataWorklogAllv->worked;
                }
            
                //bug category Query 
                
                $utsQ2 = $sqlQueries['worklog-user-timespent-filter-graph']['bugquery'];
                
                // Query substitution
                $utsQ2 = sprintf($utsQ2,
                            $dateFormat,
                            date('Y-m-d',strtotime($fromDate)),
                            date('Y-m-d',strtotime($toDate)),
                            $username,
                            $dateFormat,
                            $dateFormat
                    );

                $retDataWorklogBug = pg_query($db, $utsQ2);
                
                
                $datetimeWorkedBug = array();
                while($retDataWorklogBugv = pg_fetch_object($retDataWorklogBug)){ 
                    $datetimeWorkedBug[$retDataWorklogBugv->datemonthyear] = $retDataWorklogBugv->worked;
                }


                //Leaves Query
                $gsql = "select id,username,start_date,end_date,nb_hrs from tbl_leaves where username='".$leave_username."' order by added_at asc";
                $gretData = mysql_query($gsql);
                $datetimeLeaves = array();
                #print_r($datetimeWorkedAll);exit;
                while($Leave = mysql_fetch_object($gretData)) {
                    if(date('Y-m-d',strtotime($Leave->start_date)) == date('Y-m-d',strtotime($Leave->end_date))){
                        //same day leave
                        $datetimeLeaves[date('d-m-Y',strtotime($Leave->start_date))] = str_replace(':', '.', $Leave->nb_hrs);
                    }else{
                        //group leave
                        $startData = date('Y-m-d',  strtotime($Leave->start_date));
                        while($startData <= date('Y-m-d',strtotime($Leave->end_date))){
    						if( date('N',strtotime($startData)) < 6 ){
                                if( $startData != date('Y-m-d',strtotime($Leave->end_date)) ){
                                    $datetimeLeaves[date('d-m-Y',strtotime($startData))] = 8;
                                }else{
                                    $datetimeLeaves[date('d-m-Y',strtotime($startData))] = date('H',strtotime($Leave->end_date)) <= 14 ? 8-(18-date('H',strtotime($Leave->end_date)))+1 : 8-(18-date('H',strtotime($Leave->end_date)));
                                }
    						}
                            $startData = date('Y-m-d',  strtotime($startData.' +1 days'));
                        }
                    }
                }
                //Holiday query
                $holidaysql=mysql_query('SELECT date from tbl_holidays WHERE date BETWEEN "'.date("Y-m-d",strtotime($fromDate)).'" AND "'.date("Y-m-d",strtotime($toDate)).'"');
                $datetimeHolidays = array();
                while($holiday = mysql_fetch_object($holidaysql)) {
                         $datetimeHolidays[date('d-m-Y',strtotime($holiday->date))]=8;                            
                 }
                //Graph categories and values
                $missingHrs = 0;
                
                for (;strtotime($fromDate) <= strtotime($toDate); ) { 
                	
                	if(date('N',strtotime($fromDate)) > 5){ $fromDate = date('d-m-Y', strtotime($fromDate." +1 day")); continue; } //no log on saturday & Sundays
                	
                	$dayAllworkLog = (float) ( isset($datetimeWorkedAll[date($phpdateFormat,  strtotime($fromDate))]) ? $datetimeWorkedAll[date($phpdateFormat,  strtotime($fromDate))]: 0 );
                	$dayAllworkLogHm = explode('.', number_format($dayAllworkLog,2,'.',''));
                	$dayAllworkLog2 = $dayAllworkLogHm[0]*3600 + $dayAllworkLogHm[1]* 60; 
                	//$dayBug = (float) ( isset($datetimeWorkedBug[date($phpdateFormat,  strtotime($fromDate))]) ? $datetimeWorkedBug[date($phpdateFormat,  strtotime($fromDate))]: 0 );
                	$dayleave = (float) ( isset($datetimeLeaves[date($phpdateFormat,  strtotime($fromDate))]) ? $datetimeLeaves[date($phpdateFormat,  strtotime($fromDate))]: 0 );
                	$dayleaveHm = explode('.', number_format($dayleave,2,'.',''));
                	$dayleave2 = $dayleaveHm[0]*3600 + $dayleaveHm[1]* 60;

                	$dayholiday = (float) ( isset($datetimeHolidays[date($phpdateFormat,  strtotime($fromDate))]) ? $datetimeHolidays[date($phpdateFormat,  strtotime($fromDate))]: 0 );
                	$dayholidayHm = explode('.', number_format($dayholiday,2,'.',''));
                	$dayholiday2 = $dayholidayHm[0]*3600 + $dayholidayHm[1]* 60;
                	
                	$total = $dayAllworkLog2 + $dayleave2 + $dayholiday2;
                	if($total < 8*60*60){
                		$missingHrs += 8*60*60 - $total;
                	}
                	
                	$fromDate = date('d-m-Y', strtotime($fromDate.$incTime));
                }
                //if($username == 'abhishek.pant'){
                 //	echo gmdate("H:i",$missingHrs)."holiday";print_r($datetimeWorkedAll);exit;
                //}
        	    $init = $missingHrs;
        	    $hours = floor($init / 3600);
        	    $minutes = floor(($init / 60) % 60);
                //echo $username." -- ".($missingHrs)."(".gmdate("H:i",$missingHrs)." Hrs)";exit;
                $rowData['missing'] = $missingHrs > 0 ? "$hours:$minutes Hrs" :"";
                $rowData['per_def'] = round(($missingHrs/(($totalDaysWkHr - $holidays * 8)*60*60))*100,2);
                /*if($leave_username == 'nagraj.h'){
                  echo "$dateFormat  ->  $phpdateFormat -> $incTime -> $categFormat";
                  echo "<pre>";print_r($rowData);exit;
                }*/
                $xlsArray[$retPostData2->parent_name][] = $rowData;
        	    $log_groups[$retPostData2->parent_name]['count']++;
	        }
	        //echo "<pre>";print_r($xlsArray);exit;
        	$sql = "select * from cwd_membership c, cwd_user u where c.parent_name in ('Bengaluru','Belgaum','Noida','Mumbai') and u.active=1 and c.child_name=u.user_name";
        	$retDaysPlanData = pg_query($db, $sql);
        	
            $groups = array();
        	while($rowProject = pg_fetch_object($retDaysPlanData)){
        	    //$modelAdminTitleIndex = array_search($rowProject->child_name, array_column($xlsArray, 'legal_assignee'));
        	    if(!in_array($rowProject->child_name,$legal_assignee)){
        		    
                    if(!in_array($rowProject->parent_name, array_column($groups,'name'))){
                        $groups[$rowProject->parent_name] = array('name'=>$rowProject->parent_name,'count'=>1);
                        $xlsArray[$rowProject->parent_name][] = array ('firstCol'=>"-------",'assignee'=>"-------", 'timespent'=>'-------','holiday'=>"-------",'leave'=>"-------",'missing'=>"-------",'per_def'=>"");
                        $xlsArray[$rowProject->parent_name][] = array ('firstCol'=>"",'assignee'=>"", 'timespent'=>'Defaulters','holiday'=>"",'leave'=>"",'missing'=>"",'per_def'=>"");
                    }
                    
        		    //$AllempUN[]=$rowProject->user_name;
        		    $xlsArray[$rowProject->parent_name][] = array ('firstCol'=>$groups[$rowProject->parent_name]['count'],'assignee'=>$rowProject->child_name, 'timespent'=>"",'holiday'=>"",'leave'=>"",'missing'=>"",'per_def'=>"");
                    $groups[$rowProject->parent_name]['count']++;
        	    }
        	}
    
    //echo "<pre>";print_r($xlsArray);exit;
    // generate file (constructor parameters are optional)
    //$xls = new Excel_XML('UTF-8', false, date('M',strtotime($fromDateWD))." - ". date('Y'));
    //$xls->addArray($xlsArray);
    //$xls->generateXML(date("d M",strtotime($fromDateWD))." - ".date("d M",strtotime($toDateWD))." - ". date('Y'));
	
    require_once dirname(__FILE__) . '/../PHPExcel/Classes/PHPExcel.php';

    function cellColor($cells,$color){
        global $objPHPExcel;

        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                 'rgb' => $color
            )
        ));

    }


    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Techtree IT")
                 ->setLastModifiedBy("Maarten Balliauw")
                 ->setTitle("Office 2007 XLSX Test Document")
                 ->setSubject("Office 2007 XLSX Test Document")
                 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                 ->setKeywords("office 2007 openxml php")
                 ->setCategory("Test result file");
    $sheetNum = 0;
    
    $borderArray = array(
      'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'D3D3D3')
         )
      )
    );
    foreach ($xlsArray as $City => $data) {
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex($sheetNum);
        $objPHPExcel->getActiveSheet()->setTitle($City);
        $rowNum = 1;

        foreach ($data as $acvalue) {
            $firstChar = 'A';
            foreach ($acvalue as $finalData) {
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension($firstChar)
                    ->setAutoSize(true);
                $objPHPExcel->getActiveSheet($sheetNum)->setCellValue($firstChar.$rowNum, $finalData);
                $objPHPExcel->getActiveSheet()->getStyle($firstChar.$rowNum)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle($firstChar.$rowNum)->applyFromArray($borderArray);

                if($firstChar == 'G' && $finalData >= 10){
                    cellColor($firstChar.$rowNum,'F28A8C');
                }elseif($rowNum <= 2){
                    cellColor($firstChar.$rowNum,'A9D08E');
                }
                $firstChar++;
            }
            $objPHPExcel->getActiveSheet()->getRowDimension($rowNum)->setRowHeight(25);
            $rowNum++;

        }
        
        $sheetNum++;
    }
    


    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.date("d M",strtotime($fromDateWD))." to ".date("d M",strtotime($toDateWD))."_(". date('Y').')_Enterprise.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
} 


?>
