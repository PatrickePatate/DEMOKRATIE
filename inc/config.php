<?php
$user = "DBUSER"; $pass = "DBPASS";
try {
    $bdd = new PDO('mysql:host=HOST;dbname=DBNAME', $user, $pass);
} catch (PDOException $e) {
    die("Erreur Mysql, veuillez contacter un administrateur.");
}

$tiny_api="XXXXXXXXXXXXXX";
// Vous avez besoin de créer un compte gratuit sur https://tiny.cloud pour récuperer votre clé d'API pour l'éditeur avancé utilisé dans la création des votes.
$sitename="DEMOKRATIE";
$siteurl="https://vote.librescommeres.fr/"; // Ceci est l'url de mon instance, à vous de modifier selon l'url de la votre
?>
