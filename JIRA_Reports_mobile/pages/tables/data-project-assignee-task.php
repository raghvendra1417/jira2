<?php 

	/******************************************* include Config *************************************/
	include './../../config.php';

        $actionsData = array(
                'data-project-assignee-task'=>array(
                    'cpage'=>'Project Assignee Task'
                    ,'pbreadcrumb'=>array(
                        'Project Wise Report <span style="font-size: 10px;">(Active Projects)</span>'=>'pages/tables/data.php?view=active-projects'
                        ,'Project Wise Report'=>'pages/tables/data.php?view=assignee-total-est-project-selection&id='.$_REQUEST['pid']
                     )
                )
                
            );
          $action = 'data-project-assignee-task';
	  if ($_REQUEST['uid'] && $_REQUEST['pid']) {
	
		$sql = $sqlQueries['data-project-assignee-task']['query'];
                
                if(isset($_REQUEST['from'],$_REQUEST['to'])){
                    $addsqlFormtoFilter = "and to_char(E.startdate::date, '2YYY-MM-DD')::date >= '".date("Y-m-d",strtotime($_REQUEST['from']))."'::DATE and to_char(E.startdate::date, '2YYY-MM-DD')::date <= '".date("Y-m-d",strtotime($_REQUEST['to']))."'::DATE ";
                    $sql = sprintf($sql,$_REQUEST['pid'],$_REQUEST['uid'],$addsqlFormtoFilter);
                }else{
                    $sql = sprintf($sql,$_REQUEST['pid'],$_REQUEST['uid'],'');
                }
		
		
		$retData = pg_query($db, $sql);
	
		if(!$retData){
		   echo pg_last_error($db);
		} else {
		   //error_log( "Query successfully\n" );
		}

		
		#echo $sql;exit;
	   } 
	
	
        $filterFromTo = '';
        if(isset($_REQUEST['from'] ,$_REQUEST['to'])){
            $filterFromTo = '&from='.$_REQUEST['from'].'&to='.$_REQUEST['to'];
        }

		
		#print_r($sqlQueries);exit;
		#while($rowProject = pg_fetch_object($retProject)){ print_r($rowProject);exit;}
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
                .mybreadcrumb li {margin-left: 2px;padding-top: 10px;display: table-cell;}
                .mybreadcrumb > .active{ color: #999999;}
                .mybreadcrumb {
                    background-color: #f5f5f5;
                    border-radius: 4px;
                    list-style: outside none none;
                    margin-bottom: 20px;
                    padding: 8px 15px;
                }
                .mybreadcrumb > li + li:before {
                    color: #cccccc;
                    content: "/ ";
                    padding: 0 5px;
                }
	  </style>
          
        
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
                    <ol class="mybreadcrumb" style="margin:0 auto;">
                        <li><a href="./../../index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                        <?php 
                            foreach($actionsData[$action]['pbreadcrumb'] as $bText =>$bLink ){?>
                        <li class=""><a href="./../../<?php echo $bLink.$filterFromTo; ?>"><i class="fa fa-table"></i> <?php echo $bText;?></a></li>        
                  <?php    }
                        ?>
                        <li class="active"><?php echo $actionsData[$action]['cpage'];?></li>
                    </ol>
                </section>
                <section class="content-header">
                    <h1>
                        Reports
                        <small></small>
                    </h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
			
                        <div class="col-xs-12">
			    
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title"><span class="pname"></span> - <span class="anamet"><?php echo $_REQUEST['uid']; ?></span></h3>
					<h3 class="box-title">Estimate Given : <span class="hrs_est"></span></h3>
                                    <!--<div style="float: left;padding: 5px 20px;"><a class="btn btn-success back" style="color:white" href="./../../pages/tables/data.php?view=assignee-total-est-project-selection&id=<?php echo $_REQUEST['pid'].$filterFromTo; ?>"> Back</a></div>-->
				    <div style="float: right;padding: 10px 20px;"><button class="btn btn-success export" type="button">Export</button></div>
                                    <div>
                                        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;margin: 10px">
                                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                            <span></span> <b class="caret"></b>
                                        </div>
                                    </div>
                                </div><!-- /.box-header -->
				
                                <div class="box-body table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
					    <?php  foreach($sqlQueries[$action]['columns'] as $keycol => $columns) {
                                                        if($keycol == 'timeoriginalestimate'){
                                                                        continue;
                                                        }elseif($keycol == 'timespent'){

                                                ?>
                                            <th>Time Estimate / Spent</th>
                                                <?php  }else{ ?>
                                                        <th><?php echo $columns; ?></th>
                                            <?php      }
                                                    } ?>   
                                            </tr>
                                        </thead>
                                        <tbody>
					    
					<?php while($rowretData = pg_fetch_object($retData)){ #print_r($sqlQueries[$action]['columns']);exit;?>
						
					      	<tr>
                                            <?php foreach($sqlQueries[$action]['columns'] as $keycol => $columns) { 
                                                    if($keycol == 'timeoriginalestimate'){
							$TTtimeoriginalestimate += $rowretData->timeoriginalestimate;
                                                        continue;
                                                    }elseif($keycol == 'timespent'){
                                                        $est_time = (float) str_replace(':', '.', $rowretData->timeoriginalestimate);
                                                        $spent_time = (float) str_replace(':', '.', $rowretData->timespent);
                                                        $total_time = $est_time + $spent_time;
                                                        $so_perc_est = ($est_time/$total_time) * 100;
                                                        $so_perc_spent = ($spent_time/$total_time) * 100;
                                                        $colr = $spent_time <= $est_time ?"#51a825":"#ec8e00";

                                                        ?>
                                                            <td>
                                                                <div style="width: <?php echo $so_perc_est; ?>%; background-color: #89afd7; padding: 2px; margin: 2px auto; text-align: center; float: left;">
                                                                    <p style="margin:1px auto;min-width: 100px;"><?php echo trim($rowretData->timeoriginalestimate) != ' hrs' || $rowretData->timeoriginalestimate != null? $rowretData->timeoriginalestimate:'00:00 hrs'; ?></p>
                                                                </div>

                                                                <div style="width: <?php echo $so_perc_spent; ?>%; background-color: <?php echo $colr; ?>; padding: 2px; margin: 2px auto; text-align: center; float: left; clear: both;">
                                                                    <p style="margin:1px auto;min-width: 100px;"><?php echo $rowretData->timespent != ' hrs'? $rowretData->timespent:'00:00 hrs'; ?></p>
                                                                </div>

                                                            </td>
                                            <?php  }elseif ( $keycol == 'gotojira') { ?>
                                                            <td>
                                                                <a target="_blank"href="<?php echo $GLOBALS['jira_url']."/browse/".$rowretData->pkey."-".$rowretData->issuenum;?>">
                                                                    <img width="" title="Go to Jira" src="<?php echo $GLOBALS['jira_url']."/secure/projectavatar?pid=".$rowretData->pid;?>"/>
                                                                    <?= $rowretData->pkey."-".$rowretData->issuenum; ?>
                                                                </a>
                                                            </td>
                                                        <?php 
                                                                    continue;
                                                    }else{ ?>
                                                            <td>
                                                                <?php  
                                                                    if($rowretData->{$keycol} === null || $rowretData->{$keycol} ==' hrs'){ 
                                                                        echo "";
                                                                    }elseif ($keycol == 'summary') {
                                                                        echo $rowretData->summary.' - '.$rowretData->reporter;    
                                                                    }elseif ($keycol =='issuetypename') {
                                                                        echo "<img src='".issueIssueTypeImage($rowretData->{$keycol})."'/>".$rowretData->{$keycol};
                                                                    }elseif ($keycol =='issuestatus') {
                                                                        $colors = issuestatusColor($rowretData->{$keycol});
                                                                        $bg = $colors['bg'];
                                                                        $color = $colors['clr'];
                                                                        echo "<span title='".$rowretData->iid."' style='background-color:".$bg.";color:".$color.";"
                                                                                . "border-radius: 6px;padding: 4%;text-transform: uppercase;'>".
                                                                                $rowretData->{$keycol}."</span>";
                                                                    }else{ 
                                                                        echo $rowretData->{$keycol};
                                                                    }
                                                                ?>
                                                            </td>
                                            <?php  }
                                            } ?>
						</tr>
						
					<?php   
                                                $projectName = $rowretData->pname;
                                                $AssigneeName = $rowretData->assignee;
                                              }
                                              
                                        ?>
                                        
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
        
<!--        <script src="../../js/date-euro.js" type="text/javascript"></script>	-->
        <script src="../../js/date-uk.js" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <script src="../../js/AdminLTE/app.js" type="text/javascript"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="../../js/AdminLTE/demo.js" type="text/javascript"></script>
        <!-- page script -->
        <script type="text/javascript">
            $(function() {
                
                $('span.pname').html('<?php echo $projectName; ?>');
                $('span.anamet').html('<?php echo $AssigneeName; ?>');
		$('span.hrs_est').html('<?php echo $TTtimeoriginalestimate; ?> Hrs');
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
                                
		$('#example1').dataTable({
                    "aoColumns": [
                                    { "sType": "string" },
                                    { "sType": "string","sWidth":'10%'},
                                    { "sType": "string","sWidth":'13%'},
                                    { "sType": "string" },
                                    { "sType": "date-uk" },
                                    { "sType": "date-uk" },
                                    { "sType": "date-uk" },
                                    { "sType": "string","bSortable": false  },
                                    
                                    
                                ],
                    "aaSorting": [[3,'asc'],[4,'desc'],],
                    "iDisplayLength": 50
                });
                $('#example2').dataTable({
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });
		
		view = "data-project-assignee-task";

		$('.export').click(function(){
		    
                    redirectUrl = "./../../php-excel/topmenuexportxls.php?view="+view+"&pid="+"<?php echo $_REQUEST['pid']; ?>"+"&uid="+"<?php echo $_REQUEST['uid']; ?>";

                    if(<?php echo isset($_REQUEST['from'], $_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to']) ? 1:0; ?>){
                        redirectUrl += '&from='+'<?php echo isset($_REQUEST['from'])&& !empty($_REQUEST['from'])?$_REQUEST['from']:'0'; ?>'+'&to='+'<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])?$_REQUEST['to']:'0'; ?>';
                    }
                    window.location = redirectUrl;
		    		
		});

		//On Change Of Project Dropdown
		$('#project-select').change(function(){
			//alert($('#project-select').val());
			window.location = "./../../pages/tables/data.php?view="+view+"&id="+$('#project-select').val();
			///jiraadmin/pages/tables/data.php?view=<?php echo $action; ?>&id=
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
                     console.log(start.toISOString(), end.toISOString(), label);
                     $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                     //alert("Callback has fired: [" + start.format('MMMM D, YYYY') + " to " + end.format('MMMM D, YYYY') + ", label = " + label + "]");
                   };

                   fromDate = '<?php echo isset($_REQUEST['from']) && !empty($_REQUEST['from'])? date('m/d/Y',strtotime($_REQUEST['from'])):''; ?>';
                   startDatePic = typeof(fromDate) == "undefined" || fromDate==''? moment().subtract(3, 'years'): fromDate;
                   endDate = '<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to'])? date('m/d/Y',strtotime($_REQUEST['to'])):''; ?>';
                   endDatePic = typeof(endDate) == "undefined" || endDate=='' ? moment(): endDate;
                   
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

                            window.location= "./../../pages/tables/data-project-assignee-task.php?uid=<?php echo $_REQUEST['uid'] ?>&pid=<?php echo $_REQUEST['pid'] ?>&from="+picker.startDate.format('MMMM D, YYYY')+"&to="+picker.endDate.format('MMMM D, YYYY');

                        });
               });
        </script>
        <script type="text/javascript">
            $('aside.left-side.sidebar-offcanvas').addClass('collapse-left');
            $('aside.right-side').addClass('strech');
        </script>
    </body>
</html>
