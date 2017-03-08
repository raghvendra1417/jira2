<?php

	/******************************************* include Config *************************************/
	include './../../config.php';
        include './../../mysqlconfig.php';
        $username = $_GET['name'];
        $sql = "select *,tbl_users.id as user_id from  tbl_users inner join tbl_user_projects on tbl_users.id = tbl_user_projects.user_id where tbl_users.username='".$_GET['name']."' and tbl_users.status='1'";
        $retDataMY = mysql_query($sql);
        $retDataMYS = mysql_query($sql);
        $user_id = $_GET['user_id'];
        
        $sql_associates = "select *,(select username from tbl_users where id=manages) as asso from  tbl_users u,tbl_user_manages um where u.id=um.user_id and  u.username='".$_GET['name']."' and u.status='1'";
        $retDataAsso = mysql_query($sql_associates);
          
        $projectsData = array();
        $sqlPg = "select * from project ";
        //$retData = mysql_query($sql);
        $retDataPg = pg_query($db, $sqlPg);

        while($rowretDataPg = pg_fetch_object($retDataPg)){
            $projectsData[$rowretDataPg->id]= $rowretDataPg->pname;
        }
        
        $AssociatesData =array();
        $sqlUser = "select * from tbl_users where username!='admin' and username!='".$_GET['name']."'";
        $retDataUser = mysql_query($sqlUser);
        while($rowUser = mysql_fetch_object($retDataUser)){
            $AssociatesData[$rowUser->id]= $rowUser->username;
        }
         
        $Roles =array();
        $sqlUserR = "select * from tbl_role where id not in (select role from tbl_user_roles where status =1 and user_id='".$user_id."' )";
        $retDataUserR = mysql_query($sqlUserR);
        while($rowUserR = mysql_fetch_object($retDataUserR)){
            $Roles[$rowUserR->id]= $rowUserR->role_name;
        }

        $userRole = array();
        $sqlUserR = "select *,ur.id as ur_id from tbl_user_roles ur,tbl_role r where r.id= ur.role and ur.user_id ='".$user_id."'";
        $retDataUserR = mysql_query($sqlUserR);
        while($rowUserR = mysql_fetch_object($retDataUserR)){
            $userRole[$rowUserR->ur_id]= $rowUserR->role_name;
        }

         if(isset($_POST['user_id'],$_POST['addUser']) && !empty($_POST['user_id']) && !empty($_POST['addUser'])){
            $sql3 = "select * from tbl_user_manages um where um.user_id = '".$_POST['user_id']."' and um.status ='1'and um.manages='".mysql_real_escape_string($_REQUEST['addUser'])."'";
            $retData3 = mysql_query($sql3);
            if(mysql_num_rows($retData3) == 0){
                $InsertSql3 = "Insert into tbl_user_manages (user_id,manages,status,added_at) VALUES ";
                $InsertSql3 .= "('".$_POST['user_id']."','".$_POST['addUser']."','1','".date('Y-m-d H:i:s')."');";
                $retData4 = mysql_query($InsertSql3);
                echo 1;exit;
            }else{
                echo 2;exit;
            }
         }
         
         if(isset($_POST['user_id'],$_POST['addProject'],$_POST['project_name']) && !empty($_POST['user_id']) && !empty($_POST['addProject'])&& !empty($_POST['project_name'])){
            $sql3 = "select * from tbl_user_projects um where um.user_id = '".$_POST['user_id']."' and um.status ='1'and um.project_id='".mysql_real_escape_string($_REQUEST['addProject'])."'";
            $retData3 = mysql_query($sql3);
            if(mysql_num_rows($retData3) == 0){
                $InsertSql3 = "Insert into tbl_user_projects (user_id,project_id,project_name,status,added_at) VALUES ";
                $InsertSql3 .= "('".$_POST['user_id']."','".$_POST['addProject']."','".$_POST['project_name']."','1','".date('Y-m-d H:i:s')."');";
                $retData4 = mysql_query($InsertSql3);
                echo 1;exit;
            }else{
                echo 2;exit;
            }
         }

         if( isset($_POST['user_id'],$_POST['addRole']) && !empty($_POST['user_id']) && !empty($_POST['addRole']) ){
            $sql3 = "select * from tbl_user_roles ur where ur.user_id = '".$_POST['user_id']."' and ur.status ='1'and ur.role='".mysql_real_escape_string($_REQUEST['addRole'])."'";
            $retData3 = mysql_query($sql3);
            if(mysql_num_rows($retData3) == 0){
                $InsertSql3 = "Insert into tbl_user_roles (user_id,role,status,added_at) VALUES ";
                $InsertSql3 .= "('".$_POST['user_id']."','".$_POST['addRole']."','1','".date('Y-m-d H:i:s')."');";
                $retData4 = mysql_query($InsertSql3);
                echo 1;exit;
            }else{
                echo 2;exit;
            }
         }
	 
	 if( isset($_POST['id'],$_POST['opr']) && !empty($_POST['id']) && !empty($_POST['opr']) ){
	    if(isset($_POST['opr']) && $_POST['opr'] == 'roles'){
            	$sql3 = "delete from tbl_user_roles where id = '".$_POST['id']."'";
            }elseif(isset($_POST['opr']) && $_POST['opr'] == 'manages'){
            	$sql3 = "delete from tbl_user_manages where id = '".$_POST['id']."'";
            }elseif(isset($_POST['opr']) && $_POST['opr'] == 'projects'){
            	$sql3 = "delete from tbl_user_projects where id = '".$_POST['id']."'";
            }

            $retData3 = mysql_query($sql3);
            if($retData3){
                echo 1;exit;
            }else{
                echo 2;exit;
            }
         }
	 
        
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Jira | Reports</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../../css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="../../css/ionicons.min.css" rel="stylesheet" type="text/css" />
        
        <link href="../../css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <link href="../../css/iCheck/minimal/blue.css" rel="stylesheet" type="text/css" />
        
        
        <!-- DATA TABLES -->
        <link href="../../css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="../../css/AdminLTE.css" rel="stylesheet" type="text/css" />

	  <link rel="stylesheet" href="./../../chosen/docsupport/style.css">
	  <link rel="stylesheet" href="./../../chosen/docsupport/prism.css">
	  <link rel="stylesheet" href="./../../chosen/chosen.css">
	  <style type="text/css" media="all">
	    /* fix rtl for demo */
	    .chosen-rtl .chosen-drop { left: -9000px; }
		.pagination li {
		    margin-left: 0;
		}
        body > .header .navbar .nav > li > a > .label{ width: auto;}
        form.sidebar-form { display: none; }
        .breadcrumb li { margin-left: 2px;padding-top: 10px; }
        #loading-indicator {
            position: absolute;
            left: 10px;
            top: 10px;
        }
        thead th ,tbody tr { text-align: center; }
        .box-body.table-responsive {
            border: 1px solid;
            margin: 3% 0;
        }
	  </style>
          <link href="../../css/bootstrap.min.css" rel="stylesheet" />
        <link href="../../css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" media="all" href="./../../js/bootstrap-daterangepicker-master/daterangepicker-bs3.css" />
        
        <link rel="stylesheet" href="./../../css/jquery-ui.css">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-blue">
        <!-- header logo: style can be found in header.less -->
        <?php include 'widget-topbar.php'; ?>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="./../../img/avatar3.png" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p>Hello, Vinutha</p>

                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>
                    <!-- search form -->
                    <form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search..."/>
                            <span class="input-group-btn">
                                <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
			<?php $datatables = true; ?>
                        <?php include 'widgets-siderbar.php'; ?>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-breadcrumb">
                    <ol class="breadcrumb" style="margin:0 auto;">
                        <li><a href="./../../index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                        <li class="active">User Manage</li>
                    </ol>
                </section>
                
                <!-- Main content -->
                <section class="content">
                    <div class="row">
			
                        <div class="col-xs-12">
			    
                            <div class="box">
                                <div class="box-header" >
                                    <h3 class="box-title"><?php echo $_GET['name'] ?> - [<?php echo implode(',', $userRole); ?>]</h3>                                                                        
                                </div><!-- /.box-header -->
                                
                                <div class="box-body table-responsive">
                                    <?php if(in_array(1,$_SESSION['user_role'])){?>
                                    <div style="margin:5px">
                                    <label>Assign Role : </label>
                                    <select name="user" id="user_role" class="chosen-select">
                                        <?php foreach ($Roles as $RoleId=>$RoleName) { ?>
                                        <option value="<?php echo $RoleId; ?>"><?php echo $RoleName; ?></option>
                                        <?php } ?>
                                    </select>
                                    <button class="btn btn-success addRole" type="button">Add</button>
                                    </div>
                                    <?php } ?>
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <th>Role Name</th>
                                            <th>Action</th>
                                        </thead>
                                        <tbody>
                                        <?php   foreach ($userRole as $ur_id => $role_name) { ?>
                                            <tr>
                                                <td><?php echo $role_name; ?></td>
                                                <td><a onClick="delInfo('roles','<?php echo $ur_id; ?>');" href="#">X</a></td>                                     
                                            </tr>
                                        <?php   } ?>
                                        </tbody>
                                    </table>
                                </div>
                                

				                <div class="box-header">
                                    <h3 class="box-title">Associates under him:</h3>
                                    <div style="float:right;margin:5px">
                                    <select name="user" id="user" class="chosen-select">
                                        <?php foreach ($AssociatesData as $AssoId=>$AssoName) { ?>
                                        <option value="<?php echo $AssoId; ?>"><?php echo $AssoName; ?></option>
                                        <?php } ?>
                                    </select>
                                    <button class="btn btn-success addAssociates " type="button">Add</button>
                                    </div>
                                                     
                                </div>  
                                <div class="box-body table-responsive">
                                    
                                    
                                    <table id="example1" class="table table-bordered table-striped">     
                                        <thead>
                                            <th>Associate ID</th>
                                            <th>Associate Name</th>
	     				    <th>Action</th>
                                        </thead>
                                        <tbody>
                                            <?php                                           
                                                while($rowretDataAsso = mysql_fetch_object($retDataAsso)){
                                            ?>
                                            <tr>
                                                <td><?php echo $rowretDataAsso->manages; ?></td>
                                                <td><?php echo $rowretDataAsso->asso; ?></td>
                                                <td><a onClick="delInfo('manages','<?php echo $rowretDataAsso->id; ?>');" href="#">X</a></td>
                                            </tr><!-- ; -->
                                            <?php }?>                             
                                        </tbody>
                                    </table>
                                </div>
                                <div class="box-header">
                                    <h3 class="box-title">Projects:</h3>
                                    <div style="float:right;margin:5px">
                                        <select name="my_projects" id="my_projects" class="chosen-select">
                                        <?php foreach ($projectsData as $projectId=>$projectName) { ?>
                                        <option value="<?php echo $projectId; ?>"><?php echo $projectName; ?></option>
                                        <?php } ?>
                                    </select>
                                    <button class="btn btn-success addProjects" type="button">Add</button>
                                    </div>
                                </div>                               
                                
                                <div class="box-body table-responsive" style="width: 50%; text-align: center; margin: auto;">
                                    
                                    
                                    <table id="example1" class="table table-bordered table-striped"> 
                                        <thead>
                                            <th>Project ID</th>
                                            <th>Name</th>
					    <th>Action</th>
                                        </thead>     
                                        <tbody>
                                           <?php                                           
                                             while($rowretDataMY = mysql_fetch_object($retDataMY)){
                                            ?>
                                            <tr>
                                                <td><?php echo $rowretDataMY->project_id; ?></td>
                                                <td><?php echo $rowretDataMY->project_name; ?></td>   
                                                <td><a onClick="delInfo('projects','<?php echo $rowretDataMY->id; ?>');" href="#">X</a></td>
                                            </tr>
                                            <?php } ?>                                            
                                        </tbody>
                                        
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div>

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        <!-- COMPOSE MESSAGE MODAL -->
        
        <script src="../../js/jquery.min.js"></script>
        <script src="../../js/bootstrap.min.js" type="text/javascript"></script>
        <!-- DATA TABES SCRIPT -->
        <script src="../../js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="../../js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        
        <script src="../../js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
        
        <!-- AdminLTE App -->
        <script src="../../js/AdminLTE/app.js" type="text/javascript"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="../../js/AdminLTE/demo.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui.min.js" type="text/javascript"></script>

