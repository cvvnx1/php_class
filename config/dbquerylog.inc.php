<?php
/*
 * Class DBQueryLog logs profiling data into the database
 */

class DBQueryLog {
  /*
   * Logs the data, createing the log table if it doesn't exist. Note
   * that it's cheaper to assume the table exists, and catch the error
   * if it doesn't, than to check for its existence with every query.
   */

  private $mysql_host;
  private $mysql_user;
  private $mysql_psw;

  private $mysql_dbname;

// Initial connection information such as host,user,psw and db
  function initConnect($host = '', $user = '', $psw = '', $dbname = ''){
    $this->mysql_host = $host;
    $this->mysql_user = $user;
    $this->mysql_psw = $psw;
    $this->mysql_dbname = $dbname;
  }

// If you haven't template table you need call initTempTable()
  function initTempTable(){
    $sql = <<< 'EOF'
    CREATE TABLE IF NOT EXISTS performance_template (
     ip INT UNSIGNED NOT NULL,
     page VARCHAR(255) NOT NULL,
     utime FLOAT NOT NULL,
     wtime FLOAT NOT NULL,
     stime FLOAT NOT NULL,
     mysql_time FLOAT NOT NULL,
     mysql_count_queries INT UNSIGNED NOT NULL,
     mysql_queries TEXT NOT NULL,
     sphinx_time FLOAT NOT NULL,
     logged TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     user_agent VARCHAR(255) NOT NULL,
     referer VARCHAR(255) NOT NULL
    ) ENGINE=ARCHIVE;
EOF;

    $con = mysql_connect($this->mysql_host, $this->mysql_user, $this->mysql_psw);
    if (!$con){
      die('Could not connect: ' . mysql_error());
    } else {
      $mysql_dbname && @mysql_select_db($this->mysql_dbname, $con);
      mysql_query($sql, $con);
      mysql_close($con);
    }
  }

// Log information to database
  function logData($data){
    $table_name = 'performance_template_' . @date("ymd");

    $sql = "INSERT DELAYED INTO
     $table_name(ip, page, utime, wtime, stime, mysql_time, mysql_count_queries, mysql_queries, sphinx_time, user_agent, referer)
     VALUES('".ip2long($_SERVER["REMOTE_ADDR"])."',
      '".$_SERVER["REQUEST_URI"]."',
      '".$data["utime"]."',
      '".$data["wtime"]."',
      '".$data["stime"]."',
      '".$data["mysql_time"]."',
      '".$data["mysql_count_queries"]."',
      '".$data["mysql_queries"]."',
      '".$data["sphinx_time"]."',
      '".$_SERVER["HTTP_USER_AGENT"]."',
      '".$_SERVER["HTTP_REFERER"]."')";

    $con = mysql_connect($this->mysql_host, $this->mysql_user, $this->mysql_psw);
    if (!$con){
      die('Could not connect: ' . mysql_error());
    } else {
      $this->mysql_dbname && @mysql_select_db($this->mysql_dbname, $con);
      $result = mysql_query($sql, $con);
      if ((!$result) && (mysql_errno() == 1146)){
        $result = mysql_query("CREATE TABLE $table_name LIKE performance_template", $con);
        $result = mysql_query($sql, $con);
      }
      mysql_close($con);
    }
  }
}
?>
