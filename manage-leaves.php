<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


    include 'mysqlconfig.php';
        
        /*
         * $includeDays array(6->Saturday & 7->Sunday)
         */
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
        
        /*function datediffTime($startDate,$endDate){
            
            if(datediffExWkDays($startDate,$endDate) != 1 || (datediffExWkDays($startDate,$endDate) >=1 && date('H',  strtotime($endDate)) >= 13 ) ){
               if(date('H',  strtotime($endDate)) <= 13){
                  $lunch = datediffExWkDays($startDate,$endDate) -1 ;
               }else if(date('H',  strtotime($startDate)) >= 14){
                  $lunch = 0;
               }else{
                  $lunch = datediffExWkDays($startDate,$endDate);
               }
            }else{
               $lunch = 0;
            }
            
            $hours = 9 * (datediffExWkDays($startDate,$endDate) ) - $lunch - (date('H',  strtotime($startDate)) - 9) - (18-date('H',  strtotime($endDate)));
            $hours +=  (int) (date('i',  strtotime($startDate)) + date('i',  strtotime($endDate))) / 60;
            $minutes = (date('i',  strtotime($endDate)) - date('i',  strtotime($startDate))) % 60 ;
            
            if($minutes < 0){
                $hours -= 1;
                $minutes = 60 + $minutes;
            }  elseif ($minutes > 0) {
                $hours += (int) $minutes/60;
                $minutes = $minutes % 60;
            }
            
            return sprintf('%02d', $hours).".".sprintf('%02d', $minutes);
        }*/
	
        function datediffTime($startDate,$endDate,$weekDays = array()){

               if( (date('H',  strtotime($endDate)) <= 13 && date('dmY',  strtotime($startDate)) == date('dmY',  strtotime($endDate))) ||
                  (date('H',  strtotime($startDate)) >= 14 && date('dmY',  strtotime($startDate)) == date('dmY',  strtotime($endDate)))
               ){
                  $lunch = 0 ;
               }elseif(date('H',  strtotime($startDate)) <= 13 && date('H',  strtotime($endDate)) <= 13){
                  $lunch = datediffExWkDays($startDate,$endDate,$weekDays) - 1 ;
               }elseif(date('H',  strtotime($startDate)) <= 13 && date('H',  strtotime($endDate)) >= 13){
                  $lunch = datediffExWkDays($startDate,$endDate,$weekDays) ;
               }elseif(datediffExWkDays($startDate,$endDate,$weekDays) > 1 && date('H',  strtotime($startDate)) >= 13 && date('H',  strtotime($endDate)) <= 13){
                  $lunch = datediffExWkDays($startDate,$endDate,$weekDays) - 2 ;
               }elseif(datediffExWkDays($startDate,$endDate,$weekDays) > 1 && date('H',  strtotime($startDate)) >= 13 && date('H',  strtotime($endDate)) >= 13){
                  $lunch = datediffExWkDays($startDate,$endDate,$weekDays) - 1 ;
               }else{
                  $lunch = datediffExWkDays($startDate,$endDate,$weekDays);
               }
               
                $hours = 9 * (datediffExWkDays($startDate,$endDate,$weekDays) ) - $lunch - (date('H',  strtotime($startDate)) - 9) - (18-date('H',  strtotime($endDate)));
                $sqlHoliday = "select * from tbl_holidays where DATE(date) between '".date('Y-m-d',  strtotime($startDate))."' and '".date('Y-m-d',  strtotime($endDate))."'";

                $gretData = mysql_query($sqlHoliday);
                while($Leave = mysql_fetch_object($gretData)) {
                    if($Leave->timespan == 1){
                        $hours = $hours - 8;
                    }elseif($Leave->timespan == 2){
                        $hours = $hours - 4;
                    }
                }

                
                $minutes =  date('i',  strtotime($endDate)) - date('i',  strtotime($startDate));

                if($minutes < 0){
                    $hours--;
                    $minutes += 60;
                }
                return sprintf('%02d', $hours).".".sprintf('%02d', $minutes);
        }
        
