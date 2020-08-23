<?php
session_start();
include("inc/config.php");
include("inc/funcs.php");

if(!(isset($_SESSION['id']) AND !empty($_SESSION['id']))){
    header("Location: login.php");
    die("Vous n'êtes pas connecté.");
}

function change_img($img){
    if (!empty($img)){
        global $bdd;
        $img = "assets/images/".$img;
        $id = $_SESSION['id'];
        $updatepp = $bdd->query("UPDATE users SET imgpp='$img' WHERE id = '$id'");
        if($updatepp){
            $_SESSION['imgpp'] = $img;
        }
    } 
}

$success=array();
// UPDATE PROFIL
if(isset($_POST['mail']) AND !empty($_POST['mail'])){
    if(isset($_POST['bio']) AND !empty($_POST['bio'])){
        if($_POST['bio'] !== $_SESSION['bio']){
            //update bio
            $updatebio = $bdd->prepare("UPDATE users SET bio = ? WHERE id = ?");
            $updatebio->execute(array(htmlspecialchars($_POST['bio']),$_SESSION['id']));
            $_SESSION['bio'] = htmlspecialchars($_POST['bio']);
            $success[]="Biographie mise à jour !";
        }
    }
    //MAIL
    if($_POST['mail'] !== $_SESSION['email']){
        //check if mail already exist
        $mailaddr = htmlspecialchars($_POST['mail']);
        $checkmail = $bdd->query("SELECT * FROM users WHERE 'email' = '$mailaddr'");
        if($checkmail->rowCount()<1){
            //temporary update mail
            $id = $_SESSION['id'];

            $token=substr(hash("sha512", date("d-Y-m s:h:m")."AGn5f$6Rr#2CUY".uniqid()),0,45);
            $insertnewtmpmailandtoken = $bdd->query("UPDATE users SET tmpmail = '$mailaddr', tmptoken = '$token' WHERE id = $id");

            // send mail verification
            $to = $_POST['mail'];
            $subject = 'Confirmez votre adresse mail !';
            $htmlmail = "assets/mails/confirmmail.php";
            $messageopen = fopen($htmlmail, "r");
            $message = fread($messageopen, filesize($htmlmail));
            fclose($messageopen);
            //PARSING

            $message = str_replace("%SITENAME%", $sitename, $message);
            $message = str_replace("%CONFIRMLINK%", $siteurl."mailconfirm.php?idu=$id&token=$token", $message);

            $headers = 'From: '.$sitename.' <noreply@vote.librescommeres.fr>' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()."\r\n" .
            'Content-type : text/html; charset=utf-8';

            if(mail($to, $subject, $message, $headers)){
                $mailchange = "Vous aller recevoir un mail sur votre nouvelle adresse e-mail, merci d'aller le confirmer.";
            }else{
                $error = "Une erreur c'est produite lors du changement d'adresse e-mail.";
            }

        }


    }   
}

if(isset($_POST['submitphoto']) AND !empty($_POST['submitphoto'])){

    $erreursphoto = array();

    $target_dir = "assets/images/";
    $uploadOk = 1;
    $random = hash("sha512",uniqid());
    $target_file = $target_dir . basename($_FILES["my_file"]["name"]);

    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    $target_newname = $target_dir.$random.".".$imageFileType;
    $filename_toupdate = $random.".".$imageFileType;
    // Check if image file is a actual image or fake image

    if(isset($_POST["submitphoto"])) {
        $check = getimagesize($_FILES["my_file"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $erreursphoto[] = "Impossible de récupérer les infos de l'image";
            $uploadOk = 0;
        }
    }

    // Check file size
    if ($_FILES["my_file"]["size"] > 5000000) {
        $erreursphoto[] = "Votre image est trop lourde.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
        $erreursphoto[] = "Seul les fichiers JPG, PNG et GIF sont acceptés !";
    var_dump($imageFileType);
    $uploadOk = 0;
}
    // Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    // if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["my_file"]["tmp_name"], $target_newname)) {
        $getuserpp = $bdd->query("SELECT imgpp FROM users WHERE id = ".$_SESSION['id']);
        $getuserpp = $getuserpp->fetch()['imgpp'];
        if(strstr($getuserpp,'default.jpg')){
        }else{
            @unlink($getuserpp);
        }
        change_img($filename_toupdate);
    } else {
        $erreursphoto[] = "Désolé, une erreur c'est produite.";
    }
}
}

