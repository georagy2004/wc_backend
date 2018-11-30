<?php
$arr=array(
  0=>array(
      'run_date'=>'2017-11-21',
      'count'=>'5'
  ),
  1=>array(
      'run_date'=>'2017-11-20',
      'count'=>'10'
  ),
  2=>array(
      'run_date'=>'2017-11-22',
      'count'=>'10'
  )
);

$date = array_column($arr, 'run_date');
print_r($date);
array_multisort($date, SORT_ASC, $arr);
print_r($arr)

?>