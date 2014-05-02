<?php
session_start();
$_SESSION['sch_start'] = $_SESSION['sch_st_y'].'-'.$_SESSION['sch_st_m'].'-'.$_SESSION['sch_st_d'];
$_SESSION['sch_end']   = $_SESSION['sch_ed_y'].'-'.$_SESSION['sch_ed_m'].'-'.$_SESSION['sch_ed_d'];

function h($text){
    return htmlspecialchars($text);
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>calendar</title>
</head>
<body>
<?php if ($_SESSION['command'] == 'delete'):?>
    <h1>削除！</h1>
<?php elseif ($_SESSION['command'] == 'insert') :?>
    <h1>新規登録確認！</h1>
<?php elseif ($_SESSION['command'] == 'update'):?>
    <h1>更新確認</h1>
<?php endif;?>

<dl>
    <dt>
        予定開始日
    </dt>
    <dd>
        <?php echo $_SESSION['sch_st_y'].'年'.$_SESSION['sch_st_m'].'月'.$_SESSION['sch_st_d'].'日';?>
    </dd>
    <dt>
        予定終了日
    </dt>
    <dd>
        <?php echo $_SESSION['sch_ed_y'].'年'.$_SESSION['sch_ed_m'].'月'.$_SESSION['sch_ed_d'].'日';?>
    </dd>
    <dt>
        タイトル
    </dt>
    <dd>
        <?php echo h($_SESSION['sch_title']);?>
    </dd>
    <dt>
        内容
    </dt>
    <dd>
        <?php echo h($_SESSION['sch_plan']);?>
    </dd>
</dl>
<a href="index2.php">保存する</a>
<a href="cal_edit.php">戻る</a>
</body>
</html>