<?php 

//Check Session
if( !isset( $_SESSION['uid'] , $_SESSION['username'] ) && empty($_SESSION['uid']) && empty($_SESSION['username'])){
    
    $_SESSION['uri'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   
    echo "<script>window.location.href='login.php'</script>";
    exit;
}

/* ?>
<header class="header">
    <a href="index.php" class="logo" style="width: 100px">
        <!-- Add the class icon to your logo image or logo icon to add the margining -->
        Jira
    </a>
    
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <div class="container" style="display: inline-block; width: 72%;">
          
          <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav menubar">
                
                <li class="<?php echo $action == 'task-in-progess' ?"active":"";?>">
                    <a href="pages/tables/top-menudata.php?view=task-in-progess">
                        <span>Tasks in Progress</span>
                    </a>
                </li>
                <!--<li class="active">
                    <a href="pages/tables/top-menudata.php?view=active-projects">
                        <i class="fa fa-table"></i> <span>Active Projects</span>
                    </a>
                </li>-->
                <li class="<?php echo $action == 'active-users' ?"active":"";?>">
                    <a href="pages/tables/top-menudata.php?view=active-users">
                        <span>Active Users</span>
                    </a>
                </li>
                <li class="<?php echo $action == 'users-no-task-today' ?"active":"";?>">
                    <a href="pages/tables/top-menudata.php?view=users-no-task-today">
                        <span>Users without task</span>
                    </a>
                </li>
                <li class="<?php echo $action == 'task-no-duedate' ?"active":"";?>">
                    <a href="pages/tables/top-menudata.php?view=task-no-duedate">
                        <span>Tasks without Due Date</span>
                    </a>
                </li>
                <li class="<?php echo $action == 'unestimated_task' ?"active":"";?>">
                    <a href="pages/tables/top-menudata.php?view=unestimated_task">
                        <span>Unestimated Tasks</span>
                    </a>
                </li>
              <li class="dropdown">
                <a aria-expanded="false" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#">Dropdown <span class="caret"></span></a>
                <ul role="menu" class="dropdown-menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li class="divider"></li>
                  <li class="dropdown-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
        
        <!-- Sidebar toggle button-->
        
        <a href="#" class="navbar-btn sidebar-toggle" style="display:block" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="navbar-right">
            <ul class="nav navbar-nav">
                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown messages-menu task_inprogress">
                    <a href="pages/tables/top-menudata.php?view=task-in-progess" <?php /* class="dropdown-toggle" data-toggle="dropdown" **| ?> >
                        <i class="fa fa-envelope"></i>
                        <span class="label label-success ">0</span>
                    </a>
                    
                </li>
                <!-- Notifications: style can be found in dropdown.less -->
                <li class="dropdown notifications-menu unestimated_task">
                    <a href="pages/tables/top-menudata.php?view=unestimated_task" <?php /* class="dropdown-toggle" data-toggle="dropdown" <?php **| ?> >
                        <i class="fa fa-user"></i>
                        <span class="label label-danger">0</span>
                    </a>
                    <?php /* ?>
                    <ul class="dropdown-menu">
                        <li class="header">You have 10 notifications</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <li>
                                    <a href="#">
                                        <i class="ion ion-ios7-people info"></i> 5 new members joined today
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-warning danger"></i> Very long description here that may not fit into the page and may cause design problems
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-users warning"></i> 5 new members joined
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="ion ion-ios7-cart success"></i> 25 sales made
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="ion ion-ios7-person danger"></i> You changed your username
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer"><a href="#">View all</a></li>
                    </ul>
                    <?php
                     **| ?>
                </li>
                <!-- Tasks: style can be found in dropdown.less -->
                <li class="dropdown tasks-menu project">
                    <a href="pages/tables/data.php?view=active-projects" <?php /* class="dropdown-toggle" data-toggle="dropdown" <?php **| ?> >
                        <i class="fa fa-tasks"></i>
                        <span class="label label-warning">0</span>
                    </a>
                    <?php /*?>
                    <ul class="dropdown-menu">
                        <li class="header">You have 9 tasks</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            Design some buttons
                                            <small class="pull-right">20%</small>
                                        </h3>
                                        <div class="progress xs">
                                            <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only">20% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li><!-- end task item -->
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            Create a nice theme
                                            <small class="pull-right">40%</small>
                                        </h3>
                                        <div class="progress xs">
                                            <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only">40% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li><!-- end task item -->
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            Some task I need to do
                                            <small class="pull-right">60%</small>
                                        </h3>
                                        <div class="progress xs">
                                            <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only">60% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li><!-- end task item -->
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            Make beautiful transitions
                                            <small class="pull-right">80%</small>
                                        </h3>
                                        <div class="progress xs">
                                            <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only">80% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li><!-- end task item -->
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="#">View all tasks</a>
                        </li>
                    </ul>
                    <?php
                     **| ?>
                </li>
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-user"></i>
                        <span>Vinutha <i class="caret"></i></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header bg-light-blue">
                            <img src="img/avatar3.png" class="img-circle" alt="User Image" />
                            <p>
                                Vinutha

                            </p>
                        </li>
                        <!-- Menu Body -->
                        <?php /*
                        <li class="user-body">
                            <div class="col-xs-4 text-center">
                                <a href="#">Followers</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#">Sales</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#">Friends</a>
                            </div>
                        </li>
                        <?php
                         **| ?>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <?php /*
                            <div class="pull-left">
                                <a href="#" class="btn btn-default btn-flat">Profile</a>
                            </div>
                            <?php 
                             **| ?>
                            <div class="pull-right">
                                <a href="#" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
<style>
    body > .header .navbar{margin-left:100px;}
    .navbar-static-top .container{padding-left: 0px;padding-right: 0px;}
    .nav > li > a{  padding: 15px 5px; }
    .nav > li:after{  border-right: 1px solid #fff; }
    .navbar-static-top ul.nav.menubar.navbar-nav > li{ border-right: 1px solid #ffffff;}
</style>
<?php **/ ?>

<div class="navbar-collapse collapse" style="float:right;">
    <div style="float: left; padding: 15px;"><?php echo ucfirst($_SESSION['username']); ?></div>
    
    <ul class="nav navbar-nav menubar">
      <li class="dropdown">
        <a aria-expanded="false" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#">Menu <span class="caret"></span></a>
        <ul role="menu" class="dropdown-menu">          
            <?php if(in_array("task-in-progess",$_SESSION['role_permission'])){ ?>
            <li class="<?php echo $action == 'task-in-progess' ?"active":"";?>">
                <a href="pages/tables/top-menudata.php?view=task-in-progess">
                    <span>Tasks in Progress</span>
                </a>
            </li>
            <?php }if(in_array("active-users",$_SESSION['role_permission'])){ ?>
            <!--<li class="active">
                <a href="pages/tables/top-menudata.php?view=active-projects">
                    <i class="fa fa-table"></i> <span>Active Projects</span>
                </a>
            </li>-->
            <li class="<?php echo $action == 'active-users' ?"active":"";?>">
                <a href="pages/tables/top-menudata.php?view=active-users">
                    <span>Active Users</span>
                </a>
            </li>
            <?php }if(in_array("users-no-task-today",$_SESSION['role_permission'])){ ?>
            <li class="<?php echo $action == 'users-no-task-today' ?"active":"";?>">
                <a href="pages/tables/top-menudata.php?view=users-no-task-today">
                    <span>Users without task</span>
                </a>
            </li>
            <?php }if(in_array("task-no-duedate",$_SESSION['role_permission'])){ ?>
            <li class="<?php echo $action == 'task-no-duedate' ?"active":"";?>">
                <a href="pages/tables/top-menudata.php?view=task-no-duedate">
                    <span>Tasks without Due Date</span>
                </a>
            </li>
            <?php }if(in_array("unestimated_task",$_SESSION['role_permission'])){ ?>
            <li class="<?php echo $action == 'unestimated_task' ?"active":"";?>">
                <a href="pages/tables/top-menudata.php?view=unestimated_task">
                    <span>Unestimated Tasks</span>
                </a>
            </li>
            <?php }if(in_array("holiday",$_SESSION['role_permission'])){ ?>
            <li class="<?php echo $action == 'holiday' ?"active":"";?>">
                <a href="pages/tables/holiday.php?view=holiday">
                    <span>Holiday</span>
                </a>
            </li>
            <?php } ?>
            <li class="divider"></li>
            <?php if(in_array("project_worklogs",$_SESSION['role_permission'])||in_array("total-time-spent-assignee-last-month",$_SESSION['role_permission'])){ ?>
            <li class="dropdown-header">User Reports</li>
            <?php if(in_array("project_worklogs",$_SESSION['role_permission'])){ ?>
            <li class="<?php echo $action == 'active-projects' ?"active":"";?>"><a href="pages/tables/data.php?view=active-projects"><i class="fa fa-angle-double-right"></i> Project Worklogs</a></li>
            <?php } 
                    if(in_array("total-time-spent-assignee-last-month",$_SESSION['role_permission'])&& $_SESSION['highest_role']=='1'){ ?>
                        <li class="<?php echo $action == 'total-time-spent-assignee-last-month' ?"active":"";?>"><a href="pages/tables/top-menudata.php?view=total-time-spent-assignee-last-month"><i class="fa fa-angle-double-right"></i> User Worklogs </a></li>
            <?php  } else if(in_array("total-time-spent-assignee-last-month", $_SESSION['role_permission'])&& $_SESSION['highest_role'] !='1'){ ?>
                        <li class="<?php echo $action == 'total-time-spent-assignee-last-month' ? "active" : ""; ?>"><a href="pages/tables/user-time-spent.php?username=<?php echo $_SESSION['username'] ?>&from=<?php echo date("M 01,Y");  ?>&to=<?php echo date("M t,Y"); ?>"><i class="fa fa-angle-double-right"></i> My Worklogs</a></li>
            <?php  }if(!in_array(1,$_SESSION['user_role'])){ ?>
            <li class="<?php echo $action == 'leaves' ?"active":"";?>">
                <a target="_blank" href="leave/index.php?view=leaves&id=<?php echo base64_encode($_SESSION['username']); ?>">
                    <i class="fa fa-angle-double-right"></i>
                    <span>Leaves</span>
                </a>
            </li>
            <?php }?>
            <li class="divider"></li>
            <?php }if(in_array("epic_report",$_SESSION['role_permission'])||in_array("sprint_report",$_SESSION['role_permission'])){ ?>
            <li class="dropdown-header">Project Reports</li>
             <?php if(in_array("epic_report",$_SESSION['role_permission'])){ ?>
                <li class="<?php echo $action == 'all-projects' ?"active":"";?>"><a href="pages/tables/epic-all-projects.php?view=all-projects"><i class="fa fa-angle-double-right"></i> Epic Wise Report</a></li>
                <?php }if(in_array("sprint_report",$_SESSION['role_permission'])){ ?>
                <li class="<?php echo $action == 'sprint-all-projects' ?"active":"";?>"><a href="pages/tables/sprint-all-projects.php?view=sprint-all-projects"><i class="fa fa-angle-double-right"></i> Sprint Wise Report</a></li>
                <?php } ?>
            <li class="divider"></li>
             <?php }if(in_array("user_permission",$_SESSION['role_permission'])) { ?>
            <li class="">
                <a href="pages/tables/user_permission.php">
                    <span>User Permission</span>
                </a>
            </li>
            <?php }  if(in_array("change-password",$_SESSION['role_permission'])) { ?>
            <li class="">
                <a href="pages/tables/change-password.php">
                    <span>Change Password</span>
                </a>
            </li>
            <?php } ?>
            <li class="<?php echo $action == 'unestimated_task' ?"active":"";?>">
                <a href="logout.php">
                    <span>Logout</span>
                </a>
            </li>
        </ul>
      </li>
    </ul>
</div><!--/.nav-collapse -->
<style>
    .no-print{display: none;}
</style>
