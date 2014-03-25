<?php

require("./config/dbquerylog.inc.php");
require("./config/timer.inc.php");
require("./config/mysql.inc.php");

// Initialize timer instance and log instance
$timer = Timer::getInstance();
$log = new DBQueryLog;
$log->initConnect("10.100.151.21", "admin", "admin", "performancelog");

// Initialize mysql instance for SQL query
$mysql = new mysql;
$mysql->connect("10.100.151.21","named","named","named");

// Count timer information
$timer->startTime('Page');
$sql = "UPDATE count SET value=value+1 WHERE id=1";
$timer->stopTime('Page');

// Log performance information to database
$log->logProfilingData($timer->logData());

// Display the SQL query result
$result = $mysql->query($sql);

?>
