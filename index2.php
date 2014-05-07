<?php
session_start();
$cal_regi_link = 'http://192.168.33.10/calendar/cal_edit.php?';

date_default_timezone_get('Asia/Tolyo');
//$_getで取得
$year_month = isset($_GET['year_month']) ? $_GET['year_month'] : date('Y-n');
$timestamp  = strtotime($year_month.'-1'); 

if ($timestamp === false) {
    $timestamp = time();
}

//表示するカレンダーの数
$calendar_number = 3;

//曜日設定
$weekday_index = array('月','火','水','木','金','土','日');

//当月、先月、来月日付取得
list($today_y,$today_m,$today_d) = explode('-', date('Y-n-j',strtotime('Now')));
list($this_year, $this_month) = explode('-', date('Y-n', $timestamp));
list($prev_year, $prev_month) = explode('-', date('Y-n', mktime(0, 0, 0, $this_month -1, 1, $this_year)));
list($next_year, $next_month) = explode('-', date('Y-n', mktime(0, 0, 0, $this_month +1, 1, $this_year)));

/* 当月が真ん中に来るように、
 * $calendar_number分の年月情報$calendar_y_mを取得
 *(下のcalendar_makeも一緒にまわす？？)
 */

//カレンダーの数から、何ヶ月分前から取得する必要があるのかを計算
$c_count = -floor($calendar_number/2);
for ($i=0; $i < $calendar_number ; $i++) { 
    $y = date('Y',mktime(0, 0, 0,$this_month + $c_count, 1, $this_year));
    $m = date('n',mktime(0, 0, 0,$this_month + $c_count, 1, $this_year));
    //$calendar_y_m[$i] = array('calendar_y' => $y, 'calendar_m' => $m);
    $calendar_y_m[$i]['calendar_y'] = $y;
    $calendar_y_m[$i]['calendar_m'] = $m;
    $c_count++;
};

//祝日情報
$holidays = holidays($this_year, $this_month, $calendar_number);

//カレンダー生成順に
foreach ($calendar_y_m as $value) {
    $calendar_make[] = array(calendar($value['calendar_y'], $value['calendar_m'], $holidays));
};

//オークションコラム
$auc_colum = aucColum();

/*関数
 *年と月を引数に
 *戻り値は
 */
function calendar($year, $month, $holidays){
    //今月の最後の日
    $lastday = date('t',mktime(0, 0, 0, $month, 1, $year));

    //今月の最初の曜日(0 ~ 6)から始める
    $weekday_count = date("w",mktime(0, 0, 0, $month, 1, $year));

    //$weekに日付を代入
    $week_number = 0;
    for ($i = 1; $i <= $lastday; $i++) {
        //何年、何月、何週目、何曜日＝＞何日
        $week[$year][$month][$week_number][$weekday_count] = $i;
        //何年、何月、何日＝＞何曜日（不要？）
        $weekday[$year][$month][$i] = $weekday_count;

        //土、日の判断
        switch ($weekday_count) {
            case 0:
                $day_class[$year][$month][$i]['W'] = 'Sun';
                break;
            
            case 6:
                $day_class[$year][$month][$i]['W'] = 'Sat';
                break;
        }

        //祝日は日曜日と同じclass
        if (isset($holidays[$year][$month][$i])) {
            $day_class[$year][$month][$i]['W'] = 'Sun';
        }

        //今日判断
        list($today_y,$today_m,$today_d) = explode('-', date('Y-n-j',strtotime('Now')));

        if ($year == $today_y && $month == $today_m && $i == $today_d) {
            $day_class[$year][$month][$i]['Today'] = 'today';
        }else{
            $day_class[$year][$month][$i]['Today'] = ' '; //array('Today' => ' ');
        }

        $weekday_count++;
        if ($weekday_count == 7) {
            $week_number++;
            $weekday_count = 0;
        }

    }
    return array(
        'week' => $week,
        'year' => $year,
        'month' => $month,
        'weekday' => $weekday,
        'holidays' => $holidays,
        'day_class' => $day_class
         );
}

/*関数
 *祝日取得
 */
