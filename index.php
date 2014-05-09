<?php
require_once 'function.php';

//祝日情報
$holidays = holidays($this_year, $this_month, $calendar_number);

//カレンダー生成順に
foreach ($calendar_y_m as $value) {
    $calendar_make[] = array(calendar($value['calendar_y'], $value['calendar_m'], $holidays, $calendar_first_d));
};

//オークションコラム
$auc_colum = aucColum();
//予定
$schedule = schedulesGet($this_year, $this_month, $calendar_number);

?>


<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='utf-8'>
    <title>calendar</title>
    <link rel='stylesheet' href='style.css'>
</head>
<body>

<div align='center'>
    <h1>かれんだーだよ！</h1>
    <a href="?year_month=<?php echo $prev_year.'-'.$prev_month; ?>">先月</a>
    <a href="?year_month=<?php echo $today_y.'-'.$today_m; ?>">今月</a>
    <a href="?year_month=<?php echo $next_year.'-'.$next_month; ?>">来月</a>
    <form action='' method='get'>
        <select name='year_month'>
            <?php for ($select_y = $this_year - 1; $select_y <= $this_year + 1; $select_y++) :?> 
                <?php for ($select_m = 1; $select_m <= 12 ; $select_m++) :?>
                    <?php if ($select_y == $this_year && $select_m == $this_month):?>
                    <option value = "<?php echo $select_y.'-'.$select_m ;?>" selected>
                        <?php echo $select_y.'年'.$select_m.'月';?>
                    </option>
                    <?php else :?>
                    <option value = "<?php echo $select_y.'-'.$select_m ;?>">
                        <?php echo $select_y.'年'.$select_m.'月';?>
                    </option>
                <?php endif ;?>
                <?php endfor ;?>
            <?php endfor ;?>
        </select>
        <input type = 'submit' value = '更新'>
    </form>

</div>

<?php foreach ($calendar_make as $value) :?>
    <?php
    $week  = $value[0]['week'];
    $year  = $value[0]['year'];
    $month = $value[0]['month'];
    $weekday = $value[0]['weekday'];
    //$holidays = $value[0]['holidays'];
    //$auc_colum = $value[0]['auc_colum'];
    $day_class = $value[0]['day_class'];
    ?>
    <table class="calendar">
        <thead>
            <tr>
                <th colspan='7'><?php echo $year.'年'.$month.'月' ;?></th>
            </tr>
        </thead>
        <tbody>
        <!-- 曜日情報 -->
            <tr>
            <?php foreach ($weekday_index as $value) :?>
                <?php// $j = ($i + 6) % 7;?>
                <td style='height: 20px'> <?php echo $value;?> </td>
            <?php endforeach ?>
            </tr>
            <?php for ($j = 0; $j < count($week[$year][$month]); $j ++):?>
            <tr>
                <?php for ($i = 0; $i <= 6; $i++):?>
                    <?php $day = $week[$year][$month][$j][$i];?>
                    <td class='<?php echo $day_class[$year][$month][$j][$i]['W'];?>'>
                        <!-- 日付情報 -->
                        <div class='<?php echo $day_class[$year][$month][$j][$i]['Today'];?>'>
                            <?php if (isset($day) == false) :?>
                                <?php echo '';?>
                            <?php else :?>
                                <a href="<?php echo $cal_regi_link.'sch_y='.$year.'&amp;sch_m='.$month.'&amp;sch_d='.$day;?> " > <?php echo $day;?> </a>
                            <?php endif ?>
                        </div>
                        <!-- 祝日情報 -->
                        <div class='holidayInfo'>
                            <?php echo $holidays[$year][$month][$day];?>
                        </div>
                        <!-- オークションコラム -->
                        <div class='aucColumInfo'>
                        <?php if ($day_class[$year][$month][$j][$i]['W'] != 'not') :?>
                            <a href=" <?php echo $auc_colum[$year][$month][$day]['link'];?> " title = '<?php echo $auc_colum[$year][$month][$day]['title'];?>' >
                                <?php echo $auc_colum[$year][$month][$day]['title'];?>
                            </a>
                        <?php endif;?>
                        </div>
                        <!-- スケジュール -->
                        <div class="scheduleInfo">
                        <?php if ($day_class[$year][$month][$j][$i]['W'] != 'not') :?>
                            <?php if (isset($schedule[$year][$month][$day])) :?>
                                <?php foreach ($schedule[$year][$month][$day] as $key => $value) :?> 
                                    <a href="<?php echo 'cal_edit.php?sch_y='.$year.'&amp;sch_m='.$month.'&amp;sch_d='.$day.'&amp;sch_id='.$key ;?>" title = '<?php echo h($value['plan']);?>'> <?php echo '-'.h($value['title']);?><br></a>
                                <?php endforeach;?>
                            <?php endif ;?>
                        <?php endif;?>
                        </div>
                    </td>
                <?php endfor;?>
            </tr>
            <?php endfor;?>
        </tbody>
    </table>
<?php endforeach ;?>

<!-- <form>
    <input type="" name="">
</form>
 -->
</body>
</html>
