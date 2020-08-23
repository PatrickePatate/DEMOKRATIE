<?php
session_start();
include("inc/config.php");
include("inc/funcs.php");

if(!(isset($_GET['id']) AND is_numeric($_GET['id']))){
    Header("Location: index.php?err=no_id");
    die("Données manquantes.");
}

if(!(isset($_SESSION['id']) AND !empty($_SESSION['id']))){
    if(isset($_GET['id']) AND isset($_GET['token'])){
        header("Location: login.php?uri=".urlencode('vote.php?id='.$_GET['id'].'&token='.$_GET['token']));
    }else{
        header("Location: login.php");
    }
    
    die("Vous n'êtes pas connecté.");
}

$id = $_GET["id"];
$checkproposition = $bdd->query("SELECT * FROM tovote WHERE id = $id AND validity = 1");
if($checkproposition->rowCount() == 0){
    Header("Location: index.php?err=false_id");
}else{
    $vote = $checkproposition->fetch();
}
if($vote['type'] == "0"){ // PRIVÉ
    if(!isset($_GET['token'])){
        Header("Location: index.php?err=no_token");
    }
    if($_GET['token'] !== $vote['token']){
        Header("Location: index.php?err=invalid_token");
    }
    $participants = json_decode($vote['whosvoting'],1);
    if(!in_array($_SESSION['email'], $participants)){
        Header("Location: index.php?err=not_allowed_to_vote");
    }
    $badge = "<span class='badge badge-dark' data-toggle='tooltip' data-placement='top' title='' data-original-title='Accessibilité du vote' style='margin-right:5px;'><i class='fas fa-lock'></i> Privé</span>";
}elseif($vote['type'] == "1"){ // PUBLIC
    $badge = "<span class='badge badge-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Accessibilité du vote' style='margin-right:5px;'><i class='fas fa-lock-open'></i> Public</span>";
}elseif($vote['type'] == "0+"){ // GROUPES
    $groups = json_decode($vote['whosvoting'],1);
    $mygroups = json_decode($_SESSION['groups'],1);
    $match = 0;
    $badge_groupes ="";
    foreach ($groups as $g) {
        if(in_array($g, $mygroups)){
            $match = 1;
        }
        $badge_groupes .= "<span class='badge badge-info' style='margin-right:5px;'><i class='fas fa-bookmark'></i> ".getgroupinfo($g)['name']."</span>";
    }
    if($match!==1){
        Header("Location: index.php?err=group_limited");
    }
    $badge = "<span class='badge badge-alternate' data-toggle='tooltip' data-placement='top' title='' data-original-title='Accessibilité du vote' style='margin-right:5px;><i class='fas fa-users'></i> Par groupe(s) :</span>";
}

$getvotes = $bdd->query("SELECT * FROM votes WHERE `id_vote` = $id");
$votescount = $getvotes->rowCount();
$getvotes = $getvotes->fetchAll();
$ivote=false;
$ok=0;$notok=0;
foreach($getvotes as $v){
    if($v['opinion']==1){
        $ok++;
    }
    if($v['opinion']==0){
        $notok++;
    }                  
}

// ADD VOTES
if(isset($_POST['vote']) AND !empty($_POST['vote'])){
    $opinion = $_POST['vote'];
    if($votescount!==0){
        foreach($getvotes as $v){
            if($v['id_user']==$_SESSION['id']){
                $voted=1;
            }                
        }
        if(isset($voted) AND $voted ==1){
            $error = "Impossible de voter deux fois !";
        }else{
            if($opinion == "yes"){
                $addvote = $bdd->prepare('INSERT INTO votes(id_vote,id_user,opinion) VALUES(?,?,?)');
                $addvote->execute(array($vote['id'], $_SESSION['id'], 1));
                $ok++;
            }elseif($opinion == "no"){
                $addvote = $bdd->prepare('INSERT INTO votes(id_vote,id_user,opinion) VALUES(?,?,?)');
                $addvote->execute(array($vote['id'], $_SESSION['id'], 0));
                $notok++;
            }
            $votescount++;
            $voted=true;
        }
    }else{
        if($opinion == "yes"){
            $addvote = $bdd->prepare('INSERT INTO votes(id_vote,id_user,opinion) VALUES(?,?,?)');
            $addvote->execute(array($vote['id'], $_SESSION['id'], 1));
            $ok++;
        }elseif($opinion == "no"){
            $addvote = $bdd->prepare('INSERT INTO votes(id_vote,id_user,opinion) VALUES(?,?,?)');
            $addvote->execute(array($vote['id'], $_SESSION['id'], 0));
            $notok++;
        }
        $votescount++;
        $voted=true;
    }
    
    
}


$id = $vote['id'];

$content = preg_replace('`\[SVOTE\](.+)?\[\/SVOTE\]`isU', '<div class="dropdown d-inline">
                                            <button type="button" id="$1" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="mb-2 mr-2 dropdown-toggle btn btn-primary">$1</button>
                                            <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 33px, 0px);">
                                                <button onclick=\'subvote("$1",1);\' type="button" tabindex="0" class="dropdown-item"><i style="margin-right:5px;color: #3ac47d;" class="fa fa-thumbs-up"></i> Je suis d\'accord</button>
                                                <button type="button" onclick=\'subvote("$1",0);\' tabindex="0" class="dropdown-item"><i style="margin-right:5px;color:#d92550;" class="fa fa-thumbs-down"></i> Je ne suis pas d\'accord</button>
                                            </div>
                                        </div>', $vote['content'],-1,$subvotes); 
