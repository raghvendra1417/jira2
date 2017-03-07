<?php

/*
 *      Note : This is Not a cron report
 */

/** Error reporting */
error_reporting(E_ALL);


/** PHPExcel */
include '../PHPExcel/Classes/PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
include '../PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';


/******************************************* include Config *************************************/
include '../config.php';

$Queries = array(
    'Active-project-this-month'=>array(
        'query'=>"SELECT project,count(A.project) as task_count,B.pname as pname,B.lead as lead
					from jiraissue as A
					inner join project B
					on A.project=B.id
					where Extract(month from updated) = Extract(month from Now()) and Extract(year from updated) = Extract(year from Now())
					group by pname,lead,project",
        'columns'=>array('pname'=>'Project Name','lead'=>'Lead','task_count'=>'No of task'),
    ),
    'Assignee-not-recorded-timespent-this-month'=>array(
        'queryt'=>"SELECT distinct(W.author) as user_names,sum(timeworked) as timeworked
                    FROM worklog W
                    inner join cwd_user U on U.active = '1'
                    WHERE Extract(month from W.created) = Extract(month from Now() - INTERVAL '7 days') and Extract(year from W.created) = Extract(year from Now() - INTERVAL '7 days')
                    group by W.author
                  ",
        'query2Feb'=>"select B.user_key as user_names ,(select sum(S.timeworked) from worklog S where B.lower_user_name = S.author and Extract(month from S.created) = Extract(month from Now() - INTERVAL '7 days') and Extract(year from S.created) = Extract(year from Now() - INTERVAL '7 days')) as count_hrs
                    from cwd_user U 
                        inner join app_user B on U.user_name=B.lower_user_name 
                        where U.active = '1' and B.lower_user_name NOT IN (
                            SELECT distinct(W.author) 
                                FROM worklog W 
                                WHERE Extract(month from W.created) = Extract(month from Now() - INTERVAL '7 days') and Extract(year from W.created) = Extract(year from Now() - INTERVAL '7 days')
                        ) 
                        order by B.user_key",
        'queryDDDD'=>
                "select TO_CHAR((sum(A.timeworked) || ' second')::interval, 'HH24:MI') as timeworked ,B.user_name as user_names
                 from worklog A 
                 right join cwd_user B on A.author=B.user_name
                 where B.active=1 and (Extract(month from A.created) is null or (Extract(month from A.created) = Extract(month from Now() - INTERVAL '7 days') and Extract(year from A.created) = Extract(year from Now() - INTERVAL '7 days') ))
                 group by B.user_name order by B.user_name",
        'query'=>"select TO_CHAR((sum(C.timeworked) || ' second')::interval, 'HH24:MI') as timeworked "
        . "             ,B.user_name as user_names"
        . "             ,B.active as active "
        . "         from app_user A "
        . "         inner join cwd_user B on A.lower_user_name=B.user_name and B.active= '1'"
        . "         left join worklog C on C.author=A.user_key "
        . "         where "
        . "             Extract(month from C.created) = 2 "
        . "             and Extract(year from C.created) = 2015 "
        . "             or Extract(month from C.created) is null "
        . "         group by B.user_name ,B.active "
        . "         order by B.user_name",
        'queryGG'=>"select sum(A.timeworked),B.lower_user_name "
                    . "from worklog A "
                    . "right join app_user B on A.author=B.lower_user_name "
                    . "inner join cwd_user U on U.user_name = B.user_key "
                    . "where U.active = 1 and ((Extract(month from A.created) = 11 and Extract(year from A.created) = 2014) or Extract(month from A.created) is null) group by B.lower_user_name",
        'query0'=>"select B.user_key as user_names ,
                    from cwd_user U 
                        inner join app_user B on U.user_name=B.lower_user_name 
                        where U.active = '1' and B.lower_user_name NOT IN (
                            SELECT distinct(W.author), 
                                FROM worklog W 
                                WHERE Extract(month from W.created) = Extract(month from Now() - INTERVAL '7 days') and Extract(year from W.created) = Extract(year from Now() - INTERVAL '7 days')
                        ) 
                        order by B.user_key",
        'query2'=>"select
                        DISTINCT(E.author)
                        from jiraissue A
                        inner join worklog E on E.issueid=A.id
                        where Extract(month from E.created) = Extract(month from Now()) and Extract(year from E.created) = Extract(year from Now())
                    
                    ",
        'query3'=>"SELECT distinct(W.author)
                                                    FROM worklog W 
                                                    WHERE Extract(month from W.created) = Extract(month from Now()) and Extract(year from W.created) = Extract(year from Now())
                   ",
        
        'columns'=>array('user_names'=>'User Name','timeworked'=>'Hrs','active'=>'Active'),
        //'columns'=>array('pname'=>'Project Name','lead'=>'lead','task_count'=>'No of Task','timeorginalestimate'=>'Time Estimate','timespent'=>'Time Spent'),
    ),
    'Projects-epic-created-this-month'=>array(
        'query'=>"SELECT B.pname as pname 
                    from jiraissue A 
                    inner join project B on A.project=B.id 
                    INNER JOIN issuelink l ON  A.id = l.source and l.linktype = '10200' 
                    where A.issuetype='10000' and Extract(month from A.created) = Extract(month from Now()) and Extract(year from A.created) = Extract(year from Now())
                    group by B.pname",
        
        'columns'=>array('pname'=>'Project Name'),
    ),
    'Project-have-created-new-task-this-month'=>array(
        'query'=>"SELECT pname
                    FROM jiraissue as A 
                    inner join project B
                    on A.project=B.id 
                    WHERE Extract(month from A.created) = Extract(month from Now()) and Extract(year from A.created) = Extract(year from Now()) 
                    group by pname",
        'columns'=>array('pname'=>'Project Name'),
    ),
    /*'User-not-logged-Jira-this-month'=>array(
        'query'=>"select user_name from cwd_user 
                        where active = '1' and user_name NOT IN ( SELECT username 
                    from userhistoryitem 
                    where Extract(month from lastviewed::int4::abstime::date) = Extract(month from Now()) and Extract(year from lastviewed::int4::abstime::date) = Extract(year from Now()) 
                    and Extract(year from lastviewed) = Extract(year from Now())
                    )"
    )*/
);
//$retData = pg_query($db, $Queries['Assignee-not-recorded-timespent-this-month']['query2']);
//echo "<pre>";
//           while($rowretData = pg_fetch_object($retData)) {
//                print_r($rowretData);
//           }exit;

foreach ($Queries as $action => $sqlQueries) {
    $sql = $sqlQueries['query'];
    $xlsArray = array();
    $retData = pg_query($db, $sql);

	if(!$retData){
	   echo pg_last_error($db);
	} else {
	   //error_log( "Query successfully\n" );
	}
        
        $firstColumn = 'A';
        foreach($sqlQueries['columns'] as $keycol => $columns) { 
                $xlsArray[$firstColumn.'1']= $columns;
                $firstColumn++;
        }
        //$lastColumn = $firstColumn--;

        $i=2;
	while($rowretData = pg_fetch_object($retData)){ 
		$ArrData = array();
                $fColumn = 'A';
                
		foreach($sqlQueries['columns'] as $keycol => $columns) { 
			$xlsArray[$fColumn.$i]= $rowretData->{$keycol} === null ?"0": $rowretData->{$keycol};
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
        /*$objPHPExcel->getActiveSheet()->getStyle($excelkey)->getFill()->applyFromArray(
            array(
                'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'D8C967'),
            )
        );*/
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

    $fileName = 'Week_Report_'.$action.date('_d_M_Y').'.xlsx';

    // Save Excel 2007 file
    //echo date('H:i:s') . " Write to Excel2007 format\n";
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($fileName);
}


