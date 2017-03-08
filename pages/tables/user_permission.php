<?php



	/******************************************* include Config *************************************/
	include './../../config.php';
        include './../../mysqlconfig.php';
        
        if(!in_array("user_permission",$_SESSION['role_permission'])){
          echo "<script>window.location.href='".HOST_NAME."no_permission.php'</script>";
          exit;
        }
        
        $sql = "select *,u.status as status,u.id as userId from tbl_users u join tbl_user_roles  ur on u.id=ur.user_id join tbl_role r on r.id=ur.role where u.status='1'";
        $retDataMY = mysql_query($sql);
        $myUsers = array();
        if(mysql_num_rows($retDataMY) > 0){
            //already exists and role defined.
            
            while($rowretDataMY = mysql_fetch_object($retDataMY)){
                
                if(isset($myUsers[$rowretDataMY->username]) && !empty($myUsers[$rowretDataMY->username])){
                    $a = $myUsers[$rowretDataMY->username];
                    $a['role'] = in_array($rowretDataMY->role, explode(',', $myUsers[$rowretDataMY->username]['role'])) ? $myUsers[$rowretDataMY->username]['role']:$myUsers[$rowretDataMY->username]['role'].','.$rowretDataMY->role;
                    $a['role_name'] = in_array($rowretDataMY->role_name, explode(',', $myUsers[$rowretDataMY->username]['role_name'])) ? $myUsers[$rowretDataMY->username]['role_name'] : $myUsers[$rowretDataMY->username]['role_name'].','.$rowretDataMY->role_name;
                    $a['user_id'] = $rowretDataMY->userId;
                    //$a['manages'] = in_array($rowretDataMY->manages, explode(',', $myUsers[$rowretDataMY->username]['manages'])) ? $myUsers[$rowretDataMY->username]['manages'] :$myUsers[$rowretDataMY->username]['manages'].','.$rowretDataMY->manages;
                    
                    $userDataM = $a;
                }else{
                    $userDataM = (array) $rowretDataMY;
                }
                $myUsers[$rowretDataMY->username] = $userDataM;
            }
        }
        
        
        $sql = "select *,u.id as userId from cwd_membership c, cwd_user u where c.parent_name= 'jira-users' and u.active=1 and c.child_name=u.user_name";
        $retData = pg_query($db, $sql);
        
        $users = array();
        while($rowretData = pg_fetch_object($retData)){
            $uData = array();
            //echo "<pre>";print_r($rowretData);exit;
            if(isset($myUsers[$rowretData->user_name]) && !empty($myUsers[$rowretData->user_name])){
                $uData['user_name'] = $rowretData->user_name;
                $uData['role'] = $myUsers[$rowretData->user_name]['role_name'];
                $uData['user_id'] = $myUsers[$rowretData->user_name]['user_id'];;
                $uData['status'] = $myUsers[$rowretData->user_name]['status'];
                $users[] = $uData;
            }else{
                continue;
            }
            
            
        }
       // echo "<pre>";
       // print_r($users);exit;
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
            form.sidebar-form {
                    display: none;
                }
                .breadcrumb li {margin-left: 2px;padding-top: 10px;}
                #loading-indicator {
                position: absolute;
                left: 10px;
                top: 10px;
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
                        <li class="active">User Permission</li>
                    </ol>
                </section>
                
                <!-- Main content -->
                <section class="content">
                    <div class="row">
			
                        <div class="col-xs-12">
			    
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">User Permission</h3>
                                                                          
                                    <div style="float: right;padding: 10px 20px;">                                        
                                        <button class="btn btn-success export" type="button">Config Role</button>
                                    </div>
                                    
                                </div><!-- /.box-header -->
				
                                <div class="box-body table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                            
                                                <th>Assignee</th>
                                                <th>Role</th>
                                                <!--th>Manager</th-->
                                                <th style="width:22%;">Status</th>
                                            
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($users as $user){?>
                                            <tr>
                                                <td><a href='user_manage.php?name=<?php echo $user['user_name'] ?>&user_id=<?php echo $user['user_id']; ?>'><?php echo $user['user_name']; ?></a></td>
                                                <td><?php echo $user['role']; ?></td>
                                                <!--td><?php echo $user['manages']; ?></td-->
                                                <td><?php echo $user['status']=='1'?'Active':'Inactive'; ?></td>
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
                
                if( view == 'task-in-progess' ) {
                    $('#example1').dataTable({
			"aoColumns": [
                                    { "sType": "number" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "date-uk" },
                                    { "sType": "date-uk" },
                                    
                                ],
                        "aaSorting": [[ 4, "desc" ]],
                        "iDisplayLength": 25
                    });
                }else if( view == 'active-projects' ) {
                    $('#example1').dataTable({
                        "aaSorting": [[2,'desc']],
                        "iDisplayLength": 25
                    });
                }else if( view == 'active-users' ) {
                    $('#example1').dataTable({
                        "aoColumns": [
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "date-uk" },
                                    
                                ],
                        "aaSorting": [[1,'asc']],
                        "iDisplayLength": 50
                    });
                }else if( view == 'users-no-task-today' ) {
                    $('#example1').dataTable({
                        "iDisplayLength": 500,
                        "multipleSelection": true
                    });
                }else if( view == 'task-no-duedate' ) {
                    $('#example1').dataTable({
                                "aoColumns": [
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "date-uk" },
                                    
                                ],
                                "aaSorting": [[ 4, "desc" ],[0,"asc"]],
	                        "iDisplayLength": 50
                    });
                }else if( view == 'unestimated_task' ) {
                    $('#example1').dataTable({
			"aoColumns": [
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "date-uk" },
                                    
                                ],
                        "aaSorting": [[5,'desc']],
                        "iDisplayLength": 50
                    });
                }else if( view == 'data-project-tasks' ) {
                    $('#example1').dataTable({
			"aoColumns": [
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "date-uk" },
                                    { "sType": "date-uk" },
                                    
                                    { "sType": "string" },
                                    { "sType": "string" },
                                ],
                        "aaSorting": [[5,'desc']],
                        "iDisplayLength": 50
                    });
                }else if( view == 'total-time-spent-assignee-last-month' ) {
                    
                    jQuery.fn.dataTableExt.oSort['timehrs-asc']  = function(x,y) {

                        x = parseFloat (x.replace(':','.'));
                        y = parseFloat (y.replace(':','.'));

                        return x<y?-1:x>y?1:0;

                      };

                      jQuery.fn.dataTableExt.oSort['timehrs-desc'] = function(x,y) {

                        x = parseFloat (x.replace(':','.'));
                        y = parseFloat (y.replace(':','.'));

                          return x<y?1:x>y?-1:0;
                      };
                    
                    $('#example1').dataTable({
			"aoColumns": [
                                    { "sType": "string" },
                                    { "sType": "string" },
                                    { "sType": "timehrs" },
                                    { "sType": "timehrs" },
                                    { "sType": "string" },
                                ],
                        "aaSorting": [[0,'asc']],
                        "iDisplayLength": 25
                    });
                }else {
                    $('#example1').dataTable({"iDisplayLength": 25});
                }
                
                $('#example2').dataTable({
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });
		
		

		$('.export').click(function(){
		    
                    redirectUrl = "role_permission.php";                    			
                    window.location = redirectUrl;
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
