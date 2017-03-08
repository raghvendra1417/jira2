<?php

	/********************************************** Database Configuration ****************************************/

        /************* PHP - MYSQL Connection ************/

        $username = "root";
        //$password = "rnd@123";
        $password = "rnd@123";
        $hostname = "localhost"; 

        //connection to the database
        $dbhandle = mysql_pconnect($hostname, $username, $password)
          or die("Unable to connect to MySQL");

        //select a database to work with
        $selected = mysql_select_db("jiraadmin_new",$dbhandle)
          or die("Could not select jiraadmin_new");
