<?php 

        $IssueTypeColors= array(
                'Improvement'=>'#008080',
                'Task'=>'#9ee874', // ~ light green
                'New Feature'=>'#DB843D',  // ~ dark orange
                'Sub-task'=>'#ffc3a0',
                'Story'=>'#80699B', // ~ violet
                'Epic'=>'#464b92',
                'Technical task'=>'#A47D7C',
                'Issue'=>'#6d0b24',
                'Feature'=>'#0b6d52',
                'Bug'=>'#ff4444', //red
        );

	function secondsToTime($ss)
	{
	    	$str = '';
		$str .= floor($ss/2592000) ? "M": ''; //months
		$str .= floor(($ss%2592000)/86400) ? floor(($ss%2592000)/86400)."d": ''; //days
		$str .= floor(($ss%86400)/3600) ? floor(($ss%86400)/3600)."h": ''; //hours
		$str .= floor(($ss%3600)/60) ? floor(($ss%3600)/60)."m": ''; //minutes

		return $str;
	}
        
        function get_unixtimestamp($timestamp){
            $date = date('d/m/Y H:i:s',$timestamp/1000);
            return $date;
        }
        function get_unixtimestamp_date($timestamp){
            $date = date('d/m/Y',$timestamp/1000);
            return $date;
        }
        
        function issuestatusColor($status){
            $IssueStatusColors= array(
                    'In Progress'=>array('bg'=>'#ffd351','clr'=>'#594300','cstack'=>'#ffa700'), //V
                    'Open'=>array('bg'=>'#4a6785','clr'=>'#ffffff','cstack'=>'#2266aa'), //V
                    'Reopened'=>array('bg'=>'#4a6785','clr'=>'#ffffff','cstack'=>'#ff5555'),  //V
                    'Resolved'=>array('bg'=>'#14892c','clr'=>'#ffffff','cstack'=>'#13d538'), //V
                    'Closed'=>array('bg'=>'#14892c','clr'=>'#ffffff','cstack'=>'#009747'),
                    'In Review'=>array('bg'=>'#464b92','clr'=>'#ffffff','cstack'=>'#45e8a5'),
                    'To Do'=>array('bg'=>'#A47D7C','clr'=>'#ffffff','cstack'=>'#c43ad2'),
                    'Done'=>array('bg'=>'#6d0b24','clr'=>'#ffffff','cstack'=>'#750c41'),
            );
            
            return $IssueStatusColors[$status];
        }
        
        function issueIssueTypeImage($status){
            $IssueTypeImage= array(
                    'Improvement'=>'/images/icons/issuetypes/improvement.png',
                    'Task'=>'/images/icons/issuetypes/task.png', // ~ light green
                    'New Feature'=>'/images/icons/issuetypes/newfeature.png',  // ~ dark orange
                    'Sub-task'=>'/images/icons/issuetypes/subtask_alternate.png',
                    'Story'=>'/images/icons/issuetypes/genericissue.png', // ~ violet
                    'Epic'=>'/images/icons/ico_epic.png',
                    'Technical task'=>'/images/icons/ico_task.png',
                    'Issue'=>'#6d0b24',
                    'Feature'=>'#0b6d52',
                    'Bug'=>'/images/icons/issuetypes/bug.png', //red
            );
            
            return $GLOBALS['jira_url'].$IssueTypeImage[$status];
        }
        
        function datediffExWkDays($startDate,$endDate,$includeDays = array()){
            $start = strtotime($startDate);
            $end = strtotime($endDate);

            $count = 0;

            while(date('Y-m-d', $start) <= date('Y-m-d', $end)){
              if(in_array(date('N', $start), $includeDays)){
                  $count++;
              }else{
                  $count += date('N', $start) < 6 ? 1 : 0;
              }
              $start = strtotime("+1 day", $start);
            }

            return $count;
        }
        
        function datediffTime($startDate,$endDate,$includeDays=array()){
               
               if( (date('H',  strtotime($endDate)) <= 13 && date('dmY',  strtotime($startDate)) == date('dmY',  strtotime($endDate))) ||
                  (date('H',  strtotime($startDate)) >= 14 && date('dmY',  strtotime($startDate)) == date('dmY',  strtotime($endDate)))
               ){
                  $lunch = 0 ;
               }elseif(date('H',  strtotime($startDate)) <= 13 && date('H',  strtotime($endDate)) <= 13){
                  $lunch = datediffExWkDays($startDate,$endDate,$includeDays) - 1 ;
               }elseif(date('H',  strtotime($startDate)) <= 13 && date('H',  strtotime($endDate)) >= 13){
                  $lunch = datediffExWkDays($startDate,$endDate,$includeDays) ;
               }elseif(datediffExWkDays($startDate,$endDate,$includeDays) > 1 && date('H',  strtotime($startDate)) >= 13 && date('H',  strtotime($endDate)) <= 13){
                  $lunch = datediffExWkDays($startDate,$endDate,$includeDays) - 2 ;
               }elseif(datediffExWkDays($startDate,$endDate,$includeDays) > 1 && date('H',  strtotime($startDate)) >= 13 && date('H',  strtotime($endDate)) >= 13){
                  $lunch = datediffExWkDays($startDate,$endDate,$includeDays) - 1 ;
               }else{
                  $lunch = datediffExWkDays($startDate,$endDate,$includeDays);
               }
               
               $hours = 9 * (datediffExWkDays($startDate,$endDate,$includeDays) ) - $lunch - (date('H',  strtotime($startDate)) - 9) - (18-date('H',  strtotime($endDate)));
               $minutes =  date('i',  strtotime($endDate)) - date('i',  strtotime($startDate));

               if($minutes < 0){
                 $hours--;
                 $minutes += 60;
               }
               
               return sprintf('%02d', $hours).".".sprintf('%02d', $minutes);
        }
        
        /*function GetLeaveUserName($userName,$fromDate,$toDate){
        
            $gsql = 'select username, start_date , end_date , nb_hrs  from tbl_leaves where DATE(start_date) >="'.$fromDate.'" and DATE(end_date) <="'.$toDate.'" and username ="'.$userName.'"';
            $gretData = mysql_query($gsql);
            
            if(!empty($gretData)){
                while($LeaveData = mysql_fetch_object($gretData)) { 
                    $hrs += $LeaveData->nb_hrs;
                }
                return $hrs;
            }else{
                return 0;
            }
        }*/
        
        function GetLeaveUserName($userName,$fromDate,$toDate){
        
            //$gsql = 'select username, start_date , end_date , nb_hrs  from tbl_leaves where DATE(start_date) >="'.$fromDate.'" and DATE(end_date) <="'.$toDate.'" and username ="'.$userName.'"';
	          $gsql = 'select username, start_date , end_date , nb_hrs  from tbl_leaves where ((DATE(start_date) BETWEEN "'.$fromDate.'" and "'.$toDate.'") or (DATE(end_date) BETWEEN "'.$fromDate.'" and "'.$toDate.'"))  and username ="'.$userName.'"';

            $gretData = mysql_query($gsql);
            
            if(!empty($gretData)){
              $hrs = 0;
	            while($LeaveData = mysql_fetch_object($gretData)) { 
		            //Within the month
                	if(
	date('m',strtotime($fromDate)) == date('m',strtotime($LeaveData->start_date))  &&
	date('m',strtotime($toDate)) == date('m',strtotime($LeaveData->end_date))	){
	            		$hrs += (float) str_replace(':', '.', $LeaveData->nb_hrs);
		     	}else{
                        
		                $apStart = date('d-m-Y H:i:s',strtotime($LeaveData->start_date));
		                $apEnd = date('d-m-Y H:i:s',strtotime($LeaveData->end_date));

		                //previous to this or this to previous month
		                if(date('m',strtotime($LeaveData->start_date)) < date('m',strtotime($fromDate))){
		                    //previous applied
		                    $hrsData = datediffTime(date('d-m-Y 09:00:00',strtotime($fromDate)), $apEnd);
				    
		                    $hrs += (float) str_replace(':', '.', $hrsData);
		                }else{
		                    //applied this month till next
		                    $hrsData = datediffTime($apStart, date('d-m-Y 18:00:00',strtotime($apEnd)));			    
		                    $hrs += (float) str_replace(':', '.', $hrsData);
		                }
                }
	            }

              return $hrs;
            }else{
              return 0;
            }
        }
?>
