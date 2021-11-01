<?php 
$mysqli = new mysqli("localhost","root","admin123","nexthosting");

if ($mysqli -> connect_errno) {
    echo "Erro ao conectar em nosso banco de dados :(" . $mysqli -> connect_error;
    exit();
}