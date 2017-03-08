<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include './../../config.php';
include './../../mysqlconfig.php';


$actionGoal = isset($_REQUEST['action']) && !empty($_REQUEST['action']) ? $_REQUEST['action']: null;
$gId = isset($_REQUEST['gid']) && !empty($_REQUEST['gid']) ? $_REQUEST['gid']: null;
$pId = isset($_REQUEST['pid']) && !empty($_REQUEST['pid']) ? $_REQUEST['pid']: null;
$sPrintId = isset($_REQUEST['sid']) && !empty($_REQUEST['sid']) ? $_REQUEST['sid']: null;
$UserId = isset($_REQUEST['uid']) && !empty($_REQUEST['uid']) ? $_REQUEST['uid']: null;
$goalText = isset($_REQUEST['goal']) && !empty($_REQUEST['goal']) ? $_REQUEST['goal']: '';

//Check Data from Project Worklog.
$projectContent = isset($_REQUEST['project']) && !empty($_REQUEST['project']) ? $_REQUEST['project']: null;

//comment
$messageComment = isset($_REQUEST['message']) && !empty($_REQUEST['message']) ? $_REQUEST['message']: '';

/*
 * 1->create 
 * 2->Update 
 * 3->change status as Done/Not Done 
 * 4->Delete 
 * 5->comment
 * 6->Archive Goal
 */



if($actionGoal != 3 && $actionGoal != 4 && $actionGoal != 6){
    //not delete i.e create/update
    if($sPrintId !== null){
        $type = 1;
        $referenceid = $sPrintId;
    }elseif ($UserId !== NULL) {
        $pId = null;
        $type = 2;
        $referenceid = $UserId;
    }elseif ($projectContent !== null) {
        $type = 3;
        $referenceid = $pId;
    }
        if($actionGoal == 1){
            
            $sql = "INSERT INTO `tbl_goals` (`id`, `project_id`, `type`, `referenceid`, `goal`, `status`,`archived`,`archived_date`, `added_by`,`updated_by`, `added_at`, `updated_at`) "
                    . "VALUES (NULL, '".$pId."', '".$type."', '".$referenceid."', '".mysql_real_escape_string($goalText)."', '2','0',null, '".$_SESSION['uid']."',null, '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."');";
            
            $html = "<li data-id='%s' class='li-data-%s'>
                        <!-- drag handle -->
                        <span class='handle'>
                            <i class='fa fa-ellipsis-v'></i>
                            <i class='fa fa-ellipsis-v'></i>
                        </span>
                        <!-- checkbox -->
                        <input type='checkbox' name='' value='%s' class='goal-list'>
                        <!-- todo text -->
                        <span class='text'>".$goalText."</span>
                        <br>
                        <!-- Emphasis label -->
                        <small class='label custom label-primary'><i class='fa fa-clock-o'></i> ".ucfirst($_SESSION['username']).' '.date('d/m/Y H:i')."</small>
                        <!-- General tools such as edit or delete-->
                        <div class='tools'>
                            <i class='fa fa-edit'></i>
                            <i class='fa fa-trash-o'></i>
                        </div>
                    </li>";
        }elseif($actionGoal == 2){
            
            $sql = "UPDATE `tbl_goals` SET `goal`='".$goalText."',`updated_by`='".$_SESSION['uid']."',`updated_at`='".date('Y-m-d H:i:s')."' WHERE `id`=".$gId;
        }elseif ($actionGoal == 5) {
            //Add Comment
            $sql = "INSERT INTO `tbl_comments` (`id`, `project_id`, `type`, `reference_id`, `added_by`, `message`, `added_at`, `updated_at`) "
                    . "VALUES (NULL, '".$pId."', '".$type."', '".$referenceid."', '".$_SESSION['uid']."', '".$messageComment."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."');";
            
            $html = '<div class="item">
                                        <img src="'.HOST_NAME.'img/avatar.png" alt="user image" class="online"/>
                                        <p class="message">
                                            <a href="javascript:void(0);" class="name">
                                                <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> '.date('d/m/Y H:i').' </small>
                                                '.  ucfirst($_SESSION['username']) .'
                                            </a>
                                            '.$messageComment.'
                                        </p>
                                        
                                    </div>';
        }

}elseif ($actionGoal == 3 ) {
    //change status
    $sql = "UPDATE `tbl_goals` SET `status`='".$_REQUEST['status']."',`updated_by`='".$_SESSION['uid']."',`updated_at`='".date('Y-m-d H:i:s')."' WHERE `id`=".$gId;
}elseif($actionGoal == 4){
    //delete
    $sql = "DELETE from `tbl_goals` WHERE `id`='".$gId."'";
}elseif($actionGoal == 6){
    //archieve
    $sql = "update `tbl_goals` SET `archived`='1', `archived_date`='".date('Y-m-d H:i:s')."' WHERE `id` in (".$gId.")";
}
#echo $sql;exit;

$retData = mysql_query($sql);

if($retData == 1){
    if(isset($html)){
        $id = mysql_insert_id();
        $html = sprintf($html,$id,$id,$id);
    }else{
        $html='';
    }
    
    echo json_encode(array('success'=>'OK','html'=>$html));
}else{
    echo json_encode(array('success'=>'FAIL'));
}