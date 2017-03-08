<?php

	/******************************************* include Config *************************************/
	include './../../config.php';
        include './../../mysqlconfig.php';        
               
        
        $role_id = (int) trim($_GET['id']);
        
        if(isset($_POST['selectedPerm'],$_POST['allVals']) && !empty($_POST['selectedPerm']) && !empty($_POST['allVals'])){
            //insert or Update.
            $allCurrentPer = $_POST['allVals'];
            $selectedPerm = $_POST['selectedPerm'];
            
            $sql1 = "select * from  tbl_role_permissions where role_id='".$role_id."'";
            $retData1 = mysql_query($sql1);  
            $allreadyAssigned = array();
            while($rowretData1 = mysql_fetch_object($retData1)){
                $allreadyAssigned[] = $rowretData1->permission;
            }
            
            //Insert the new Config
            $toInsertArr = array_diff($allCurrentPer, $allreadyAssigned);
            $InsertSql = "Insert into tbl_role_permissions (role_id,permission,status,added_at) VALUES ";
            if(!empty($toInsertArr)){
                foreach ($toInsertArr as $toInsert) {
                    $InsertSql .= "('".$role_id."','".$toInsert."','2','".date('Y-m-d H:i:s')."'),";
                }
                $retData2 = mysql_query(trim($InsertSql, ','));  
            }
            
            //now update as per selection
            $uncheckedOnes = array_diff($allCurrentPer, $selectedPerm);
            $updateSql1 = "update tbl_role_permissions set status = '2' where role_id = '".$role_id."' and permission in ('".trim(implode("','",$uncheckedOnes),",'")."')";
            $retData11 = mysql_query($updateSql1); 
            
            $updateSql2 = "update tbl_role_permissions set status = '1' where role_id = '".$role_id."' and permission in ('".trim(implode("','",$selectedPerm),",'")."')";
            $retData12 = mysql_query($updateSql2);  
            echo 1;exit;
        }
        
        $sqlMYRP = "select *,rp.status as rp_status from  tbl_role_permissions rp, tbl_role r where rp.role_id = r.id and rp.status='1' and role_id='".$role_id."'";
        $retDataMYRP = mysql_query($sqlMYRP);  
        $roleInfo = array();
        $i = 0;
        while($rowretDataMYRP = mysql_fetch_object($retDataMYRP)){
            $roleInfo['role_id'] =$rowretDataMYRP->role_id;
            $roleInfo['role_name'] = $rowretDataMYRP->role_name;
            
            if(isset($roleInfo['permission']) && !empty($roleInfo['permission'])){
                $roleInfo['permission'][$i] = $rowretDataMYRP->permission;
            }else{
                $roleInfo['permission'][$i] = $rowretDataMYRP->permission;
            }
            $i++;
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
                        <li class="active">Manage Role</li>
                    </ol>
                </section>
                
                <!-- Main content -->
                <section class="content">
                    <div class="row">
			
                        <div class="col-xs-12">
			    
                            <div class="box">
                                <div class="box-header">
                                    <div style="float:left">
                                        <h3 class="box-title">Manage Role - <?php echo $roleInfo['role_name']; ?></h3>
                                    </div>
                                    <div style="float:right;margin: 5px;">
                                        <input type="button" value="Save" class="btn btn-success SavePerm">
                                    </div>
                                </div><!-- /.box-header -->
				
                                <div class="box-body table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">                                        
                                        <thead>
                                            <tr>                                           
                                                <th><input id="chkAll" type="checkbox" > Roles</th>                                                                      
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php                                           
                                            foreach($permission as $perKey=>$perVal){
                                            ?>
                                            <tr>
                                                <td><input class="chkthis" name="PR[]" type="checkbox" name="role" <?php if(isset($roleInfo['permission']) && in_array($perKey, $roleInfo['permission'])){echo "checked"; } ?> value="<?php echo $perKey; ?>" >&nbsp;&nbsp;<?php echo $perVal; ?></td>                                           
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
                
                
                
                
                $('#chkAll').on('ifClicked', function(){
                    
                    if($('#chkAll').is(':checked')){
                        $('#chkAll,.chkthis').iCheck('uncheck');
                    }else{
                        $('#chkAll,.chkthis').iCheck('check');
                    }
                });
                
                $('.SavePerm').click(function(){
                    allVals = [];
                    CheckedVals = [];
                    $('.chkthis').each(function() {
                        if($(this).is(':checked')){
                            CheckedVals.push($(this).val());
                        }
                        allVals.push($(this).val());
                    });
                    
                    $.ajax({
                        type : 'POST',
                        data:{selectedPerm:CheckedVals,allVals:allVals},
                        url : '',
                        success : function(data){
                            if(data == 1){
                                window.location.reload();
                            }else{
                                alert("Try again Later.");
                            }
                            
                        },
                    });
                    
                    
                    
                });
                
                
                
                
                //By Default
                //$('#chkAll').iCheck('uncheck');
                $('input[name="email_to"],.wysihtml5-sandbox').css('border-color','#ccc');
                
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
