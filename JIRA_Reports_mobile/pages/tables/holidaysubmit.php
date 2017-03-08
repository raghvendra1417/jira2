<?php

/* * ***************************************** include Config ************************************ */
include './../../config.php';
include './../../mysqlconfig.php';

if (isset($_REQUEST['date']) &&!isset($_REQUEST['edit_id'])) {
   
    $newDate = date("Y-m-d H:i:s", strtotime($_REQUEST['date']));
    $sql = "INSERT INTO tbl_holidays (event_name,date,timespan,added_by,added_at,status) values('" . mysql_real_escape_string($_REQUEST['event_name']) . "','" . mysql_real_escape_string($newDate) . "','" . mysql_real_escape_string($_REQUEST['timespan']) . "','" . $_SESSION['uid'] . "',now(),1)";
    $retData = mysql_query($sql);
    echo 100;
} 

if (isset($_REQUEST['edit_id'])) {
    
    $newDate = date("Y-m-d H:i:s", strtotime($_REQUEST['date']));
    $sql = "update tbl_holidays set timespan='".mysql_real_escape_string($_REQUEST['timespan'])."',event_name='".mysql_real_escape_string($_REQUEST['event_name'])."', date='".mysql_real_escape_string($newDate)."' where id='".$_REQUEST['edit_id']."'";
    $retData = mysql_query($sql);
    echo 100;
} 

if(isset($_REQUEST['delete_id'])){
    $sql = "delete from tbl_holidays where id='".$_REQUEST['delete_id']."'";
    $retData = mysql_query($sql); 
    echo "<script>window.location.href='".HOST_NAME."pages/tables/holiday.php?view=holiday'</script>";
    exit;
}
?>