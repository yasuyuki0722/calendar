<?php
session_start();

$token_session = $_SESSION['nk_token'];
$token_post = $_POST['nk_token'];
if ($token_post == null || $token_post !== $token_session) {
    $result = array('error_msg' => 'miss:不正な接続ですお');
    echo json_encode($result);
    exit;
}

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

if ($command === 'resister') {
    if ($id === '') {
        $command = 'insert';
    } else {
        $command = 'update';
    }
}

//MySQLに接続
$link = mysqli_connect($url, $user, $pass, $db);

//接続状態チェック
if (mysqli_connect_errno()) {
    $result = array('error_msg' => '接続に失敗しました');
    echo json_encode($result);
    exit;
}


if ($command == 'select') {
    //if ($stmt = mysqli_prepare($link, 'SELECT * FROM cal_schedules WHERE schedule_id = ?')){
    if ($stmt = mysqli_prepare($link, "select schedule_id,user_id,schedule_title,schedule_plan,date_format(schedule_start,'%Y/%c/%e %T'),date_format(schedule_end,'%Y/%c/%e %T') from cal_schedules WHERE schedule_id = ?")){
        mysqli_stmt_bind_param($stmt, 's', $id);
        mysqli_stmt_execute($stmt);
        // mysqli_stmt_bind_result($stmt, $sch_id, $user_id, $sch_title, $sch_plan, $sch_start, $sch_end, $creat, $update, $delete);
        mysqli_stmt_bind_result($stmt, $sch_id, $user_id, $sch_title, $sch_plan, $sch_start, $sch_end);
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
    $result = array('error_msg' => 'success');
    echo json_encode($result);

    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    session_destroy();
}

mysqli_stmt_close($stmt);
mysqli_close($link);


?>