<?php

$this_month = isset($_GET['ym']) ? $_GET['ym'] : date('Y-n');

$timestamp = strtotime($this_month . "-01");

$today_d = date('d');
$today_m = date("Y-n");

if ($timestamp === false) {
	$timestamp = time();
}


$prev_month = date("Y-n",mktime(0,0,0,date("m",$timestamp)-1,1,date("Y",$timestamp)));
$next_month = date("Y-n",mktime(0,0,0,date("m",$timestamp)+1,1,date("Y",$timestamp)));

$prev_timestamp = strtotime($prev_month . "-01");
$next_timestamp = strtotime($next_month . "-01");



//祝日取得
//取得開始日を自動でできるようにしたい...

$syukujitu_p =  date("Y-m-01",mktime(0,0,0,date("m",$timestamp)-1,1,date("Y",$timestamp))) ;
$syukujitu_n =  date("Y-m-t",mktime(0,0,0,date("m",$timestamp)+1,1,date("Y",$timestamp))) ;
var_dump(date("Y-m-01",mktime(0,0,0,date("m",$timestamp)-1,1,date("Y",$timestamp))));
var_dump(date("Y-m-t",mktime(0,0,0,date("m",$timestamp)+1,1,date("Y",$timestamp))));

$holidays_url = sprintf(
    'http://74.125.235.142/calendar/feeds/%s/public/full-noattendees?start-min=%s&start-max=%s&max-results=%d&alt=json' ,
    'outid3el0qkcrsuf89fltf7a4qbacgt9@import.calendar.google.com' ,
     $syukujitu_p,    // 取得開始日
     $syukujitu_n,   // 取得終了日
    50               // 最大取得数
);





if($results=file_get_contents($holidays_url)) {
    $results = json_decode($results, true);
    $holidays = array();
    foreach($results['feed']['entry'] as $val) {
        $date = $val['gd$when'][0]['startTime']; // 日付を取得
        $title = $val['title']['$t']; // 何の日かを取得
        $holidays[$date] = $title; // 日付をキーに、祝日名を値に格納
    }
    ksort($holidays); // 日付順にソート
}

var_dump($holidays);

//今月のカレンダー処理

$this_m_lastday = date("t",$timestamp);//今月の末日
$this_m_youbi = date("w",mktime(0,0,0,date("n",$timestamp),1,date("Y",$timestamp)));//今月の最初の曜日

$this_m_weeks =array();
$week =  '';

$week .= str_repeat('<td></td>', $this_m_youbi);


for ($day = 1; $day <= $this_m_lastday ; $day ++,$this_m_youbi ++) { 
if ($day == $today_d && $this_month == $today_m) {
		$week .= sprintf('<td class = "today_color">%d</td>', $day);
	}else{
		$week .= sprintf('<td class = "youbi_%d">%d</td>',$this_m_youbi % 7, $day);
	}

	if ($this_m_youbi % 7 == 6 || $day == $this_m_lastday) {
		
		if ($day == $this_m_lastday) {
			$week .= str_repeat('<td></td>',6 - ($this_m_youbi % 7));
		}
		
		$this_m_weeks[] = '<tr>'.$week.'</tr>'; 	
		$week = ''; //week初期化
	}
}



//先月のカレンダー処理

$prev_m_lastday = date("t",$prev_timestamp);//今月の末日
$prev_m_youbi = date("w",mktime(0,0,0,date("n",$prev_timestamp),1,date("Y",$prev_timestamp)));//今月の最初の曜日

$prev_m_weeks =array();
$week =  '';

$week .= str_repeat('<td></td>', $prev_m_youbi);

for ($day = 1; $day <= $prev_m_lastday ; $day ++,$prev_m_youbi ++) { 
	if ($day == $today_d && $prev_month == $today_m) {
		$week .= sprintf('<td class ="today_color" >%d</td>', $day);
	}else{
		$week .= sprintf('<td class = "youbi_%d">%d</td>',$prev_m_youbi % 7, $day);
	}
	//$week .= sprintf('<td>%d</td>', $day);

	if ($prev_m_youbi % 7 == 6 || $day == $prev_m_lastday) {
		
		if ($day == $prev_m_lastday) {
			$week .= str_repeat('<td></td>',6 - ($prev_m_youbi % 7));
		}
		
		$prev_m_weeks[] = '<tr>'.$week.'</tr>'; 	
		$week = ''; //week初期化
	}
}

//来月のカレンダー処理

$next_m_lastday = date("t",$next_timestamp);//今月の末日
$next_m_youbi = date("w",mktime(0,0,0,date("n",$next_timestamp),1,date("Y",$next_timestamp)));//今月の最初の曜日

$next_m_weeks =array();
$week =  '';

$week .= str_repeat('<td></td>', $next_m_youbi);

for ($day = 1; $day <= $next_m_lastday ; $day ++,$next_m_youbi ++) { 
	if ($day == $today_d && $next_month == $today_m) {
		$week .= sprintf('<td class="today_color" >%d</td>', $day);
	}else{
		$week .= sprintf('<td class = "youbi_%d">%d</td>',$next_m_youbi % 7, $day);
	}
	//$week .= sprintf('<td>%d</td>', $day);

	if ($next_m_youbi % 7 == 6 || $day == $next_m_lastday) {
		
		if ($day == $next_m_lastday) {
			$week .= str_repeat('<td></td>',6 - ($next_m_youbi % 7));
		}
		
		$next_m_weeks[] = '<tr>'.$week.'</tr>'; 	
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

<form action="?ym= <?php echo $prev; ?>" method="get">
	<select name="ym" >
		<?php
		for ($y = 2014; $y < 2016 ; $y ++) { 
			for ($m = 1; $m <= 12 ; $m ++) { 
				if ($y == date("Y",$timestamp) && $m == date("n",$timestamp)) {
					echo "<option selected>".$y."-".$m."</option>";
				}else{
					echo "<option>".$y."-".$m."</option>";
				}			
			}
		 	
		 } 
		?>
	</select>
	<input type="submit"  value="月へ飛ぶ">
</form>

<a href="?ym=<?php echo $prev_month; ?>">先月</a>
かれんだーだよ！
<a href="?ym=<?php echo $next_month; ?>">来月</a>

<!-- 
<table>
	<tr>
		<td> 
		-->
		<div align="center">
		<table border = "3">
		<thead>
			<tr>
				<th colspan = "7"><?php echo $prev_month;?></th>
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
			foreach ($prev_m_weeks as $week ) {
				echo $week;
			}
			?>
		</tbody>
		</table>

<!-- 	</td>


	<td> -->
		<table border = "3">
		<thead>
			<tr>
				<th colspan = "7"><?php echo $this_month;?></th>
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
			foreach ($this_m_weeks as $week ) {
				echo $week;
			}
			?>
		</tbody>
		</table>

<!-- 	</td>

	<td> -->
		<table border = "3">
		<thead>
			<tr>
				<th colspan = "7"><?php echo $next_month;?></th>
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
			foreach ($next_m_weeks as $week ) {
				echo $week;
			}
			?>
		</tbody>
		</table>
		</div>
<!-- 	</td>
	</tr>
</table> -->

</body>