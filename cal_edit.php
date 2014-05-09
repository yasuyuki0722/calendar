<?php
session_cache_limiter(none);
session_start();

//index.phpからのアクセスか、確認フェーズのアクセスか
if (empty($_SESSION['flg'])) {
    $_SESSION['flg'] = 'on';
    date_default_timezone_get('Asia/Tolyo');
    $sch_y = isset($_GET['sch_y']) ? $_GET['sch_y'] : date('Y');
    $sch_m = isset($_GET['sch_m']) ? $_GET['sch_m'] : date('n');
    $sch_d = isset($_GET['sch_d']) ? $_GET['sch_d'] : date('d');

    $sch_id = isset($_GET['sch_id']) ? $_GET['sch_id'] : NULL;

    $url = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'calendar';

    //$sch_idの有無をチェック
    if (isset($sch_id)) {
        //$sch_idに対応する予定をDBからSELECT
        $link = mysqli_connect($url, $user, $pass, $db);
        //接続状態チェック
        if (mysqli_connect_errno()) {
            die(mysqli_conect_error());
        }
        $select = sprintf('SELECT * FROM cal_schedules WHERE schedule_id = "%s"', $sch_id);
        //mysqli_queryに配列がかえるかfalseがかえる
        if ($result = mysqli_query($link, $select)) {
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $_SESSION['schedule_id'] = $sch_id;
                $_SESSION['schedule']['start'] = date('Y-n-j-H-i', strtotime($row['schedule_start']));
                $_SESSION['schedule']['end']   = date('Y-n-j-H-i', strtotime($row['schedule_end']));
                list($_SESSION['schedule']['start_y'], $_SESSION['schedule']['start_m'], $_SESSION['schedule']['start_d'], $_SESSION['schedule']['start_h'], $_SESSION['schedule']['start_i']) = explode('-', $_SESSION['schedule']['start']);
                list($_SESSION['schedule']['end_y'], $_SESSION['schedule']['end_m'], $_SESSION['schedule']['end_d'], $_SESSION['schedule']['end_h'], $_SESSION['schedule']['end_i']) = explode('-', $_SESSION['schedule']['end']);
                $_SESSION['schedule']['title'] = $row['schedule_title'];
                $_SESSION['schedule']['plan']  = $row['schedule_plan'];
                // $sch_start = date('Y-n-j-H-i', strtotime($row['schedule_start']));
                // $sch_end = date('Y-n-j-H-i', strtotime($row['schedule_end']));
                // $_SESSION = array_merge(
                //     $_SESSION, array(
                //         'sch_id' => $sch_id;
                //         'sch_start' => $sch_start;
                //         'sch_end'   => $sch_end;
                // list($_SESSION['sch_st_y'], $_SESSION['sch_st_m'], $_SESSION['sch_st_d'], $_SESSION['sch_st_h'], $_SESSION['sch_st_i']) = explode('-', $_SESSION['sch_start']);
                // list($_SESSION['sch_ed_y'], $_SESSION['sch_ed_m'], $_SESSION['sch_ed_d'], $_SESSION['sch_ed_h'], $_SESSION['sch_ed_i']) = explode('-', $_SESSION['sch_end']);
                // $_SESSION['sch_title'] = $row['schedule_title'];
                // $_SESSION['sch_plan']  = $row['schedule_plan'];
            }
            mysqli_free_result($result);
        } else {
            echo "失敗！";
        }
        mysqli_close($link);
    } else {
        //$sch_idがない場合はGETで受け取った日付を入れる
        // $_SESSION['sch_id']    = null;
        // $_SESSION['sch_st_y']  = $_SESSION['sch_ed_y'] = $sch_y;
        // $_SESSION['sch_st_m']  = $_SESSION['sch_ed_m'] = $sch_m;
        // $_SESSION['sch_st_d']  = $_SESSION['sch_ed_d'] = $sch_d;
        // $_SESSION['sch_st_h']  = 0;
        // $_SESSION['sch_st_i']  = 0;
        // $_SESSION['sch_ed_h']  = 0;
        // $_SESSION['sch_ed_i']  = 0;
        // $_SESSION['sch_title'] = '無題の予定';
        // $_SESSION['sch_plan']  = '内容がないよ';
        // $_SESSION['sch_start'] = $_SESSION['sch_st_y'].'-'.$_SESSION['sch_st_m'].'-'.$_SESSION['sch_st_d'].'-'.$_SESSION['sch_st_h'].'-'.$_SESSION['sch_st_i'];
        // $_SESSION['sch_end']   = $_SESSION['sch_ed_y'].'-'.$_SESSION['sch_ed_m'].'-'.$_SESSION['sch_ed_d'].'-'.$_SESSION['sch_ed_h'].'-'.$_SESSION['sch_ed_i'];
        $_SESSION['schedule_id'] = null;
        $_SESSION['schedule'] = array(
            'start_y' => $sch_y,
            'start_m' => $sch_m,
            'start_d' => $sch_d,
            'start_h' => 0,
            'start_i' => 0,
            'end_y' => $sch_y,
            'end_m' => $sch_m,
            'end_d' => $sch_d,
            'end_h'  => 0,
            'end_i'  => 0,
            'title' => '無題の予定',
            'plan'  => '内容がないよ',
            'start' => $sch_y.'-'.$sch_m.'-'.$sch_d.'-0-0',
            'end'   => $sch_y.'-'.$sch_m.'-'.$sch_d.'-0-0'
            );
var_dump($_SESSION);
    }

