<?php

$url = 'localhost';
$user = 'root';
$pass  ='';
$db= 'calendar';

//aJax受け取り
$title = $_POST['schedule_title'];
$plan  = $_POST['schedule_plan'];
$start = $_POST['schedule_start'];
$end   = $_POST['schedule_end'];
$id    = $_POST['schedule_id'];
$command = $_POST['command'];

//MySQLに接続
$link = mysqli_connect($url, $user, $pass, $db);

//接続状態チェック
if (mysqli_connect_errno()) {
    // die(mysqli_conect_error());
    echo 'DBへの接続が失敗しました。';
    return;
}


if ($command == 'select') {
    if ($stmt = mysqli_prepare($link, 'SELECT * FROM cal_schedules WHERE schedule_id = ?')){
        mysqli_stmt_bind_param($stmt, 's', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $sch_id, $user_id, $sch_title, $sch_plan, $sch_start, $sch_end, $creat, $update, $delete);
        while (mysqli_stmt_fetch($stmt)){
            //DBよりデータ受け取り
            $result = array(
                'schedule_title' => $sch_title,
                'schedule_plan' => $sch_plan,
                'schedule_start' => $sch_start,
                'schedule_end' => $sch_end
                );
            //エンコード
            $result = json_encode($result);
        }
        //受け渡しデータ出力
        echo $result;
    }
} else {
    switch ($command) {
        case 'update':
            $stmt = mysqli_prepare($link, 'UPDATE cal_schedules SET schedule_title = ?, schedule_plan = ?, schedule_start = ?, schedule_end = ?, created_at = created_at, update_at = NOW() WHERE schedule_id = ?');
            mysqli_stmt_bind_param($stmt, 'ssssd', $title, $plan, $start, $end, $id);
            break;
        case 'insert':
            $stmt = mysqli_prepare($link, 'INSERT INTO cal_schedules (schedule_title, schedule_plan, schedule_start, schedule_end, update_at) VALUES (?, ?, ?, ?, NOW())');
            mysqli_stmt_bind_param($stmt, 'ssss', $title, $plan, $start, $end);
            break;
        case 'delete';
            $stmt = mysqli_prepare($link, 'UPDATE cal_schedules SET  created_at = created_at, update_at = NOW(), deleted_at = NOW() WHERE schedule_id = ?');
            mysqli_stmt_bind_param($stmt, 'd', $id);
            break;
    }
    //SQL実行
    mysqli_stmt_execute($stmt);

}

mysqli_stmt_close($stmt);
mysqli_close($link);


?>