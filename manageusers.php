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

if(isset($_POST['group_selector']) AND isset($_POST['group_uid'])){
    if(is_numeric($_POST['group_uid'])){
        $getusergroups = $bdd->prepare("SELECT * FROM users WHERE id = ?");
        $getusergroups->execute(array($_POST['group_uid']));
        if($getusergroups->rowCount()==1){
            $getusergroups = $getusergroups->fetch();
            if($getusergroups['groups'] == json_encode($_POST['group_selector'])){

            }else{
                $updategroups = $bdd->prepare("UPDATE users SET groups = ? WHERE id = ?");
                $updategroups->execute(array(json_encode($_POST['group_selector']),$_POST["group_uid"]));
                $success_action = "Groupe(s) de l'utilisateur mis à jour !";
            }
        }else{
            $error_action = "Mauvais identifiant, utilisateur inconnu.";
        }

    }else{
        $error_action = "Une erreur s'est produite, impossible d'identifier l'utilisateur sur lequel l'action doit être effectuée.";
    }
    
}
if(isset($_POST['rank_selector']) AND isset($_POST['rank_uid'])){
    if(is_numeric($_POST['rank_uid']) AND is_numeric($_POST['rank_selector'])){
        $getuserrank = $bdd->prepare("SELECT * FROM users WHERE id = ?");
        $getuserrank->execute(array($_POST['rank_uid']));
        if($getuserrank->rowCount()==1){
            $getuserrank = $getuserrank->fetch();
            if($getuserrank['rank'] == $_POST['rank_selector']){

            }else{
                $updaterank = $bdd->prepare("UPDATE users SET rank = ? WHERE id = ?");
                $updaterank->execute(array($_POST['rank_selector'],$_POST["rank_uid"]));
                $success_action = "Grade de l'utilisateur mis à jour !";
            }
        }else{
            $error_action = "Mauvais identifiant, utilisateur inconnu.";
        }

    }else{
        $error_action = "Une erreur s'est produite, impossible d'identifier l'utilisateur sur lequel l'action doit être effectuée.";
    }
    
}
if(isset($_POST['delete_uid'])){
    if(is_numeric($_POST['delete_uid'])){
        $deleteuser = $bdd->prepare("DELETE FROM users WHERE id = ? LIMIT 1");
        $deleteuser->execute(array($_POST['delete_uid']));
        if($deleteuser){
            $success_action = "Utilisateur supprimé !";
        }else{
            $error_action = "Une erreur s'est produite lors de la suppression.";
        }

    }else{
        $error_action = "Données incorectes.";
    }
    
}
$getallusers = $bdd->query("SELECT * FROM users");
$userscount = $getallusers->rowCount();
$getallusers = $getallusers->fetchAll();
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
                                Gestion d'utilisateurs
                                <div class="page-title-subheading">Validez les nouveaux utilisateurs, et plus encore...</div>
                            </div>
                            
                        </div>
                    </div>
                </div>            
                <div class="row">
                    <div class="col-md-6 col-xl-4">
                        <div class="card mb-3 widget-content">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left">
                                    <div class="widget-heading">Utilisateurs</div>
                                    <div class="widget-subheading">Au total</div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-success"><span><?=$userscount;?></span></div>
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
                                    Nouveaux utilisateurs à valider
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="tovalidatetable" data-toggle="table" data-show-columns="true" data-locale="fr_FR" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="Date de création" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <th data-field="Nom d'utilisateur"  data-sortable="true">Nom d'utilisateur</th>
                                            <th data-field="E-mail" data-sortable="true">E-mail</th>
                                            <th data-field="Date de création" data-sortable="true">Date de création du compte</th>
                                            <th data-field="action" data-sortable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        foreach ($getallusers as $d) {
                                            if($d['validate'] == 0){
                                                $datecreated = new DateTime($d['datecreateaccount']);
                                                echo "<tr id='row".$i."'><td>@".$d['username']."</td><td>".$d['email']."</td><td>".$datecreated->format("d-m-Y H:i")."</td><td>
                                                <span onclick='actionuser(".$d['id'].",\"validate\",".$i.");' style='color:#4dbd4d;font-size:1em;' class='fa-stack fa-2x'>
                                                <i class='fas fa-circle fa-stack-2x'></i><i class='fas fa-check fa-stack-1x fa-inverse'></i>
                                                </span>
                                                <span onclick='actionuser(".$d['id'].",\"kill\",".$i.");' style='color:red;font-size:1em;' class='fa-stack fa-2x'>
                                                <i class='fas fa-circle fa-stack-2x'></i><i class='fas fa-times fa-stack-1x fa-inverse'></i>
                                                </span></td></tr>";

                                                $i++;
                                            }
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
                                    Tous les utilisateurs <small style="margin-left:5px;">(validés)</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <table data-toggle="table" id="alluserstable" data-show-columns="true" data-locale="fr_FR" data-search="true" data-select-item-name="toolbar2" data-pagination="true" data-sort-name="Date de création" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <th data-field="Photo de profil"  data-sortable="false">Photo de profil</th>
                                            <th data-field="Nom d'utilisateur"  data-sortable="true">Nom d'utilisateur</th>
                                            <th data-field="E-mail" data-sortable="true">E-mail</th>
                                            <th data-field="Date de création" data-sortable="true">Date de création du compte</th>
                                            <th data-field="Rang" data-sortable="true">Rang</th>
                                            <th data-field="Groupes" data-sortable="true">Groupes</th>
                                            <th data-field="action" data-sortable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($getallusers as $d) {

                                            if($d['validate'] == 1){
                                                $datecreated = new DateTime($d['datecreateaccount']);
                                                echo "<tr>
                                                <td><img src='inc/slir/4x4/".$d['imgpp']."' style='height:80px;'/></td>
                                                <td>@".$d['username']."</td>
                                                <td>".$d['email']."</td>
                                                <td>".$datecreated->format("d-m-Y H:i")."</td>
                                                <td>".human_readable_rank($d['rank'])."</td>
                                                <td>".human_readable_groups($d['id'])."</td>
                                                <td>".'
                                                <div class="dropdown d-inline-block">
                                                <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="mb-2 mr-2 dropdown-toggle btn btn-dark"><i class="fa fa-cog"></i></button>
                                                <div tabindex="-1" role="menu" aria-hidden="true" style="z-index:50;" class="dropdown-menu">
                                                <button type="button" data-toggle="modal" data-target=".group-modal" tabindex="0" onclick="usertoedit_group('.$d['id'].');" class="dropdown-item">Gérer les groupes</button>
                                                <button type="button" data-toggle="modal" data-target=".rank-modal" onclick="usertoedit_rank('.$d['id'].');" tabindex="0" class="dropdown-item">Gérer le grade</button>
                                                <div tabindex="-1" class="dropdown-divider"></div>
                                                <button type="button" data-toggle="modal" data-target=".deleteModal" onclick="usertoedit_delete('.$d['id'].');" tabindex="0" class="dropdown-item text-danger">Supprimer</button>
                                                </div>
                                                </div>'."</td></tr>";
                                            }
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
    <!-- MODAL GROUPS -->
    <div class="modal fade group-modal" tabindex="-1" role="dialog" aria-labelledby="group-modal" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Gérer les groupes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form role="form" id="form_group" method="POST">
                        <fieldset>
                            <input type="hidden" name="group_uid" id="group_uid"/>
                            <div class="form-group">
                                <label>Utilisateur :</label><br />
                                <div style="display:flex; align-items: top; ">
                                    <img id="group_pp_user" src="assets/images/default.jpg" style="width:80px;margin-right: 5px;" />
                                    <span><b id='username_group'>@USERNAME</b><br /><span id='email_group'>default@domain.com</span></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Modifier les groupes de l'utilisateur :</label>
                                <select multiple name="group_selector[]" id="group_selector" style="width:180px;">
                                    <?php $getgroups = $bdd->query("SELECT * FROM ugroups");
                                    while($d=$getgroups->fetch()){
                                        echo "<option value='".$d['id']."'>".$d['name']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onclick="document.getElementById('form_group').submit();" class="btn btn-primary">Mettre à jour</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL RANK -->
    <div class="modal fade rank-modal" tabindex="-1" role="dialog" aria-labelledby="rank-modal" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Gérer le grade</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form role="form" id="form_rank" method="POST">
                        <fieldset>
                            <input type="hidden" name="rank_uid" id="rank_uid"/>
                            <div class="form-group">
                                <label>Utilisateur :</label><br />
                                <div style="display:flex; align-items: top; ">
                                    <img id="rank_pp_user" src="assets/images/default.jpg" style="width:80px;margin-right: 5px;" />
                                    <span><b id='username_rank'>@USERNAME</b><br /><span id='email_rank'>default@domain.com</span></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Modifier le grade de l'utilisateur :</label>
                                <select name="rank_selector" class="form-control" id="rank_selector" style="width:180px;">
                                    <?php 
                                    // LES RANKS SONT HARDCODÉS, SI NECESSAIRE CELA CHANGERA DANS LE FUTUR
                                    $ranks = array(0,1,2,5);
                                    foreach ($ranks as $r) {
                                        echo "<option value='$r'>".human_readable_rank($r)."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onclick="document.getElementById('form_rank').submit();" class="btn btn-primary">Mettre à jour</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL SUPPRESSION -->
    <div class="modal fade deleteModal" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModal" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Supprimer un utilisateur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="delete_form">
                        <input type="hidden" name="delete_uid" id="delete_uid"/>
                        <div class="form-group">
                            <p>Vous voulez supprimer cet utilisateur :</p>
                            <label>Utilisateur :</label><br />
                            <div style="display:flex; align-items: top; ">
                                <img id="delete_pp_user" src="assets/images/default.jpg" style="width:80px;margin-right: 5px;" />
                                <span><b id='username_delete'>@USERNAME</b><br /><span id='email_delete'>default@domain.com</span></span>
                            </div>
                        </div>
                        <p class="mb-0"><b>Attention ! cette action est irréversible !</b></p>
                        <hr />
                        <div class="form-group">
                            <label>Pour valider votre action, merci de recopier : "Je souhaite supprimer cet utilisateur."</label>
                            <input class="form-control" onchange="if(this.value.toLowerCase() == 'je souhaite supprimer cet utilisateur.'){document.getElementById('deletebutton').removeAttribute('disabled'); }" type="text" placeholder="Je souhaite supprimer cet utilisateur.">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" disabled="true" onclick="document.getElementById('delete_form').submit();" id="deletebutton" class="btn btn-danger">Supprimer l'utilisateur</button>
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
        function usertoedit_group(id){
            $.ajax({
                type: 'POST',
                data: {'uid':id, 'action':'getinfos'},
                url: "<?=$siteurl;?>"+'newlayout/inc/api/usermanagment.php',
                cache: false,
                success: function(response){
                    if(response.status== "error"){
                        alert(response.message);
                    }else{
                        JSON.parse(response.groups).forEach(set_groups);
                        document.getElementById('group_uid').value=id;
                        document.getElementById('username_group').innerHTML = "@"+response.username;
                        document.getElementById('email_group').innerHTML = response.email;
                        document.getElementById('group_pp_user').src = "/inc/slir/4x4/"+response.imgpp;
                    }
                },
                error: function(){
                    alert('Une erreur s\'est produite lors de l\'action sur l\'utilisateur.');
                }
            });
        }
        function usertoedit_rank(id){
            $.ajax({
                type: 'POST',
                data: {'uid':id, 'action':'getinfos'},
                url: "<?=$siteurl;?>"+'newlayout/inc/api/usermanagment.php',
                cache: false,
                success: function(response){
                    if(response.status== "error"){
                        alert(response.message);
                    }else{
                        $("#rank_selector option").each(function()
                        {
                            if(this.value == response.rank){
                                this.setAttribute("selected","true");
                            }
                            
                        });
                        // setrank response.rank
                        document.getElementById('rank_uid').value=id;
                        document.getElementById('username_rank').innerHTML = "@"+response.username;
                        document.getElementById('email_rank').innerHTML = response.email;
                        document.getElementById('rank_pp_user').src = "/inc/slir/4x4/"+response.imgpp;
                    }
                    

                },
                error: function(){
                    alert('Une erreur s\'est produite lors de l\'action sur l\'utilisateur.');
                }
            });
        }
        function usertoedit_delete(id){
            $.ajax({
                type: 'POST',
                data: {'uid':id, 'action':'getinfos'},
                url: "<?=$siteurl;?>"+'newlayout/inc/api/usermanagment.php',
                cache: false,
                success: function(response){
                    if(response.status== "error"){
                        alert(response.message);
                    }else{
                        document.getElementById('delete_uid').value=id;
                        document.getElementById('username_delete').innerHTML = "@"+response.username;
                        document.getElementById('email_delete').innerHTML = response.email;
                        document.getElementById('delete_pp_user').src = "/inc/slir/4x4/"+response.imgpp;
                    }
                    

                },
                error: function(){
                    alert('Une erreur s\'est produite lors de l\'action sur l\'utilisateur.');
                }
            });
        }
        function set_groups(item,index){
            $("#group_selector option").each(function()
            {
                if(this.value == item){
                    this.setAttribute("selected","true");
                }
                
            });
            $(document).ready(function() {
                $('#group_selector').select2();
            });
        }
        function actionuser(id,action,row){
            $.ajax({
                type: 'POST',
                data: {'id':id, action:action},
                url: "<?=$siteurl;?>"+'inc/actionuser.php',
                cache: false,
                success: function(response){
                    response = JSON.parse(response);
                    if(response.status== "error"){
                        alert(response.message);
                    }
                    row = document.getElementById('row'+row);
                    row.parentNode.removeChild(row);
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
