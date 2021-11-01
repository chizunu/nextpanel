<?php
session_start();
error_reporting(0);
ini_set('display_errors', 0);

$address = $_SESSION['address'];
$user = 'root';
$password = 'uz2kzlb0jk98@';
$port = $_SESSION['port'];
$dir = $_SESSION['dir'];
$email = $_SESSION['email'];

if($_GET['q'] == "") {
    header("Location: /panel/");
    exit;
} else {
    $q = $_GET['q'];
}

if($q == "startweb"){
    include("db.php");
    $_SESSION['startedweb'] = true;
    $mysqli -> query("UPDATE users SET startedweb = '1' WHERE email = '{$email}';");
    set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
    include('Net/SSH2.php');
    $ssh = new Net_SSH2($address);
    if (!$ssh->login($user, $password)) {
        exit('Erro ao conectar em nossos servidores :(');
    }
    $ssh->exec("cd $dir && php -S $address:$port  > /dev/null 2>&1 &");
    header("Location: /panel/");
} else if($q == "stopweb"){
    include("db.php");
    $_SESSION['startedweb'] = false;
    $mysqli -> query("UPDATE users SET startedweb = '0' WHERE email = '{$email}';");
    set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
    include('Net/SSH2.php');

    $ssh = new Net_SSH2($address);
    if (!$ssh->login($user, $password)) {
        exit('Erro ao conectar em nossos servidores :(');
    }
    $ssh->exec("fuser -k $port/tcp");
    header("Location: /panel/");
} else if($q == "refresh"){
    include("db.php");
    set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
    include('Net/SSH2.php');

    $ssh = new Net_SSH2($address);
    if (!$ssh->login($user, $password)) {
        exit('Erro ao conectar em nossos servidores :(');
    }
    $files = $ssh->exec("cd $dir && ls -a");
    $mysqli -> query("UPDATE users SET files = '{$files}' WHERE email = '{$email}';");
    $_SESSION['files'] = $files;
    header("Location: /panel/");
} else if($q == "delete"){
    $q2 = $_GET['q2'];
    set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
    include('Net/SSH2.php');

    $ssh = new Net_SSH2($address);
    if (!$ssh->login($user, $password)) {
        exit('Erro ao conectar em nossos servidores :(');
    }
    $ssh->exec("cd $dir && rm $q2");
    header("Location: /panelutil/ssh.php?q=refresh");
}



?>