//flgがたってるとき
} else {
    //削除
    if ($_POST['submit'] == '削除') {
        $_SESSION['command'] = 'delete';
        header('Location: http://192.168.33.10/calendar/cal_edit_comp.php');
    }

    //
    if (isset($_POST['start_y_m'])) {
        list($start_y, $start_m) = explode('-', $_POST['start_y_m']);
        list($end_y, $end_m) = explode('-', $_POST['end_y_m']);
        $_SESSION['schedule'] = array(
            'start_y'  => $start_y,
            'start_m'  => $start_m,
            'start_d'  => $_POST['start_d'],
            'start_h'  => $_POST['start_h'],
            'start_i'  => $_POST['start_i'],
            'end_y'  => $end_y,
            'end_m'  => $end_m,
            'end_d'  => $_POST['end_d'],
            'end_h'  => $_POST['end_h'],
            'end_i'  => $_POST['end_i'],
            'title' => $_POST['sch_title'],
            'plan'  => $_POST['sch_plan'],
            'start' => $start_y.'-'.$start_m.'-'.$_POST['start_d'].'-'.$_POST['start_h'].'-'.$_POST['start_i'],
            'end'   => $end_y.'-'.$end_m.'-'.$_POST['end_d'].'-'.$_POST['end_h'].'-'.$_POST['end_i']
            );
 var_dump($_SESSION);
 echo "とおと";
    }

    //確認
    if ($_POST['submit'] == '確認') {

        //$error_msg初期化
        $error_msg = array();

        //エラーチェック
         //if (strtotime($_SESSION['sch_st_y'].'-'.$_SESSION['sch_st_m'].'-'.$_SESSION['sch_st_d']) > strtotime($_SESSION['sch_ed_y'].'-'.$_SESSION['sch_ed_m'].'-'.$_SESSION['sch_ed_d'])) {
        if (strtotime($_SESSION['schedule']['start']) > strtotime($_SESSION['schedule']['end'])) {
            $error_msg['date'] = '日時を正しく入力してください';
        }
        if ($_SESSION['schedule']['title'] == '') {
            $error_msg['title'] = '予定タイトルを入力してください';
        }
        if ($_SESSION['schedule']['plan'] == '') {
            $error_msg['plan'] = '予定内容を入力してください';
        }
        //文字列チェックもいるよね
        //文字コードもみるの？？


        //エラーの有無
        if (count($error_msg) == 0) {
            if (isset($_SESSION['schedule_id'])) {
                $_SESSION['command'] = 'update';
            } else {
                $_SESSION['command'] = 'insert';
            }
            header('Location: http://192.168.33.10/calendar/cal_edit_comp.php');
        } 
    }
}

//予定開始年月設定
$k = 0;
$sch_y_m = array();
for ($i = -1; $i <= 1; $i++) { 
    for ($j=1; $j <= 12; $j++) { 
        $sch_y_m[$k]['year']  = date('Y', mktime(0, 0, 0, $j, 1, $_SESSION['schedule']['start_y'] + $i));
        $sch_y_m[$k]['month'] = date('n', mktime(0, 0, 0, $j, 1, $_SESSION['schedule']['start_y'] + $i));
        $k++;
    }
}

