<?php
date_default_timezone_get('Asia/Tolyo');
$year_month_day = isset($_GET['y_m_d']) ? $_GET['y_m_d'] : date('Y-n-d');
$timestamp  = strtotime($year_month_day); 
if ($timestamp === false) {
    $timestamp = time();
}

$schedule_id = isset($_GET['id']) ? $_GET['id']: NULL;

$url = 'localhost';
$user = 'root';
$pass  ='';
$db = 'calendar';

//$schedule_idの有無をチェック
if (isset($schedule_id)) {
    //$schedule_idに対応する予定をDBからSELECT
    //MySQLに接続
    $link = mysqli_connect($url, $user, $pass, $db);

    //接続状態チェック
    if (mysqli_connect_errno()) {
        die(mysqli_conect_error());
    }

    $select = sprintf('SELECT * FROM cal_schedules WHERE schedule_id = "%s"', $schedule_id);
    var_dump($select);
    //mysqli_queryに配列がかえるかfalseがかえる
    if ($result = mysqli_query($link, $select)) {
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            var_dump($row);
            $sch_detail['start'] = date('Y-n-j', strtotime($row['schedule_start']));
            $sch_detail['end'] = date('Y-n-j', strtotime($row['schedule_end']));
            $sch_detail['title'] = $row['schedule_title'];
            $sch_detail['plan'] = $row['schedule_plan'];
        }
        mysqli_free_result($result);
    } else {
        echo "失敗！";
    }
    mysqli_close($link);
} else {
    //$schedule_idがない場合はGETで受け取った日付を入れる
    $sch_detail['start'] = $year_month_day;
    $sch_detail['end'] = $year_month_day;
    $sch_detail['title'] = null;
    $sch_detail['plan'] = null;
}

?>

<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='utf-8'>
    <title>calendar_regist</title>
</head>
<body>
<h1>予定編集画面</h1>
<form action="index2.php" method="POST">
    <dl>
        <dt>
            予定開始日
        </dt>
        <dd>
            <input type="text" name="sch_start" maxlength="8" value="<?php echo $sch_detail['start'];?>">
        </dd>
        <dt>
            予定終了日
        </dt>
        <dd>
            <input type="text" name="sch_end" maxlength="8" value="<?php echo $sch_detail['end'];?>">
        </dd>
        <dt>
            予定タイトル
        </dt>
        <dd>
            <input type="text" name="sch_title" value="<?php echo $sch_detail['title'];?>">
        </dd>
        <dt>
            内容
        </dt>
        <dd>
<!--         <textarea name="sch_plan"></textarea> -->
            <input type="textarea" name="sch_plan" value="<?php echo $sch_detail['plan'];?>">
        </dd>
    </dl>

<!-- $schedule_isの有無で更新か登録をcheck -->
    <?php if (isset($schedule_id)) :?>
        <input type="hidden" name="sch_id" value="<?php echo $schedule_id ;?>">
        <input type="hidden" name="command" value="update">
        <input type="submit" value="更新">
    <?php else:?>
        <input type="submit" value="登録">
        <input type="hidden" name="command" value="insert">
    <?php endif;?>
</form>

<!-- 削除の場合 -->
<form action="index2.php" method="POST">
    <input type="hidden" name="sch_id" value="<?php echo $schedule_id ;?>">
    <input type="hidden" name="command" value="delete">
    <input type="submit" value="削除">
</form>
</body>
</html>