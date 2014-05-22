<?php

/**
*曜日の順番をかえて配列を戻す
*
*@param int $calendar_first_d 
*@return array $weekday_index
*/

function weekdaySet($calendar_first_day){
    $w_index = array('日', '月', '火', '水', '木', '金', '土');
    $weekday_index = array();
    for ($i = 0; $i <= 6; $i++) {
        $j = ($i + $calendar_first_day) % 7;
        $weekday_index[$i] = $w_index[$j];
    }
    return $weekday_index;
}

/**
*生成するカレンダーの開始月、終了月を返す
*
*@param int $calendar_number 表示するカレンダーの数
*@param int $this_year カレンダーで表示するメインの年
*@param int $this_month カレンダーで表示するメインの月
*@return array $calendar_y_m 表示する年月の配列
*/
function calYearMonth($calendar_number, $this_year, $this_month){
    //当月が真ん中に来るように、$calendar_number分の年月情報$calendar_y_mを取得
    //カレンダーの数から、何ヶ月分前から取得する必要があるのかを計算
    $count = calendarCount($calendar_number);
    for ($i = 0; $i < $calendar_number ; $i++) { 
        $y = date('Y', mktime(0, 0, 0, $this_month + $count, 1, $this_year));
        $m = date('n', mktime(0, 0, 0, $this_month + $count, 1, $this_year));
        $calendar_y_m[$i]['calendar_y'] = $y;
        $calendar_y_m[$i]['calendar_m'] = $m;
        $count++;
    };
    return $calendar_y_m;
}

/**
*カレンダーの前月、次月リンク用の変数を返す
*
*@return array 中心の年月、先月の年月、次月の年月
*/
function yearMonth(){
    //$_getで取得
    $year_month = isset($_GET['year_month']) ? $_GET['year_month'] : date('Y-n');

    $timestamp  = strtotime($year_month.'-1'); 

    if ($timestamp === false) {
        $timestamp = time();
    }

    //先月、来月日付取得
    list($this_year, $this_month) = explode('-', date('Y-n', $timestamp));

    //四桁の年数に対応
    if (strlen($this_year) != 4 ) {
        $this_year = date('Y');
    }

    list($prev_year, $prev_month) = explode('-', date('Y-n', mktime(0, 0, 0, $this_month - 1, 1, $this_year)));
    list($next_year, $next_month) = explode('-', date('Y-n', mktime(0, 0, 0, $this_month + 1, 1, $this_year)));
        return array(
            $this_year,
            $this_month,
            $prev_year,
            $prev_month,
            $next_year,
            $next_month,
            );
}

/**
*年と月の入ったコンボボックス（前後一年分）の年月を返す
*
*@param int $year 
*@return array 前後一年分の年月
*/
function comboBoxMake($year){
    $k = 0;
    $sch_y_m = array();
    for ($i = -1; $i <= 1; $i++) { 
        for ($j=1; $j <= 12; $j++) { 
            $sch_y_m[$k]['year']  = date('Y', mktime(0, 0, 0, $j, 1, $year + $i));
            $sch_y_m[$k]['month'] = date('n', mktime(0, 0, 0, $j, 1, $year + $i));
            $k++;
        }
    }
    return $sch_y_m;
}

