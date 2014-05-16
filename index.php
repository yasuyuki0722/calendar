<?php
session_start();
require_once 'function.php';
sessionReset();

$cal_regi_link = 'cal_edit.php?';
date_default_timezone_get('Asia/Tolyo');

//表示するカレンダーの数
$calendar_number = 3;
//カレンダーの先頭の曜日(sun=7, mon = 6)
$calendar_first_day = 7;

//曜日設定
$weekday_index = weekdaySet($calendar_first_day);

//年月日情報
list($this_year, $this_month, $prev_year, $prev_month, $next_year, $next_month) = yearMonth();

//祝日情報
$holidays = holidays($this_year, $this_month, $calendar_number);

//カレンダー用年月情報
$calendar_y_m = calYearMonth($calendar_number, $this_year, $this_month);

//コンボボックス用年月
$combo_y_m = comboBoxMake($this_year);

//カレンダー
foreach ($calendar_y_m as $value) {
    $calendar_make[] = array(calendar($value['calendar_y'], $value['calendar_m'], $holidays, $calendar_first_day));
}

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
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
</head>
<body>
<div id="schedule_edit" >
<h1>予定編集画面</h1>
<form action="" name="info" method="POST">
    <dl>
        <dt>
            予定開始日
        </dt>
        <dd id="start_date">
<!--             <select name="start_y" class="combo_year_month sch_year" onchange="combo1();">
 -->            <select name="start_y" class="combo_year_month sch_year">
                <?php for ($i = 1; $i <=3; $i++):?>
                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                <?php endfor;?>
            </select>
            年
            <select name="start_m" class="combo_year_month sch_month">
                <?php for ($i = 1; $i <=12; $i++):?>
                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                <?php endfor;?>
            </select>
            月
            <select name="start_d" class="sch_day">
                <?php for ($i = 1; $i <= 31; $i++):?>
                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                <?php endfor;?>
            </select>
            日
            <select name="start_h">
                <?php for ($i = 0; $i <= 23; $i++):?>
                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                <?php endfor;?>
            </select>
            時
            <select name="start_i">
                <?php for ($i = 0; $i <=30; $i = $i + 30):?>
                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                <?php endfor;?>
            </select>
            分
        </dd>

        <dt>
            予定終了日
        </dt>
        <dd id="end_date">
            <select name="end_y" class="combo_year_month sch_year" onchange="combo2();">
                <?php for ($i = 1; $i <=3; $i++):?>
                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                <?php endfor;?>
            </select>
            年
            <select name="end_m" class="combo_year_month sch_month"　onchange="combo2();">
                <?php for ($i = 1; $i <=12; $i++):?>
                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                <?php endfor;?>
            </select>
            月
            <select name="end_d" class="sch_day">
                <?php for ($i = 1; $i <= 31; $i++):?>
                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                <?php endfor;?>
            </select>
            日
            <select name="end_h">
                <?php for ($i = 0; $i <= 23; $i++):?>
                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                <?php endfor;?>
            </select>
            時
            <select name="end_i">
                <?php for ($i = 0; $i <=30; $i = $i + 30):?>
                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                <?php endfor;?>
            </select>
            分
        </dd>

            <span id="error_msg_date"></span>

        <dt>
            予定タイトル
        </dt>
        <dd>
            <input type="text" name="sch_title" value="" id="schedule_title">
        </dd>
            <span id="error_msg_title"></span>
        <dt>
            内容
        </dt>
        <dd>
            <textarea name="sch_plan" value="" id="schedule_plan"></textarea> 
        </dd>
            <span id="error_msg_plan"></span>
    </dl>
    <input type="submit" name="submit" value="保存">
    <input type="submit" name="submit" value="削除">
</form>
<button name="reset" id="reset">キャンセル</button>

</div>


<script type="text/javascript" src='script.js'></script>
<script type="text/javascript">
// $(function(){
//     $('table td a').click(function(){
//         $('.aaaaa').slideToggle();
//         return false;
//     });
//     $('.aaaaa').click(function(){
//         $(this).fadeOut();
//     });
// });
</script>
<div align='center'>
    <h1>かれんだーだよ！</h1>
    <a href="?year_month=<?php echo $prev_year.'-'.$prev_month; ?>">前月</a>
    <a href="?year_month=<?php echo date('Y-n'); ?>">今月</a>
    <a href="?year_month=<?php echo $next_year.'-'.$next_month; ?>">次月</a>
    <form action='' method='get'>
        <select name='year_month'>
            <?php foreach ($combo_y_m as $value):?>
                <?php if ($value['year'] == $this_year && $value['month'] == $this_month):?>
                    <option value="<?php echo $value['year'].'-'.$value['month'];?>" selected><?php echo $value['year'].'年'.$value['month'].'月'?></option>
                <?php else:?>
                    <option value="<?php echo  $value['year'].'-'.$value['month'];?>"><?php echo $value['year'].'年'.$value['month'].'月';?></option>
                <?php endif;?>
            <?php endforeach;?>
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
    $day_class = $value[0]['day_class'];
    ?>
    <table class="calendar">
        <thead>
            <tr>
                <th id="test" colspan='7'><?php echo $year.'年'.$month.'月' ;?></th>
            </tr>
        </thead>
        <tbody>
        <!-- 曜日情報 -->
            <tr>
            <?php foreach ($weekday_index as $value) :?>
                <td style='height: 20px'> <?php echo $value;?> </td>
            <?php endforeach ?>
            </tr>
            <?php for ($j = 0; $j < count($week[$year][$month]); $j ++):?>
            <tr>
                <?php for ($i = 0; $i <= 6; $i++):?>
                    <?php $day = $week[$year][$month][$j][$i];?>
                    <td class="<?php echo $day_class[$year][$month][$j][$i]['W'];?>">
                        <!-- 日付情報 -->
                        <div class="<?php echo $day_class[$year][$month][$j][$i]['Today'];?>">
                            <?php if (isset($day) == false) :?>
                                <?php echo '';?>
                            <?php else :?>
<!--                                 <a href="<?php// echo $cal_regi_link.'sch_y='.$year.'&amp;sch_m='.$month.'&amp;sch_d='.$day;?> " > <?php// echo $day;?> </a>-->
                                <div class="day" id="<?php echo $year.'-'.$month.'-'.$day;?>"><?php echo $day;?></div>
                            <?php endif ?>
                        </div>
                        <!-- 祝日情報 -->
                        <div class="holidayInfo">
                            <?php echo $holidays[$year][$month][$day];?>
                        </div>
                        <!-- オークションコラム -->
                        <div class="aucColumInfo">
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
<!--                                     <a href="<?php// echo 'cal_edit.php?sch_y='.$year.'&amp;sch_m='.$month.'&amp;sch_d='.$day.'&amp;sch_id='.$key ;?>" title = '<?php //echo h($value['plan']);?>'> <?php// echo '-'.h($value['title']);?><br></a>-->
                                    <div class="calendar_schedule" id="<?php echo 'sch_id='.$key ;?>"> <?php echo '-'.h($value['title']);?><br></div>
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
</body>
</html>
