<?php
/*
 * Class Timer, implementation of time logging in PHP
 */

class Timer {
  private $aTIMES = array();

  function startTime($point){
    $dat = getrusage();

    $this->aTIMES[$point]['start'] = microtime(TRUE);
    $this->aTIMES[$point]['start_utime'] = $dat["ru_utime.tv_sec"]*1e6 + $dat["ru_utime.tv_usec"];
    $this->aTIMES[$point]['start_stime'] = $dat["ru_stime.tv_sec"]*1e6 + $dat["ru_stime.tv_usec"];
  }

  function stopTime($point, $sql = ''){
    $dat = getrusage();

    $this->aTIMES[$point]['end'] = microtime(TRUE);
    $this->aTIMES[$point]['end_utime'] = $dat["ru_utime.tv_sec"]*1e6 + $dat["ru_utime.tv_usec"];
    $this->aTIMES[$point]['end_stime'] = $dat["ru_stime.tv_sec"]*1e6 + $dat["ru_stime.tv_usec"];

    $this->aTIMES[$point]['sql'] .= $sql;

    $this->aTIMES[$point]['sum'] += $this->aTIMES[$point]['end'] - $this->aTIMES[$point]['start'];
    $this->aTIMES[$point]['sum_utime'] += ($this->aTIMES[$point]['end_utime'] - $this->aTIMES[$point]['start_utime']) / 1e6;
    $this->aTIMES[$point]['sum_stime'] += ($this->aTIMES[$point]['end_stime'] - $this->aTIMES[$point]['start_stime']) / 1e6;
  }

  function logdata(){
  	$data = array();

    $data['utime'] = $this->aTIMES['Page']['sum_utime'];
    $data['wtime'] = $this->aTIMES['Page']['sum'];
    $data['stime'] = $this->aTIMES['Page']['sum_stime'];

    $data['mysql_time'] = $this->aTIMES['MySQL']['sum'];
    $data['mysql_count_queries'] = $this->aTIMES['MySQL']['cnt'];
    $data['mysql_queries'] = $this->aTIMES['MySQL']['sql'];
    $data['sphinx_time'] = $this->aTIMES['Sphinx']['sum'];

    return($data);
  }

  // This helper function implements the Singleton pattern
  function getInstance(){
    static $instance;

    if(!isset($instance)){
      $instance = new Timer();
    }
    return($instance);
  }
}
?>
