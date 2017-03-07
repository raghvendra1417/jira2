<?php


/** Error reporting */
error_reporting(E_ALL);


/** PHPExcel */
include '../PHPExcel/Classes/PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
include '../PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';


/******************************************* include Config *************************************/
include '../config.php';

$actionsData = array(
                'task-in-progess'=>'Tasks in Progress'
                ,'active-projects'=>'Active Projects'
                ,'active-users'=>'Active Users'
                ,'users-no-task-today'=>'Users without task'
                ,'total-time-spent-assignee-last-month'=>'Time Spent - Users'
                ,'task-no-duedate'=>'Tasks without Due Date'
                ,'unestimated_task'=>'Unestimated Tasks'
            );

$action = isset($argv['1']) && $argv['1']!='' ? $argv['1']: (isset($_REQUEST['action'])?$_REQUEST['action']:'');

$timespan = isset($argv['2']) && $argv['2']!='' ? $argv['2']: (isset($_REQUEST['timespan'])?$_REQUEST['timespan']:'');

if (in_array($action,$actionArr)) {
    
	$sql = $sqlQueries[$action]['query'];
	//$sql = sprintf($sql,$_REQUEST['id']);

        
        if( isset($timespan) && $timespan !='' ){
            
            $from = date('Y-m-d',  strtotime(date('Y-m-d')."- $timespan days"));
            $to = date('Y-m-d');
            
//            $sqlStrAddTotalTimeSpentAssignee = "and (resolutiondate, resolutiondate) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($from))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($to))."'::DATE) ";
//            $sqlStrAddActiveProjects = "(updated, updated) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($from))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($to))."'::DATE) ";
//            $sqlStrAddTaskInProgess = "and (A.created, A.created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($from))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($to))."'::DATE) ";
//            $sqlStrAddTaskNoDues = "and (A.created, A.created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($from))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($to))."'::DATE) ";
//            $sqlUnestimated = "(A.created, A.created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime($from))."'::DATE, '".date("Y-m-d 23:59:59",strtotime($to))."'::DATE) ";
            
            $sqlStrAddTotalTimeSpentAssignee = " created::date >= '".date("Y-m-d",strtotime($from))."'::DATE and created::date < '".date("Y-m-d",strtotime($to.' +1 days'))."'::DATE ";
            $sqlStrAddActiveProjects = "updated::date >= '".date("Y-m-d",strtotime($from))."'::DATE and updated::date < '".date("Y-m-d",strtotime($to.' +1 days'))."'::DATE ";
            $sqlStrAddTaskInProgess = "and A.created::date >= '".date("Y-m-d",strtotime($from))."'::DATE and created::date < '".date("Y-m-d",strtotime($to.' +1 days'))."'::DATE ";
            $sqlStrAddTaskNoDues = "and A.created::date >= '".date("Y-m-d",strtotime($from))."'::DATE and created::date < '".date("Y-m-d",strtotime($to.' +1 days'))."'::DATE ";
            $sqlUnestimated = "A.created::date >= '".date("Y-m-d",strtotime($from))."'::DATE and created::date < '".date("Y-m-d",strtotime($to.' +1 days'))."'::DATE ";
        }else{
//            $sqlStrAddTotalTimeSpentAssignee = "and resolutiondate::date BETWEEN (now() - '31 days'::interval)::timestamp AND now()";
//            $sqlStrAddActiveProjects = "updated::date > (CURRENT_DATE::date -  '7 days'::INTERVAL )";
//            $sqlStrAddTaskInProgess = "";
//            $sqlStrAddTaskNoDues = "and created::date BETWEEN (now() - '31 days'::interval)::timestamp AND now()";
//            $sqlUnestimated = "A.created > (CURRENT_DATE::date - INTERVAL '7 days')";
            
            //$sqlStrAddTotalTimeSpentAssignee = " created::date >= (CURRENT_DATE::date - INTERVAL '31 days')";//"and (created, created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime(date('Y-m-d').' - 30 days'))."'::DATE, '".date("Y-m-d 23:59:59",strtotime(date('Y-m-d')))."'::DATE) ";
            $sqlStrAddTotalTimeSpentAssignee = " Extract(month from created) = Extract(month from CURRENT_DATE::date)";//"and (created, created) OVERLAPS ('".date("Y-m-d 00:00:00",strtotime(date('Y-m-d').' - 30 days'))."'::DATE, '".date("Y-m-d 23:59:59",strtotime(date('Y-m-d')))."'::DATE) ";
            $sqlStrAddActiveProjects = "updated::date > (CURRENT_DATE::date -  '7 days'::INTERVAL )";
            $sqlStrAddTaskInProgess = "";
            $sqlStrAddTaskNoDues = "and created::date >= (CURRENT_DATE::date - INTERVAL '31 days')";//"and created::date BETWEEN (now() - '31 days'::interval)::timestamp AND now()";
            $sqlUnestimated = "A.created::date >= (CURRENT_DATE::date - INTERVAL '7 days')";
        }

        if($action == 'total-time-spent-assignee-last-month'){
            $sql = sprintf($sql,$sqlStrAddTotalTimeSpentAssignee);
        }elseif ($action == 'active-projects') {
            $sql = sprintf($sql,$sqlStrAddActiveProjects);
        }elseif ($action =='task-in-progess') {
            $sql = sprintf($sql,$sqlStrAddTaskInProgess);
        }elseif ($action == 'task-no-duedate') {
            $sql = sprintf($sql,$sqlStrAddTaskNoDues);
        }elseif ($action == 'unestimated_task') {
            $sql = sprintf($sql,$sqlUnestimated);
        }
        
        #echo '2'.$sql;exit;
	$retData = pg_query($db, $sql);

	if(!$retData){
	   echo pg_last_error($db);
	} else {
	   //error_log( "Query successfully\n" );
	}
        
        $firstColumn = 'A';
        foreach($sqlQueries[$action]['columns'] as $keycol => $columns) { 
                $xlsArray[$firstColumn.'1']= $columns;
                $firstColumn++;
        }
        //$lastColumn = $firstColumn--;

        $i=2;
	while($rowretData = pg_fetch_object($retData)){ 
		$ArrData = array();
                $fColumn = 'A';
                
		foreach($sqlQueries[$action]['columns'] as $keycol => $columns) { 
			$xlsArray[$fColumn.$i]= $rowretData->{$keycol} === null ?"-": $rowretData->{$keycol};
                        $fColumn++;
		}
		//$xlsArray[] = $ArrData;
                $i++;
	
	}

#echo "<pre>";print_r($xlsArray);exit;






    // Create new PHPExcel object
    //echo date('H:i:s') . " Create new PHPExcel object\n";
    $objPHPExcel = new PHPExcel();

    // Set properties
    //echo date('H:i:s') . " Set properties\n";
    $objPHPExcel->getProperties()->setCreator("Techtree IT");
    $objPHPExcel->getProperties()->setLastModifiedBy("Techtree IT");
    $objPHPExcel->getProperties()->setTitle("Techtree IT");
    $objPHPExcel->getProperties()->setSubject("Techtree IT");
    $objPHPExcel->getProperties()->setDescription("Techtree IT");


    // Add some data
    //echo date('H:i:s') . " Add some data\n";
    $objPHPExcel->setActiveSheetIndex(0);

    foreach ($xlsArray as $excelkey => $excelvalue) {
        $objPHPExcel->getActiveSheet()->SetCellValue($excelkey, $excelvalue);    

        //Color the excel content
        $objPHPExcel->getActiveSheet()->getStyle($excelkey)->getFill()->applyFromArray(
            array(
                'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'D8C967'),
            )
        );
    }

    $lastCol = substr($excelkey, 0,1);

    // Rename sheet
    //echo date('H:i:s') . " Rename sheet\n";
    $objPHPExcel->getActiveSheet()->setTitle('Week Report');

    //Set all colums to their width
    foreach(range('A',$lastCol) as $columnID) {
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
            ->setAutoSize(true);
    }

    //Set horizontally centered data. 
    $objPHPExcel->getActiveSheet()->getStyle("A:$lastCol")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    //Color the excel titles
    $objPHPExcel->getActiveSheet()->getStyle('A1:'.$lastCol.'1')->getFill()->applyFromArray(
        array(
            'type'       => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'D35427'),
        )
    );

    $fileName = 'Week_Report_'.$actionsData[$action].date('_d_M_Y').'.xlsx';

    // Save Excel 2007 file
    //echo date('H:i:s') . " Write to Excel2007 format\n";
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($fileName);

    // Echo done
    //echo date('H:i:s') . " Done writing file.\r\n";

    /***************************** Send Mail ************************************/

    /**
     * This example shows sending a message using a local sendmail binary.
     */

    require '../PHPMailer-master/PHPMailerAutoload.php';

    //Create a new PHPMailer instance
    $mail = new PHPMailer;
    // Set PHPMailer to use the sendmail transport
    $mail->isSMTP();                                      // Set mailer to use SMTP
    
    
    $mail->Host = 'smtp.gmail.com';                       // Specify main and backup server
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'tt.683.2015@gmail.com';                   // SMTP username
    $mail->Password = 'Techtreeit@123';               // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
    $mail->Port = 587;     
   
    //Set who the message is to be sent from
    $mail->setFrom('tt.683.2015@gmail.com', 'Jira Admin');
    //Set an alternative reply-to address
    $mail->addReplyTo('replyto@jiraadmin.com', 'Jira Admin');

    //Set who the message is to be sent to
    $mail->addAddress('raghvendra.yadav@techtreeit.com', 'Raghvendra Yadav');
    
    $mail->addCC('pritpal.s@techtreeit.com');
    $mail->addCC('murali.kg@techtreeit.com');
    //Set the subject line
    $mail->Subject = 'Jira Admin Report : '.$actionsData[$action].date(' d M Y');
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $mail->msgHTML('Hi,  PFA');
    //Replace the plain text body with one created manually
    $mail->AltBody = 'This is a plain-text message body';
    //Attach an image file
    $mail->addAttachment($fileName);

    $mail->isHTML(true); 
    //send the message, check for errors
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message sent!";
        #unlink($fileName);
    }


}  else {
    echo "Parameters Missing";
}