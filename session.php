<?php
session_start();
// $session_token = hash('sha256', session_id());
//$session_token = openssl_random_pseudo_bytes(16);
$session_token = md5(uniqid(rand(), true));
$_SESSION['nk_token'] = $session_token;
echo $session_token;
?>