if(isset($_POST['actualpass']) AND !empty($_POST['actualpass'])){
    if($_POST['newpass'] == $_POST['newpass2']){
        $idu = $_SESSION['id'];
        $getuserinfos = $bdd->query("SELECT password FROM users WHERE id = $idu");
        $getuserinfos = $getuserinfos->fetch();
        if(password_verify($_POST['actualpass'], $getuserinfos['password'])){
            $pass = password_hash($_POST['newpass'], PASSWORD_DEFAULT);
            $updatepass = $bdd->prepare("UPDATE users SET password = ? WHERE id = $idu");
            $updatepass->execute(array($pass));
            $successpass = "Mot de passe mis à jour !";
        }else{
            $errorpass="Le mot de passe actuel est incorect !";
        }
    }else{
        $errorpass = "Les mots de passe ne correspondent pas !";
    }

}

$user = getuserinfo($_SESSION['id']);
$idu = $user["id"];
$getvotes = $bdd->query("SELECT * FROM votes WHERE id_user = $idu");
$votescount = $getvotes->rowCount();
$votes = $getvotes->fetch();
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
<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.css">
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
                                <i class="pe-7s-home icon-gradient bg-mean-fruit">
                                </i>
                            </div>
                            <div>
                                Profil
                                <div class="page-title-subheading">Changez vos informations personnelles et votre mot de passe.</div>
                            </div>
                        </div>
                    </div>
                </div>            
                <div class="row">
                  <!--  <div class="col-md-6 col-xl-4">
                        <div class="card mb-3 widget-content bg-midnight-bloom">
                            <div class="widget-content-wrapper text-white">
                                <div class="widget-content-left">
                                    <div class="widget-heading">Total Orders</div>
                                    <div class="widget-subheading">Last year expenses</div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-white"><span>1896</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card mb-3 widget-content bg-arielle-smile">
                            <div class="widget-content-wrapper text-white">
                                <div class="widget-content-left">
                                    <div class="widget-heading">Clients</div>
                                    <div class="widget-subheading">Total Clients Profit</div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-white"><span>$ 568</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card mb-3 widget-content bg-grow-early">
                            <div class="widget-content-wrapper text-white">
                                <div class="widget-content-left">
                                    <div class="widget-heading">Followers</div>
                                    <div class="widget-subheading">People Interested</div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-white"><span>46%</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                -->
                <div class="d-xl-none d-lg-block col-md-6 col-xl-4">
                    <div class="card mb-3 widget-content bg-premium-dark">
                        <div class="widget-content-wrapper text-white">
                            <div class="widget-content-left">
                                <div class="widget-heading">Vos votes</div>
                                <div class="widget-subheading">Au total</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-warning"><span><?=$votescount;?></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-6">
                    <div class="mb-3 card">
                        <div class="card-header-tab card-header-tab-animation card-header">
                            <div class="card-header-title">
                                Mes informations
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if(isset($error) AND !empty($error)){
                                echo '<div class="alert alert-warning" role="alert">'.$error.'</div><hr/>'; } 
                                ?>
                                <?php if(isset($success) AND !empty($success)){
                                    echo '<div class="alert alert-success" role="alert"><ul>';
                                    foreach ($success as $value) {
                                        echo '<li>'.$value.'</li>'; } 
                                        echo "</ul></div>";
                                    } ?>
                                    <div>
                                        <form method="POST">
                                            <div class="form-group">
                                                <input placeholder="username" name="username" disabled type="text" class="form-control" value="@<?=$_SESSION['username'];?>">
                                            </div>
                                            <br>
                                            <div class="form-group">
                                                <input placeholder="Adresse e-mail" name="mail" type="mail" class="form-control" value="<?=$_SESSION['email'];?>">
                                            </div><br />
                                            <?php if(isset($mailchange) AND !empty($mailchange)){
                                                echo '<div class="alert bg-success" role="alert">'.$mailchange.'</div><hr/>'; } ?>
                                                <br>
                                                <div class="form-group">
                                                    <textarea placeholder="Biographie" rows="7" name="bio" class="form-control"><?=$_SESSION['bio'];?></textarea>
                                                </div>
                                                <br>
                                            </div>
                                            <br />
                                            <button type="submit" class="btn btn-success">Enregistrer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="mb-3 card">
                                    <div class="card-header-tab card-header-tab-animation card-header">
                                        <div class="card-header-title">
                                            Modifier ma photo de profil
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" enctype="multipart/form-data">
                                            <div >
                                                <div style="position:relative;">
                                                    <img width="200px" height="auto" src="inc/slir/c1x1/<?=$_SESSION['imgpp'];?>" style="align:left;" id="MonImage" alt="Ma photo">
                                                    <input type="file" name="my_file" id="my-file">
                                                </div>
                                                <style>
                                                    #my-file { visibility: hidden; }
                                                </style>
                                            </div>
                                            <p>
                                            </br><input id="validphoto" name="submitphoto" type="submit" value="Mettre à jour" class="btn btn-success">
                                        </p>
                                    </form><br />
                                    <?php
                                    if (isset($uploadOk) AND $uploadOk == 0) {
                                        echo '<div class="alert bg-danger" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                        Votre fichier n\'a pas été uploadé
                                        </div>';
                                    }
                                    if(!empty($erreursphoto)){
                                        foreach ($erreursphoto as $key => $value) {
                                            echo '<div class="alert bg-danger" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                            '.$value.'
                                            </div>';
                                        }
                                    }
                                    ?>
                                    <p>Pour changer votre photo de profil, cliquez sur la précédente...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3 card">
                                    <div class="card-header-tab card-header-tab-animation card-header">
                                        <div class="card-header-title">
                                            Modifier mon mot de passe
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <div class="form-group">
                                                <label>Mot de passe actuel</label>
                                                <input required placeholder="Mot de passe actuel" name="actualpass" type="password" class="form-control">
                                            </div>
                                            <hr />
                                            <div class="form-group">
                                                <label>Entrez votre nouveau mot de passe</label>
                                                <input required placeholder="Nouveau mot de passe" name="newpass" type="password" class="form-control">
                                            </div><br />
                                            <div class="form-group">
                                                <label>Confirmer votre nouveau mot de passe</label>
                                                <input required placeholder="Confirmation du nouveau mot de passe" name="newpass2" type="password" class="form-control">
                                            </div>
                                            <br />
                                            <button type="submit" class="btn btn-success">Mettre à jour</button>
                                        </form>
                                        <br />
                                        <?php
                                        if (isset($successpass) AND !empty($successpass)) {
                                            echo '<div class="alert alert-success" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                            '.$successpass.'
                                            </div>';
                                        }
                                        if(!empty($errorpass)){
                                            echo '<div class="alert alert-danger" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                            '.$errorpass.'
                                            </div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3 card">
                                    <div class="card-header-tab card-header-tab-animation card-header">
                                        <div class="card-header-title">
                                            Mes derniers votes
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table data-toggle="table" data-show-columns="true" data-locale="fr_FR" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="Date de vote" data-sort-order="desc">
                                            <thead>
                                                <tr>
                                                    <th data-field="Titre"  data-sortable="true">Titre</th>
                                                    <th data-field="Par" data-sortable="true">Par</th>
                                                    <th data-field="Date de vote" data-sortable="true">Date de vote</th>
                                                    <th data-field="Je suis" data-sortable="false">Je suis :</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $id = $_SESSION['id'];
                                                $getallpropositions = $bdd->query("SELECT * FROM votes WHERE id_user = $id");
                                                while($d = $getallpropositions->fetch()){
                                                    $datevoted = new DateTime($d['datevoted']);
                                                    $vote = getvoteinfos($d['id_vote']);
                                                    if($d['opinion'] == 1){
                                                        $opinion = "d'accord !";
                                                    }else{
                                                        $opinion = "pas d'accord !";
                                                    }
                                                    echo "<tr><td>".$vote['title']."</td><td>@".getuserinfo($vote['fromu'])['username']."</td><td>".$datevoted->format("d-m-Y H:i")."</td><td><b>$opinion</b></td>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <script src="https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.js"></script>
                <script src="assets/scripts/locale/bootstrap-table-fr_FR.js"></script>
                <script>
                    $(function () {
                        $('#hover, #striped, #condensed').click(function () {
                            var classes = 'table';

                            if ($('#hover').prop('checked')) {
                                classes += ' table-hover';
                            }
                            if ($('#condensed').prop('checked')) {
                                classes += ' table-condensed';
                            }
                            $('#tablepriv').bootstrapTable('destroy')
                            .bootstrapTable({
                                classes: classes,
                                striped: $('#striped').prop('checked'),
                                locale: 'fr-FR'
                            });
                            $('#tablepub').bootstrapTable('destroy')
                            .bootstrapTable({
                                classes: classes,
                                striped: $('#striped').prop('checked'),
                                locale: 'fr-FR'
                            });
                        });
                    });

                    function rowStyle(row, index) {
                        var classes = ['active', 'success', 'info', 'warning', 'danger'];

                        if (index % 2 === 0 && index / 2 < classes.length) {
                            return {
                                classes: classes[index / 2]
                            };
                        }
                        return {};
                    }
                </script>
                <script>
                function readURL(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();

                        reader.onload = function (e) {
                            $('#MonImage').attr('src', e.target.result);
                        }

                        reader.readAsDataURL(input.files[0]);
                    }
                }

                $("#my-file").change(function () {
                    readURL(this);
                });



                $("#validphoto").hide();
                $( '#MonImage' ).click( function() { 
                    $('#my-file').click(); 
                    $("#validphoto").show()
                } );

            </script>
                <script type="text/javascript" src="./assets/scripts/main.js"></script></body>
                </html>
