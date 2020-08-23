<?php
session_start();
include('../config.php');
Header("Content-type: text/json");
if(isset($_SESSION['id'])){
	if(isset($_POST['id']) AND is_numeric($_POST['id']) AND isset($_POST['opinion']) AND is_numeric($_POST['opinion']) AND isset($_POST['votename'])){
		$idv = $_POST['id']; $votename=htmlspecialchars($_POST['votename']); $idu =$_SESSION['id']; $opinion = $_POST['opinion'];
		$getvoteinfos = $bdd->query("SELECT id FROM tovote WHERE id = $idv");
		if($getvoteinfos->rowCount() == 1){
			$getalreadyvoted = $bdd->query("SELECT * FROM subvotes WHERE id_vote = $idv AND votename = '$votename' AND id_user = $idu");
			if($getalreadyvoted->rowCount()>0){
				die(json_encode(array('status'=>"error", "message"=>"Vous avez déjà voté pour ce sous-vote.")));
			}else{
				$addvote = $bdd->query("INSERT INTO subvotes(id_vote,votename,id_user,opinion) VALUES($idv,'$votename',$idu,$opinion)");
				die(json_encode(array("status" => "success","message"=>"Votre vote a bien été prit en compte")));
			}
			
		}else{
			die(json_encode(array("status"=>"error","message"=>"Impossible de trouver le vote correspondant !")));
		}
	}else{
		die(json_encode(array("status"=>"error","message"=>"Aucune donnée ou donnée incorecte reçue !")));
	}
}else{
	die(json_encode(array("status"=>"error","message"=>"Vous n'êtes pas connecté !")));
}


?>