//Get all leaves
if(isset($_POST['username'],$_POST['get']) && $_POST['get']==1 && !empty($_POST['username']) ){
    
    $gsql = "select id,username,start_date,end_date,nb_hrs from tbl_leaves where username='".$_POST['username']."' order by added_at asc";
    $gretData = mysql_query($gsql);
    
    $returnArr = array();
    $html ='';
    
    while($Leave = mysql_fetch_object($gretData)) {
        $data =array();
        $data['id'] = base64_encode($Leave->id);
        $data['username'] = $Leave->username;
        $data['start_date'] = $Leave->start_date;
        $data['end_date'] = $Leave->end_date;
        $data['nb_hrs'] = $Leave->nb_hrs;
        
        $returnArr[] = $data;
        
        //
        $html .= '<tr id="'.md5($Leave->id).'">
                    <td>'.date('d-m-Y H:i',  strtotime($Leave->start_date)).'</td>
                    <td>'.date('d-m-Y H:i',  strtotime($Leave->end_date)).'</td>
                    <td>'.$Leave->nb_hrs.'</td>
                    <td>
			<!--a href="#" onclick="Edit(\''.base64_encode($Leave->id).'\')">Edit</a-->
			<a href="#" onclick="deleted(\''.base64_encode($Leave->id).'\')">Delete</a>
		    </td>
                </tr>';
    }
    
    if($html == ''){
        $html = 
                '<tr id="emptyR">
                    <td colspan="4"> No Leaves </td>
                </tr>';
    }
    
    echo json_encode(array('data'=>$returnArr,'html'=>$html));
    exit;
}

//Add leave

if(isset($_POST['username'],$_POST['from'],$_POST['to']) && !empty($_POST['username']) && !empty($_POST['from']) && !empty($_POST['to'] ) ){
    
    //calculate Hrs
    $fromDateL = date('Y-m-d H:i',  strtotime($_POST['from']));
    $toDateL = date('Y-m-d H:i',  strtotime($_POST['to']));
    $weekEnds = $_POST['weekends'];
    $nodaysL = datediffExWkDays($fromDateL,$toDateL,$weekEnds);
    //if(date('H:i',  strtotime($_POST['from'])) >  ){
    
    $time = str_replace('.',':',datediffTime($fromDateL,$toDateL,$weekEnds));
    
    $gsql = "INSERT INTO `tbl_leaves` (`id`, `username`, `start_date`, `end_date`, `nb_hrs`, `added_at`) "
            . "VALUES (NULL, '".$_POST['username']."', '".date('Y-m-d H:i:s',strtotime($_POST['from']))."', '".date('Y-m-d H:i:s',strtotime($_POST['to']))."', '".$time."', '".date('Y-m-d H:i:s')."');";
    $gretData = mysql_query($gsql);
    
    
    $html = '<tr id="'.md5(mysql_insert_id()).'">
                    <td>'.date('d-m-Y H:i',  strtotime($_POST['from'])).'</td>
                    <td>'.date('d-m-Y H:i',  strtotime($_POST['to'])).'</td>
                    <td>'.$time.'</td>
                    <td>
			<!--a href="#" onclick="Edit(\''.base64_encode(mysql_insert_id()).'\')">Edit</a-->
			<a href="#" onclick="deleted(\''.base64_encode(mysql_insert_id()).'\')">Delete</a>
			
		    </td>
                </tr>';
    
    if( $gretData ==1 ){
        echo json_encode(array('success'=>'OK','html'=>$html));
        exit;
    }else{
        echo json_encode(array('success'=>'FAIL'));
        exit;
    }
}

//Delete Leaves
if(isset($_POST['id'],$_POST['del']) && !empty($_POST['id']) && $_POST['del'] ==1 ){
    
    $gId = base64_decode($_POST['id']);
    
    $sql = "DELETE from `tbl_leaves` WHERE `id`='".$gId."'";
    
    $gretData = mysql_query($sql);
    
    if( $gretData ==1 ){
        echo json_encode(array('success'=>'OK','id'=>md5($gId)));
        exit;
    }else{
        echo json_encode(array('success'=>'FAIL'));
        exit;
    }
}

//Edit Leaves
if(isset($_POST['id'],$_POST['edit']) && !empty($_POST['id']) && $_POST['edit'] ==1 ){
    
    $gId = base64_decode($_POST['id']);
    
    $sql = "select * from `tbl_leaves` WHERE `id`='".$gId."'";
    
    $gretData = mysql_query($sql);
    $data =array();
    	while($Leave = mysql_fetch_object($gretData)) {
		
		$data['id'] = base64_encode($Leave->id);
		$data['username'] = $Leave->username;
		$data['start_date'] = $Leave->start_date;
		$data['end_date'] = $Leave->end_date;
		$data['nb_hrs'] = $Leave->nb_hrs;
	
	}
    if( isset($data) && !empty($data) ){
	
        echo json_encode(array('success'=>'OK','id'=>md5($gId),'data'=>$data)); 	
        exit;
    }else{
        echo json_encode(array('success'=>'FAIL'));
        exit;
    }
}
