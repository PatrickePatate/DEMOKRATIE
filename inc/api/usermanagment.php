<?php
session_start();
include('../config.php');
Header("Content-type: text/json");
if(isset($_SESSION['rank']) AND $_SESSION['rank']>4){
	if(isset($_POST['uid']) AND is_numeric($_POST['uid']) AND isset($_POST['action']) AND $_POST['action'] == "getinfos"){
		$getuserinfos = $bdd->query("SELECT * FROM users WHERE id = ".$_POST['uid']);
		if($getuserinfos->rowCount() == 1){
			$getuserinfos = $getuserinfos->fetch();
			if(!empty($getuserinfos['groups'])){
				$groups =json_decode($getuserinfos['groups'],1);
			}else{
				$groups = array("NULL");
			}
			$jsoncompatiblegroups="";
			foreach ($groups as $key => $value) {
				$jsoncompatiblegroups.=$key.",";
			}
			$jsoncompatiblegroups=substr($jsoncompatiblegroups,0,-1);
			die(json_encode(array("status" => "success","imgpp"=>$getuserinfos['imgpp'],"username"=>$getuserinfos['username'],"email"=>$getuserinfos['email'],"rank"=>$getuserinfos['rank'],"groups"=>$getuserinfos['groups'],"validate"=>$getuserinfos['validate'])));
		}else{
			die(json_encode(array("status"=>"error","message"=>"Aucun utilisateur correspondant !")));
		}
	}else{
		die(json_encode(array("status"=>"error","message"=>"Aucune donnée ou donnée incorecte reçue !")));
	}
}else{
	die(json_encode(array("status"=>"error","message"=>"Vous n'êtes pas connecté !")));
}


?>