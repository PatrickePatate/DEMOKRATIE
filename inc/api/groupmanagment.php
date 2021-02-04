<?php
include('../config.php');
if(isset($_POST['deletegroupid'])){
    if(is_numeric($_POST['deletegroupid'])){
        $id = $_POST['deletegroupid'];
        $getusersofthegroup = $bdd->query("SELECT * FROM users WHERE groups REGEXP '\"$id\"' ");
        foreach ($getusersofthegroup->fetchALl() as $d) {
            // Je sais que c'est du code de merde, mais je n'ai pas d'autre idée pour le moment, le problème vient de la façon dont j'ai conçu le concept des groupes dans ma base et mon code...
            // Evitez de supprimer un groupe de plusieurs milliers d'utilisateurs ahah
            $groups = json_decode($d['groups'],1);
            if(count($groups) == 1){
                $groups="";
            }else{
                if(in_array($id,$groups)){
                    $key = array_search($id,$groups);
                    unset($groups[$key]);
                    $groups = json_encode($groups);
                }
            }
            $update_user = $bdd->prepare("UPDATE users SET groups = ? WHERE id = ?");
            $update_user->execute(array($groups,$d['id']));
            if($update_user){
                $msg = "Groupe enlevés aux utilisateurs.";
                $status = "ok";
            }else{
                $msg = "Échec de la désasossiation des utilisateurs au groupe.";
                $status = "error";
            }
        }
        
        $deletegroup = $bdd->query("DELETE FROM ugroups WHERE id = $id");
        
    }else{
        $msg = 'Identifiant de groupe incorrect.';
        $status = "error";
    }
    echo json_encode(array('status'=>$status,'message'=>$msg));
}

?>