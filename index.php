<?php
session_start();
require_once 'function.php';

date_default_timezone_get('Asia/Tolyo');


//表示するカレンダーの数
$calendar_number = calendarNumber();
//カレンダーの先頭の曜日(sun=0, mon = 1,,,sat=6)
$calendar_first_day = calendarIndex();

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
$calendar_make = array();
foreach ($calendar_y_m as $value) {
    array_push($calendar_make, calendar($value['calendar_y'], $value['calendar_m'], $holidays, $calendar_first_day));
}

//オークションコラム
$auc_columns = aucColumns();

//予定
$schedule = schedulesGet($this_year, $this_month, $calendar_number);

?>

<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='utf-8'>
    <title>calendar</title>
    <link rel='stylesheet' href='style.css'>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
</head>
<body>
<div id="shadow"></div>
<div id="schedule_edit">
    <div id="form">
    <h1>予定編集画面</h1>
    <form action="" name="info" method="POST">
        <dl id="form_list">
        <!-- schedule_id用 -->
        <div id="schedule_id" style="display:none;"></div>
            <dt>
                予定開始日
            </dt>
            <dd id="start_date">
                <select name="start_y" class="combo_year_month sch_year">
                </select>
                年
                <select name="start_m" class="combo_year_month sch_month">
                </select>
                月
                <select name="start_d" class="sch_day">
                </select>
                日
                <select name="start_h" class="sch_hour">
                </select>
                時
                <select name="start_i" class="sch_minute">
                </select>
                分
            </dd>

            <dt>
                予定終了日
            </dt>
            <dd id="end_date">
                <select name="end_y" class="combo_year_month sch_year">
                </select>
                年
                <select name="end_m" class="combo_year_month sch_month">
                </select>
                月
                <select name="end_d" class="sch_day">
                </select>
                日
                <select name="end_h" class="sch_hour">
                </select>
                時
                <select name="end_i" class="sch_minute">
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
        <input type="hidden" name="nk_token" value="">
        <button type="button" name="submit" id="submit">保存</button>
        <button type="button" name="delete" id="delete">削除</button>
        <button type="button" name="reset" id="reset">キャンセル</button>
    </form>
    </div>
</div>

<div>
<script type="text/javascript" src='script.js'></script>

<div align='center'>
    <h1>かれんだーだよ！</h1>
    <a href="?year_month=<?php echo $prev_year.'-'.$prev_month; ?>">次月</a>
    <a href="?year_month=<?php echo date('Y-n'); ?>">今月</a>
    <a href="?year_month=<?php echo $next_year.'-'.$next_month; ?>">次月</a>
    <form action='' method='get'>
        <select name='year_month'>
            <?php foreach ($combo_y_m as $value):?>
                <?php if ($value['year'] == $this_year && $value['month'] == $this_month):?>
                    <option value="<?php echo $value['year'].'-'.$value['month'];?>" selected><?php echo $value['year'].'年'.$value['month'].'月'?></option>
                <?php else:?>
                    <option value="<?php echo $value['year'].'-'.$value['month'];?>"><?php echo $value['year'].'年'.$value['month'].'月';?></option>
                <?php endif;?>
            <?php endforeach;?>
        </select>
        <input type = 'submit' value = '更新'>
    </form>
</div>
<?php foreach ($calendar_make as $value) :?>
    <?php
    $week  = $value['week'];
    $year  = $value['year'];
    $month = $value['month'];
    $weekday = $value['weekday'];
    $day_class = $value['day_class'];
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
                    <?php $day = $week[$year][$month][$j][$i]['d'];?>
                    <td class="<?php echo $day_class[$year][$month][$j][$i]['weekday_index'];?>">
                        <!-- 日付情報 -->
                        <div class="<?php echo $day_class[$year][$month][$j][$i]['Today'];?>" onmouseover="this.style.backgroundColor='orange'" onmouseout="this.style.backgroundColor=''">
                            <?php if (isset($day) == false) :?>
                                <?php echo '';?>
                            <?php else :?>
                                 <div class="day" data-dateinfo="<?php echo $week[$year][$month][$j][$i]['y'].'-'.$week[$year][$month][$j][$i]['m'].'-'.$day ;?>"><?php echo $day;?></div>
                            <?php endif ?>
                        </div>
                        <!-- 祝日情報 -->
                        <div class="holiday_info">
                            <?php echo $holidays[$year][$month][$day];?>
                        </div>
                        <!-- オークションコラム -->
                        <div class="auc_columns_info">
                        <?php if ($day_class[$year][$month][$j][$i]['weekday_index'] != 'not' && isset($auc_columns[$year][$month][$day])) :?>
                            <?php foreach ($auc_columns[$year][$month][$day] as $value) :?>
                                <a href=" <?php echo $value['link'];?> " title = '<?php echo $value['title'];?>' >
                                    <?php echo $value['title'];?>
                                </a><br>
                            <?php endforeach;?>
                        <?php endif;?>
                        </div>
                        <!-- スケジュール -->
                        <div class="schedule_info">
                        <?php if ($day_class[$year][$month][$j][$i]['weekday_index'] != 'not' && isset($schedule[$year][$month][$day])) :?>
                            <?php foreach ($schedule[$year][$month][$day] as $key => $value) :?> 
                                <div class="calendar_schedule" data-scheduleid="<?php echo $key ;?>" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration=''"> <?php echo '-'.h($value['title']);?><br></div>
                            <?php endforeach;?>
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
