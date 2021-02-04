<?php
session_start();
include("inc/config.php");
include("inc/funcs.php");

if(!(isset($_SESSION['id']) AND !empty($_SESSION['id']))){
    Header("Location: login.php");
    die("Vous n'êtes pas connecté.");
}
if(!(isset($_SESSION['rank']) AND $_SESSION['rank']>4)){
    Header("Location: index.php?err=not_allowed");
    die("Vous n'avez la permission.");
}

if(isset($_POST['group_name'])){
    if(!is_numeric($_POST['group_name'])){
        $name = htmlspecialchars($_POST['group_name']);
        $addgroup = $bdd->prepare("INSERT INTO ugroups(name) VALUES(?)");
        $addgroup->execute(array($name));

        if($addgroup){
            $success_action = "Groupe ajouté avec succès !";
        }else{
            $error_action = "Échec de la création du groupe !";
        }
    }
}


$getallgroups = $bdd->query("SELECT id,name,(SELECT COUNT(*) FROM users WHERE groups LIKE CONCAT('%\"', ugroups.id ,'\"%')) AS nbu FROM ugroups");
$groupscount = $getallgroups->rowCount();
$getallgroups = $getallgroups->fetchAll();
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
                                <i class="pe-7s-users icon-gradient bg-mean-fruit">
                                </i>
                            </div>
                            <div>
                                Gestion des groupes d'utilisateurs
                                <div class="page-title-subheading">Créez ou supprimez les groupes d'utilisateurs...</div>
                            </div>
                            
                        </div>
                    </div>
                </div>            
                <div class="row">
                    <div class="col-md-6 col-xl-4">
                        <div class="card mb-3 widget-content">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left">
                                    <div class="widget-heading">Groupes d'utilisateurs</div>
                                    <div class="widget-subheading">Au total</div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-success"><span><?=$groupscount;?></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-xl-4">
                        <?php
                        if(isset($success_action)){
                            ?>
                            <div class="alert alert-success fade show" role="alert"><?=$success_action;?></div><br /><?php
                        }
                        if(isset($error_action)){
                            ?>
                            <div class="alert alert-danger fade show" role="alert"><?=$error_action;?></div><br />
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
                                    Gestion des groupes
                                </div>
                            </div>
                            <div class="card-body">
                                <button type="button" data-toggle="modal" data-target=".group-add-modal" tabindex="0" class="btn btn-primary">Ajouter un groupe</button>
                                <table id="tovalidatetable" data-toggle="table" data-show-columns="true" data-locale="fr_FR" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="Date de création" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <th data-field="Nom d'utilisateur"  data-sortable="true">Nom du groupe</th>
                                            <th data-field="E-mail" data-sortable="true">Nombre de membres</th>
                                            <th data-field="action" data-sortable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        foreach ($getallgroups as $d) {
                                            echo "<tr id='row".$d['id']."'><td>".$d['name']."</td><td> ".$d['nbu']." </td><td>
                                            <span title='supprimer le groupe' onclick='document.getElementById(\"supprbutn\").setAttribute(\"onclick\",\"group_delete(".$d['id'].");\")' data-toggle='modal' data-target='.group-modal'  style='color:red;font-size:1em;' class='fa-stack fa-2x'>
                                            <i class='fas fa-circle fa-stack-2x'></i><i class='fas fa-trash fa-stack-1x fa-inverse'></i>
                                            </span></td></tr>";
                                            $i++;
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
    <!-- MODAL SUPPR GROUP  -->
    <div class="modal fade group-modal" tabindex="-1" role="dialog" aria-labelledby="group-modal" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Supprimer un groupe</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    Cette action est définitive.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="button" onclick="" id="supprbutn"  data-dismiss="modal" class="btn btn-primary">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL AJOUT GROUP  -->
    <div class="modal fade group-add-modal" tabindex="-1" role="dialog" aria-labelledby="group-add-modal" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Ajouter un groupe</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formadd">
                        <div class="form-group">
                            <label>Nom du groupe :</label><br>
                            <input class="form-control" type="text" name="group_name" placeholder="Nom du groupe à créer" />
                        </div> 
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="button" onclick="document.getElementById('formadd').submit();" class="btn btn-primary">Ajouter</button>
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
                $('#tovalidatetable').bootstrapTable('destroy')
                .bootstrapTable({
                    classes: classes,
                    striped: $('#striped').prop('checked'),
                    locale: 'fr-FR'
                });
                $('#alluserstable').bootstrapTable('destroy')
                .bootstrapTable({
                    classes: classes,
                    striped: $('#striped').prop('checked'),
                    locale: 'fr-FR'
                });
            });
        });
        
        function group_delete(id){
            $.ajax({
                type: 'POST',
                data: {'deletegroupid':id,},
                url: 'inc/api/groupmanagment.php',
                cache: false,
                success: function(response){
                    if(response.status== "error"){
                        alert(response.message);
                    }else{
                        document.getElementById('row'+id).style.display = "none";
                    }
                    
                },
                error: function(){
                    alert('Une erreur s\'est produite lors de l\'action sur l\'utilisateur.');
                }
            });
        }

        

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
