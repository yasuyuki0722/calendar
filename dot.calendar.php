<?php

$this_month = isset($_GET['ym']) ? $_GET['ym'] : date("Y-m");

$timestamp = strtotime($this_month . "-01");

if ($timestamp === false) {
	$timestamp = time();
}


$prev_month = date("Y-m",mktime(0,0,0,date("m",$timestamp)-1,1,date("Y",$timestamp)));
$next_month = date("Y-m",mktime(0,0,0,date("m",$timestamp)+1,1,date("Y",$timestamp)));

//今月の末日
$this_m_lastday = date("t",$timestamp);

//今月の最初の曜日
$this_m_youbi = date("w",mktime(0,0,0,date("m",$timestamp),1,date("Y",$timestamp)));

$weeks =array();
$week =  '';

$week .= str_repeat('<td></td>', $this_m_youbi);


//var_dump($week);
//exit;


for ($day = 1; $day <= $this_m_lastday ; $day ++,$this_m_youbi ++) { 
	$week .= sprintf('<td class = "youbi_%d">%d</td>',$this_m_youbi % 7, $day);
	//$week .= sprintf('<td>%d</td>', $day);

	if ($this_m_youbi % 7 == 6 OR $day == $this_m_lastday) {
		
		if ($day == $this_m_lastday) {
			$week .= str_repeat('<td></td>',6 - ($this_m_youbi % 7));
		}
		
		$weeks[] = '<tr>'.$week.'</tr>'; 	
		$week = ''; //week初期化
	}
}



?>




<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>calendar</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>



	<table border = "3">
		<thead>
			<tr>
				<th><a href="?ym=<?php echo $prev_month; ?>">&laquo;</a></th>
				<th colspan = "5"><?php echo date("Y",$timestamp)."-".date("m",$timestamp);?></th>
				<th><a href="?ym=<?php echo $next_month; ?>">&raquo;</a></th>
			</tr>
		</thead>
		<tbody>
		<tr>
		<th>日</th>
		<th>月</th>
		<th>火</th>
		<th>水</th>
		<th>木</th>
		<th>金</th>
		<th>土</th>
		</tr>
			<?php
			foreach ($weeks as $week ) {
				echo $week;
			}
			?>
		</tbody>
	</table>

</body>