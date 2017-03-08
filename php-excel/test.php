<?php

// load library
require './../config.php';

// create a simple 2-dimensional array
$sql = "select id,group_name from cwd_group";
$sql = 'select CM.parent_id,s."NAME" as sprintname,p.pname as pname,TO_CHAR((sum(w.timeworked) || \' second\')::interval, \'HH24:MI\') as spent
                    from jiraissue i
                      inner join "AO_60DB71_SPRINT" s on s."ID" = 108
                      inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                      inner join project p on p.id = i.project
                      inner join worklog w on w.issueid = c.issue
                      inner join app_user U on U.lower_user_name = w.author
                      inner join cwd_membership CM on CM.child_name=U.user_key and CM.parent_id in (10711,10710,10610 ,10001)
                    group by CM.parent_id,s."NAME",p.pname';

$sql ='select CM.parent_id,s."NAME" as sprintname,p.id as pid,p.pname as pname,TO_CHAR((sum(w.timeworked) || \' second\')::interval, \'HH24:MI\') as spent
                    from jiraissue i
                      inner join "AO_60DB71_SPRINT" s on s."ID" = 74
                      inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
                      inner join project p on p.id = i.project
                      inner join worklog w on w.issueid = c.issue
                      inner join app_user U on U.lower_user_name = w.author
                      inner join cwd_membership CM on CM.child_name=U.user_key where CM.parent_id in (10610)
                    group by CM.parent_id,s."NAME",p.id,p.pname';

$sql = "SELECT 
                        SUM(CASE WHEN (i.issuetype ='1') THEN 1 ELSE 0 END ) as bug
                        ,SUM(CASE WHEN (i.issuetype != '1') THEN 1 ELSE 0 END ) as others
                     FROM jiraissue i
                     WHERE assignee = 'pritpal.s' and i.updated::date >= '2015-02-01'::DATE and i.updated <= '2015-02-28'::DATE";

$sql = "SELECT A.id,A.issuenum,project,W.timeworked as timespent,pname,W.created,W.updated as resolutiondate FROM jiraissue as A inner join project B on A.project=B.id inner join worklog W on A.id=w.issueid WHERE author like 'akshata.a@techtreeit.com' and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '2015-02-01'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE < '2015-02-07'::DATE group by project,pname,A.id,A.issuenum,W.created,W.updated,W.timeworked order by A.id,project,W.created";
$sql = "SELECT A.id as iid, A.summary as summary, A.assignee as assignee, C.pname as issuetypename, D.pname as issuestatus, B.pname as pname, B.pkey as pkey, B.id as pid, A.issuenum as issuenum, A.reporter as reporter, TO_CHAR(A.created, 'DD/MM/2YYY') as created, TO_CHAR(A.duedate, 'DD/MM/2YYY') as duedate, CONCAT(TO_CHAR((A.timeoriginalestimate || ' second')::interval, 'HH24:MI'),' hrs' ) as timeoriginalestimate, TO_CHAR(A.resolutiondate, 'DD/MM/YYYY') as resolutiondate, CONCAT(TO_CHAR((sum(E.timeworked) || ' second')::interval, 'HH24:MI'),' hrs' ) as timespent from jiraissue as A inner join project B on A.project=B.id inner join issuetype C on C.id=A.issuetype inner join issuestatus D on D.id=A.issuestatus inner join worklog E on E.issueid=A.id where A.project= 10235 and (E.author like 'akshata.a@techtreeit.com') and E.startdate::date >= '2015-02-01'::DATE and E.startdate::date <= '2015-02-06'::DATE and E.issueid=22151 group by E.issueid,A.id,C.pname,D.pname,B.id ";
$sql = "select * from worklog E where (E.author like 'akshata.a@techtreeit.com') and E.startdate::date >= '2015-02-01'::DATE and E.startdate::date <= '2015-02-06'::DATE and E.issueid=22151 order by E.created ASC";

