<?php

$schedule_id = isset($_GET['id']) ? $_GET['id']: NULL;



$url = 'localhost';
$user = 'root';
$pass  ='';
$db = 'calendar';

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


?>

<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='utf-8'>
    <title>calendar_regist</title>
</head>
<body>
<h1>予定編集画面</h1>
<form action="cal_edit_comp.php" method="POST">
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
    <input type="hidden" name="sch_id" value="<?php echo $schedule_id ;?>">
    <input type="submit" value="更新">
</form>

<form action="cal_del.php" method="POST">
    <input type="hidden" name="sch_id" value="<?php echo $schedule_id ;?>">
    <input type="submit" value="削除">
</form>
</body>
</html>