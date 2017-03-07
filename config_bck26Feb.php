<?php

session_start();

/* * ******************************************** Database Configuration *************************************** */

/* * **** server Credentials **** */

//$host        = "host=182.74.170.33";
$host = "host=192.168.2.33";
$port = "port=5432";
$dbname = "dbname=bugtracker";
$credentials = "user=phpreports password=phpreporter@123";

/* * **** local Credentials **** */

/* $host        = "host=127.0.0.1";
  $port        = "port=5432";
  $dbname      = "dbname=jira_6feb";
  $credentials = "user=phpreports password=phpreporter@123"; */

/*********** IMAGE IMPORT URL ******************/
$GLOBALS['jira_url'] = 'http://192.168.2.33:8080';

$db = pg_pconnect("$host $port $dbname $credentials");
if (!$db) {
    echo "Error : Unable to open database\n";
} else {
    //error_log( "Opened database successfully\n" );
}



/* * ******************************************** Queries ***************************************************** */

$sqlQueries = array(
    'assignee-time-est-resolutiondate' => array(
        "query" => "select
						to_char(resolutiondate, 'YYYY-MM-DD') as resolutiondate, assignee,
						sum(coalesce(timespent, 0)) as TimeSpent

						from jiraissue
						where resolutiondate is not null
						and timespent > 0
						and assignee is not null

						group by to_char(resolutiondate, 'YYYY-MM-DD'), assignee
						order by to_char(resolutiondate, 'YYYY-MM-DD')",
        "columns" => array('resolutiondate' => 'Resolution Date', 'assignee' => 'Assignee', 'TimeSpent' => 'Time Spent'),
    ),
    'Task_Estimate_report' => array(
        "query" => "select assignee,count(issuetype) as Total_Tasks,sum(timespent) as Estimated_hours
					from jiraissue where to_char(created,'YYYY-MM-DD')= ?
					group by assignee",
        "columns" => array('Total_Tasks' => 'Total Tasks', 'Estimated_hours' => 'Estimated hours'),
    ),
    'total-time-spent-assignee-last7days' => array(
        "query" => "select
						to_char(resolutiondate, 'YYYY-MM-DD') as resolutiondate,
						left(to_char(resolutiondate, 'day'),3) as day,
						assignee,
						sum(coalesce(timespent, 0)) as TimeSpent
					
						from jiraissue
						where resolutiondate is not null
						and resolutiondate::date > (CURRENT_DATE::date - INTERVAL '7 days')
					
						group by to_char(resolutiondate, 'YYYY-MM-DD'), 
						assignee,left(to_char(resolutiondate, 'day'),3)
						having sum(coalesce(timespent, 0)) != 0
						order by to_char(resolutiondate, 'YYYY-MM-DD')",
        "columns" => array('resolutiondate' => 'Resolution Date', 'day' => 'Day', 'assignee' => 'Assignee', 'TimeSpent' => 'Time Spent'),
    ),
    'total-time-spent-assignee-last-month' => array(
        
        "query_old"=>"select author as assignee, TO_CHAR((sum(E.timeworked) || ' second')::interval, 'HH24:MI') as timespent 
                    from worklog E where %s 
                    group by author 
                    having sum(coalesce(E.timeworked, 0)) != 0 
                    order by author",
        
        "query"=>"select E.author as assignee, C.parent_name as parent_name,TO_CHAR((sum(E.timeworked) || ' second')::interval, 'HH24:MI') as timespent 
                    from 
                    worklog E,
                    cwd_membership C,
                    app_user B,
		    cwd_user D
                    where %s and D.id = C.child_id and C.parent_id not in (10002,10823,10824) and D.user_name = B.lower_user_name
                    and E.author = B.user_key
                    group by E.author,B.id,C.id,D.id
                    order by E.author",
        "query_timeremaining"=>"select j.assignee,TO_CHAR((sum(j.timeestimate) || ' second')::interval, 'HH24:MI') as timeestimateremaining 
                        from (
                            select 
                                i.id as iid,i.assignee,i.timeestimate
                                from worklog W,jiraissue i
                                where i.id=w.issueid and %s
                        UNION
                            select i.id as iid,i.assignee,i.timeestimate
                                from jiraissue i
                                where %s
                        ) as j
                        group by j.assignee",
        "columns" => array('assignee' => 'Assignee','parent_name'=>'Department' ,'timespent' => 'Time Spent'),
    ),
    'total-time-spent-assignee-daterange' => array(
        "query" => "select
						to_char(resolutiondate, 'YYYY-MM-DD') as resolutiondate,
						left(to_char(resolutiondate, 'day'),3) as day,
						assignee,
						sum(coalesce(timespent, 0)) as TimeSpent

						from jiraissue
						where resolutiondate is not null
						and to_char(resolutiondate, 'YYYY-MM-DD') BETWEEN ? AND ?

						group by to_char(resolutiondate, 'YYYY-MM-DD'), 
						assignee,left(to_char(resolutiondate, 'day'),3)

						having sum(coalesce(timespent, 0)) != 0
						order by to_char(resolutiondate, 'YYYY-MM-DD')",
        "columns" => array('resolutiondate' => 'Resolution Date', 'day' => 'Day', 'assignee' => 'Assignee', 'TimeSpent' => 'Time Spent'),
    ),
    'total-time-spent-assignee-monthwise' => array(
        "query" => "select
						to_char(resolutiondate, 'YYYY-MM-DD') as resolutiondate,
						left(to_char(resolutiondate, 'day'),3) as day,
						left(to_char(resolutiondate, 'month'),3) as month,
						to_char(resolutiondate, 'YYYY') as Year,
						assignee,
						sum(coalesce(timespent, 0)) as TimeSpent

						from jiraissue
						where resolutiondate is not null
						and left(to_char(resolutiondate, 'month'),3)= ?
						and to_char(resolutiondate, 'YYYY') = ?

						group by to_char(resolutiondate, 'YYYY-MM-DD'), 
						assignee,left(to_char(resolutiondate, 'day'),3),
						left(to_char(resolutiondate, 'month'),3),
						to_char(resolutiondate, 'YYYY')


						having sum(coalesce(timespent, 0)) != 0
						order by to_char(resolutiondate, 'YYYY-MM-DD') ",
        "columns" => array('resolutiondate' => 'Resolution Date', 'day' => 'Day', 'month' => 'Month', 'Year' => 'Year', 'assignee' => 'Assignee', 'TimeSpent' => 'Time Spent'),
    ),
    'total-time-spent-assignee-projectwise' => array(
        "query" => "select
						to_char(a.resolutiondate, 'YYYY-MM-DD') as resolutiondate, 
						left(to_char(a.resolutiondate, 'day'),3) as day,
						a.assignee as assignees,
						b.pname as pnames,
						sum(coalesce(a.timespent, 0)) as TimeSpent

						from jiraissue a
						left join project b on a.project = b.id
						where a.resolutiondate is not null
						and to_char(a.resolutiondate, 'YYYY-MM-DD') BETWEEN ? AND ?

						group by to_char(a.resolutiondate, 'YYYY-MM-DD'), 
						a.assignee,
						b.pname,
						left(to_char(a.resolutiondate, 'day'),3)

						having sum(coalesce(a.timespent, 0)) != 0

						order by to_char(a.resolutiondate, 'YYYY-MM-DD')",
        "columns" => array('resolutiondate' => 'Resolution Date', 'day' => 'Day', 'assignees' => 'Assignee', 'pnames' => 'Project Name', 'TimeSpent' => 'Time Spent'),
    ),
    'total-time-spent-assignee' => array(
        "query" => "select
						to_char(resolutiondate, 'YYYY-MM-DD') as resolutiondate, 
						left(to_char(resolutiondate, 'day'),3) as day,
						assignee,
						sum(coalesce(timespent, 0)) as TimeSpent

						from jiraissue
						where resolutiondate is not null
						and to_char(resolutiondate, 'YYYY-MM-DD') BETWEEN ? AND ?

						group by to_char(resolutiondate, 'YYYY-MM-DD'), 
						assignee,left(to_char(resolutiondate, 'day'),3)
						having sum(coalesce(timespent, 0)) != 0
						order by to_char(resolutiondate, 'YYYY-MM-DD')",
        "columns" => array('resolutiondate' => 'Resolution Date', 'day' => 'Day', 'assignee' => 'Assignee', 'TimeSpent' => 'Time Spent'),
    ),
    'assignee-total-est-project-selection_old' => array(
        "query" => "select A.assignee as assignee,B.pname as pnames,count(A.issuetype) as Total_Tasks,
						concat(TO_CHAR((sum(A.timespent) || ' second')::interval, 'HH24:MI'), ' hrs') as timespent,
						concat(TO_CHAR((sum(A.timeoriginalestimate) || ' second')::interval, 'HH24:MI'), ' hrs') as timeorginalestimate

						from jiraissue A
						inner join
						project B
						on A.project=B.id
						where B.id= %d
                                                %s
						group by A.assignee,B.pname
						order by B.pname",
        "columns" => array('assignee' => 'Assignee', 'total_tasks' => 'Total Task', 'timeorginalestimate' => 'Original Time Estimate', 'timespent' => 'Time Spent'),
    ),
    'allprojects' => array(
        "query" => 'select id,pname from project',
    ),
    'getProjectName' => array('query' => 'select pname from project where id= %d'),
    'this_weekchart_dashboard' => array(//inner join worklog W on A.project=B.id
        "query"=>"SELECT 
                        A.project as pid,
                        B.pname as pnames,
                        concat(TO_CHAR((sum(W.timeworked) || ' second')::interval, 'HH24:MI'), ' hrs') as timespent,
                        concat(TO_CHAR((sum(A.timeoriginalestimate) || ' second')::interval, 'HH24:MI'), ' hrs') as timeorginalestimate
                    from
                    jiraissue as A
                    inner join project B on A.project=B.id
                    inner join worklog W on A.id=W.issueid
                    where to_char(W.startdate::date, '2YYY-MM-DD')::DATE > (CURRENT_DATE::date - INTERVAL '7 days')
                    group by B.pname,A.project ",
        "columns" => array('pnames' => 'Project Name', 'timeorginalestimate' => 'Original Time Estimate', 'timespent' => 'Time Spent'),
        "reportColumns" => array('pnames' => 'Project Name', 'timeorginalestimate' => 'Time Estimate', 'timespent' => 'Time Spent'),
        "query_old" => "select  
                            A.project as pid, 
                            B.pname as pnames,
                            concat(TO_CHAR((sum(A.timespent) || ' second')::interval, 'HH24'), ' hrs') as timespent,
                            concat(TO_CHAR((sum(A.timeoriginalestimate) || ' second')::interval, 'HH24:MI'), ' hrs') as timeorginalestimate

                        from jiraissue A
                        inner join project B on A.project=B.id
                        where A.updated::date > (CURRENT_DATE::date - INTERVAL '7 days')
                        and timespent is not null
                        group by A.project,B.pname
                        order by B.pname",        
    ),
    'active_projects' => array(
        'query' => "SELECT count(distinct project) as project
                                           FROM jiraissue i inner join worklog w on w.issueid = i.id WHERE to_char(w.startdate::date, '2YYY-MM-DD')::DATE BETWEEN (now() - '7 days'::interval)::timestamp AND now()"),
    'task_in_progress' => array('query' => "select count(*) as task_inprogress from jiraissue where issuestatus = '3'"),
    'no_active_users' => array('query' => "select count(*) as no_active_users from cwd_user where active='1'"),
    'unestimated_task' => array(
        'query' => "select  
                            TO_CHAR(A.created, 'DD/MM/YYYY') as created, 
                            A.reporter as reporter,
                            A.assignee as assignee,
                            B.pkey as pkey,
                            A.issuenum as issuenum,
                            CONCAT(B.pname,CONCAT(' - ',A.summary)) as summary 
                        from jiraissue A 
                        inner join project B on A.project=B.id 
                        where timeoriginalestimate is null and %s",
        'count_query' => "select count(*) as unestimted_task_count
                                from jiraissue A 
                                inner join project B on A.project=B.id 
                                where timeoriginalestimate is null 
                                  and A.created > (CURRENT_DATE::date - INTERVAL '7 days')",
        'columns' => array('gotojira' => 'Jira Link', 'summary' => 'Task', 'reporter' => 'Reporter', 'assignee' => 'Asssignee', 'created' => 'Created At')),
    'task-in-progess' => array(
        'query' => "select A.project as pid
                          ,A.issuenum as issuenum
                          ,B.pkey as pkey
                          ,A.reporter as reporter
                          ,A.assignee as assignee
                          , CONCAT(B.pname,CONCAT(' - ',A.summary)) as summary
                          ,TO_CHAR(A.created, 'DD/MM/2YYY') as created 
                          ,TO_CHAR(A.duedate, 'DD/MM/2YYY') as duedate 
                    from jiraissue as A 
                    inner join project B on A.project=B.id 
                    where issuestatus = '3'
                    %s",
        'columns' => array('gotojira'=>'Jira Link','summary' => 'Summary','reporter' => 'Reporter', 'assignee' => 'Assignee', 'created' => 'Created','duedate'=>"Due date"),
    ),
    'active-projects' => array(
        'query' => "SELECT project,
                           count(DISTINCT(A.id)) as task_count,
                           B.pname as pname,
                           B.lead as lead,
                           concat(TO_CHAR((sum(W.timeworked) || ' second')::interval, 'HH24:MI'), ' hrs') as timespent,
                           concat(TO_CHAR((sum(A.timeoriginalestimate) || ' second')::interval, 'HH24:MI'), ' hrs') as timeorginalestimate
                    from
                    jiraissue as A
                    inner join project B on A.project=B.id
                    inner join worklog W on A.id=W.issueid
                    where %s
                    group by pname,lead,project ",
        'columns' => array('pname' => 'Project', 'lead' => 'Lead', 'task_count' => "No of Tasks", 'timeorginalestimate' => "Estimate", 'timespent' => "Time Spent"),
    ),
    'all-projects' => array(
        'query' => "SELECT project,count(A.id) as task_count,
                        B.pname as pname,B.lead as lead,
                        concat(TO_CHAR((sum(A.timespent) || ' second')::interval, 'HH24:MI'), ' hrs') as timespent,
                        concat(TO_CHAR((sum(A.timeoriginalestimate) || ' second')::interval, 'HH24:MI'), ' hrs') as timeorginalestimate
                    from jiraissue as A
                    inner join project B on A.project=B.id
                    where issuetype='10000'
                    group by pname,lead,project ",
        'columns' => array('pname' => 'Project', 'lead' => 'Lead', 'task_count' => "No of Epics", /*'timeorginalestimate' => "Original Time Estimate", 'timespent' => "Time Spent"*/),
    ),
    'epic-data' => array(
        'query' => "SELECT 
                        count(distinct(j.assignee)) as assignee, 
                        count(j.id) as tasks,
                        (SELECT pname from project where id = '%s') as project,
                        (SELECT summary from jiraissue where issuetype='10000' and project = '%s' and id = l.source) as summary,
                        (SELECT id from jiraissue where issuetype='10000' and project = '%s' and id = l.source) as id,
                        (SELECT TO_CHAR(duedate,'DD/MM/2YYY') from jiraissue where issuetype='10000' and project = '%s' and id = l.source) as duedate,
                        (SELECT TO_CHAR(created,'DD/MM/2YYY') from jiraissue where issuetype='10000' and project = '%s' and id = l.source) as created,
                        (SELECT timeoriginalestimate from jiraissue where issuetype='10000' and project = '%s' and id = l.source) as estimate 
                    FROM jiraissue j 
                    INNER JOIN issuelink l ON  j.id = l.destination and linktype = '10200' 
                    WHERE project = '%s' group by l.source",
        'columns' => array('summary' => 'Epic Name', 'created' => 'Start Date', 'duedate' => 'End Date','tasks'=>'Nb of Issues', 'assignee' => 'No. of Assignees'),
    ),
    'epic-dashboard-pie' => array(
        'query' => "SELECT count(l.destination) as tasks,(SELECT count(id) as open from jiraissue i where i.id IN (select destination from issuelink where source = '%s') AND issuestatus = '1') as openTasks,(SELECT count(id) as progress from jiraissue i where i.id IN (select destination from issuelink where source = '%s') AND issuestatus = '3') as inProgress,(SELECT count(id) as repoened from jiraissue i where i.id IN (select destination from issuelink where source = '%s') AND issuestatus = '4') as reOpenedTasks,(SELECT count(id) as resolved from jiraissue i where i.id IN (select destination from issuelink where source = '%s') AND issuestatus = '5') as resolvedTasks,(SELECT count(id) as closed from jiraissue i where i.id IN (select destination from issuelink where source = '%s') AND issuestatus = '6') as closedTasks,(SELECT count(id) as inReview from jiraissue i where i.id IN (select destination from issuelink where source = '%s') AND issuestatus = '10000') as inReviewTasks,(SELECT count(id) as done from jiraissue i where i.id IN (select destination from issuelink where source = '%s') AND issuestatus = '10001') as doneTasks,(SELECT count(id) as todo from jiraissue i where i.id IN (select destination from issuelink where source = '%s') AND issuestatus = '10002') as todoTasks,(SELECT count(id) as unestimated from jiraissue i where i.id IN (select destination from issuelink where source = '%s') AND assignee IS NULL) as unassignedTasks from issuelink l where source = '%s'
    "
    ),
    'epic-dashboard' => array(
        'query' => "SELECT sum(timeoriginalestimate) as totalestimate,sum(timespent) as timespent,(SELECT pname from project where id = (SELECT project from jiraissue where id= '%s')) as project,(SELECT summary FROM jiraissue WHERE id = '%s') as summary,(SELECT project from jiraissue where id= '%s') as pid FROM jiraissue WHERE id IN (select destination from issuelink where source = '%s' ) "
    ),
    'task-plan' => array(
        'query' => "SELECT count(id) as total,(select count(id) as unestimated from jiraissue where id IN (select destination from issuelink where source = '%s') AND timeoriginalestimate IS NULL) as unestimated,(select count(id) as estimated from jiraissue where id IN (select destination from issuelink where source = '%s') AND timeoriginalestimate IS NOT NULL) as estimated FROM jiraissue WHERE id IN (select destination from issuelink where source = '%s')"
    ),
    'active-users' => array(
        'query' => "select user_name,email_address,TO_CHAR(created_date, 'DD/MM/2YYY') as created_date from cwd_user where active='1'",
        'columns' => array('profile_pic'=>'Jira Profile','user_name' => 'Username', 'email_address' => 'Email', 'created_date' => 'Created At'),
    ),
    'users-no-task-today' => array(
        'query' => "select user_name,email_address from cwd_user 
                                            where user_name NOT IN (
                                                SELECT distinct(B.lower_user_name) from jiraissue as A
                                                    inner join app_user B
                                                    on A.assignee=B.user_key WHERE A.issuestatus = '3' and A.assignee IS NOT NULL
                                            ) AND active = '1'",
        // sub query => was SELECT distinct(assignee) from jiraissue WHERE issuestatus = '3' and assignee IS NOT NULL
//'query'=>"select * from cwd_user",
//'query'=>"SELECT distinct(assignee) from jiraissue",
        'count_query' => "select count(user_name) as user_no_task from cwd_user where user_name NOT IN (SELECT distinct(assignee) from jiraissue WHERE issuestatus = '3' and assignee IS NOT NULL) AND active = '1'",
        'columns' => array('user_name' => 'User Name', 'email_address' => 'Email'),
    ),
    'data-project-assignee-task' => array(
        'query' => "SELECT 
                        A.id as iid,
                        A.summary as summary,
                        A.assignee as assignee,
                        C.pname as issuetypename,
                        D.pname as issuestatus,
                        B.pname as pname,
                        B.pkey as pkey,
                        B.id as pid,
                        A.issuenum as issuenum,
                        A.reporter as reporter,
                        TO_CHAR(A.created, 'DD/MM/2YYY') as created,
                        TO_CHAR(A.duedate, 'DD/MM/2YYY') as duedate,
                        
                        CONCAT(TO_CHAR((A.timeoriginalestimate || ' second')::interval, 'HH24:MI'),' hrs' ) as timeoriginalestimate,
                        TO_CHAR(A.resolutiondate, 'DD/MM/YYYY') as resolutiondate,
                        CONCAT(TO_CHAR((sum(E.timeworked) || ' second')::interval, 'HH24:MI'),' hrs' ) as timespent
                    from
                    jiraissue as A
                    inner join project B on A.project=B.id
                    inner join issuetype C on C.id=A.issuetype
                    inner join issuestatus D on D.id=A.issuestatus
                    inner join worklog E on E.issueid=A.id
                    where A.project= %d and (E.author like '%s') %s
                    group by E.issueid,A.id,C.pname,D.pname,B.id
                ",
        'columns' => array('gotojira'=>'Jira Link','summary' => 'Summary - reporter', 'issuetypename' => 'Issue Type', 'issuestatus' => 'Task Status',
            'created' => 'Created on', 'resolutiondate' => 'Resolution Date', 'duedate' => 'Duedate', 'timeoriginalestimate' => 'Original Time estimate', 'timespent' => 'Time Spent'),
    ),
    'task-no-duedate' => array(
        'query' => "SELECT 
                        CONCAT(B.pname,CONCAT(' - ',A.summary)) as summary
                       ,B.pkey as pkey
                       ,A.issuenum as issuenum
                       ,A.assignee as assignee
                       ,A.reporter as reporter
                       ,TO_CHAR(A.created, 'DD/MM/YYYY') as created
					from
					jiraissue as A
					inner join project B
					on A.project=B.id
                                        where A.duedate is null %s",
        'columns' => array('gotojira' => 'Jira Link', 'summary' => 'Summary', 'assignee' => 'Assignee', 'reporter' => 'Report To',
            'created' => 'Created At'),
    ),
    'user-task-time-spent-individual' => array(
        /*'query' => "SELECT project,timespent,pname,A.created,resolutiondate 
                                            FROM jiraissue as A 
                                            inner join project B
                                            on A.project=B.id 
                                            WHERE resolutiondate is not null and assignee like '%s' %s
                                            group by project,B.pname,A.created,resolutiondate,timespent order by project,created",
        */
        'query' => "SELECT project,W.timeworked as timespent,pname,W.startdate,W.updated as resolutiondate
                        FROM jiraissue as A 
                            inner join project B on A.project=B.id 
                            inner join worklog W on A.id=w.issueid 
                        WHERE author like '%s' %s 
                        group by project,pname,W.startdate,A.id,W.updated,W.timeworked 
                        order by A.id,project,W.startdate",
        'query_timeremaining'=>"select j.project,TO_CHAR((sum(j.timeestimate) || ' second')::interval, 'HH24:MI') as timeestimateremaining 
                        from (
                            select 
                                i.id as iid,i.project,i.timeestimate
                                from worklog W,jiraissue i
                                where i.id=w.issueid and %s
                        UNION
                            select i.id as iid,i.project,i.timeestimate
                                from jiraissue i
                                where %s
                        ) as j
                        group by j.project
        ",
        'columns' => array(
         
            'assignee' => 'Assignee', 'pname' => 'Project', 'from' => 'From', 'to' => 'To',
            'totaldays' => 'Total Days', 'totaltimespent' => 'Total Time Spent',
        ),
    ),
    'jiraworklogdata' => array(
        'query' => "select 
                        w.issueid as issueid,
                        w.author as author,
                        w.timeworked as timeworked,
                        TO_CHAR(w.startdate::date, 'DD-MM-2YYY') as created,
                        i.project as projectid,
                        i.updated as updated,
                        i.timeoriginalestimate as estimate,
                        i.timeestimate as remaining
                    from worklog w,jiraissue i 
                    where w.issueid = i.id 
                    group by i.project,w.author,w.issueid,w.timeworked,w.startdate,i.updated,i.timeestimate,i.timeoriginalestimate",
    ),
    'assignee-total-est-project-selection' => array(
        'query' => "select k.assignee,count(k.w_issueid) as total_task,
                           CONCAT(SEC_TO_TIME(sum(k.m_estimate)),' Hrs') as orgtimeestimate,
                           CONCAT(SEC_TO_TIME(sum(k.m_timeworked)),' Hrs') as timespent,
                           CONCAT(SEC_TO_TIME(sum(k.m_timeremaining)),' Hrs') as remaining 
                        from (
                            select w_author as assignee,
                                w_issueid,
                                i_timeorgestimate as m_estimate,
                                sum(w_timeworked) as m_timeworked, 
                                i_timeremaining as m_timeremaining 
                            from jira_worklog_issue 
                            where i_projectid=%d %s 
                            group by w_issueid,assignee
                        ) as k 
                        group by k.assignee",
        'columns' => array('assignee' => 'Assignee', 'total_task' => 'Total Tasks', 'orgtimeestimate'=>'Time Estimate','timespent' => 'Total Time Spent','remaining'=>'Remaining'),
    ),
    
    'epic-task-assigned-to-assignee' => array(
        'query' => "SELECT DISTINCT(assignee) as assignees ,TO_CHAR((sum(timeoriginalestimate) || ' second')::interval, 'HH24')  as total_timeestimate  
                    FROM jiraissue 
                     WHERE id IN (
                         select destination 
                             from issuelink 
                             where source = '%s' ) 
                     group by assignee
                     having sum(coalesce(timeoriginalestimate, 0)) != 0"
    ),
    'epic-assignee-report-gyr' => array(
        'query' => "SELECT DISTINCT(assignee) as assignees 
                        ,SUM(CASE WHEN (issuestatus IN ('5','6','10001')) THEN 1 ELSE 0 END ) as resolved
                        ,SUM(CASE WHEN (issuestatus IN ('3','4','10000')) THEN 1 ELSE 0 END ) as pending
                        ,SUM(CASE WHEN (issuestatus IN ('1','10002')) THEN 1 ELSE 0 END ) as overdue 
                     FROM jiraissue 
                     WHERE id IN (
                        select destination 
                            from issuelink 
                            where source = '%s'
                        ) 
                     group by assignees"
    ),
    'data-project-tasks' => array(
        'query' => "SELECT A.summary as summary,
                            A.assignee as assignee,
                            C.pname as issuetypename,
                            D.pname as issuestatus,
                            B.pname as pname,
                            TO_CHAR(A.created, 'DD/MM/YYYY') as created,
                            TO_CHAR(A.updated, 'DD/MM/YYYY') as updated,
                            TO_CHAR((A.timeoriginalestimate || ' second')::interval, 'HH24:MI') as timeoriginalestimate,
                            TO_CHAR((sum(W.timeworked) || ' second')::interval, 'HH24:MI') as timespent
                        from
                        jiraissue as A
                        inner join project B on A.project=B.id
                        inner join worklog W on A.id=W.issueid
                        inner join issuetype C on C.id=A.issuetype
                        inner join issuestatus D on D.id=A.issuestatus

                        where A.project= %d %s
                        group by A.summary,W.issueid,C.pname,D.pname,
                        A.created,A.updated,A.timeoriginalestimate,A.assignee,B.pname",
        'columns' => array('summary' => 'Summary', 'issuetypename' => 'Issue Type', 'issuestatus' => 'Task Status', 'assignee' => 'Assignee',
            'created' => 'Created on', 'updated' => 'Updated On','timeoriginalestimate' => 'Original Time estimate', 'timespent' => 'Time Spent'),
    ),
    'sprint-all-projects'=>array(
        "query"=>'select 
                        p.pname as pname,
                        p.id as pid,
                        p.lead as lead,
                        concat(TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\'), \' hrs\') as timespent,
                        concat(TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\'), \' hrs\') as timeorginalestimate,
                        count(DISTINCT(s."ID")) as nb_sprint
                    from
                        "customfieldvalue" as c, 
                        "jiraissue"  as i, 
                        "project" as p, 
                        "AO_60DB71_SPRINT" as s
                    where 
                        p.id = i.project and
                        c.issue = i.id and CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002
                    group by p.id,p.pname;',
        'columns' => array(/*'pid' => 'Project Id',*/ 'pname' => 'Project Name','lead'=>'Lead','nb_sprint'=>'No of Sprints','timeorginalestimate'=>'Original Time Estimate','timespent'=>'Time Spent'),
    ),
    'projects-sprint'=>array(
        "query"=>'select 
                        p.pname as pname,
                        s."ID" as sid,
                        s."NAME" as sname, 
                        s."START_DATE" as start_date, 
                        s."END_DATE" as end_date,
                        s."COMPLETE_DATE" as complete_date,
                        count(i.id) as nb_task,
                        concat(TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\'), \' hrs\') as totalestimate,
                        concat(TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\'), \' hrs\') as timespent,
                        (CASE WHEN(s."CLOSED") THEN \'CLOSED\' ELSE \'IN PROGRESS\' END)  as status
                    from
                        "customfieldvalue" as c, 
                        "jiraissue"  as i, 
                        "project" as p, 
                        "AO_60DB71_SPRINT" as s
                    where 
                        p.id = %s and p.id = i.project and
                        c.issue = i.id and CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002
                    group by s."ID",s."NAME",p.pname,s."START_DATE",s."END_DATE",s."COMPLETE_DATE";',
        "small_query"=>'select 
                        p.id as pid,
                        s."ID" as id,
                        s."NAME" as sname                        
                    from
                        "customfieldvalue" as c, 
                        "jiraissue"  as i, 
                        "project" as p, 
                        "AO_60DB71_SPRINT" as s
                    where 
                        p.id = %s and p.id = i.project and
                        c.issue = i.id and CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002
                    group by s."ID",s."NAME",p.id,s."START_DATE",s."END_DATE",s."COMPLETE_DATE";',
        'columns' => array('sname' => 'Sprint Name','start_date' => 'Start Date','end_date' => 'End Date','complete_date'=>'Completed At','nb_task'=>'No of Issues','totalestimate' => 'Time Estimate','timespent'=>'Time Spent','status'=>'Status'),
    ),
    'epics-all-tasks'=>array(
        "query"=>'select 
                       i.id,i.summary
                        
                    from
                        "customfieldvalue" as c, 
                        "jiraissue"  as i, 
                        "project" as p, 
                        "AO_60DB71_SPRINT" as s
                    where 
                        s."ID"= %s and p.id = i.project and
                        c.issue = i.id and CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002
                    group by s."NAME",s."ID",p.pname,i.id,i.summary;',
        'columns' => array('pname' => 'Project Name','sname' => 'Sprint Name',/*'nb_task'=>'nb_task',*/ 'start_date' => 'Start Date','end_date' => 'End Date','complete_date' => 'Complete Date',),
    ),
    'sprint-all-tasks'=>array(
        "query"=>'select 
                       i.id as id ,i.summary as summary,i.assignee as assignee,i.timeoriginalestimate as estimate,i.timespent as timespent
                        
                    from
                        "customfieldvalue" as c, 
                        "jiraissue"  as i, 
                        "project" as p, 
                        "AO_60DB71_SPRINT" as s
                    where 
                        s."ID"= %s and i.project= %s and p.id = i.project and
                        c.issue = i.id and CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002
                    group by s."NAME",s."ID",p.pname,i.id,i.summary;',
        'columns' => array('id' => 'Task Id','assignee'=>'assignee','summary' => 'Summary','estimate' => 'estimate','timespent' => 'timespent'),
    ),
    'sprint-dashboard-gyr'=>array(
        "query"=>'select i.project,(i.assignee) as assignees,
                     count(i.project),
                     SUM(CASE WHEN (i.issuestatus IN (\'1\') and i.issuetype IN (\'1\')) THEN 1 ELSE 0 END ) as b_open, 
                     SUM(CASE WHEN (i.issuestatus IN (\'10000\') and i.issuetype IN (\'1\')) THEN 1 ELSE 0 END ) as b_in_review,
                     SUM(CASE WHEN (i.issuestatus IN (\'10001\') and i.issuetype IN (\'1\')) THEN 1 ELSE 0 END ) as b_done,
                     SUM(CASE WHEN (i.issuestatus IN (\'10002\') and i.issuetype IN (\'1\')) THEN 1 ELSE 0 END ) as b_to_do,
                     SUM(CASE WHEN (i.issuestatus IN (\'3\') and i.issuetype IN (\'1\')) THEN 1 ELSE 0 END ) as b_in_progress,
                     SUM(CASE WHEN (i.issuestatus IN (\'4\') and i.issuetype IN (\'1\')) THEN 1 ELSE 0 END ) as b_reopened ,
                     SUM(CASE WHEN (i.issuestatus IN (\'5\') and i.issuetype IN (\'1\')) THEN 1 ELSE 0 END ) as b_resolved ,
                     SUM(CASE WHEN (i.issuestatus IN (\'6\') and i.issuetype IN (\'1\')) THEN 1 ELSE 0 END ) as b_closed ,
                     
                     SUM(CASE WHEN (i.issuestatus IN (\'1\') and i.issuetype NOT IN (\'1\')) THEN 1 ELSE 0 END ) as nb_open, 
                     SUM(CASE WHEN (i.issuestatus IN (\'10000\') and i.issuetype NOT IN (\'1\')) THEN 1 ELSE 0 END ) as nb_in_review,
                     SUM(CASE WHEN (i.issuestatus IN (\'10001\') and i.issuetype NOT IN (\'1\')) THEN 1 ELSE 0 END ) as nb_done,
                     SUM(CASE WHEN (i.issuestatus IN (\'10002\') and i.issuetype NOT IN (\'1\')) THEN 1 ELSE 0 END ) as nb_to_do,
                     SUM(CASE WHEN (i.issuestatus IN (\'3\') and i.issuetype NOT IN (\'1\')) THEN 1 ELSE 0 END ) as nb_in_progress,
                     SUM(CASE WHEN (i.issuestatus IN (\'4\') and i.issuetype NOT IN (\'1\')) THEN 1 ELSE 0 END ) as nb_reopened ,
                     SUM(CASE WHEN (i.issuestatus IN (\'5\') and i.issuetype NOT IN (\'1\')) THEN 1 ELSE 0 END ) as nb_resolved ,
                     SUM(CASE WHEN (i.issuestatus IN (\'6\') and i.issuetype NOT IN (\'1\')) THEN 1 ELSE 0 END ) as nb_closed ,
                     s."NAME" as sprintname,
                     p.pname as pname
                  from 
                     "customfieldvalue" as c, 
                     "jiraissue" as i, 
                     "project" as p, 
                     "AO_60DB71_SPRINT" as s 
                 where s."ID"= %s and p.id = i.project 
                       and c.issue = i.id 
                       and CAST(c.stringvalue AS BIGINT) = s."ID" 
                       and c.customfield = 10002 
                 group by i.assignee,s."NAME",p.pname,i.project;',
        
    ),
    'sprint-project-days-status'=>array(
        
        'query'=>'select (
                        CASE WHEN(CURRENT_TIMESTAMP::DATE <= TO_CHAR(TO_TIMESTAMP(s."END_DATE"/1000),\'2YYY-MM-DD\')::DATE ) 
                            THEN (
                                CASE WHEN(s."CLOSED") 
                                    THEN (
                                        SELECT count(*)-1 AS count_days_no_weekend
                                            FROM generate_series(TO_CHAR(TO_TIMESTAMP(s."END_DATE"/1000),\'2YYY-MM-DD\')::DATE, TO_CHAR(TO_TIMESTAMP(s."COMPLETE_DATE"/1000),\'2YYY-MM-DD\')::DATE, \'-1 day\') d(the_day)
                                            WHERE  extract(\'ISODOW\' FROM the_day) < 6
                                    ) ELSE (
                                        SELECT count(*)-1 AS count_days_no_weekend
                                            FROM generate_series(TO_CHAR(TO_TIMESTAMP(s."END_DATE"/1000),\'2YYY-MM-DD\')::DATE, TO_CHAR(CURRENT_TIMESTAMP, \'YYYY-MM-DD\')::date, \'-1 day\') d(the_day)
                                            WHERE  extract(\'ISODOW\' FROM the_day) < 6
                                    )
                                    END
                                
                            ) ELSE (
                                CASE WHEN(s."CLOSED") 
                                    THEN (
                                        CASE WHEN( TO_CHAR(TO_TIMESTAMP(s."COMPLETE_DATE"/1000),\'2YYY-MM-DD\')::DATE > TO_CHAR(TO_TIMESTAMP(s."END_DATE"/1000),\'2YYY-MM-DD\')::DATE ) 
                                            THEN (
                                                SELECT count(*)-1 AS count_days_no_weekend
                                                    FROM generate_series(TO_CHAR(TO_TIMESTAMP(s."END_DATE"/1000),\'2YYY-MM-DD\')::DATE, TO_CHAR(TO_TIMESTAMP(s."COMPLETE_DATE"/1000),\'2YYY-MM-DD\')::DATE, \'1 day\') d(the_day)
                                                    WHERE  extract(\'ISODOW\' FROM the_day) < 6
                                            ) ELSE (
                                                SELECT count(*)-1 AS count_days_no_weekend
                                                    FROM generate_series(TO_CHAR(TO_TIMESTAMP(s."END_DATE"/1000),\'2YYY-MM-DD\')::DATE, TO_CHAR(TO_TIMESTAMP(s."COMPLETE_DATE"/1000),\'2YYY-MM-DD\')::DATE, \'-1 day\') d(the_day)
                                                    WHERE  extract(\'ISODOW\' FROM the_day) < 6
                                            )
                                            END
                                    ) ELSE (
                                        SELECT count(*)-1 AS count_days_no_weekend
                                            FROM generate_series(TO_CHAR(TO_TIMESTAMP(s."END_DATE"/1000),\'2YYY-MM-DD\')::DATE, TO_CHAR(CURRENT_TIMESTAMP, \'YYYY-MM-DD\')::date, \'1 day\') d(the_day)
                                            WHERE  extract(\'ISODOW\' FROM the_day) < 6
                                    )
                                    END
                            ) 
                            END  
                    ) as countdays_no_weekend,
                    (CASE WHEN( (TO_CHAR(TO_TIMESTAMP(s."END_DATE"/1000),\'2YYY-MM-DD\')::DATE >= CURRENT_TIMESTAMP::DATE) or (TO_CHAR(TO_TIMESTAMP(s."END_DATE"/1000),\'2YYY-MM-DD\')::DATE >= TO_CHAR(TO_TIMESTAMP(s."COMPLETE_DATE"/1000),\'2YYY-MM-DD\')::DATE and s."CLOSED")  ) 
                        THEN 1 
                        ELSE 2 
                        END ) as state,
                    (CASE WHEN(s."CLOSED") THEN 1 ELSE 2 END) as closed,
                    TO_CHAR(TO_TIMESTAMP(s."START_DATE"/1000),\'2YYY-MM-DD HH24:MI:SS\') as startdate,
                    TO_CHAR(TO_TIMESTAMP(s."END_DATE"/1000),\'2YYY-MM-DD HH24:MI:SS\') as enddate,
                    TO_CHAR(TO_TIMESTAMP(s."COMPLETE_DATE"/1000),\'2YYY-MM-DD HH24:MI:SS\') as completed_at
                  from 
                      "AO_60DB71_SPRINT" as s 
                  where s."ID"= %s 
                  group by s."ID";'
    ),
    
    'sprint-timespent-rolewise'=>array(
        
        'query'=>'select 
                        (select TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\') as spenttester
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on s."ID"=%s and i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10822 or parent_id =10610)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                            
                        ) as s_tester,

                        (select TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\') as esttester
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on s."ID"=%s and i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10822 or parent_id =10610)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as est_tester,

                        (select TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\') as spentdev
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on s."ID"=%s and i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10001 or parent_id =10819)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as s_developer,

                        (select TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\') as estdev
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on s."ID"=%s and i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10001 or parent_id =10819)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as est_developer,

                        (select TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\') as spentpm
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on s."ID"=%s and i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10820 or parent_id =10000)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as s_pm,

                        (select TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\') as estpm
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on s."ID"=%s and i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10820 or parent_id =10000)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as est_pm,

                        (select TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\') as spentdesign
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on s."ID"=%s and i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10818)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as s_design,

                        (select TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\') as estdesign
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on s."ID"=%s and i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10818)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as est_design,
                        
                    s."NAME" as sprintname,p.id as pid,p.pname as pname 
              from jiraissue A
                     inner join customfieldvalue cf on cf.issue = A.id and CAST(cf.stringvalue AS BIGINT) = %s and A.project = %s and cf.customfield = 10002
                     inner join project p on p.id = A.project
                     inner join "AO_60DB71_SPRINT" s on s."ID"= %s and A.project = %s
              group by A.project,s."NAME",p.id'
        ),
        'project-allsprint-time-management'=>array(
            'query'=>'select it.pname
                        ,TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\') as estimate
                        ,TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\') as spent
                            from jiraissue i
                              ,"AO_60DB71_SPRINT" s 
                              ,"customfieldvalue" c 
                              ,issuetype it
                            where CAST(c.stringvalue AS BIGINT) = s."ID" 
                                and c.customfield = 10002 
                                and c.issue = i.id 
                                and i.project = %s 
                                and it.id = i.issuetype
                            group by i.project,i.issuetype,it.pname;'
        ),
        'users-attribute-month'=>array(
            'query'=>"select display_name 
                        from cwd_user 
                        where active = 1 and 
                        id not in ( 
                            select DISTINCT(user_id) 
                            from cwd_user_attributes cw 
                            where cw.attribute_name = 'lastAuthenticated' and 
                            Extract(month from TO_TIMESTAMP(CAST(cw.attribute_value AS BIGINT)/1000)) = %s and 
                            Extract(year from TO_TIMESTAMP(CAST(cw.attribute_value AS BIGINT)/1000)) = %s) 
                        order by display_name ASC",
            'options_are'=>array('lastAuthenticated')
        ),
        'sprint-timespent-rolewise-all'=>array(
            'query'=>'select 
                        (select TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\') as spenttester
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10822 or parent_id =10610)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                            
                        ) as s_tester,

                        (select TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\') as esttester
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10822 or parent_id =10610)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as est_tester,

                        (select TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\') as spentdev
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10001 or parent_id =10819)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as s_developer,

                        (select TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\') as estdev
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10001 or parent_id =10819)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as est_developer,

                        (select TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\') as spentpm
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10820 or parent_id =10000)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as s_pm,

                        (select TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\') as estpm
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10820 or parent_id =10000)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as est_pm,

                        (select TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\') as spentdesign
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10818)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as s_design,

                        (select TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\') as estdesign
                            from jiraissue i
                              inner join "AO_60DB71_SPRINT" s on i.project = %s
                              inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                              inner join project p on p.id = i.project
                              inner join app_user U on U.lower_user_name = i.assignee
                              inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10818)
                            where CM.parent_id not in (10002,10823,10824) and CM.child_name=U.user_key
                        ) as est_design,

                        p.id as pid,p.pname as pname 
                  from jiraissue A
                         inner join "AO_60DB71_SPRINT" s on A.project = %s
                         inner join customfieldvalue cf on cf.issue = A.id and CAST(cf.stringvalue AS BIGINT) = s."ID" and A.project = %s and cf.customfield = 10002
                         inner join project p on p.id = A.project
                  group by A.project,p.id'
        ),
        'issue-piechart-type-based'=>array(
            'query'=>'select it.pname,count(A.id) from jiraissue A
                         inner join issuetype it on it.id = A.issuetype
                         inner join "AO_60DB71_SPRINT" s on A.project = %s
                         inner join customfieldvalue cf on cf.issue = A.id and CAST(cf.stringvalue AS BIGINT) = s."ID" and A.project = %s and cf.customfield = 10002
                         inner join project p on p.id = A.project
                 group by A.issuetype,it.pname'
        ),
        'user-time-spent-est-spent'=>array(
            'query'=>"select A2.pname as pname,A1.estimate,A2.spent
                            from (
                                select h.pname as pname,TO_CHAR((sum(h.est) || ' second')::interval, 'HH24:MI') as estimate
                                    from (
                                        select B.pname as pname,A.timeoriginalestimate as est
                                            from jiraissue A,issuetype B ,worklog W
                                            where A.issuetype = B.id and W.issueid = A.id and W.author = '%s' and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '%s'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::DATE
                                            group by A.id,B.pname
                                    ) as h
                                    group by h.pname) as A1
                        inner join 
                        (select B.pname as pname,TO_CHAR((sum(W.timeworked) || ' second')::interval, 'HH24:MI') as spent
                                from jiraissue A,issuetype B ,worklog W
                            where A.issuetype = B.id and W.issueid = A.id and W.author = '%s' and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '%s'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::DATE
                            group by B.pname) as A2 on A1.pname = A2.pname",
        ),
        'user-time-spent-cat-bug'=>array(
            'query_old'=>"SELECT 
                            SUM(CASE WHEN (i.issuetype ='1') THEN 1 ELSE 0 END ) as bug
                            ,SUM(CASE WHEN (i.issuetype != '1') THEN 1 ELSE 0 END ) as others
                        FROM jiraissue i
                        WHERE assignee = '%s' and i.updated::date >= '%s'::DATE and i.updated <= '%s'::DATE",
            'query'=>"SELECT SUM(CASE WHEN (i.issuetype ='1') THEN 1 ELSE 0 END ) as bug
                            ,SUM(CASE WHEN (i.issuetype != '1') THEN 1 ELSE 0 END ) as others 
                         FROM jiraissue i 
                         WHERE i.id in (
                            select wl.issueid 
                            from worklog wl,jiraissue ji 
                            where wl.issueid = ji.id 
                              and wl.author = '%s' 
                              and to_char(wl.startdate::date, '2YYY-MM-DD')::DATE >= '%s'::DATE 
                              and to_char(wl.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::DATE
                            )"
        ),
        'user-time-spent-task-est-spent'=>array(
            'query'=>"Select 
                        (   select TO_CHAR((sum(h.est) || ' second')::interval, 'HH24.MI') as estimate
                                    from (
                                        select A.timeoriginalestimate as est
                                            from jiraissue A,worklog W
                                            where A.issuetype = '1' and W.issueid = A.id and W.author = '%s' and W.author!=A.assignee and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '%s'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::DATE
                                            group by A.id
                                    ) as h
                                
                        ) as bugs_est_others,
                        (   select TO_CHAR((sum(h.est) || ' second')::interval, 'HH24.MI') as estimate
                                    from (
                                        select A.timeoriginalestimate as est
                                            from jiraissue A,worklog W
                                            where A.issuetype = '1' and W.issueid = A.id and W.author = '%s' and A.assignee = '%s' and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '%s'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::DATE
                                            group by A.id
                                    ) as h
                                
                        ) as bugs_est_me,
                        (
                            select TO_CHAR((sum(w.timeworked) || ' second')::interval, 'HH24.MI') as worked
                                from jiraissue i
                                inner join worklog w on w.issueid = i.id and w.author ='%s' and w.author != i.assignee
                                and to_char(w.startdate::date, '2YYY-MM-DD')::DATE >='%s'::date and to_char(w.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::date
                                where i.issuetype = '1'
                        ) as bugs_spent_others,
                        (
                            select TO_CHAR((sum(w.timeworked) || ' second')::interval, 'HH24.MI') as worked
                                from jiraissue i
                                inner join worklog w on w.issueid = i.id and w.author ='%s' and i.assignee = '%s'
                                and to_char(w.startdate::date, '2YYY-MM-DD')::DATE >='%s'::date and to_char(w.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::date
                                where i.issuetype = '1'
                        ) as bugs_spent_me,
                        (   select TO_CHAR((sum(h.est) || ' second')::interval, 'HH24.MI') as estimate
                                    from (
                                        select A.timeoriginalestimate as est
                                            from jiraissue A,worklog W
                                            where A.issuetype != '1' and W.issueid = A.id and W.author = '%s' and W.author != A.assignee and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '%s'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::DATE
                                            group by A.id
                                    ) as h
                                
                        ) as others_est_others,
                        (   select TO_CHAR((sum(h.est) || ' second')::interval, 'HH24.MI') as estimate
                                    from (
                                        select A.timeoriginalestimate as est
                                            from jiraissue A,worklog W
                                            where A.issuetype != '1' and W.issueid = A.id and W.author = '%s' and A.assignee = '%s' and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '%s'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::DATE
                                            group by A.id
                                    ) as h
                                
                        ) as others_est_me,
                        (
                            select TO_CHAR((sum(w.timeworked) || ' second')::interval, 'HH24.MI') as worked
                                from jiraissue i
                                inner join worklog w on w.issueid = i.id and w.author ='%s' and w.author != i.assignee
                                and to_char(w.startdate::date, '2YYY-MM-DD')::DATE >='%s'::date and to_char(w.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::date
                                where i.issuetype != '1'
                        ) as others_spent_others,
                        (
                            select TO_CHAR((sum(w.timeworked) || ' second')::interval, 'HH24.MI') as worked
                                from jiraissue i
                                inner join worklog w on w.issueid = i.id and w.author ='%s' and i.assignee='%s' 
                                and to_char(w.startdate::date, '2YYY-MM-DD')::DATE >='%s'::date and to_char(w.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::date
                                where i.issuetype != '1'
                        ) as others_spent_me"
        ),
        'worklog-user-timespent-filter-graph'=>array(
            'query'=>"select 
                        to_char(w.startdate::date, '%s') as datemonthyear ,
                        to_char((sum(w.timeworked) || ' second')::interval, 'HH24.MI') as worked
                        from worklog w
                        where 
                        to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '%s'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::DATE 
                        and w.author= '%s'
                        group by to_char(w.startdate::date, '%s')
                        order by to_char(w.startdate::date, '%s')
                    ",
            'bugquery'=>"select  
                        to_char(w.startdate::date, '%s') as datemonthyear ,
                        to_char((sum(w.timeworked) || ' second')::interval, 'HH24:MI') as worked
                        from worklog w,jiraissue i
                        where i.id = w.issueid and i.issuetype='1' and to_char(w.startdate::date, '2YYY-MM-DD')::DATE >= '%s'::DATE and to_char(w.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::DATE 
                        and w.author= '%s'
                        group by to_char(w.startdate::date, '%s')
                        order by to_char(w.startdate::date, '%s')"
        ),
        'worklog-user-timespent-calendar'=>array(
            'query'=>"select 
                        j.summary,p.pkey,j.issuenum,to_char(((w.timeworked) || ' second')::interval, 'HH24:MI') as timeworked,
                        to_char(w.startdate::date, '2YYY-MM-DD')::DATE as wstartdate
                        
                        from worklog w ,jiraissue j,project p
                        where j.id=w.issueid and j.project=p.id and w.author='%s' %s
                        group by w.id,j.summary,p.pkey,j.issuenum
                     ",
        ),
);


$actionArr = array_keys($sqlQueries);
$action = isset($_REQUEST['view']) && $_REQUEST['view'] != '' ? $_REQUEST['view'] : '';

include 'common-functions.php';