$sql = "SELECT A.id,project,W.timeworked as timespent,pname,W.startdate,W.updated as resolutiondate FROM jiraissue as A inner join project B on A.project=B.id inner join worklog W on A.id=w.issueid WHERE author like 'akshata.a@techtreeit.com' and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '2015-02-01'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE < '2015-02-07'::DATE group by project,pname,W.startdate,A.id,W.updated,W.timeworked order by A.id,project,W.startdate";
$sql = 'select i.id,(i.timespent) as spentdev
            from jiraissue i
               "AO_60DB71_SPRINT" s 
               "customfieldvalue" c 
               project p 
               cwd_membership CM 
            where CM.parent_id != 10002 and s."ID" = 105 and i.project = 12002
            and CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
            and  p.id = i.project
            and  CM.child_name=i.assignee and CM.child_name not in (select child_name from cwd_membership where parent_name in (select group_name from cwd_group where id not in (10001,10002)))
            group by i.id,CM.parent_id,s."NAME",p.id,p.pname
            order by i.id';
/*$sql = 'select 
            i.id as id ,i.summary as summary,i.assignee as assignee,i.timeoriginalestimate as estimate,i.timespent as timespent

         from
             "customfieldvalue" as c, 
             "jiraissue"  as i, 
             "project" as p, 
             "AO_60DB71_SPRINT" as s
         where 
             s."ID"= %s and i.project= %s and p.id = i.project and
             c.issue = i.id and CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002
         group by s."NAME",s."ID",p.pname,i.id,i.summary;';
*/
$sql = 'select 
            (
                select TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\') as spenttester from jiraissue i inner join "AO_60DB71_SPRINT" s on i.project = 12002 inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id inner join project p on p.id = i.project inner join app_user U on U.lower_user_name = i.assignee inner join cwd_membership CM on CM.child_name=U.user_key and CM.child_name in (select child_name from cwd_membership where parent_id =10822 or parent_id =10610) group by i.project
            ) as s_tester, 
            (
                select TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\') as esttester from jiraissue i inner join "AO_60DB71_SPRINT" s on i.project = 12002 inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id inner join project p on p.id = i.project inner join app_user U on U.lower_user_name = i.assignee inner join cwd_membership CM on CM.child_name=U.user_key and CM.child_name in (select child_name from cwd_membership where parent_id =10822 or parent_id =10610) group by i.project
            ) as est_tester,
            (
                select TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\') as spentdev from jiraissue i inner join "AO_60DB71_SPRINT" s on i.project = 12002 inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id inner join project p on p.id = i.project inner join app_user U on U.lower_user_name = i.assignee inner join cwd_membership CM on CM.child_name=U.user_key and CM.child_name in (select child_name from cwd_membership where parent_id =10001 or parent_id =10819) group by i.project
            ) as s_developer, 
            (
                select TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\') as estdev from jiraissue i inner join "AO_60DB71_SPRINT" s on i.project = 12002 inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id inner join project p on p.id = i.project inner join app_user U on U.lower_user_name = i.assignee inner join cwd_membership CM on CM.child_name=U.user_key and CM.child_name in (select child_name from cwd_membership where parent_id =10001 or parent_id =10819) group by i.project
            ) as est_developer, 
            (
                select TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\') as spentpm from jiraissue i inner join "AO_60DB71_SPRINT" s on i.project = 12002 inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id inner join project p on p.id = i.project inner join app_user U on U.lower_user_name = i.assignee inner join cwd_membership CM on CM.child_name=U.user_key and CM.child_name in (select child_name from cwd_membership where parent_id =10820 or parent_id =10000) group by i.project
            ) as s_pm, 
            (
                select TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\') as estpm from jiraissue i inner join "AO_60DB71_SPRINT" s on i.project = 12002 inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id inner join project p on p.id = i.project inner join app_user U on U.lower_user_name = i.assignee inner join cwd_membership CM on CM.child_name=U.user_key and CM.child_name in (select child_name from cwd_membership where parent_id =10820 or parent_id =10000) group by i.project
            ) as est_pm, 
            (
                select TO_CHAR((sum(i.timespent) || \' second\')::interval, \'HH24:MI\') as spentdesign from jiraissue i inner join "AO_60DB71_SPRINT" s on i.project = 12002 inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id inner join project p on p.id = i.project inner join app_user U on U.lower_user_name = i.assignee inner join cwd_membership CM on CM.child_name=U.user_key and CM.child_name in (select child_name from cwd_membership where parent_id =10818) group by i.project
            ) as s_design, 
            (
                select TO_CHAR((sum(i.timeoriginalestimate) || \' second\')::interval, \'HH24:MI\') as estdesign from jiraissue i inner join "AO_60DB71_SPRINT" s on i.project = 12002 inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id inner join project p on p.id = i.project inner join app_user U on U.lower_user_name = i.assignee inner join cwd_membership CM on CM.child_name=U.user_key and CM.child_name in (select child_name from cwd_membership where parent_id =10818) group by i.project
            ) as est_design,
            p.id as pid,p.pname as pname 
        from jiraissue A inner join "AO_60DB71_SPRINT" s on A.project = 12002 inner join customfieldvalue cf on cf.issue = A.id and CAST(cf.stringvalue AS BIGINT) = s."ID" and A.project = 12002 and cf.customfield = 10002 inner join project p on p.id = A.project group by A.project,p.id
        ';
$sql = 'select i.* '
        . 'from jiraissue i '
        . 'inner join "AO_60DB71_SPRINT" s on i.project = 12002 '
        . 'inner join "customfieldvalue" c on CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id '
        . 'inner join project p on p.id = i.project '
        
        . 'inner join cwd_membership CM on CM.child_name in (select DISTINCT(child_name) from cwd_membership where parent_id =10001 or parent_id =10819) '
        . 'where CM.parent_id != 10002 and CM.child_name=i.assignee and s."ID"=119'
        . '';

$sql = "select w.author,sum(w.timeworked) as spent from worklog w where w.startdate::date >= '2015-02-01'::date and w.startdate::date <= '2015-02-28'::date group by w.author order by w.author";
#$sql = 'select child_name,count(child_name) from cwd_membership where parent_name in (\'jira-developers\',\'Developers\') group by child_name';
$sql="select h.pname,sum(h.est) from (select B.pname as pname,A.timeoriginalestimate as est
                                from jiraissue A,issuetype B ,worklog W
                            where A.issuetype = B.id and W.issueid = A.id and W.author = 'raghvendra.yadav' and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '2015-03-01'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '2015-03-31'::DATE
                            group by A.id,B.pname) as h
            group by h.pname";

$sql = "select A1.pname as pname,A1.estimate,A2.spent
                            from (
                                select h.pname as pname,sum(h.est) as estimate
                                    from (
                                        select B.pname as pname,A.timeoriginalestimate as est
                                            from jiraissue A,issuetype B ,worklog W
                                            where A.issuetype = B.id and W.issueid = A.id and W.author = 'raghvendra.yadav' and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '2015-03-01'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '2015-03-31'::DATE
                                            group by A.id,B.pname
                                    ) as h
                                    group by h.pname) as A1
                 inner join 
                        (select TO_CHAR((sum(W.timeworked) || ' second')::interval, 'HH24:MI') as spent
                                from jiraissue A,issuetype B ,worklog W
                            where A.issuetype = B.id and W.issueid = A.id and W.author = 'raghvendra.yadav' and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '2015-03-01'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '2015-03-31'::DATE
                            group by B.pname) as A2 on A1.pname = A2.pname";

$sql = "select sum(h.est) as estimate
                                    from (
                                        select A.timeoriginalestimate as est
                                            from jiraissue A,worklog W
                                            where A.issuetype != '1' and W.issueid = A.id and W.author = '%s' and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '%s'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '%s'::DATE
                                            group by A.id
                                    ) as h";
$sql = "select author as assignee,TO_CHAR((sum(w.timeworked) || ' second')::interval, 'HH24:MI') as timespent 
                    from worklog w ,
                    cwd_membership c,
                    app_user a
                    where 
                    (w.author = a.user_key or w.author = a.lower_user_name) and 
                    c.parent_id != 10002 and c.child_id = a.id 
                    
                    and  Extract(month from to_char(startdate::date, '2YYY-MM-DD')::DATE) = Extract(month from CURRENT_DATE::date) and Extract(year from to_char(startdate::date, '2YYY-MM-DD')::DATE) = Extract(year from CURRENT_DATE::date)
                    
                    group by w.author
                    having sum(coalesce(w.timeworked, 0)) != 0 
                    order by w.author";
$sql = "select E.author as assignee, TO_CHAR((sum(E.timeworked) || ' second')::interval, 'HH24:MI') as timespent 
                    from 
                    worklog E,
                    cwd_membership C,
                    app_user B,
		    cwd_user D
                    where D.id = C.child_id and C.parent_id != 10002 and D.user_name = B.lower_user_name
                    and E.author = B.user_key
                    group by E.author,B.id,C.id,D.id
                    order by E.author";
#$sql = "select * from app_user";
$sql = "select 
                        to_char(w.startdate::date, 'DD-MM-2YYY') as datemonthyear ,
                        to_char((sum(w.timeworked) || ' second')::interval, 'HH24:MI') as worked
                        from worklog w
                        where 
                        to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '2015-03-01'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '2015-04-02'::DATE 
                        and w.author= 'raghvendra.yadav'
                        group by to_char(w.startdate::date, 'DD-MM-2YYY')";

$sql = "select * from worklog W
            where to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '2015-03-01'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '2015-04-02'::DATE and W.author= 'murali.kg'";
$sql = "select  
                to_char(w.startdate::date, 'DD-MM-2YYY') as datemonthyear ,
                to_char((sum(w.timeworked) || ' second')::interval, 'HH24:MI') as worked
                from worklog w,jiraissue i
                where i.id = w.issueid and i.issuetype='1' and to_char(w.startdate::date, '2YYY-MM-DD')::DATE >= '2015-03-01'::DATE and to_char(w.startdate::date, '2YYY-MM-DD')::DATE <= '2015-04-02'::DATE and w.author= 'shahbaz.sa'
                group by to_char(w.startdate::date, 'DD-MM-2YYY')";

$sql = "select j.assignee,TO_CHAR((sum(j.timeestimate) || ' second')::interval, 'HH24:MI') as timeestimateremaining from (
            select 
                i.id as iid,i.assignee,i.timeestimate
                from worklog W,jiraissue i
                where i.id=w.issueid and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '2015-03-01'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '2015-04-02'::DATE
        UNION
            select i.id as iid,i.assignee,i.timeestimate
                from jiraissue i
                where to_char(i.created::date, '2YYY-MM-DD')::DATE >= '2015-03-01'::DATE and to_char(i.created::date, '2YYY-MM-DD')::DATE <= '2015-04-02'::DATE
        ) as j
        group by j.assignee
        
            
        ";//,to_char(W.startdate::date, '2YYY-MM-DD')::DATE as created 
        //,to_char(i.created::date, '2YYY-MM-DD')::DATE as created

$sql =" Select ( select TO_CHAR((sum(h.est) || ' second')::interval, 'HH24:MI') as estimate from ( select A.timeoriginalestimate as est from jiraissue A,worklog W where A.issuetype = '1' and W.issueid = A.id and W.author = 'shahbaz.sa' and W.author!=A.assignee and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '2015-03-01'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '2015-03-31'::DATE group by A.id ) as h ) as bugs_est_others, ( select TO_CHAR((sum(h.est) || ' second')::interval, 'HH24:MI') as estimate from ( select A.timeoriginalestimate as est from jiraissue A,worklog W where A.issuetype = '1' and W.issueid = A.id and W.author = 'shahbaz.sa' and A.assignee = 'shahbaz.sa' and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '2015-03-01'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '2015-03-31'::DATE group by A.id ) as h ) as bugs_est_me, ( select TO_CHAR((sum(w.timeworked) || ' second')::interval, 'HH24:MI') as worked from jiraissue i inner join worklog w on w.issueid = i.id and w.author ='shahbaz.sa' and w.author != A.assignee and to_char(w.startdate::date, '2YYY-MM-DD')::DATE >='2015-03-01'::date and to_char(w.startdate::date, '2YYY-MM-DD')::DATE <= '2015-03-31'::date where i.issuetype = '1' ) as bugs_spent_others, ( select TO_CHAR((sum(w.timeworked) || ' second')::interval, 'HH24:MI') as worked from jiraissue i inner join worklog w on w.issueid = i.id and w.author ='shahbaz.sa' and A.assignee = 'shahbaz.sa' and to_char(w.startdate::date, '2YYY-MM-DD')::DATE >='2015-03-01'::date and to_char(w.startdate::date, '2YYY-MM-DD')::DATE <= '2015-03-31'::date where i.issuetype = '1' ) as bugs_spent_me, ( select TO_CHAR((sum(h.est) || ' second')::interval, 'HH24:MI') as estimate from ( select A.timeoriginalestimate as est from jiraissue A,worklog W where A.issuetype != '1' and W.issueid = A.id and W.author = 'shahbaz.sa' and W.author != A.assignee and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '2015-03-01'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '2015-03-31'::DATE group by A.id ) as h ) as others_est_others, ( select TO_CHAR((sum(h.est) || ' second')::interval, 'HH24:MI') as estimate from ( select A.timeoriginalestimate as est from jiraissue A,worklog W where A.issuetype != '1' and W.issueid = A.id and W.author = 'shahbaz.sa' and A.assignee = 'shahbaz.sa' and to_char(W.startdate::date, '2YYY-MM-DD')::DATE >= '2015-03-01'::DATE and to_char(W.startdate::date, '2YYY-MM-DD')::DATE <= '2015-03-31'::DATE group by A.id ) as h ) as others_est_me, ( select TO_CHAR((sum(w.timeworked) || ' second')::interval, 'HH24:MI') as worked from jiraissue i inner join worklog w on w.issueid = i.id and w.author ='shahbaz.sa' and w.author != i.assignee and to_char(w.startdate::date, '2YYY-MM-DD')::DATE >='2015-03-01'::date and to_char(w.startdate::date, '2YYY-MM-DD')::DATE <= '2015-03-31'::date where i.issuetype != '1' ) as others_spent_others, ( select TO_CHAR((sum(w.timeworked) || ' second')::interval, 'HH24:MI') as worked from jiraissue i inner join worklog w on w.issueid = i.id and w.author ='shahbaz.sa' and i.assignee='shahbaz.sa' and to_char(w.startdate::date, '2YYY-MM-DD')::DATE >='2015-03-01'::date and to_char(w.startdate::date, '2YYY-MM-DD')::DATE <= '2015-03-31'::date where i.issuetype != '1' ) as others_spent_me";
$sql ='select 
                        p.id as pid,
                        s."ID" as id,
                        s."NAME" as sname                        
                    from
                        "customfieldvalue" as c, 
                        "jiraissue"  as i, 
                        "project" as p, 
                        "AO_60DB71_SPRINT" as s
                    where 
                        p.id = 12304 and p.id = i.project and
                        c.issue = i.id and CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002
                    group by s."ID",s."NAME",p.pname,s."START_DATE",s."END_DATE",s."COMPLETE_DATE";';

//$sql = "select * from cwd_membership where parent_name= 'jira-users'";
$sql = "SELECT * FROM customfieldvalue WHERE stringvalue = '527'";
$retDaysPlanData = pg_query($db, $sql);
#echo pg_num_rows($retDaysPlanData);exit;
$sum =  0;
while($rowProject = pg_fetch_object($retDaysPlanData)){
    //$AllempUN[]=$rowProject->itsupport;
  echo "<pre>";print_r($rowProject);
 //echo date('d-m-Y H:i:s',strtotime($rowProject->now));exit;
    continue;
    //$sum += $rowProject->est;  //and CAST(c.stringvalue AS BIGINT) = s."ID" and c.customfield = 10002 and c.issue = i.id
    //$count++;
    //echo "<pre>";print_r($rowProject); "AO_60DB71_SPRINT" s,
             // customfieldvalue c
}exit;
echo $count.'Sum :'.$sum;

?>
