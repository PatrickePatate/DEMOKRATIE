<?php
$user = "root"; $pass = "";
try {
    $bdd = new PDO('mysql:host=localhost;dbname=vote', $user, $pass);
} catch (PDOException $e) {
    die("Erreur Mysql, veuillez contacter un administrateur.");
}

$tiny_api="gt44dbyveoferxgf1em3t9ww2x4oywvroik3pftdi6dhflp5";
// Vous avez besoin de créer un compte gratuit sur https://tiny.cloud pour récuperer votre clé d'API pour l'éditeur avancé utilisé dans la création des votes.
$sitename="DEMOKRATIE";
$siteurl="https://vote.librescommeres.fr/"; // Ceci est l'url de mon instance, à vous de modifier selon l'url de la votre
?>
