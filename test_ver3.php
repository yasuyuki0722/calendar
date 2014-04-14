

<h1>カレンダー</h1>

<?php


$ym = isset($_GET['ym']) ? $_GET['ym'] : date("Y-m");

$timestamp = strtotime($ym . "-01");

if ($timestamp === false) {
	$timestamp = time();
}

$prev = date("Y-m",mktime(0,0,0,date("m",$timestamp)-1,1,date("Y",$timestamp)));
$next = date("Y-m",mktime(0,0,0,date("m",$timestamp)+1,1,date("Y",$timestamp)));

$prev_month = date("m",mktime(0,0,0,date("m",$timestamp)-1,1,date("Y",$timestamp)));
$next_month = date("m",mktime(0,0,0,date("m",$timestamp)+1,1,date("Y",$timestamp)));


$day   = idate(d);
//$month = idate(m);
//$year  = idate(Y);


$month = date("m",$timestamp);
$year  = date("Y",$timestamp);



var_dump($ym);
//var_dump($_GET['ym']);
var_dump($prev);
//var_dump($timestamp);
var_dump($next);

var_dump($day);
var_dump($month);
var_dump($year);

var_dump($prev_month);
var_dump($next_month);





?>


<!DOCTYPE html>
<html>
<head>
	<title>カレンダー</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>



<br>

<a href="?ym=<?php echo $prev; ?>">先月</a>

<form action="?ym= <?php echo $prev; ?>" method="get">
	<select name="ym" size="3">
		<?php
		for ($y = 2000; $y < 2100 ; $y ++) { 
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
	<input type="submit" >
</form>
</form>

<a href="?ym=<?php echo $next; ?>">来月</a>



<table>
<tr>
<td>

<table border = "3">

    <thead>
			<tr>
				<th colspan = "7"><?php echo date("Y",$timestamp)."年".$prev_month;?>月</th>
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
<tr>

<?php

$c_day = 1;



$weekdayfirst = date("w",mktime(0,0,0,$prev_month,1,$year));
for($i = 1;$i <= $weekdayfirst;$i ++){
	echo "<td>  </td>";
} 

while(checkdate($prev_month, $c_day, $year)){

	echo '<td class = "youbi_'.date("w",mktime(0,0,0,$prev_month,$c_day,$year)).'">'.$c_day.'</td>';

	if (date("w",mktime(0,0,0,$prev_month,$c_day,$year)) == 6){
		echo "</tr>";
	    if(checkdate($prev_month,$c_day + 1,$year)){
		    echo '<tr>';
	    }
	}

	$c_day ++;
}

$weekdaylast = date("w", mktime(0, 0, 0, $prev_month+1, 0, $year));
for($i = 1; $i <7 - $weekdaylast; $i ++){
	echo "<td>  </td>";
}


?>
</tr>
</tbody>
</table>
</td>

<td>
	<table border = "3">

    <thead>
			<tr>
				<th colspan = "7"><?php echo date("Y",$timestamp)."年".$month;?>月</th>
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
<tr>

<?php

$c_day = 1;

$weekdayfirst = date("w",mktime(0,0,0,$month,1,$year));
for($i = 1;$i <= $weekdayfirst;$i ++){
	echo "<td>  </td>";
} 

while(checkdate($month, $c_day, $year)){

	echo '<td class = "youbi_'.date("w",mktime(0,0,0,$month,$c_day,$year)).'">'.$c_day.'</td>';

	if (date("w",mktime(0,0,0,$month,$c_day,$year)) == 6){
		echo "</tr>";
	    if(checkdate($month,$c_day + 1,$year)){
		    echo '<tr>';
	    }
	}

	$c_day ++;
}

$weekdaylast = date("w", mktime(0, 0, 0, $month+1, 0, $year));
for($i = 1; $i <7 - $weekdaylast; $i ++){
	echo "<td>  </td>";
}


?>
</tr>
</tbody>
</table>
</td>


<td>
	<table border = "3">

    <thead>
			<tr>
				<th colspan = "7"><?php echo date("Y",$timestamp)."年".$next_month;?>月</th>
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
<tr>

<?php

$c_day = 1;

$weekdayfirst = date("w",mktime(0,0,0,$next_month,1,$year));
for($i = 1;$i <= $weekdayfirst;$i ++){
	echo "<td>  </td>";
} 

while(checkdate($next_month, $c_day, $year)){

	echo '<td class = "youbi_'.date("w",mktime(0,0,0,$next_month,$c_day,$year)).'">'.$c_day.'</td>';

	if (date("w",mktime(0,0,0,$next_month,$c_day,$year)) == 6){
		echo "</tr>";
	    if(checkdate($next_month,$c_day + 1,$year)){
		    echo '<tr>';
	    }
	}

	$c_day ++;
}

$weekdaylast = date("w", mktime(0, 0, 0, $next_month+1, 0, $year));
for($i = 1; $i <7 - $weekdaylast; $i ++){
	echo "<td>  </td>";
}


?>
</tr>
</tbody>
</table>

</td>
</tr>
</table>


</body>
</html>