function h($text){
    return htmlspecialchars($text);
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
<form action="" method="POST">
    <dl>
        <dt>
            予定開始日
        </dt>
        <dd>
            <select name="start_y_m">
            <?php foreach ($sch_y_m as $value):?>
                <?php if ($value['year'] == $_SESSION['schedule']['start_y'] && $value['month'] == $_SESSION['schedule']['start_m']):?>
                    <option value="<?php echo $value['year'].'-'.$value['month'];?>" selected><?php echo $value['year'].'年'.$value['month'].'月'?></option>
                <?php else:?>
                    <option value="<?php echo  $value['year'].'-'.$value['month'];?>"><?php echo $value['year'].'年'.$value['month'].'月';?></option>
                <?php endif;?>
            <?php endforeach;?>
            </select>
            <select name="start_d">
                <?php for ($i = 1; $i <= 31; $i++):?>
                    <?php if ($i == $_SESSION['schedule']['start_d']):?>
                        <option value="<?php echo $i;?>" selected><?php echo $i;?>日</option>
                    <?php else:?>
                        <option value="<?php echo $i;?>"><?php echo $i;?>日</option>
                    <?php endif;?>
                <?php endfor;?>
            </select>
            <select name="start_h">
                <?php for ($i = 0; $i <= 23; $i++):?>
                    <?php if ($i == $_SESSION['schedule']['start_h']):?>
                        <option value="<?php echo $i;?>" selected><?php echo $i;?>時</option>
                    <?php else:?>
                        <option value="<?php echo $i;?>"><?php echo $i;?>時</option>
                    <?php endif;?>
                <?php endfor;?>
            </select>
            <select name="start_i">
                <?php for ($i = 0; $i <=30; $i = $i + 30):?>
                    <?php if ($i == $_SESSION['schedule']['start_i']):?>
                        <option value="<?php echo $i;?>" selected><?php echo $i;?>分</option>
                    <?php else:?>
                        <option value="<?php echo $i;?>"><?php echo $i;?>分</option>
                    <?php endif;?>
                <?php endfor;?>
            </select>
        </dd>
        <dt>
            予定終了日
        </dt>
        <dd>
            <select name="end_y_m">
            <?php foreach ($sch_y_m as $value):?>
                <?php if ($value['year'] == $_SESSION['schedule']['end_y'] && $value['month'] == $_SESSION['schedule']['end_m']):?>
                    <option value="<?php echo $value['year'].'-'.$value['month'];?>" selected><?php echo $value['year'].'年'.$value['month'].'月';?></option>
                <?php else:?>
                    <option value="<?php echo $value['year'].'-'.$value['month'];?>"><?php echo $value['year'].'年'.$value['month'].'月';?></option>
                <?php endif;?>
            <?php endforeach;?>
            </select>
            <select name="end_d">
                <?php for ($i = 1; $i <= 31; $i++):?>
                    <?php if ($i == $_SESSION['schedule']['end_d']):?>
                        <option value="<?php echo $i;?>" selected><?php echo $i;?>日</option>
                    <?php else:?>
                        <option value="<?php echo $i;?>"><?php echo $i;?>日</option>
                    <?php endif;?>
                <?php endfor;?>
            </select>
            <select name="end_h">
                <?php for ($i = 0; $i <= 23; $i++):?>
                    <?php if ($i == $_SESSION['schedule']['end_h']):?>
                        <option value="<?php echo $i;?>" selected><?php echo $i;?>時</option>
                    <?php else:?>
                        <option value="<?php echo $i;?>"><?php echo $i;?>時</option>
                    <?php endif;?>
                <?php endfor;?>
            </select>
            <select name="end_i">
                <?php for ($i = 0; $i <=30; $i = $i + 30):?>
                    <?php if ($i == $_SESSION['schedule']['end_i']):?>
                        <option value="<?php echo $i;?>" selected><?php echo $i;?>分</option>
                    <?php else:?>
                        <option value="<?php echo $i;?>"><?php echo $i;?>分</option>
                    <?php endif;?>
                <?php endfor;?>
            </select>
        <?php echo $error_msg['date'];?>
        </dd>
        <dt>
            予定タイトル
        </dt>
        <dd>
            <input type="text" name="sch_title" value="<?php echo h($_SESSION['schedule']['title']);?>">
            <?php echo $error_msg['title'];?>
        </dd>
        <dt>
            内容
        </dt>
        <dd>
            <textarea name="sch_plan"><?php echo h($_SESSION['schedule']['plan']);?></textarea> 
            <?php echo $error_msg['plan'];?>
        </dd>
    </dl>
    <input type="submit" name="submit" value="確認">
    <input type="submit" name="submit" value="削除">
</form>

<a href="index.php?year_month=<?php echo $_SESSION['schedule']['start_y'].'-'.$_SESSION['schedule']['start_m'];?>"> カレンダーに戻る </a>

</body>
</html>