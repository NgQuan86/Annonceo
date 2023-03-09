<?php

$pdo = new PDO('mysql:host=localhost;dbname=annonceo', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'));

session_start();

define('RACINE_SITE', $_SERVER['DOCUMENT_ROOT'] .'/annonceo/' );


// URL de projet en local ->le changement avant mettre en ligne (hébergement) 
define('URL', 'http://localhost/annonceo/');


$erreur = "";
$erreur_index = "";
$validate = "";
$validate_index = "";
$content = "";



foreach($_POST as $key => $value){
    $_POST[$key] = htmlspecialchars(trim($value));
}
foreach($_GET as $key => $value){
    $_GET[$key] = htmlspecialchars(trim($value));
}
    


require_once('fonction.php');

?>