/**
*年と月を引数に
*
*@param int   $year
*@param int   $month
*@param array $holidays
*@param int   $calendar_first_day
*@return array 週の情報、
*/
function calendar($year, $month, $holidays, $calendar_first_day){
    //今月の最後の日
    $lastday = date('t', mktime(0, 0, 0, $month, 1, $year));
    //今月の最初の曜日(0 ~ 6)から始める
    $weekday_count = date("w", mktime(0, 0, 0, $month, 1, $year));
    //$weekに日付を代入
    $week_number = 0;
    //mod7で日付をずらす
    $day_count = abs($weekday_count - $calendar_first_day) % 7;
    //前月の日付を入れる

    for ($i = 0; $i < $day_count; $i++) { 
        $cell_count = $day_count - 1 - $i;
        $week[$year][$month][$week_number][$cell_count]['y'] = date('Y', mktime(0, 0, 0, $month, -$i, $year));
        $week[$year][$month][$week_number][$cell_count]['m'] = date('n', mktime(0, 0, 0, $month, -$i, $year));
        $week[$year][$month][$week_number][$cell_count]['d'] = date('j', mktime(0, 0, 0, $month, -$i, $year));

        $day_class[$year][$month][$week_number][$i]['weekday_index'] = 'not';
    }

    //今月の日付を代入
    for ($i = 1; $i <= $lastday; $i++) {
        //何年、何月、何週目、左から何番目＝＞何日
        $week[$year][$month][$week_number][$day_count]['y'] = $year;
        $week[$year][$month][$week_number][$day_count]['m'] = $month;
        $week[$year][$month][$week_number][$day_count]['d'] = $i;

        //何年、何月、何日＝＞何曜日（不要？）
        //$weekday[$year][$month][$i] = $weekday_count;
        //土、日の判断
        switch (($calendar_first_day + $day_count) % 7) {
            case 0:
                $day_class[$year][$month][$week_number][$day_count]['weekday_index'] = 'sun';
                break;
            case 6:
                $day_class[$year][$month][$week_number][$day_count]['weekday_index'] = 'sat';
                break;
        }

        //祝日は日曜日と同じclass
        if (isset($holidays[$year][$month][$i])) {
            $day_class[$year][$month][$week_number][$day_count]['weekday_index'] = 'sun';
        }

        //今日判断
        if ($year == date('Y') && $month == date('n') && $i == date('j')) {
            $day_class[$year][$month][$week_number][$day_count]['Today'] = 'today';
        }else{
            $day_class[$year][$month][$week_number][$day_count]['Today'] = ' '; //array('Today' => ' ');
        }
        $day_count++;
        if ($day_count == 7) {
            $week_number++;
            $day_count = 0;
        }
    }
    //来月の日付を入れる
    if ($day_count != 0) {
        $next_d = 1;

        for ($i = $day_count; $i < 7; $i++) { 
            $week[$year][$month][$week_number][$i]['y'] = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
            $week[$year][$month][$week_number][$i]['m'] = date('n', mktime(0, 0, 0, $month + 1, 1, $year));
            $week[$year][$month][$week_number][$i]['d'] = $next_d;

            $day_class[$year][$month][$week_number][$i]['weekday_index'] = 'not';
            $next_d++;
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

/**
*祝日取得
*@param int $year
*@param int $month
*@param int $calendar_number
*@return array 年月日に対応した祝日の配列
*/
function holidays($year, $month, $calendar_number){
    // $count = -floor($calendar_number/2);
    $count = calendarCount($calendar_number);
    //祝日取得開始日
    $start_date  = date('Y-m-01', mktime(0, 0, 0, $month + $count, 1, $year));

    //祝日取得終了日
    $finish_date = date('Y-m-t', mktime(0, 0, 0, $month + $count + $calendar_number - 1, 1, $year));

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
            $date = explode('-', $val['gd$when'][0]['startTime']);
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
    }
    return $holidays;
}

/**
*オークファンコラムフィード取得
*
*@return array 年月日に対応したオーくファンコラム情報
*/
function aucColumns() {
    $rss ='http://aucfan.com/article/feed/';
    $xml = simplexml_load_file($rss);
    //ファイルがとれなければ終わる
    if (! isset($xml)) {
        echo '接続に失敗しました';
        return;
    }
    //xmlから年月日とtitle、linkを取得
    foreach ($xml->channel->item as $value) {
        $pub_date = $value->pubDate;
        list($year, $month, $day) = explode('-', date('Y-n-j', strtotime($pub_date)));
        $auc_columns_data[$year][$month][$day][] = array(
            'title' => $value->title,
            'link' => $value->link
            );
    }
    return $auc_columns_data;
}

/**
*DBから予定取得
*@param int $year 
*@param int $month
*@param int $calendar_number
*@return array 年月日に対応した予定の情報
*/
function schedulesGet($year, $month, $calendar_number){

    $count = calendarCount($calendar_number);
    //予定取得開始日
    $start_date  = date('Y-n-01 00:00:00', mktime(0, 0, 0, $month + $count, 1, $year));

    //予定取得終了日
    $finish_date = date('Y-n-t 23:59:59', mktime(0, 0, 0, $month + $count + $calendar_number - 1, 1, $year));

    $url = 'localhost';
    $user = 'root';
    $pass  ='';
    $db= 'calendar';

    //MySQLに接続
    $link = mysqli_connect($url, $user, $pass, $db);

    //接続状態チェック
    if (mysqli_connect_errno()) {
        echo '接続に失敗しました';
    }
    //プリペアドステートメントを用いてselect
    if ($stmt = mysqli_prepare($link, 'SELECT * FROM cal_schedules WHERE deleted_at IS NULL AND ((schedule_start BETWEEN ? AND ?) OR (schedule_end BETWEEN ? AND ?))')){;
    //     echo 'miss!';
    // } else {
        mysqli_stmt_bind_param($stmt, 'ssss', $start_date, $finish_date, $start_date, $finish_date);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $sch_id, $user_id, $sch_title, $sch_plan, $sch_start, $sch_end, $creat, $update, $delete);
        while (mysqli_stmt_fetch($stmt)){
            //予定開始日と予定終了日の差を計算
            list($sch_st_y, $sch_st_m, $sch_st_d) = explode('-', date('Y-n-j',strtotime($sch_start)));
            list($sch_ed_y, $sch_ed_m, $sch_ed_d) = explode('-', date('Y-n-j',strtotime($sch_end)));
            $sch_count_day = (strtotime($sch_ed_y.'-'.$sch_ed_m.'-'.$sch_ed_d) - strtotime($sch_st_y.'-'.$sch_st_m.'-'.$sch_st_d)) / 86400 ;
            //予定の日数分forでまわす
            for ($i = 0; $i <= $sch_count_day ; $i++) { 
                list($sch_y, $sch_m, $sch_d) = explode('-', date('Y-n-j', mktime(0, 0, 0, $sch_st_m, $sch_st_d + $i, $sch_st_y)));
                $schedule[$sch_y][$sch_m][$sch_d][$sch_id]['title'] = $sch_title;
                $schedule[$sch_y][$sch_m][$sch_d][$sch_id]['plan'] = $sch_plan;
            }
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    return $schedule;
}

/**
*SESSION初期化
*
*@return なし
*/
function sessionReset(){
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    session_destroy();
    return;
}

/**
*XSS対策
*
*@param mixed
*@return string  
*/
function h($text){
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
*トークンをチェック
*
*@return なし
*/
function tokenCheck(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['token']!== $_SESSION['token']) {
            echo '不正名アクセスです！';
        }
    }
    return;
}

/**
*表示するカレンダーの数から、月の計算に関するカウントを返す
*
*@param int
*@return int 
*/
function calendarCount($calendar_number){
    $count = -floor($calendar_number/2);
    return $count;
}


/**
*表示するカレンダーの数を返す
*
*@return int
*/
function calendarNumber(){
    if (isset($_GET['calendar_number']) && ctype_digit($_GET['calendar_number'])) {
        $number = $_GET['calendar_number'];
        $number = intval($number);
    } else {
        $number = 3;
    }
    return $number;
}

/**
*表示するカレンダーの始まるの曜日を返す
*
*@return int 0 ~ 6
*/
function calendarIndex(){
    if (isset($_GET['calendar_first_day'])) {
        
    }
    if (isset($_GET['calendar_first_day']) && ctype_digit($_GET['calendar_first_day'])) {
        $number = $_GET['calendar_first_day'];
        $number = intval($number);
    } else {
        $number = 0;
    }
    return $number;
}




?>