<?php
session_start();
include("inc/config.php");
include("inc/funcs.php");

if(!(isset($_SESSION['id']) AND !empty($_SESSION['id']))){
    Header("Location: login.php");
    die("Vous n'êtes pas connecté.");
}
//SPECIFIC TO THIS FILE FUNCTION :
function sendmail($to){
    global $sitename,$idvote,$token,$title,$siteurl;
    $subject = 'Vous avez été ajouté à un nouveau vote !';
    $htmlmail = "assets/mails/newvote.php";
    $messageopen = fopen($htmlmail, "r");
    $message = fread($messageopen, filesize($htmlmail));
    fclose($messageopen);
                            //PARSING
    $message = str_replace("%SITENAME%", $sitename, $message);
    $message = str_replace("%TITLE%", $title, $message);
    $message = str_replace("%VOTELINK%", $siteurl."vote.php?id=$idvote&token=".$token, $message);
    $message = str_replace('%FROMU%', $_SESSION['username'], $message);

    $headers = 'From: '.$sitename.' <noreply@vote.librescommeres.fr>' . "\r\n" .
    'X-Mailer: PHP/' . phpversion()."\r\n" .
    'Content-type : text/html; charset=utf-8';
    mail($to, $subject, $message, $headers);
}

if(isset($_POST['title']) AND isset($_POST['content']) AND isset($_POST['type']) AND isset($_POST['validitydate']) AND !empty($_POST['title']) AND !empty($_POST['content']) AND !empty($_POST['validitydate'])){
    $error = array(); $success = 0;
    $title = htmlspecialchars($_POST['title']);
    $content = $_POST['content'];
    $validitydate = $_POST['validitydate'];
    $pretoken = hash("SHA256",date("m-d-Y i:H:s").uniqid().$_SESSION['username'].'##$§&²²=^');
    if(isset($_POST['secret'])){$secret=1;}else{$secret=0;}
    if($_POST['type'] == "1"){
        // PUBLIQUE
        if($_SESSION['rank'] >=2){
            //rank suffisant
            $token = "PUB_".$pretoken;
            $addvote = $bdd->prepare("INSERT INTO tovote(title,content,fromu,type,secret,date_validity,token) VALUES(?,?,?,?,?,?,?)");
            $addvote->execute(array($title,$content,$_SESSION['id'],$_POST['type'],$secret,$validitydate,$token));
            $idvote = $bdd->lastInsertId();
            if($addvote){
                $success = 1;
                $getallmails = $bdd->query("SELECT email FROM users");
                while($d = $getallmails->fetch()['mail']){
                    sendmail($d);
                }
            }else{
                $error[] = "Erreur lors de la création du vote, veuillez contacter un administrateur.";
            }
                //lors de l'envoi de mail avec token, conditionné l'envoi à une adresse email correct via regex ou filter_var
        }else{
            $error[] = "Vous n'avez pas la permission de créer ce vote.";
        }
    }elseif($_POST['type'] == "0"){
        // PRIVÉ
        if($_SESSION['rank'] >=1){
            //rank suffisant
            if(isset($_POST['privateaddress']) AND !empty($_POST['privateaddress'])){
                $emailaddress = preg_split( "/( |,)/", $_POST['privateaddress']);
                $token = "PRIV_".$pretoken;
                $addvote = $bdd->prepare("INSERT INTO tovote(title,content,fromu,type,whosvoting,secret,date_validity,token) VALUES(?,?,?,?,?,?,?,?)");
                $addvote->execute(array($title,$content,$_SESSION['id'],$_POST['type'],json_encode($emailaddress),$secret,$validitydate,$token));
                $idvote = $bdd->lastInsertId();
                if($addvote){
                    $success = 1;
                    $mailerror = array();
                    foreach ($emailaddress as $d) {
                        // ENVOI DE L'EMAIL
                        if(filter_var($d, FILTER_VALIDATE_EMAIL)) {
                            sendmail($d);
                        }else{
                            if(!($d=" ")){
                                $mailerror[] = $d;
                            }
                        }
                    }

                }else{
                    $error[] = "Erreur lors de la création du vote, veuillez contacter un administrateur.";
                }
                //lors de l'envoi de mail avec token, conditionné l'envoi à une adresse email correct via regex ou filter_var
            }else{
                $error[] = "Aucune adresse e-mail saisies pour les participants au vote.";
            }

        }else{
            $error[] = "Vous n'avez pas la permission de créer ce vote.";
        }
    }elseif($_POST['type'] == "0+"){
        // GROUPS
        if($_SESSION['rank'] >=2){
            //rank suffisant
            if(isset($_POST['groups']) AND !empty($_POST['groups'])){
                $groupsid = json_encode($_POST['groups']);

                $token = "PUB_".$pretoken;
                $addvote = $bdd->prepare("INSERT INTO tovote(title,content,fromu,type,whosvoting,secret,date_validity,token) VALUES(?,?,?,?,?,?,?,?)");
                $addvote->execute(array($title,$content,$_SESSION['id'],$_POST['type'],$groupsid,$secret,$validitydate,$token));
                $idvote = $bdd->lastInsertId();
                if($addvote){
                    $success = 1; 
                    $groupsarr = json_decode($groupsid,1);
                    // BUILDING REGEX SEARCH
                    $search="(\"["; foreach($groupsarr as $d){$search.=$d.",";} $search = substr($search,0,-1)."]\")";

                    $getallmails = $bdd->query("SELECT email FROM users WHERE groups REGEXP $search");
                    while($d = $getallmails->fetch()['email']){
                        sendmail($d);
                    }
                }else{
                    $error[] = "Erreur lors de la création du vote, veuillez contacter un administrateur.";
                }
            }else{
                $error = "Aucun groupe défini !";
            }
        }else{
            $error[] = "Vous n'avez pas la permission de créer ce vote.";
        }
    }else{
        $error[] = "Type de vote incorect.";
    }

}
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?=$sitename;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <!--<meta name="description" content="This is an example dashboard created using build-in elements and components.">-->
    <meta name="msapplication-tap-highlight" content="no">
    <!--
    =========================================================
    * ArchitectUI HTML Theme Dashboard - v1.0.0
    =========================================================
    * Product Page: https://dashboardpack.com
    * Copyright 2019 DashboardPack (https://dashboardpack.com)
    * Licensed under MIT (https://github.com/DashboardPack/architectui-html-theme-free/blob/master/LICENSE)
    =========================================================
    * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<script src="assets/scripts/jquery.min.js"></script>
<script src="https://cdn.tiny.cloud/1/<?=$tiny_api;?>/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<link href="./main.css" rel="stylesheet">
</head>
<body>

    <?php 
    include('inc/menu_haut.php'); 
    ?>
    <div class="app-main">
        <?php
        include('inc/menu_lateral.php');
        ?>

        <div class="app-main__outer">
            <div class="app-main__inner">
                <div class="app-page-title">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div class="page-title-icon">
                                <i class="pe-7s-plug icon-gradient bg-mean-fruit">
                                </i>
                            </div>
                            <div>
                                Créer un vote
                                <div class="page-title-subheading">Choisissez bien l'accessibilité de votre vote ;)</div>
                            </div>
                        </div>
                    </div>
                </div>            
                <div class="row">
                    <div class="col-md-6 col-xl-4">
                        <?php
                        if(isset($success) AND $success==1){
                            ?>
                            <div class="alert alert-success" role="alert">
                                <a href="vote.php?id=<?=$idvote;?>&token=<?=$token;?>">Vote</a> créé avec succès ! <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
                                </div><br /><?php
                            }
                            if(isset($mailerror) AND !empty($mailerror)){
                                ?>
                                <div class="alert alert-warning" role="alert">
                                    Certains mails de participants étaient incorects :<br /><ul><?php foreach ($mailerror as $mail) {
                                        echo "<li>".$mail."</li>";
                                    }?></ul><a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
                                </div><br />
                                <?php
                            }
                            if(isset($error) AND !empty($error)){
                                ?>
                                <div class="alert alert-danger" role="alert">
                                    Une ou plusieurs erreur(s) ont été recontrée :<br /><ul>
                                        <?php 
                                        foreach ($error as $e) {
                                            echo "<li>".$e."</li>";
                                        }
                                        ?></ul><a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
                                    </div><br />
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="mb-3 card">
                                    <div class="card-header-tab card-header-tab-animation card-header">
                                        <div class="card-header-title">
                                            Nouveau vote
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <form role="form" method="POST">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label>Titre du vote :</label>
                                                    <input class="form-control" placeholder="Titre du vote" name="title" type="text" autofocus="">
                                                </div>
                                                <div class="form-group">
                                                    <textarea rows="20" id="content" class="form-control" placeholder="Décrivez votre proposition..." name="content"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>Qui peut voir ce vote ?</label>
                                                    <select onchange="whocansee(this.options[this.selectedIndex].value);" class="form-control" name="type">
                                                        <option value="1">Public</option>
                                                        <option value="0">Privé</option>
                                                        <option value="0+">Certains groupes</option>
                                                    </select>
                                                </div>
                                                <div id="privatemodal" style="display:none;" class="form-group">
                                                    <label>Paramètres avancés d'accessibilité :</label>
                                                    <textarea cols="80" class="form-control" name="privateaddress" placeholder="Entrez les adresses mails des participants en les séparants par une virgule ou un espace."></textarea>
                                                </div>
                                                <div id="groupsmodal" style="display:none;" class="form-group">
                                                    <label>Paramètres avancés d'accessibilité :</label>
                                                    <select multiple name="groups[]" style="width:180px;" class="form-control" id="groupsselector" placeholder="Choissez un ou plusieurs groupes">
                                                        <?php $getgroups = $bdd->query("SELECT * FROM ugroups");
                                                        while($d=$getgroups->fetch()){
                                                            echo "<option value='".$d['id']."'>".$d['name']."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Valide jusqu'au :</label>
                                                    <input type="date" class="form-control" placeholder="Choissez une date" name="validitydate" />
                                                </div>
                                                <hr />
                                                <div class="form-group">
                                                    <div class="custom-checkbox custom-control custom-control-inline">
                                                        <input name="secret" type="checkbox" id="secretbox" class="custom-control-input">
                                                        <label class="custom-control-label" for="secretbox">Vote à bulletin secret ?</label>
                                                    </div>
                                                </div>
                                                <hr />
                                                <button type="submit" class="btn btn-primary">Créer ce vote</a>
                                                </fieldset>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script src="https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.js"></script>
                <script src="assets/scripts/locale/bootstrap-table-fr_FR.js"></script>
                <script>
                    tinymce.init({
                        selector: '#content',
                        plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                        toolbar_mode: 'floating',
                    });

                    $(document).ready(function() {
                        $('#groupsselector').select2();
                    });

                    function whocansee(type){
                        if(type==1){
                            document.getElementById("groupsmodal").style.display = "none";
                            document.getElementById('privatemodal').style.display ="none";
                        }
                        if(type==0){
                            document.getElementById('privatemodal').style.display ="inline-block";
                            document.getElementById("groupsmodal").style.display = "none";
                        }
                        if(type=="0+"){
                            document.getElementById("groupsmodal").style.display = "inline-block";
                            document.getElementById('privatemodal').style.display ="none";
                        }
                    }
                </script>   
                <script type="text/javascript" src="./assets/scripts/main.js"></script></body>
                </html>
