<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



	/********************************************** Database Configuration ****************************************/

        /************* PHP - MYSQL Connection ************/

        $username = "root";
        $password = "root";
        $hostname = "localhost"; 

        //connection to the database
        $dbhandle = mysql_pconnect($hostname, $username, $password)
          or die("Unable to connect to MySQL");

        //select a database to work with
        $selected = mysql_select_db("jiraadmin",$dbhandle)
          or die("Could not select jiraadmin");
?>