function holidays($year, $month, $calendar_number){
    $count = -floor($calendar_number/2);
    //祝日取得開始日
    $start_date  = date('Y-m-01', mktime(0, 0, 0,$month + $count, 1, $year));

    //祝日取得終了日
    $finish_date = date('Y-m-t', mktime(0, 0, 0,$month + $count + $calendar_number - 1, 1, $year));

    //googleカレンダーより
    $holidays_url = sprintf(
        'http://74.125.235.142/calendar/feeds/%s/public/full-noattendees?start-min=%s&start-max=%s&max-results=%d&alt=json' ,
        'outid3el0qkcrsuf89fltf7a4qbacgt9@import.calendar.google.com' ,
        $start_date,    // 取得開始日
        $finish_date,   // 取得終了日
        30               // 最大取得数
    );

    $results = file_get_contents($holidays_url);
    //file_getでかえってこなかった場合false
    if($results) {
        //JSONを連想配列へ
        $results = json_decode($results, true);
        if (count($results['feed']['entry']) == 0) {
            return;
        }

        //$holidays = array();
        foreach($results['feed']['entry'] as $val) {
            //日付を取得
            $date = explode('-',$val['gd$when'][0]['startTime']);

            //日付を'year'.'month','day'に分解し配列にいれる
            $date_fix = array(
                'year' => $date[0],
                'month' => sprintf('%d',$date[1]),
                'day'=> sprintf('%d',$date[2])
                );

            //何の日か取得（日本語部分のみ）
            $title = explode('/', $val['title']['$t']);

            // 日付をキーに、祝日名を値に格納
            $holidays[$date_fix['year']][$date_fix['month']][$date_fix['day']] = $title[0]; 
        }
        //ksort($holidays); // 日付順にソート
    }
    return $holidays;
}

/*関数
 *オークファンコラムフィード取得
 *
 */

function aucColum() {
    //$rss = 'http://college.aucfan.com/column/feed/';
    $rss ='http://aucfan.com/article/feed/';
    $xml = simplexml_load_file($rss);
    //ファイルがとれなければ終わる
    if (isset($xml)) {
    }else{
        return;
    }

//xmlから年月日とtitle、linkを取得
    foreach ($xml->channel->item as $value) {
        $pub_date = $value->pubDate;
        list($year, $month, $day) = explode('-', date('Y-n-j', strtotime($pub_date)));
        $auc_colum_data[$year][$month][$day] = array(
            'title' => $value->title,
            'link' => $value->link
            );
    }
    return $auc_colum_data;
}


/*DBから予定取得
 *
 *
 */
