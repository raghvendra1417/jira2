<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'config.php';
include 'mysqlconfig.php';

if( !isset( $_SESSION['uid'] , $_SESSION['username'] ) && empty($_SESSION['uid']) && empty($_SESSION['username'])){
    
    $_SESSION['uri'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   
    //echo "<script>window.location.href='login.php'</script>";
    //exit;
}

if(isset($_REQUEST['username'],$_REQUEST['password']) ){
    $sql = "select *,r.role from tbl_users,tbl_user_roles r where r.user_id = tbl_users.id and username='".mysql_real_escape_string($_REQUEST['username'])."' and password='".mysql_real_escape_string($_REQUEST['password'])."'";
    $retData = mysql_query($sql);
    $found =0;
    
    while($rowretData = mysql_fetch_object($retData)){
        
        //Getting User roles.
        $sql1 = "select * from  tbl_role_permissions where role_id='".$rowretData->role."' and status='1'";
        $retData1 = mysql_query($sql1);  
        $AssignedPer = array();
        while($rowretData1 = mysql_fetch_object($retData1)){
            $AssignedPer[] = $rowretData1->permission;
        }

        $found =1;
        $redirect = 'index.php';
        
        
        $_SESSION['uid']= $rowretData->id;
        $_SESSION['username']= $rowretData->username;
        $_SESSION['alt_username']= $rowretData->username;
        $_SESSION['role_permission'] = $AssignedPer; 
        $_SESSION['user_role'] = array(0=>1);//array(0=>1)
        $_SESSION['highest_role'] = '1';        
    }

    if($found){
        echo json_encode(array('code'=>1,'redirectUrl'=> $redirect));
        exit;
    }else{
        echo json_encode(array('code'=>0,'msg'=>'Invalid Username and Password'));
        exit;
    }
}
?>
<!DOCTYPE html>
<html class="bg-black">
    <head>
        <meta charset="UTF-8">
        <title>AdminLTE | Log in</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" />
        <style>.form-box{margin: 250px auto 0;}</style>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="bg-black">

        <div class="form-box" id="login-box">
            <div class="header">Sign In</div>
            <form action="login.php" method="post">
                <div class="body bg-gray">
                    
                    <div class="form-group">
                        <center>Role:                           
                        <select id="role" onchange="javascript:employees()">
                            <option value="admin">Admin</option>
                            <option  value="employee" >Employee</option>
                        </select></center>
                    </div>
                    <div class="form-group">
                        <input type="text" name="userid" class="inpt form-control" placeholder="User ID"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" id="password" class="inpt form-control" placeholder="Password"/>
                    </div>          
                </div>
                <div class="footer">                                                               
                    <button type="button" class="btn btnclick bg-olive btn-block">Sign me in</button>  
                    
                    <!--p><a href="#">I forgot my password</a></p-->
                    
                </div>
            </form>

        </div>
        
        <script src="js/jquery.min.js"></script>
        <!--<script src="js/bootstrap.min.js" type="text/javascript"></script>-->
        <script type="text/javascript">
            $(function(){
                function login(){
                    if($("#role").val()=='admin'){
                    $.ajax({
                        type :'POST',
                        url :'login.php',
                        data:'username='+$('input[name="userid"]').val()+'&password='+$('input[name="password"]').val(),
                        success:function(response){
                            data = $.parseJSON(response);
                            
                            if(data.code == 1){
                                window.location.href=data.redirectUrl;
                            }else{
                                alert('Invalid Username/Password');
                            }
                        }
                    }); 
                }else{
                    $.ajax({
                        type :'POST',
                        url :'employeelogin.php',
                        data:'username='+$('input[name="userid"]').val(),
                        success:function(response){
                            data = $.parseJSON(response);
                            
                            if(data.code == 1){
                                window.location.href=data.redirectUrl;
                            }else{
                                alert('Invalid Username');
                }
                        }
                    }); 
                }
                }
                
                $('.btn.btnclick').click(function(){
                    login();
                });
                
                $('.inpt').keydown(function(event) {
                    
                    if (event.keyCode == 13) {
                        login();
                        return false;
                     }
                });
                
            });
            function employees(){
               
                if($("#role").val()=='employee'){                     
                   $('#password').css('display', 'none');
                }else{
                   $('#password').css('display', 'inline');            
                }
            }      
            
        </script>
    </body>
</html>
