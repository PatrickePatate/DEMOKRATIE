<?php
session_start();
include("inc/config.php");
include("inc/funcs.php");
require("inc/timeline.class.php");

if(!(isset($_SESSION['id']) AND !empty($_SESSION['id']))){
    Header("Location: login.php");
    die("Vous n'êtes pas connecté.");
}
$timeline = new Timeline($_SESSION,$bdd);
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
                                Tableau de bord
                                <div class="page-title-subheading">Vous êtes sur l'accueil de <?=$sitename;?>.</div>
                            </div>
                        </div>
                    </div>
                </div>            
                <!--<div class="row">
                    <div class="col-md-6 col-xl-4">
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
                    <div class="d-xl-none d-lg-block col-md-6 col-xl-4">
                        <div class="card mb-3 widget-content bg-premium-dark">
                            <div class="widget-content-wrapper text-white">
                                <div class="widget-content-left">
                                    <div class="widget-heading">Products Sold</div>
                                    <div class="widget-subheading">Revenue streams</div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-warning"><span>$14M</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>-->
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="mb-3 card">
                            <div class="card-header-tab card-header-tab-animation card-header">
                                <div class="card-header-title">
                                    Dernières propositions pour vous
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="tablepriv" data-toggle="table" data-show-columns="true" data-locale="fr_FR" data-search="true" data-select-item-name="toolbar2" data-pagination="true" data-sort-name="Date" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <th data-field="Titre"  data-sortable="true">Titre</th>
                                            <th data-field="Par" data-sortable="true">Par</th>
                                            <th data-field="Date" data-sortable="true">Date de création</th>
                                            <th data-field="action" data-sortable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $getallpropositions = $bdd->query("SELECT * FROM tovote WHERE validity = 1 AND type = 0");
                                        
                                        foreach($timeline->buildTimeline() as $d){
                                            $datecreated = new DateTime($d['datecreated']);
                                            $id = $d['id']; $token = $d['token'];
                                            echo "<tr><td>".$d['title']."</td><td>".getuserinfo($d['fromu'])['username']."</td><td>".$datecreated->format("d-m-Y H:i")."</td><td><a href='vote.php?id=$id&token=$token'>Voter</a></td>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="mb-3 card">
                            <div class="card-header-tab card-header-tab-animation card-header">
                                <div class="card-header-title">
                                    Dernières propositions publiques
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="tablepub" data-toggle="table" data-show-columns="true" data-locale="fr_FR" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="Date" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <th data-field="Titre"  data-sortable="true">Titre</th>
                                            <th data-field="Par" data-sortable="true">Par</th>
                                            <th data-field="Date" data-sortable="true">Date de création</th>
                                            <th data-field="action" data-sortable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $getallpropositions = $bdd->query("SELECT * FROM tovote WHERE validity = 1 AND type = 1");
                                        while($d = $getallpropositions->fetch()){
                                            $datecreated = new DateTime($d['datecreated']);
                                            $id = $d['id']; $token = $d['token'];
                                            echo "<tr><td>".$d['title']."</td><td>".getuserinfo($d['fromu'])['username']."</td><td>".$datecreated->format("d-m-Y H:i")."</td><td><a href='vote.php?id=$id&token=$token'>Voter</a></td>";
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
    <script type="text/javascript" src="./assets/scripts/main.js"></script></body>
    </html>
