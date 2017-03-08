<?php 

	/******************************************* include Config *************************************/
	include './../../config.php';
        include './../../mysqlconfig.php';
        if(!in_array("sprint_report",$_SESSION['role_permission'])){
     echo "<script>window.location.href='".HOST_NAME."no_permission.php'</script>";
     exit;
 }
        //Get User Project.
        $projectIds = array();
        $sql_project = "select *,tbl_users.id as user_id from  tbl_users inner join tbl_user_projects on tbl_users.id = tbl_user_projects.user_id where tbl_users.id='".$_SESSION['uid']."' and tbl_users.status='1'";
        $retDataMY = mysql_query($sql_project);  
        while($rowretDataMY = mysql_fetch_object($retDataMY)){
             $projectIds[]= $rowretDataMY->project_id;
        }

        $actionsData = array(
                'sprint-all-projects'=>array('cpage'=>'Sprint Wise Report','pbreadcrumb'=>array())
                
            );
        
	  if(array_key_exists($action,$actionsData) && $action == 'sprint-all-projects') {
               
               $ismysqlQuery = 0; 
                $sql = $sqlQueries[$action]['query'];
                $sqlStrAllProjects = '';
                if($_SESSION['highest_role'] !=1){
                    if(!empty($projectIds)){
                        $sqlStrAllProjects .= ' and p.id in ('.trim(implode(",", $projectIds)).') ';
                    }else{
                        $sqlStrAllProjects .= ' and p.id in (0)';
                    }
                }
		$sql = sprintf($sql,$sqlStrAllProjects);
		
		$retData = pg_query($db, $sql);
	
		if(!$retData){
		   echo pg_last_error($db);
		} else {
		   //error_log( "Query successfully\n" );
		}
                
           }
	
	//project data
		
		$sqlProject = $sqlQueries['sprint-all-projects']['query'];
                $sqlProject = sprintf($sqlProject,$sqlStrAllProjects);
		$retProject = pg_query($db, $sqlProject);
	
		if(!$retProject){
		   echo pg_last_error($db);
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
	  </style>
          
          <link href="../../css/bootstrap.min.css" rel="stylesheet" />
        <link href="../../css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" media="all" href="./../../js/bootstrap-daterangepicker-master/daterangepicker-bs3.css" />
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
                            <img src="../../img/avatar3.png" class="img-circle" alt="User Image" />
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
                         <?php if($_SESSION['highest_role']==1){ ?>
                        <li><a href="./../../index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                         <?php } ?>
                        <?php 
                            foreach($actionsData[$action]['pbreadcrumb'] as $bText =>$bLink ){?>
                        <li class=""><a href="./../../<?php echo $bLink.$filterFromTo; ?>"><i class="fa fa-table"></i> <?php echo $bText;?></a></li>        
                  <?php    }
                        ?>
                        <li class="active"><?php echo $actionsData[$action]['cpage'];?></li>
                    </ol>
                </section>
                <section class="content-header">
                    
                    <div style="text-align: center;">
                        <div style="width:10%;display: inline-block;">&nbsp;</div>
                        <div style="width:64%;display: inline-block;">
                            <span style="font-size: 16px;"><?php echo "Project Sprint Report"; ?> : </span>
                            <select id="project-select" name="id" data-placeholder="Choose a Project..." class="chosen-select" style="width: 280px;float:right;" tabindex="1">
                                    <option value="">Select Project</option>
                            <?php #$action = $_REQUEST['view'];?>
                            <?php while($rowProject = pg_fetch_object($retProject)){ ?>    
                                    <option value="<?php echo $rowProject->pid; ?>" <?php echo isset($_REQUEST['id']) && $rowProject->pid == $_REQUEST['id']?"selected":""; ?>><?php echo $rowProject->pname; ?></option>
                            <?php } ?>
                            </select>
                        </div>
                        <div style="display: inline-block;width: 5%;margin-left: 15%;">
                            <?php if($_SESSION['highest_role']==1){ ?>
                            <button class="btn btn-success export" type="button">Export</button>
                            <?php } ?>
                        </div>
                    </div>
                    
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
			
                        <div class="col-xs-12">
			    
                            <div class="box" style="min-height: 300px;">
                                <div class="box-header">
                                    <div style="width:33%;float: left;">
                                        <h3 class="box-title" style="min-width:240px;"><?php echo isset($projectName)?$projectName:""; ?></h3>
                                        
                                    </div>
				    
                                </div><!-- /.box-header -->
				
                                <div class="box-body table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
					    <?php foreach($sqlQueries[$action]['columns'] as $keycol => $columns) { ?>
                                                <th><?php echo $columns; ?></th>
                                             <?php } ?>   
                                            </tr>
                                        </thead>
                                        <tbody>
					
					<?php $queryObj = $ismysqlQuery ==1?'mysql_fetch_object':'pg_fetch_object';
                                                while($rowretData = $queryObj($retData)){ #print_r($sqlQueries[$action]['columns']);exit;?>
						
					      	<tr>
						<?php foreach($sqlQueries[$action]['columns'] as $keycol => $columns) { ?>
                                                    
						    <td>
                                                        <?php if ($action == 'sprint-all-projects' && $keycol=='pname') { ?>
                                                            <a href="./../../pages/tables/sprint-data.php?view=projects-sprint&id=<?php echo $rowretData->pid;?>">            
                                                        <?php }?>
                                                        
                                                        <?php echo $rowretData->{$keycol} === null || $rowretData->{$keycol} == ' hrs' ?"-": $rowretData->{$keycol} ; ?>
                                                        
                                                        <?php if ($action == 'all-projects' && $keycol=='pname') { ?>
                                                            </a>            
                                                        <?php }?>
                                                    
                                                    </td>
                                                    
						<?php } ?>
						</tr>
						
					<?php }?>
                                            
                                        </tbody>
                                        
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div>

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        <script src="../../js/jquery.min.js"></script>
        <script src="../../js/bootstrap.min.js" type="text/javascript"></script>
        
        <!-- DATA TABES SCRIPT -->
        <script src="../../js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="../../js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <script src="../../js/AdminLTE/app.js" type="text/javascript"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="../../js/AdminLTE/demo.js" type="text/javascript"></script>
        <!-- page script -->
        <script type="text/javascript">
            $(function() {
                
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
                                
		$('#example1').dataTable({"iDisplayLength": 25});
                $('#example2').dataTable({
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });
		
		view = "<?php echo $_REQUEST['view']; ?>";

		$('.export').click(function(){
                    //alert(view);
		    if(view == 'sprint-all-projects'){
                        redirectUrl = "./../../php-excel/topmenuexportxls.php?view="+view+"&id="+$('#project-select').val();
                        
                        if(<?php echo isset($_REQUEST['from'], $_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to']) ? 1:0; ?>){
                            redirectUrl += '&from='+'<?php echo isset($_REQUEST['from'])&& !empty($_REQUEST['from'])?$_REQUEST['from']:'0'; ?>'+'&to='+'<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])?$_REQUEST['to']:'0'; ?>';
                        }
                        
			window.location = redirectUrl;
		    }			
		});

		//On Change Of Project Dropdown
		$('#project-select').change(function(){
			//alert($('#project-select').val());
                    if($('#project-select').val() != ''){
                       
                        window.location = "./../../pages/tables/sprint-data.php?view=projects-sprint&id="+$('#project-select').val();
                    }else{
                        window.location = "./../../pages/tables/epic-all-projects.php?view=all-projects";
                    }
		});
            });
        </script>
	
        <script src="./../../chosen/chosen.jquery.js" type="text/javascript"></script>
	<script src="./../../chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
          
        <script type="text/javascript" src="./../../js/bootstrap-daterangepicker-master/moment.js"></script>
        <script type="text/javascript" src="./../../js/bootstrap-daterangepicker-master/daterangepicker.js"></script>
        
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
          <script type="text/javascript">
                $(document).ready(function() {

                   var cb = function(start, end, label) {
                     //console.log(start.toISOString(), end.toISOString(), label);
                     $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                     //alert("Callback has fired: [" + start.format('MMMM D, YYYY') + " to " + end.format('MMMM D, YYYY') + ", label = " + label + "]");
                   };

                   fromDate = '<?php echo isset($_REQUEST['from']) && !empty($_REQUEST['from'])? date('m/d/Y',strtotime($_REQUEST['from'])):''; ?>';
                   startDatePic = typeof(fromDate) == "undefined" || fromDate==''? moment().subtract(3, 'years'): fromDate;
                   endDate = '<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])? date('m/d/Y',strtotime($_REQUEST['to'])):''; ?>';
                   endDatePic = typeof(endDate) == "undefined" || endDate=='' ? moment(): endDate;
                   
                   if(<?php echo ($action =='active-projects') && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0; ?>){
                       startDatePic = moment().subtract(6, 'days');//.format('MMMM D, YYYY');
                   }
                   
                   //alert('First '+startDatePic+ '  '+endDatePic );
                   var optionSet1 = {
                            startDate: startDatePic,
                            endDate: endDatePic,
                           // minDate: '01/01/2012',
                            //maxDate: '12/31/2014',
                            //dateLimit: { days: 60 },
                            showDropdowns: true,
                            showWeekNumbers: true,
                            timePicker: false,
                            timePickerIncrement: 1,
                            timePicker12Hour: true,
                            ranges: {
                               'Today': [moment(), moment()],
                               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                               'This Month': [moment().startOf('month'), moment().endOf('month')],
                               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                            },
                            opens: 'left',
                            buttonClasses: ['btn btn-default'],
                            applyClass: 'btn-small btn-primary',
                            cancelClass: 'btn-small',
                            format: 'MM/DD/YYYY',
                            separator: ' to ',
                            locale: {
                                applyLabel: 'Submit',
                                cancelLabel: 'Clear',
                                fromLabel: 'From',
                                toLabel: 'To',
                                customRangeLabel: 'Custom',
                                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
                                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                                firstDay: 1
                            }
                        };
                        
                        fromDate = '<?php echo isset($_REQUEST['from']) && !empty($_REQUEST['from'])? date('M d,Y',  strtotime($_REQUEST['from'])):''; ?>';
                        startDatePic = typeof(fromDate) == "undefined" || fromDate =='' ? moment().subtract(3, 'years').format('MMMM D, YYYY'): fromDate;
                        endDate = '<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])? date('M d,Y',  strtotime($_REQUEST['to'])):''; ?>';
                        endDatePic = typeof(endDate) == "undefined" || endDate =='' ? moment().format('MMMM D, YYYY'): endDate;
                        
                        if(<?php echo ($action =='active-projects' ) && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ?1:0; ?>){
                            startDatePic = moment().subtract(6, 'days').format('MMMM D, YYYY');
                        }
                        
                        //alert('Second '+startDatePic+ '  '+endDatePic );
                        $('#reportrange span').html(startDatePic + ' - ' + endDatePic);

                        $('#reportrange').daterangepicker(optionSet1, cb);

                        $('#options1').click(function() {
                            $('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
                        });


                        $('#reportrange').on('apply.daterangepicker', function(ev, picker) { 
                            /*console.log("apply event fired, start/end dates are " 
                              + picker.startDate.format('MMMM D, YYYY') 
                              + " to " 
                              + picker.endDate.format('MMMM D, YYYY')
                            );*/ 

                            window.location= "./../../pages/tables/data.php?view=<?php echo $_REQUEST['view'] ?>&id=<?php echo $_REQUEST['id'] ?>&from="+picker.startDate.format('MMMM D, YYYY')+"&to="+picker.endDate.format('MMMM D, YYYY');

                        });
               });
        </script>
        <script type="text/javascript">
            $('aside.left-side.sidebar-offcanvas').addClass('collapse-left');
            $('aside.right-side').addClass('strech');
        </script>
        <style>
            #project_select_chosen{text-align: left;}
        </style>
    </body>
</html>