function schedulesGet($year, $month, $calendar_number){

    $count = -floor($calendar_number/2);
    //予定取得開始日
    $start_date  = date('Y-n-01 00:00:00', mktime(0, 0, 0,$month + $count, 1, $year));

    //予定取得終了日
    $finish_date = date('Y-n-t 23:59:59', mktime(0, 0, 0,$month + $count + $calendar_number - 1, 1, $year));

    $url = 'localhost';
    $user = 'root';
    $pass  ='';
    $db= 'calendar';

    //MySQLに接続
    $link = mysqli_connect($url, $user, $pass, $db);

    //接続状態チェック
    if (mysqli_connect_errno()) {
        die(mysqli_conect_error());
    }
    //SQLの判断
    // switch ($_POST['command']) {
    //     case 'update':
    //         $command = sprintf('UPDATE cal_schedules SET schedule_title = "%s", schedule_plan = "%s", schedule_start = "%s", schedule_end = "%s", created_at = created_at, update_at = NOW() WHERE schedule_id = %d', $_POST['sch_title'], $_POST['sch_plan'], $_POST['sch_start'], $_POST['sch_end'], $_POST['sch_id']);
    //         break;
    //     case 'insert':
    //         $command = sprintf('INSERT INTO cal_schedules (schedule_title, schedule_plan, schedule_start, schedule_end, update_at) VALUES ("%s", "%s", "%s", "%s", NOW())', $_POST['sch_title'], $_POST['sch_plan'], $_POST['sch_start'], $_POST['sch_end']);
    //         break;
    //     case 'delete';
    //         $command = sprintf('UPDATE cal_schedules SET  created_at = created_at, deleted_at = NOW() WHERE schedule_id = "%d"', $_POST['sch_id']);
    if ($_POST['submit'] == '保存') {
        switch ($_SESSION['command']) {
            case 'update':
                $command = sprintf('UPDATE cal_schedules SET schedule_title = "%s", schedule_plan = "%s", schedule_start = "%s", schedule_end = "%s", created_at = created_at, update_at = NOW() WHERE schedule_id = %d', $_SESSION['sch_title'], $_SESSION['sch_plan'], $_SESSION['sch_start'], $_SESSION['sch_end'], $_SESSION['sch_id']);
                break;
            case 'insert':
                $command = sprintf('INSERT INTO cal_schedules (schedule_title, schedule_plan, schedule_start, schedule_end, update_at) VALUES ("%s", "%s", "%s", "%s", NOW())', $_SESSION['sch_title'], $_SESSION['sch_plan'], $_SESSION['sch_start'], $_SESSION['sch_end']);
                break;
            case 'delete';
                $command = sprintf('UPDATE cal_schedules SET  created_at = created_at, update_at = NOW(), deleted_at = NOW() WHERE schedule_id = "%d"', $_SESSION['sch_id']);
            }

            //SQL実行
            if (isset($command)) {
                if ($result = mysqli_query($link, $command)) {
                } else {
                    echo "失敗";
                }
            }
    }
    //SQL該当月予定取得
    $select = sprintf('SELECT * FROM cal_schedules WHERE deleted_at IS NULL AND ((schedule_start BETWEEN "%s" AND "%s") OR (schedule_end BETWEEN "%s" AND "%s"))', $start_date, $finish_date, $start_date, $finish_date);
    //mysqli_queryに配列がかえるかfalseがかえる
    if ($result = mysqli_query($link, $select)) {
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            //予定開始日と予定終了日の差を計算
            list($sch_st_y, $sch_st_m, $sch_st_d) = explode('-', date('Y-n-j',strtotime($row['schedule_start'])));
            list($sch_ed_y, $sch_ed_m, $sch_ed_d) = explode('-', date('Y-n-j',strtotime($row['schedule_end'])));
            $sch_count_day = (strtotime($sch_ed_y.'-'.$sch_ed_m.'-'.$sch_ed_d) - strtotime($sch_st_y.'-'.$sch_st_m.'-'.$sch_st_d)) / 86400 ;
            //予定の日数分forでまわす
            for ($i = 0; $i <= $sch_count_day ; $i++) { 
                list($sch_y, $sch_m, $sch_d) = explode('-', date('Y-n-j', mktime(0, 0, 0, $sch_st_m, $sch_st_d + $i, $sch_st_y)));
                $schedule[$sch_y][$sch_m][$sch_d][$row['schedule_id']]['title'] = $row['schedule_title'];
                $schedule[$sch_y][$sch_m][$sch_d][$row['schedule_id']]['plan'] = $row['schedule_plan'];
            }
            // list($sch_y, $sch_m, $sch_d) = explode('-', date('Y-n-j',strtotime($row['schedule_start'])));
            // $schedule[$sch_y][$sch_m][$sch_d][$row['schedule_id']]['title'] = $row['schedule_title'];
            // $schedule[$sch_y][$sch_m][$sch_d][$row['schedule_id']]['plan'] = $row['schedule_plan'];
        }
        mysqli_free_result($result);
    } else {
        echo "失敗！";
    }
    mysqli_close($link);
    //SESSION初期化
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    session_destroy();
    return $schedule;
}

function h($text){
    return htmlspecialchars($text);
}


$schedule = schedulesGet($this_year, $this_month, $calendar_number);

?>


<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='utf-8'>
    <title>calendar</title>
    <link rel='stylesheet' href='style2.css'>
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
            <?php for ($i=0; $i <= 6; $i++) :?>
                <td style='height: 20px'> <?php echo $weekday_index[$i];?> </td>
            <?php endfor ?>
            </tr>

            <?php for ($j = 0; $j < count($week[$year][$month]);$j ++):?>
            <tr>
                <?php for ($i=0; $i <= 6; $i++):?>
                    <?php //$i = ($i + 1)%7 mod7で折り返したい;?>
                    <?php $day = $week[$year][$month][$j][$i]?>
                    <td class='<?php echo $day_class[$year][$month][$day]['W'];?>'>

                        <!-- 日付情報 -->
                        <div class='<?php echo $day_class[$year][$month][$day]['Today'];?>'>
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
                            <a href=" <?php echo $auc_colum[$year][$month][$day]['link'];?> " title = '<?php echo $auc_colum[$year][$month][$day]['title'];?>' >
                            <?php echo $auc_colum[$year][$month][$day]['title'];?>
                            </a>
                        </div>

                        <!-- スケジュール -->
                        <div class="scheduleInfo">
                        <?php if (isset($schedule[$year][$month][$day])):?>
                        <?php foreach ($schedule[$year][$month][$day] as $key => $value) :?> 
                            <a href="<?php echo 'cal_edit.php?sch_y='.$year.'&amp;sch_m='.$month.'&amp;sch_d='.$day.'&amp;sch_id='.$key ;?>" title = '<?php echo h($value['plan']);?>'> <?php echo '-'.h($value['title']);?><br></a>
                        <?php endforeach;?>
                        <?php endif ;?>
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
