<?php /* ?>


    <ul class="sidebar-menu">
            <li class="active">
                <a href="index.php">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <?php $datatables = 1;?>
            
            <li class="<?php echo $action == 'task-in-progess' ?"active":"";?>">
                <a href="pages/tables/top-menudata.php?view=task-in-progess">
                    <i class="fa fa-table"></i> <span>Tasks in Progress</span>
                </a>
            </li>
            <!--<li class="active">
                <a href="pages/tables/top-menudata.php?view=active-projects">
                    <i class="fa fa-table"></i> <span>Active Projects</span>
                </a>
            </li>-->
            <li class="<?php echo $action == 'active-users' ?"active":"";?>">
                <a href="pages/tables/top-menudata.php?view=active-users">
                    <i class="fa fa-table"></i> <span>Active Users</span>
                </a>
            </li>
            <li class="<?php echo $action == 'users-no-task-today' ?"active":"";?>">
                <a href="pages/tables/top-menudata.php?view=users-no-task-today">
                    <i class="fa fa-table"></i> Users without task
                </a>
            </li>
            <li class="<?php echo $action == 'task-no-duedate' ?"active":"";?>">
                <a href="pages/tables/top-menudata.php?view=task-no-duedate">
                    <i class="fa fa-table"></i> <span>Tasks without Due Date</span>
                </a>
            </li>
            <li class="<?php echo $action == 'unestimated_task' ?"active":"";?>">
                <a href="pages/tables/top-menudata.php?view=unestimated_task">
                    <i class="fa fa-table"></i> <span>Unestimated Tasks</span>
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-table"></i> <span>User Reports</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu" style="<?php echo $datatables ?"display:block":"";?>">
                    <li class="<?php echo $action == 'active-projects' ?"active":"";?>"><a href="pages/tables/data.php?view=active-projects"><i class="fa fa-angle-double-right"></i> Project Worklogs</a></li>
                    <li class="<?php echo $action == 'total-time-spent-assignee-last-month' ?"active":"";?>"><a href="pages/tables/top-menudata.php?view=total-time-spent-assignee-last-month"><i class="fa fa-angle-double-right"></i> User Worklogs </a></li>

                </ul>

            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-table"></i> <span>Project Reports</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu" style="<?php echo $datatables ?"display:block":"";?>">
                    <li class="<?php echo $action == 'all-projects' ?"active":"";?>"><a href="pages/tables/epic-all-projects.php?view=all-projects"><i class="fa fa-angle-double-right"></i> Epic Wise Report</a></li>
                    <li class="<?php echo $action == 'sprint-all-projects' ?"active":"";?>"><a href="pages/tables/sprint-all-projects.php?view=sprint-all-projects"><i class="fa fa-angle-double-right"></i> Sprint Wise Report</a></li>

                </ul>

            </li>
    </ul>
<?php */ ?>