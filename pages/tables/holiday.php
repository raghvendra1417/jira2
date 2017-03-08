<?php
 
 
/* * ***************************************** include Config ************************************ */
include './../../mysqlconfig.php';
include './../../config.php';

if(!in_array("holiday",$_SESSION['role_permission'])){
     echo "<script>window.location.href='".HOST_NAME."no_permission.php'</script>";
     exit;
 }

$query1 = mysql_query("SELECT `id`, `event_name`, `date`,`timespan`, `status`, `added_by`, `added_at` FROM `tbl_holidays` order by `date` asc");
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
                        <?php if($_SESSION['highest_role']==1){ ?>
                        <li><a href="./../../index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                        <li class="active">Holidays</li>
                        <?php } ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">

                        <div class="col-xs-12">

                            <div class="box">
                                <div class="box-header">

                                    <h3 class="box-title">Holidays</h3>                                                                        


                                    <div style="float: right;padding: 10px 20px;">
                                        <?php if($_SESSION['highest_role']==1){ ?>
                                        <button class="btn btn-success export" id="add" type="button">Add</button>
                                        <?php } ?>
                                    </div>

                                </div><!-- /.box-header -->

                                <div class="box-body table-responsive">                                    
                                    <table id="example10" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Event</th>
                                                <th>Period</th>
                                                <?php if($_SESSION['highest_role']==1){ ?>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        while ($query2 = mysql_fetch_array($query1)) {
                                            ?>
                                                <tr><td><?php echo date("d-m-Y", strtotime($query2['date'])) ?></td>
                                                    <td><?php echo $query2['event_name'] ?></td>
                                                    <td><?php echo $query2['timespan']==1?"Full Day":"Half Day" ?></td>
                                                    <?php if($_SESSION['highest_role']==1){ ?>
                                                    <td><a href='holidayadd.php?id=<?php echo $query2['id'] ?>'>Edit</a></td>
                                                    <td><a href='holidaysubmit.php?delete_id=<?php echo $query2['id'] ?>'><span class="glyphicon glyphicon-trash"></span></a></td>
                                                    <?php } ?>
                                                <tr>
                                        <?php }
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

        <!-- COMPOSE MESSAGE MODAL -->
        <div class="modal fade" id="compose-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-envelope-o"></i> Compose New Message</h4>
                    </div>
                    <form action="#" method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">TO:</span>
                                    <input name="email_to" id="sendtoaddress" class="form-control" placeholder="Email TO">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">CC:</span>
                                    <input name="email_to_cc" id="sendtocc" class="form-control" placeholder="Email CC">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">BCC:</span>
                                    <input name="email_to_bcc" class="form-control" placeholder="Email BCC">
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea name="message" id="email_message" class="form-control" placeholder="Message" style="height: 120px;"></textarea>
                            </div>
                            <div class="form-group" style="text-align: center;">
                                <img src="../../img/ajax-loader1.gif" style="display:none" id="loading-I"/>
                            </div>


                        </div>
                        <div class="modal-footer clearfix">

                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>

                            <button type="submit" class="btn btn-primary pull-left sendMail"><i class="fa fa-envelope"></i> Send Message</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

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

            $('#add').click(function () {
                redirectUrl = "./../../pages/tables/holidayadd.php?view=holidayadd";
                window.location = redirectUrl;
            });


            $(function () {
                //"use strict";
                function datatopbarupdate() {
                    $.ajax({
                        type: 'POST',
                        url: './../../ajax-topbardata.php',
                        success: function (data) {
                            var json = $.parseJSON(data);
                            //alert(json.user_no_task_today);
                            $('.unestimated_task span.label').html(json.unestimted_task_count);
                            $('.project span.label').html(json.project);
                            $('.task_inprogress span.label').html(json.task_inprogress);
                        },
                    });
                }
                ;
                datatopbarupdate();

                var refInterval = setInterval(function () {
                    datatopbarupdate();
                }, 30000); // 30 seconds


                var view = "<?php echo $_REQUEST['view']; ?>";
                var pid = "<?php echo $_REQUEST['id']; ?>";

                    $('#example1').dataTable({
                        "aoColumns": [
                            {"sType": "date-uk"},
                            {"sType": "string"},
                            {"sType": "string"},
                        {"sType": "string"}
                        ],
                    //"aaSorting": [[0, "asc"]],
                        "iDisplayLength": 25
                    });

                //On Change Of Project Dropdown
                $('#project-select').change(function () {
                    //alert($('#project-select').val());
                    window.location = "./../../pages/tables/data.php?view=" + view + "&id=" + $('#project-select').val();
                    ///jiraadmin/pages/tables/data.php?view=<?php echo $action; ?>&id=
                });

                $('#chkAll').on('ifClicked', function () {
                    if ($('#chkAll').is(':checked')) {
                        $('#chkAll,.chkthis').iCheck('uncheck');
                    } else {
                        $('#chkAll,.chkthis').iCheck('check');
                    }
                });

                $('.sendMailPOP').click(function () {
                    //First - Collect all the users emailid (comma seperated)
                    var emailids = '';
                    $.each($('.chkthis:checkbox:checked'), function () {
                        emailids += $(this).val() + ',';
                    });

                    $('#sendtoaddress').val(emailids);
                    //Clear form Errors
                    $('input[name="email_to"],.wysihtml5-sandbox').css('border-color', '#ccc');

                    //Fourthly -  Show Modal box
                    $("#compose-modal").modal('show');

                });


                function validateEmail(field) {
                    //var regex=/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i;
                    var regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
                    return (regex.test((field))) ? true : false;
                }
                function validateMultipleEmailsCommaSeparated(value) {
                    var result = value.split(",");

                    //alert(result);
                    for (var i = 0; i < result.length; i++)
                        if (!validateEmail(result[i]))
                            return false;
                    return true;
                }

                //By Default
                $('#chkAll,.chkthis').iCheck('uncheck');
                $('input[name="email_to"],.wysihtml5-sandbox').css('border-color', '#ccc');
                //Initialize WYSIHTML5 - text editor
                $("#email_message").wysihtml5();

                $('.sendMail').click(function () {
                    var flag = 1;

                    $.each($('input[name="email_to"],#email_message'), function () {
                        if ($(this).val() == '') {

                            if ($(this).attr('id') == 'email_message') {
                                $('.wysihtml5-sandbox').css('border-color', 'red');
                            } else {
                                $(this).css('border-color', 'red');
                            }
                            flag = 0;
                        } else {
                            $(this).css('border-color', '#ccc');
                        }

                        if (flag == 1 && $(this).attr('name') == 'email_to') {
                            k = $(this).val();
                            flag = validateMultipleEmailsCommaSeparated(k.substring(0, k.length - 1)) ? 1 : 0;

                            $(this).css('border-color', '#ccc');
                        }
                    });

                    if (flag) {

                        $('#loading-I').show();

                        $.ajax({
                            url: './../../sendmail.php',
                            type: 'post',
                            data: 'to=' + k.substring(0, k.length - 1) + '&message=' + $('#email_message').val(),
                            success: function (response) {
                                if (response == 'Message sent!') {
                                    alert('Message Sent!!');
                                    $('#compose-modal').modal('hide');
                                } else {
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
                '.chosen-select': {},
                '.chosen-select-deselect': {allow_single_deselect: true},
                '.chosen-select-no-single': {disable_search_threshold: 10},
                '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
                '.chosen-select-width': {width: "95%"}
            }
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
        </script>

        <script type="text/javascript" src="./../../js/bootstrap-daterangepicker-master/moment.js"></script>
        <script type="text/javascript" src="./../../js/bootstrap-daterangepicker-master/daterangepicker.js"></script>

        <script type="text/javascript">
            $(document).ready(function () {


                var cb = function (start, end, label) {
                    //console.log(start.toISOString(), end.toISOString(), label);
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    //alert("Callback has fired: [" + start.format('MMMM D, YYYY') + " to " + end.format('MMMM D, YYYY') + ", label = " + label + "]");
                };

                fromDate = '<?php echo isset($_REQUEST['from']) && !empty($_REQUEST['from']) ? date('m/d/Y', strtotime($_REQUEST['from'])) : ''; ?>';
                startDatePic = typeof (fromDate) == "undefined" || fromDate == '' ? moment().subtract(3, 'years') : fromDate;
                endDate = '<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to']) ? date('m/d/Y', strtotime($_REQUEST['to'])) : ''; ?>';
                endDatePic = typeof (endDate) == "undefined" || endDate == '' ? moment() : endDate;


                if (<?php echo $action == 'task-no-duedate' && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ? 1 : 0 ?>) {
                    startDatePic = moment().subtract(31, 'days');//.format('MMMM D, YYYY');
                } else if (<?php echo $action == 'total-time-spent-assignee-last-month' && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ? 1 : 0 ?>) {
                    startDatePic = moment().startOf('month');
                    endDatePic = moment().endOf('month');
                } else if (<?php echo ($action == 'active-projects' || $action == 'unestimated_task') && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ? 1 : 0; ?>) {
                    startDatePic = moment().subtract(7, 'days');//.format('MMMM D, YYYY');
                }

                //alert('fromDate'+moment().subtract(3, 'days'));
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
                        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        firstDay: 1
                    }
                };

                fromDate = '<?php echo isset($_REQUEST['from']) && !empty($_REQUEST['from']) ? date('M d,Y', strtotime($_REQUEST['from'])) : ''; ?>';
                startDatePic = typeof (fromDate) == "undefined" || fromDate == '' ? moment().subtract(3, 'years').format('MMM D, YYYY') : fromDate;
                endDate = '<?php echo isset($_REQUEST['to']) && !empty($_REQUEST['to']) ? date('M d,Y', strtotime($_REQUEST['to'])) : ''; ?>';
                endDatePic = typeof (endDate) == "undefined" || endDate == '' ? moment().format('MMM D, YYYY') : endDate;

                if (<?php echo $action == 'task-no-duedate' && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ? 1 : 0; ?>) {
                    startDatePic = moment().subtract(31, 'days').format('MMM D, YYYY');
                } else if (<?php echo $action == 'total-time-spent-assignee-last-month' && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ? 1 : 0 ?>) {
                    startDatePic = moment().startOf('month').format('MMM D, YYYY');
                    endDatePic = moment().endOf('month').format('MMM D, YYYY');
                } else if (<?php echo ($action == 'active-projects' || $action == 'unestimated_task' ) && !isset($_REQUEST['from']) && empty($_REQUEST['from']) ? 1 : 0; ?>) {
                    startDatePic = moment().subtract(7, 'days').format('MMM D, YYYY');
                }


                //alert('Second '+startDatePic+ '  '+endDatePic );
                $('#reportrange span').html(startDatePic + ' - ' + endDatePic);

                $('#reportrange').daterangepicker(optionSet1, cb);

                $('#options1').click(function () {
                    $('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
                });


                $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
                    /*console.log("apply event fired, start/end dates are " 
                     + picker.startDate.format('MMMM D, YYYY') 
                     + " to " 
                     + picker.endDate.format('MMMM D, YYYY')
                     );*/

                    window.location = "./../../pages/tables/top-menudata.php?view=<?php echo $_REQUEST['view'] ?>&id=<?php echo $_REQUEST['id'] ?>&from=" + picker.startDate.format('MMMM D, YYYY') + "&to=" + picker.endDate.format('MMMM D, YYYY');

                });
            });
        </script>
        <style>
            .modal-dialog{left: 0%;}
        </style>
        <script type="text/javascript">
            $('aside.left-side.sidebar-offcanvas').addClass('collapse-left');
            $('aside.right-side').addClass('strech');
        </script>
    </body>
</html>
