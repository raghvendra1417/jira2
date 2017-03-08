<?php 

    
    include './../../config.php';
    include './../../mysqlconfig.php';
    if(!in_array("change-password",$_SESSION['role_permission'])){
          echo "<script>window.location.href='".HOST_NAME."no_permission.php'</script>";
          exit;
    }
    
    if(isset($_POST['User']) && !empty($_POST['User'])){
        
        $sql = "select * from tbl_users where username='".mysql_real_escape_string($_POST['User']['username'])."' and password='".mysql_real_escape_string($_REQUEST['User']['p_password'])."'";
        $retData = mysql_query($sql);
        $retDataD = mysql_fetch_object($retData);
        
        if(isset($retDataD->id)){
            
            //found and update
            $sqlUp = "update tbl_users set password='".mysql_real_escape_string($_POST['User']['password'])."' where username='".mysql_real_escape_string($_POST['User']['username'])."' and password='".mysql_real_escape_string($_REQUEST['User']['p_password'])."'";
            $retDataUps = mysql_query($sqlUp);
            
            if($retDataUps ==1){
                $redirect =1;
            }
        }else{
            $didntFound= 1;
        }
    }
?>
<!DOCTYPE html>
<html class="bg-black">
    <head>
        <meta charset="UTF-8">
        <title>Jira Admin | Change Password</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../../css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="../../css/AdminLTE.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <style>
            .error{ border :1px solid #ff0000 !important; }
        </style>
        
        <script>
        <?php if($redirect == 1 ){ ?>
            window.location.href = './../../';
        <?php }elseif($didntFound == 1){ ?>
            alert("Old Password Didn't match");
        <?php } ?>
        </script>
    </head>
    <body class="bg-black">

        <div class="form-box" id="login-box">
            <div class="header">Change Password</div>
            <form action="" method="post" id="ch-pass">
                <div class="body bg-gray">
                    <div class="form-group">
                        <input type="text" style="display: none;" name="User[username]" value="<?php echo $_SESSION['username']; ?>" class="form-control" placeholder="User ID"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="User[p_password]" class="form-control" placeholder="Old Password"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="User[password]" class="form-control" placeholder="New Password"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="User[password2]" class="form-control" placeholder="Retype password"/>
                    </div>
                </div>
                <div class="footer">                    

                    <input type="button" class="chng-pass btn bg-olive btn-block" value="Change Password"/>
                </div>
            </form>

        </div>

        <script src="../../js/jquery.min.js"></script>
        <script src="../../js/bootstrap.min.js" type="text/javascript"></script>
        <script>
            $(function(){
                $('.chng-pass').on('click',function(){
                    old = $('input[name="User[p_password]"]').val();
                    new1 = $('input[name="User[password]"]').val();
                    new2 = $('input[name="User[password2]"]').val();
                    
                    empty = 0;
                    $('.form-control').each(function(){
                        if($.trim($(this).val()) == ''){
                            $(this).addClass('error');
                            empty =1;
                        }else{
                            $(this).removeClass('error');
                        }
                    });
                    
                    if(empty){
                        return false;
                    }
                    
                    if(new1 != new2){
                        
                        $('input[name="User[password]"]').addClass('error');
                        $('input[name="User[password2]"]').addClass('error');
                        return false;
                    }else{
                        //send Request
                        $('#ch-pass').submit();
                    }
                    
                });
            });
        </script>
    </body>
</html>