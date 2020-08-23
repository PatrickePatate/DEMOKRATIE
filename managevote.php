<?php
session_start();
include("inc/config.php");
include("inc/funcs.php");

if(!(isset($_SESSION['id']) AND !empty($_SESSION['id']))){
    if(isset($_GET['id']) AND isset($_GET['token'])){
        header("Location: login.php?uri=".urlencode('vote.php?id='.$_GET['id'].'&token='.$_GET['token']));
    }else{
        header("Location: login.php");
    }
    
    die("Vous n'êtes pas connecté.");
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

                <?php
                if(!(isset($_GET['id']) AND is_numeric($_GET['id']))){
                    // AFFICHER UNE TABLE AVEC NOS VOTES
                    $idu = $_SESSION['id'];
                    $getvotes = $bdd->query("SELECT * FROM tovote WHERE fromu = $idu AND validity = 1");
                    echo '<div class="row">
                    <div class="col-md-12 col-lg-12">
                    <div class="mb-3 card">
                    <div class="card-header-tab card-header-tab-animation card-header">
                    <div class="card-header-title">
                    Vos dernières propositions de vote
                    </div>
                    </div>
                    <div class="card-body">
                    <table id="tablelast" data-toggle="table" data-show-columns="true" data-locale="fr_FR" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="Date" data-sort-order="desc">
                    <thead>
                    <tr>
                    <th data-field="Titre"  data-sortable="true">Titre</th>
                    <th data-field="Par" data-sortable="true">Par</th>
                    <th data-field="Date" data-sortable="true">Date de création</th>
                    <th data-field="action" data-sortable="false">Actions</th>
                    </tr>
                    </thead>
                    <tbody>';
                    while($d = $getvotes->fetch()){
                        $datecreated = new DateTime($d['datecreated']);
                        $id = $d['id'];
                        echo "<tr><td>".$d['title']."</td><td>".getuserinfo($d['fromu'])['username']."</td><td>".$datecreated->format("d-m-Y H:i")."</td><td><a href='managevote.php?id=$id'>Accéder aux infos</a></td>";
                    }
                    echo '</tbody>
                    </table>
                    </div>
                    </div>
                    </div>
                    </div>';
                }else{
                    $id = $_GET["id"]; $idu=$_SESSION['id'];
                    $checkproposition = $bdd->query("SELECT * FROM tovote WHERE id = $id AND fromu = $idu AND validity = 1");
                    if($checkproposition->rowCount() == 0){
                        Header("Location: managevote.php");
                    }else{
                        $getvotes = $bdd->query("SELECT * FROM votes WHERE `id_vote` = $id");
                        $votescount = $getvotes->rowCount();
                        $votesresults = $getvotes->fetchAll();
                        $vote = $checkproposition->fetch();
                        $userinfos=getuserinfo($vote['fromu']);
                        // AFFICHER LES STATS DU VOTE
                        if($vote['type'] == "0"){ // PRIVÉ

                            $badge = "<span class='badge badge-dark' data-toggle='tooltip' data-placement='top' title='' data-original-title='Accessibilité du vote' style='margin-right:5px;'><i class='fas fa-lock'></i> Privé</span>";
                        }elseif($vote['type'] == "1"){ // PUBLIC
                            $badge = "<span class='badge badge-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Accessibilité du vote' style='margin-right:5px;'><i class='fas fa-lock-open'></i> Public</span>";
                        }elseif($vote['type'] == "0+"){ // GROUPES
                            $groups = json_decode($vote['whosvoting'],1);
                            $badge_groupes ="";
                            foreach ($groups as $g) {
                                $badge_groupes .= "<span class='badge badge-info' style='margin-right:5px;'><i class='fas fa-bookmark'></i> ".getgroupinfo($g)['name']."</span>";
                            }
                            $badge = "<span class='badge badge-alternate' data-toggle='tooltip' data-placement='top' title='' data-original-title='Accessibilité du vote' style='margin-right:5px;><i class='fas fa-users'></i> Par groupe(s) :</span>";
                        }

                        ?>
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
                                $ok=0;$notok=0;
                                foreach($votesresults as $v){
                                    if($v['opinion']==1){
                                        $ok++;
                                    }
                                    if($v['opinion']==0){
                                        $notok++;
                                    }                  
                                }
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
                            <?php } 
                            // FORMATING VOTE CONTENT
                                $content = preg_replace('`<script ?(.+)?> ?(.+)?<\/script>`isU', '', $vote['content']); //ANTI-XSS
                                $content = preg_replace('`\[SVOTE\](.+)?\[\/SVOTE\]`isU', '<span style="color:white;border-radius:8px;padding:7px; background-color:#16aaff;"><strong>Sous-vote:</strong> $1</span>', $content, -1, $subvotes);
                            ?>
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
                                        <hr />
                                        <?=$content;?>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <?php if($vote['secret'] == 0){ ?>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3 card">
                                        <div class="card-header-tab card-header-tab-animation card-header">
                                            <div class="card-header-title">
                                             Résultats détaillés
                                         </div>
                                     </div>
                                     <div class="card-body">
                                        <table data-toggle="table" data-show-columns="true" data-locale="fr_FR" data-search="true" data-select-item-name="toolbar1" id="results" data-pagination="true" data-sort-name="Date de vote" data-sort-order="desc">
                                            <thead>
                                                <tr>
                                                    <th data-field="Titre"  data-sortable="true">Titre</th>
                                                    <th data-field="Par" data-sortable="true">Par</th>
                                                    <th data-field="Date de vote" data-sortable="true">Date de vote</th>
                                                    <th data-field="Je suis" data-sortable="false">À voté :</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach($votesresults as $d){
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

                            <?php if($subvotes>0){
                                ?><div class="col-lg-6">
                                    <div class="mb-3 card">
                                        <div class="card-header-tab card-header-tab-animation card-header">
                                            <div class="card-header-title">
                                             Résultats détaillés des sous-votes
                                         </div>
                                     </div>
                                     <div class="card-body">
                                        <table data-toggle="table" data-show-columns="true" data-locale="fr_FR" data-search="true" data-select-item-name="toolbar1" id="results" data-pagination="true" data-sort-name="Date de vote" data-sort-order="desc">
                                            <thead>
                                                <tr>
                                                    <th data-field="Titre"  data-sortable="true">Titre du sous-vote</th>
                                                    <th data-field="Par" data-sortable="true">Par</th>
                                                    <th data-field="Date de vote" data-sortable="true">Date de vote</th>
                                                    <th data-field="Je suis" data-sortable="false">À voté :</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $getsubvotes = $bdd->query("SELECT * FROM subvotes WHERE `id_vote` = $id");
                                                $resultssubvotes = array();
                                                while($d = $getsubvotes->fetch()){
                                                    $datevoted = new DateTime($d['datevoted']);
                                                    
                                                    if($d['opinion'] == 1){
                                                        $opinion = "d'accord !";
                                                        if(isset($resultssubvotes[$d['votename']]['ok'])){
                                                            $resultssubvotes[$d['votename']]['ok']++;
                                                        }else{
                                                            $resultssubvotes[$d['votename']]['ok'] = 1;
                                                        }
                                                    }else{
                                                        $opinion = "pas d'accord !";
                                                        if(isset($resultssubvotes[$d['votename']]['nop'])){
                                                            $resultssubvotes[$d['votename']]['nop']++;
                                                        }else{
                                                            $resultssubvotes[$d['votename']]['nop'] = 1;
                                                        }
                                                    }

                                                    echo "<tr><td>".$d['votename']."</td><td>@".getuserinfo($d['id_user'])['username']."</td><td>".$datevoted->format("d-m-Y H:i")."</td><td><b>$opinion</b></td>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <br /><hr />
                                        <?php
                                        foreach ($resultssubvotes as $key => $v) {
                                            if(isset($v['nop'])){ $nop=$v['nop']; }else{ $nop = 0; }
                                            if(isset($v['ok'])){ $ok=$v['ok']; }else{ $ok = 0; }
                                            $total = $ok+$nop;
                                            $okprop = ($ok/$total)*100;
                                            $nopprop = ($nop/$total)*100;
                                            ?>
                                            <p><strong><?=$key;?> :</strong></p>
                                            <div class="widget-progress-wrapper">
                                                <div class="progress-bar-xs progress">
                                                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="<?=$ok;?>" aria-valuemin="0" aria-valuemax="<?=$total;?>" style="width:<?=$okprop;?>%;"><i class="fa fa-thumbs-up"></i></div>
                                                    <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="<?=$nop;?>" aria-valuemin="<?=$ok;?>" aria-valuemax="<?=$total;?>" style="width:<?=$nopprop;?>%;"><i class="fa fa-thumbs-down"></i></div>
                                                </div>
                                            </div><br />
                                            <?php
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                    }else{
                        ?>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3 card">
                                    <div class="card-body">
                                        <b>Les résultats détaillés ne sont pas accessibles sur un bulletin secret.</b>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                    }
                }
            }
            ?>

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
            $('#tablelast').bootstrapTable('destroy')
            .bootstrapTable({
                classes: classes,
                striped: $('#striped').prop('checked'),
                locale: 'fr-FR'
            });
            $('#results').bootstrapTable('destroy')
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
<script type="text/javascript" src="./assets/scripts/main.js"></script></body>
</html>