$content = preg_replace('`<script ?(.+)?> ?(.+)?<\/script>`isU', '', $content); //ANTI-XSS

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
<link href="./main.css" rel="stylesheet">
</head>

<body>

    <?php 
    include('inc/menu_haut.php'); 
    ?>
    <div class="app-main">
        <?php
        include('inc/menu_lateral.php');
        $userinfos=getuserinfo($vote['fromu']);
        ?>

        <div class="app-main__outer">
            <div class="app-main__inner">
                <div class="app-page-title">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div class="page-title-icon">
                                <i class="pe-7s-way icon-gradient bg-mean-fruit">
                                </i>
                            </div>
                            <div>
                                Voter
                                <div class="page-title-subheading">Donner votre avis sur cette proposition.</div>
                            </div>
                            
                        </div>
                    </div>
                </div>            
                <div class="row">
                    <div class="col-md-6 col-xl-4">
                        <div class="card mb-3 widget-content">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left">
                                    <div class="widget-heading">Votes</div>
                                    <div class="widget-subheading">Au total</div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-success"><span><?=$votescount;?></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if($votescount>0){ 
                        $okprop = ($ok/$votescount)*100;
                        $notokprop = ($notok/$votescount)*100;
                        ?>
                        <div class="col-lg-6 col-xl-4">
                            <div class="card mb-3 widget-content">
                                <div class="widget-content-outer">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">État actuel du vote</div>
                                            <div class="widget-subheading">Les résultats ne sont pas définitifs...</div>
                                        </div>
                                    </div>
                                    <div class="widget-progress-wrapper">
                                        <div class="progress-bar-xs progress">
                                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="<?=$ok;?>" aria-valuemin="0" aria-valuemax="<?=$votescount;?>" style="width:<?=$okprop;?>%;"><i class="fa fa-thumbs-up"></i></div>
                                            <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="<?=$notok;?>" aria-valuemin="<?=$ok;?>" aria-valuemax="<?=$votescount;?>" style="width:<?=$notokprop;?>%;"><i class="fa fa-thumbs-down"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="mb-3 card">
                            <div class="card-header-tab card-header-tab-animation card-header">
                                <div class="card-header-title">
                                    <b style="padding-right: 5px;"><?=$vote['title'];?> </b> <?=$badge;?><?php if($vote['type'] == "0+"){ echo $badge_groupes; } if($vote['secret']==1){echo "<span class='badge badge-secondary'><i class='fa fa-key'></i> Vote à bulletin secret</span>"; }?>
                                </div>
                            </div>
                            <div class="card-body">
                                <p>Soumis au vote par :</p>
                                <div style="display: flex; flex-wrap: wrap; flex-direction: row; justify-content: flex-start;align-items: flex-start;">
                                    <img src="<?=$userinfos['imgpp'];?>" style="margin-right: 10px;height: 80px; width:auto;"/>
                                    <div>
                                        <b style="font-size: 1.3em;">@<?=$userinfos['username'];?></b><br />
                                        <small><?=$userinfos['email'];?></small>
                                    </div>
                                </div>
                                <?php if($subvotes>0){ echo "<hr /><p><i class='fa fa-exclamation-triangle'></i> Ce vote contient des sous-votes.";} ?>
                                <hr />
                                <?=$content;?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="mb-3 card">
                            <div class="card-body">
                                <?php
                                if($votescount!==0){
                                    foreach($getvotes as $v){
                                        if($v['id_user']==$_SESSION['id']){
                                            $voted=1;
                                        }                
                                    }
                                    if(isset($voted) AND $voted ==1){
                                        ?>
                                        <b>J'ai déjà voté !</b>
                                        <?php
                                    }else{
                                        ?>
                                        <b>Je suis :</b><br />
                                        <form style="display:inline;" method="POST">
                                            <input type="hidden" name="vote" value="yes" />
                                            <button class="btn btn-success"><i class="fa fa-thumbs-up"></i> D'accord !</button>
                                        </form>
                                        <form style="display:inline;" method="POST">
                                            <input type="hidden" name="vote" value="no" />
                                            <button class="btn btn-danger"><i class="fa fa-thumbs-down"></i> Pas d'accord !</button>
                                        </form>
                                        <?php
                                    }
                                }else{
                                    ?>
                                    <b>Je suis :</b><br />
                                    <form style="display:inline;" method="POST">
                                        <input type="hidden" name="vote" value="yes" />
                                        <button class="btn btn-success"><i class="fa fa-thumbs-up"></i> D'accord !</button>
                                    </form>
                                    <form style="display:inline;" method="POST">
                                        <input type="hidden" name="vote" value="no" />
                                        <button class="btn btn-danger"><i class="fa fa-thumbs-down"></i> Pas d'accord !</button>
                                    </form>
                                <?php } ?>
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
        function subvote(votename,vote){
            $.ajax({
                type: 'POST',
                data: {'id':<?=$_GET['id'];?>, 'votename':votename, 'opinion':vote},
                url: "<?=$siteurl;?>"+'newlayout/inc/api/subvotes.php',
                cache: false,
                success: function(response){
                    if(response.status== "error"){
                        alert(response.message);
                    }else{
                        document.getElementById(votename).setAttribute("disabled","true");
                        document.getElementById(votename).style.backgroundColor = 'black';
                    }
                },
                error: function(){
                    alert('Une erreur s\'est produite lors du vote.');
                }
            });
        }
    </script>
    <script type="text/javascript" src="./assets/scripts/main.js"></script></body>
    </html>
