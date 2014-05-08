<?php
session_cache_limiter(none);
session_start();
// $_SESSION['sch_start'] = $_SESSION['sch_st_y'].'-'.$_SESSION['sch_st_m'].'-'.$_SESSION['sch_st_d'].'-'.$_SESSION['sch_st_h'].'-'.$_SESSION['sch_st_i'];
// $_SESSION['sch_end']   = $_SESSION['sch_ed_y'].'-'.$_SESSION['sch_ed_m'].'-'.$_SESSION['sch_ed_d'].'-'.$_SESSION['sch_ed_h'].'-'.$_SESSION['sch_ed_i'];

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
        <?php echo $_SESSION['schedule']['start_y'].'年'.$_SESSION['schedule']['start_m'].'月'.$_SESSION['schedule']['start_d'].'日  '.$_SESSION['schedule']['start_h'].'時'.$_SESSION['schedule']['start_i'].'分';?>
    </dd>
    <dt>
        予定終了日
    </dt>
    <dd>
        <?php echo $_SESSION['schedule']['end_y'].'年'.$_SESSION['schedule']['end_m'].'月'.$_SESSION['schedule']['end_d'].'日  '.$_SESSION['schedule']['end_h'].'時'.$_SESSION['schedule']['end_i'].'分';?>
    </dd>
    <dt>
        タイトル
    </dt>
    <dd>
        <?php echo h($_SESSION['schedule']['title']);?>
    </dd>
    <dt>
        内容
    </dt>
    <dd>
        <?php echo h($_SESSION['schedule']['plan']);?>
    </dd>
</dl>
<form action="index.php?year_month=<?php echo $_SESSION['schedule']['start_y'].'-'.$_SESSION['schedule']['start_m'];?>" method="POST">
    <input type="submit" name="submit" value="保存">
</form>
<a href="cal_edit.php">戻る</a>
</body>
</html>