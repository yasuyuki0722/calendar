<?php
session_start();
//index2.phpからのアクセスか、確認フェーズのアクセスか
if (!isset($_POST['flg'])) {
    $_POST['flg'] = 'check';
    date_default_timezone_get('Asia/Tolyo');
    $sch_year  = isset($_GET['sch_y']) ? $_GET['sch_y'] : date('Y');
    $sch_month = isset($_GET['sch_m']) ? $_GET['sch_m'] : date('n');
    $sch_day   = isset($_GET['sch_d']) ? $_GET['sch_d'] : date('d');
    // $year_month_day = isset($_GET['y_m_d']) ? $_GET['y_m_d'] : date('Y-n-d');
    // $timestamp  = strtotime($year_month_day); 
    // if ($timestamp === false) {
    //     $timestamp = time();
    //}

    $sch_id = isset($_GET['sch_id']) ? $_GET['sch_id']: NULL;

    $url = 'localhost';
    $user = 'root';
    $pass  ='';
    $db = 'calendar';

    //$sch_idの有無をチェック
    if (isset($sch_id)) {
        //$sch_idに対応する予定をDBからSELECT
        //MySQLに接続
        $link = mysqli_connect($url, $user, $pass, $db);

        //接続状態チェック
        if (mysqli_connect_errno()) {
            die(mysqli_conect_error());
        }

        $select = sprintf('SELECT * FROM cal_schedules WHERE schedule_id = "%s"', $sch_id);
        //var_dump($select);
        //mysqli_queryに配列がかえるかfalseがかえる
        if ($result = mysqli_query($link, $select)) {
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                //var_dump($row);
                $sch_detail['id'] = $sch_id;
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
        //$sch_idがない場合はGETで受け取った日付を入れる
        $sch_detail['id'] = null;
        $sch_detail['start'] = $sch_year.'-'.$sch_month.'-'.$sch_day;
        $sch_detail['end'] = $sch_year.'-'.$sch_month.'-'.$sch_day;
        $sch_detail['title'] = '無題の予定';
        $sch_detail['plan'] = null;
    }
    //削除が押されたとき
} elseif ($_POST['flg'] == 'check' && $_POST['submit'] == '削除') {
    $check = true;
    $_SESSION['sch_id'] = $_POST['sch_id'];
    //$_SESSION['sch_start'] = $_POST['sch_start'];
    //$_SESSION['sch_end'] = $_POST['sch_end'];
    $_SESSION['sch_start'] = $_POST['start_y_m'].'-'.$_POST['start_d'];
    $_SESSION['sch_end'] = $_POST['end_y_m'].'-'.$_POST['end_d'];
    $_SESSION['sch_title'] = $_POST['sch_title'];
    $_SESSION['sch_plan'] = $_POST['sch_plan'];
} elseif ($_POST['flg'] == 'check') {
    //確認フェーズ
    //$error_msg初期化
    $error_msg = array();
    //エラーチェック
    if (strtotime($_POST['sch_start']) > strtotime($_POST['sch_end'])) {
        $error_msg['date'] = '日時を正しく入力してください';
    }
    if ($_POST['sch_title'] == '') {
        $error_msg['title'] = '予定タイトルを入力してください';
    }
    //エラー個数で判断
    if (count($error_msg) == 0) {
        $check = true;
        $_SESSION['sch_id'] = $_POST['sch_id'];
        //$_SESSION['sch_start'] = $_POST['sch_start'];
        //$_SESSION['sch_end'] = $_POST['sch_end'];
        $_SESSION['sch_start'] = $_POST['start_y_m'].'-'.$_POST['start_d'];
        $_SESSION['sch_end'] = $_POST['end_y_m'].'-'.$_POST['end_d'];
        $_SESSION['sch_title'] = $_POST['sch_title'];
        $_SESSION['sch_plan'] = $_POST['sch_plan'];
    } else {
        $check = false;
    }
    $sch_detail['id'] = $_POST['sch_id'];
    //$sch_detail['start'] = $_POST['sch_start'];
    //$sch_detail['end'] = $_POST['sch_end'];
    $sch_detail['start'] = $_POST['start_y_m'].'-'.$_POST['start_d'];
    $sch_detail['end'] = $_POST['end_y_m'].'-'.$_POST['end_d'];
    $sch_detail['title'] = $_POST['sch_title'];
    $sch_detail['plan'] = $_POST['sch_plan'];
} else {
    $check = true;
$_SESSION['sch_id'] = $_POST['sch_id'];
$_SESSION['sch_start'] = $_POST['start_y_m'].'-'.$_POST['start_d'];
$_SESSION['sch_end'] = $_POST['end_y_m'].'-'.$_POST['end_d'];
$_SESSION['sch_title'] = $_POST['sch_title'];
$_SESSION['sch_plan'] = $_POST['sch_plan'];
}
//予定開始年月設定
for ($i = -1; $i <= 1; $i++) { 
    for ($j=1; $j <= 12; $j++) { 
        $sch_y_m[] = date('Y年n月', mktime(0, 0, 0, $j, 1, $sch_year + $i));
    }
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
    <?php if (!$_POST['flg']):?>
        <form action="" method="POST">
    <?php else:?>
        <?php if ($check):?>
            <form action="index2.php" method="POST">
        <?php else :?>
            <form action="" method="POST">
        <?php endif;?>
    <?php endif;?>
    <dl>
        <dt>
            予定開始日
        </dt>
        <dd>
        <?php if ($check):?>
            <?php echo $_SESSION['sch_start'];?>
        <?php else:?>
            <select name="start_y_m">
            <?php foreach ($sch_y_m as $value):?>
                <?php if ($value == $sch_year.'年'.$sch_month.'月'):?>
                    <option value="<?php echo $sch_year.'-'.$sch_month ;?>" selected><?php echo $value;?></option>
                <?php else:?>
                    <option value="<?php echo $sch_year.'-'.$sch_month ;?>"><?php echo $value;?></option>
                <?php endif;?>
            <?php endforeach;?>
            </select>
            <select name="start_d">
                <?php for ($i = 1; $i <= 31; $i++):?>
                    <?php if ($i == $sch_day):?>
                        <option value="<?php echo $sch_day ;?>" selected><?php echo $i;?>日</option>
                    <?php else:?>
                        <option value="<?php echo $sch_day ;?>"><?php echo $i;?>日</option>
                    <?php endif;?>
                <?php endfor;?>
            </select>
<!--             <input type="text" name="sch_start" maxlength="10" value="<?php // echo $sch_detail['start'];?>"> -->
        <?php endif;?>
        </dd>
        <dt>
            予定終了日
        </dt>
        <dd>
        <?php if ($check):?>
            <?php echo $_SESSION['sch_end'];?>
        <?php else:?>
            <select name="end_y_m">
            <?php foreach ($sch_y_m as $value):?>
                <?php if ($value == $sch_year.'年'.$sch_month.'月'):?>
                    <option value="<?php echo $sch_year.'-'.$sch_month ;?>" selected><?php echo $value;?></option>
                <?php else:?>
                    <option value="<?php echo $sch_year.'-'.$sch_month ;?>"><?php echo $value;?></option>
                <?php endif;?>
            <?php endforeach;?>
            </select>
            <select name="end_d">
                <?php for ($i = 1; $i <= 31; $i++):?>
                    <?php if ($i == $sch_day):?>
                        <option value="<?php echo $sch_day ;?>" selected><?php echo $i;?>日</option>
                    <?php else:?>
                        <option value="<?php echo $sch_day ;?>"><?php echo $i;?>日</option>
                    <?php endif;?>
                <?php endfor;?>
            </select>
<!--             <input type="text" name="sch_end" maxlength="10" value="<?php // echo $sch_detail['end'];?>">-->        <?php endif;?>
        <?php echo $error_msg['date'];?>
        </dd>
        <dt>
            予定タイトル
        </dt>
        <dd>
        <?php if ($check):?>
            <?php echo $_SESSION['sch_title'];?>
        <?php else:?>
            <input type="text" name="sch_title" value="<?php echo $sch_detail['title'];?>">
            <?php echo $error_msg['title'];?>
        <?php endif;?>
        </dd>
        <dt>
            内容
        </dt>
        <dd>
        <?php if ($check):?>
            <pre><?php echo $_SESSION['sch_plan'];?></pre>
        <?php else:?>
            <textarea name="sch_plan"><?php echo $sch_detail['plan'];?></textarea> 
            <!-- <input type="textarea" name="sch_plan" value="<?php // echo $sch_detail['plan'];?>"> -->
        <?php endif;?>
        </dd>
    </dl>

<?php if (!$check):?>
        <input type="hidden" name="sch_id" value="<?php echo $sch_detail['id'];?>">
        <input type="hidden" name="flg" value="check">
        <input type="submit" value="確認">
        <input type="submit" name="submit" value="削除">
<?php else:?>
    <!-- 更新登録削除をcheck -->
    <!-- 削除でなくidが指定されている：更新 -->
    <?php if (empty($_POST['submit']) && $_SESSION['sch_id'] != '') :?>
        <input type="hidden" name="sch_id" value="<?php echo $_SESSION['sch_id'] ;?>">
        <input type="hidden" name="command" value="update">
        <input type="submit" value="更新">
    <!-- 削除でなくidが指定されていない：登録 -->
    <?php elseif (empty($_POST['submit']) && $_SESSION['sch_id'] == ''):?>
        <input type="submit" value="登録">
        <input type="hidden" name="command" value="insert">
    <!-- 削除 -->
    <?php elseif ($_POST['submit'] == '削除'):?>
        <input type="hidden" name="sch_id" value="<?php echo $_SESSION['sch_id'] ;?>">
        <input type="hidden" name="command" value="delete">
        <input type="hidden" name="flg" value="del">
        <input type="submit" value="削除">
    <?php endif;?>
<?php endif;?>
</form>

<a href="index2.php"> カレンダーに戻る </a>

<!-- 削除の場合 -->
<!-- <form action="index2.php" method="POST">
    <input type="hidden" name="sch_id" value="<?php // echo $_SESSION['sch_id'] ;?>">
    <input type="hidden" name="command" value="delete">
    <input type="submit" value="削除">
</form> -->
<!-- <form action="" method="POST">
    <input type="hidden" name="sch_id" value="<?php// echo $_SESSION['sch_id'] ;?>">
    <input type="hidden" name="command" value="delete">
    <input type="hidden" name="flg" value="del">
    <input type="submit" value="削除">
</form> -->
</body>
</html>