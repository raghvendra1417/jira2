<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include 'config.php';
include 'mysqlconfig.php';

if(isset($_REQUEST['username']) ){
    //$sql = "select * from tbl_users where username='".mysql_real_escape_string($_REQUEST['username'])."' and status='1'";
    $username = trim($_REQUEST['username']);
    
    $mapped_username = array('shivang'=>'shivang.ahuja','pavitra.k'=>'pavithra.k');

    $alt_username = in_array($username,$mapped_username) ? array_search($username,$mapped_username): $username;
    
    $sql = "select * from cwd_membership c, cwd_user u where c.parent_name= 'jira-users' and u.active=1 and c.child_name=u.user_name and u.user_name='".$username."'";
    //$retData = mysql_query($sql);
    $retData = pg_query($db, $sql);
    
    $found =0;
    while($rowretData = pg_fetch_object($retData)){
        
        #echo "<pre>";print_r($rowretData);exit;
        $found =1;
        
        $redirect = 'index.php';
        
        $sql = "select *,u.id as id,r.role from tbl_users u,tbl_user_roles r where r.user_id = u.id and u.username='".mysql_real_escape_string($username)."' and u.status='1'";
        $retDataMY = mysql_query($sql);
        if(mysql_num_rows($retDataMY) > 0){
            //already exists and role defined.
            
            while($rowretDataMY = mysql_fetch_object($retDataMY)){
                
                //get Roles
                $role_sql = "select * from tbl_user_roles where user_id='".$rowretDataMY->id."' and status='1'";
                $retDataMYRole = mysql_query($role_sql);
                $roles = array();
                while($rowretDataMYRole = mysql_fetch_object($retDataMYRole)){
                    $roles[] = $rowretDataMYRole->role;
                }
                
                //Getting Menu.
                $sql1 = "select * from  tbl_role_permissions where role_id='".$rowretDataMY->role."' and status='1'";
                $retData1 = mysql_query($sql1);  
                $AssignedPer = array();
                 while($rowretData1 = mysql_fetch_object($retData1)){
                    $AssignedPer[] = $rowretData1->permission;
                 }
                
                $_SESSION['uid']= $rowretDataMY->id;
                $_SESSION['username']= $rowretDataMY->username;
                $_SESSION['alt_username']= $rowretDataMY->alt_username;
                $_SESSION['role_permission'] = $AssignedPer;
                $_SESSION['user_role'] = $roles;
                sort($roles);
                $_SESSION['highest_role'] = $roles[0];
                
                //Redirection page
                if($roles[0] == 2){ // Project Manager/BA
                    $redirect = HOST_NAME.'index.php';
                }elseif($roles[0] == 3){ // Project Manager
                    $redirect = HOST_NAME.'index.php';
                }elseif($roles[0] == 4){ //Team Leads
                    $redirect = HOST_NAME.'index.php';
                }elseif($roles[0] == 5){ //Dev/Tester
                    $redirect = HOST_NAME.'pages/tables/user-time-spent.php?username='.$alt_username.'&from='.date('M 1,Y').'&to='.date('M t,Y');
                }
            }

        }else{
            //insert the user to mysql database as default Employee.
            $user_sql = "INSERT INTO tbl_users (username,alt_username,status,added_at) VALUES ('".mysql_real_escape_string($username)."','".mysql_real_escape_string($alt_username)."','1','now()')";
            $retData1 = mysql_query($user_sql);
            
            $user_id = mysql_insert_id();
            
            $userrole_sql = "INSERT INTO tbl_user_roles (user_id,role,status,added_at) VALUES ('".$user_id."','5','1','now()')";
            $retDataURI = mysql_query($userrole_sql);
            
            //Getting Menu.
            $sqlGT = "select * from  tbl_role_permissions where role_id='5' and status='1'";
            $retDataGT = mysql_query($sqlGT);  
            $AssignedPer = array();
            while($rowretData2 = mysql_fetch_object($retDataGT)){
               $AssignedPer[] = $rowretData2->permission;
            }
            
            $sqlGTU = "select *,u.id as id,r.role from tbl_users u,tbl_user_roles r where r.user_id = u.id and u.username='".mysql_real_escape_string($username)."' and u.status='1'";
            $retDataMYN = mysql_query($sqlGTU);
            $_SESSION['uid']= $retDataMYN->id;
            $_SESSION['username']= $retDataMYN->username;
            $_SESSION['alt_username']= $retDataMYN->alt_username;
            $_SESSION['role_permission'] = $AssignedPer;
            $_SESSION['user_role'] = array(0=>5);
            $_SESSION['highest_role'] = '5';
            
            $redirect = HOST_NAME.'pages/tables/user-time-spent.php?username='.$alt_username.'&from='.date('M 1,Y').'&to='.date('M t,Y');
        }
    }

    if($found){
        echo json_encode(array('code'=>1,'redirectUrl'=> $redirect));
        exit;
    }else{
        echo json_encode(array('code'=>0,'msg'=>'Invalid Username'));
        exit;
    }
}
?>