<!--	<script src="../../js/date-euro.js" type="text/javascript"></script>	-->
        <script src="../../js/date-uk.js" type="text/javascript"></script>
        <!-- page script -->
         
        <script type="text/javascript">
             
	    function delInfo(info,id){
			
		if(confirm("Want to Delete this record ?")){
			    $.ajax({
				type : 'POST',
				data:{id:id,opr:info},
				url : 'user_manage.php',
				success : function(data){
				    if(data == 1){
				        window.location.reload();
				    }else{
				        alert("Try again Later.");
				    }
				    
				},
			    });
		}
	    }

            $(function() {
                //"use strict";
                function datatopbarupdate() {
			    $.ajax({
				type : 'POST',
				url : './../../ajax-topbardata.php',
				success : function(data){
				    var json = $.parseJSON(data);
				    //alert(json.user_no_task_today);
                                    $('.unestimated_task span.label').html(json.unestimted_task_count);
				    $('.project span.label').html(json.project);
                                    $('.task_inprogress span.label').html(json.task_inprogress);
				},
			    });
			};
			datatopbarupdate();	
		
			var refInterval = setInterval(function() {
				      datatopbarupdate();
				}, 30000); // 30 seconds
                                
                                
                var view = "<?php echo $_REQUEST['view']; ?>";
                var pid = "<?php echo $_REQUEST['id']; ?>";            
                
                
                $('#example2').dataTable({
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });
                
                $('.addAssociates').click(function(){    
                    
                    $.ajax({
                        type : 'POST',
                        data:{username:"<?php echo $username; ?>",addUser:$("#user").val(),user_id:"<?php echo $user_id; ?>"},
                        url : 'user_manage.php',
                        success : function(data){
                            if(data == 1){
                                window.location.reload();
                            }else if(data == 2){
                                alert("Associate Already Exists.");
                            }else{
                                alert("Try again Later.");
                            }
                            
                        },
                    });                   
		        });
                
                
                $('.addProjects').click(function(){
                       var e =  document.getElementById("my_projects");
                       var strUser = e.options[e.selectedIndex].text; 
               
                    $.ajax({
                        type : 'POST',
                        data:{username:"<?php echo $username; ?>",addProject:$("#my_projects").val(),user_id:"<?php echo $user_id; ?>",project_name:strUser},
                        url : 'user_manage.php',
                        success : function(data){
                            if(data == 1){
                                window.location.reload();
                            }else if(data == 2){
                                alert("Project Already Added.");
                            }else{
                                alert("Try again Later.");
                            }
                            
                        },
                    });                   
		        });

                $('.addRole').click(function(){
               
                    $.ajax({
                        type : 'POST',
                        data:{addRole:$("#user_role").val(),user_id:"<?php echo $user_id; ?>"},
                        url : 'user_manage.php',
                        success : function(data){
                            if(data == 1){
                                window.location.reload();
                            }else if(data == 2){
                                alert("Role Already Added.");
                            }else{
                                alert("Try again Later.");
                            }
                            
                        },
                    });                   
                });
		
		
		//On Change Of Project Dropdown
		$('#project-select').change(function(){
			//alert($('#project-select').val());
			window.location = "./../../pages/tables/data.php?view="+view+"&id="+$('#project-select').val();
			///jiraadmin/pages/tables/data.php?view=<?php echo $action; ?>&id=
		});
                
                $('#chkAll').on('ifClicked', function(){
                    if($('#chkAll').is(':checked')){
                        $('#chkAll,.chkthis').iCheck('uncheck');
                    }else{
                        $('#chkAll,.chkthis').iCheck('check');
                    }
                });
                
                $('.sendMailPOP').click(function(){
                    //First - Collect all the users emailid (comma seperated)
                    var emailids = '';
                    $.each($('.chkthis:checkbox:checked'),function(){
                        emailids += $(this).val()+',';
                    });
                    
                    $('#sendtoaddress').val(emailids);
                    //Clear form Errors
                    $('input[name="email_to"],.wysihtml5-sandbox').css('border-color','#ccc');
                    
                    //Fourthly -  Show Modal box
                    $( "#compose-modal" ).modal('show');
                    
                });
                
                
                
                
                //By Default
                $('#chkAll,.chkthis').iCheck('uncheck');
                $('input[name="email_to"],.wysihtml5-sandbox').css('border-color','#ccc');
                //Initialize WYSIHTML5 - text editor
                $("#email_message").wysihtml5();
                
                $('.sendMail').click(function(){
                    var flag = 1;
                    
                    $.each($('input[name="email_to"],#email_message'),function(){
                        if($(this).val() == ''){
                            
                            if( $(this).attr('id') == 'email_message'){
                                $('.wysihtml5-sandbox').css('border-color','red');
                            }else{
                                $(this).css('border-color','red');
                            }
                            flag = 0;
                        }else{
                            $(this).css('border-color','#ccc');
                        }
                        
                        if(flag == 1 && $(this).attr('name') == 'email_to'){
                            k = $(this).val();
                            flag = validateMultipleEmailsCommaSeparated(k.substring(0,k.length -1)) ? 1 : 0;
                            
                            $(this).css('border-color','#ccc');
                        }
                    });
                    
                    if(flag){
                        
                        $('#loading-I').show();
                        
                        $.ajax({
                            url:'./../../sendmail.php',
                            type:'post',
                            data:'to='+k.substring(0,k.length -1)+'&message='+$('#email_message').val(),
                            success: function(response){
                                if(response == 'Message sent!'){
                                    alert('Message Sent!!');
                                    $('#compose-modal').modal('hide');
                                }else{
                                    alert('Message sending Failed');
                                    $('#loading-I').hide();
                                }
                            }
                        });
                    }
                    
                    return false;
                });
                
                
            });
        </script>
	  <script src="./../../chosen/chosen.jquery.js" type="text/javascript"></script>
	  <script src="./../../chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
	  <script type="text/javascript">
	    var config = {
	      '.chosen-select'           : {},
	      '.chosen-select-deselect'  : {allow_single_deselect:true},
	      '.chosen-select-no-single' : {disable_search_threshold:10},
	      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
	      '.chosen-select-width'     : {width:"95%"}
	    }
	    for (var selector in config) {
	      $(selector).chosen(config[selector]);
	    }
	  </script>
          
        <script type="text/javascript" src="./../../js/bootstrap-daterangepicker-master/moment.js"></script>
        <script type="text/javascript" src="./../../js/bootstrap-daterangepicker-master/daterangepicker.js"></script>
        
       
        <style>
            .modal-dialog{left: 0%;}
        </style>
        <script type="text/javascript">
            $('aside.left-side.sidebar-offcanvas').addClass('collapse-left');
            $('aside.right-side').addClass('strech');
        </script>
    </body>
</html>
