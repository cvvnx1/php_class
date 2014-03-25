<?php

require("./config/dbquerylog.inc.php");
require("./config/timer.inc.php");
require("./config/mysql.inc.php");

// Initialize timer instance and log instance
$timer = Timer::getInstance();
$log = new DBQueryLog;
$log->initConnect("mysql.yourdomain.com", "admin", "admin", "performancelog");

// Start page timer
$timer->startTime('Page');

// Run php code
for ($i=0;$i<10000000;$i++){
  $s += $i;
}

// Start mysql timer
$timer->startTime('MySQL');

// Run mysql query process
$mysql = new mysql;
$mysql->connect("10.100.151.21","named","named","named");
$sql = "UPDATE count SET value=value+1 WHERE id=1";

// Stop page timer
$timer->stopTime('MySQL', $sql);

// Run php code
$result = $mysql->query($sql);

// Stop page timer
$timer->stopTime('Page');

// Log performance information to database
$log->logData($timer->logData());

?>
