


<?php
/*
$homepage = file_get_contents('http://calendar.infocharge.net/cal/2014/');
echo $homepage;
?>

<br><br>

<?php

//2012年の祝日を取得
$holidays = getHolidays(2014);
 
//Googleカレンダーから祝日を取得
function getHolidays($year) {
    $holidays = array();
 
    //Googleカレンダーから、指定年の祝日情報をJSON形式で取得するためのURL
    $url = sprintf(
        'http://www.google.com/calendar/feeds/%s/public/full?alt=json&%s&%s',
        'japanese__ja%40holiday.calendar.google.com',
        'start-min='.$year.'-01-01',
        'start-max='.$year.'-12-31'
    );
 
    //JSON形式で取得した情報を配列に変換
    $results = json_decode(file_get_contents($url), true);
 
    //年月日（例：20120512）をキーに、祝日名を配列に格納
    foreach ($results['feed']['entry'] as $value) {
        $date = str_replace('-', '', $value['gd$when'][0]['startTime']);
        $title = $value['title']['$t'];
        $holidays[$date] = $title;
    }
 
    //祝日の配列を早い順に並び替え
    ksort($holidays);
 
    //配列として祝日を返す
    return $holidays;
}

*/
$owari = '';
$kaisi = '2014-04-01';

$owari = '2014-06-31';


var_dump($kaisi);
var_dump($owari);
$holidays_url = sprintf(
    'http://74.125.235.142/calendar/feeds/%s/public/full-noattendees?start-min=%s&start-max=%d&max-results=%d&alt=json' ,
    'outid3el0qkcrsuf89fltf7a4qbacgt9@import.calendar.google.com' ,
    $kaisi,    // 取得開始日
    $owari,    // 取得終了日
    50               // 最大取得数
);

var_dump($holidays_url);

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

//echo $holidays[2014-05-05];
var_dump($holidays["20140507"]);

/*
// API
define("CALENDAR_URL", "japanese__ja@holiday.calendar.google.com");
//define("CALENDAR_URL", "japanese@holiday.calendar.google.com");
//define("CALENDAR_URL", "outid3el0qkcrsuf89fltf7a4qbacgt9@import.calendar.google.com");

// 日付のタイムゾーンを変更
ini_set("date.timezone", "Asia/Tokyo");

// debug
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

// 簡単なクラス
class CalenderUtil{

    public static function getGoogleCalender($min_date, $max_date){

        $list = array();

        // google apiのurl
        $url = "http://www.google.com/calendar/feeds/%s/public/full-noattendees?%s";

        // パラメータ
        $params = array(
            "start-min" => $min_date,
            "start-max" => $max_date,
            "max-results" => 50,
            "alt" => "json",
        );
        $queryString = http_build_query($params);

        // URLを取得
        $getUrl = sprintf($url, CALENDAR_URL, $queryString);

        // データ取得
        if($results = file_get_contents($getUrl)) {

            // デコードしたデータ
            $resultsDecode = json_decode($results, true);

            // 休日を設定するリスト
            $list = array();

            // リスト分出力
            foreach ($resultsDecode['feed']['entry'] as $key => $value ) {
                // 日付
                $date  = $value['gd$when'][0]['startTime'];
                // タイトル
                $title = $value['title']['$t'];

                // 日付をキーに設定
                $list[$date] = $title;
            }
        }

        return $list;

    }
}

// 現在の年より年初～年末までを取得
$nowYear = date("Y");
$date1 = date("Y-m-d", strtotime("{$nowYear}0101"));
$date2 = date("Y-m-d", strtotime("{$nowYear}1231"));

// 出力
$cal = CalenderUtil::getGoogleCalender($date1, $date2);
ksort($cal);
var_dump($cal);
*/

