<?php
$user = "DBUSER"; $pass = "DBPASS";
try {
    $bdd = new PDO('mysql:host=HOST;dbname=DBNAME', $user, $pass);
} catch (PDOException $e) {
    die("Erreur Mysql, veuillez contacter un administrateur.");
}

$sitename="DEMOKRATIE";
$siteurl="https://vote.librescommeres.fr/"; // Ceci est l'url de mon instance, à vous de modifier selon l'url de la votre
?>
