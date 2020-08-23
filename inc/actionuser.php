<?php
session_start();
include('config.php');

function sendmail($to, $action){
	global $sitename,$siteurl;
	// send mail verification
	if($action=="kill"){
		$subject = 'Votre compte n\'a pas été validé par un administrateur :(';
		$infos = "Votre compte n'a pas été accepté par un administrateur, désole.";
	}elseif($action=="validate"){
		$subject = "Votre compte a été validé par un administrateur !";
		$infos = "Votre compte a été accepté par un administrateur ! Bienvenue !";
	}
	
	$htmlmail = "../assets/mails/mailvalidation.php";
	$messageopen = fopen($htmlmail, "r");
	$message = fread($messageopen, filesize($htmlmail));
	fclose($messageopen);
    //PARSING
	$message = str_replace("%SITENAME%", $sitename, $message);
	$message = str_replace("%LINK%", $siteurl."login.php", $message);
	$message = str_replace("%INFOS%", $infos, $message);

	$headers = 'From: '.$sitename.' <noreply@vote.librescommeres.fr>' . "\r\n" .
	'X-Mailer: PHP/' . phpversion()."\r\n" .
	'Content-type : text/html; charset=utf-8';

	if(mail($to, $subject, $message, $headers)){
		return true;
	}else{
		return false;
	}
}

if(isset($_SESSION['rank']) AND $_SESSION['rank']>4){
	if(isset($_POST['id']) AND isset($_POST['action'])){
		if(is_numeric($_POST['id'])){
			if($_POST['action'] == "validate"){
			// VALIDATION DU COMPTE
				$getusertovalidate = $bdd->prepare("SELECT * FROM users WHERE validate = 0 AND id = ?");
				$getusertovalidate->execute(array($_POST['id']));
				if($getusertovalidate->rowCount() == 1){
					$validate = $bdd->prepare("UPDATE users SET validate = 1 WHERE id = ?");
					$validate->execute(array($_POST['id']));
					// SEND MAIL TO : $getusertovalidate->fetch()['email'];
					if(sendmail($getusertovalidate->fetch()['email'],"validate")){
						die(json_encode(array('status'=>'success','message'=>'Utilisateur validé.')));
					}else{
						die(json_encode(array('status'=>'error','message'=>"L'utilisateur a été validé mais le mail l'informant n'a pas pu être envoyé, veuillez le contacter manuellement.")));
					}
					
				}else{
					die(json_encode(array('status'=>'error','message'=>'Impossible d\'effectuer l\'action sur l\'utilisateur.')));
				}
			}elseif($_POST['action'] == "kill"){
			// SUPPRESSION DU COMPTE
				$getusertodelete = $bdd->prepare("SELECT * FROM users WHERE validate = 0 AND id = ?");
				$getusertodelete->execute(array($_POST['id']));
				if($getusertodelete->rowCount() == 1){
					$validate = $bdd->prepare("DELETE FROM users WHERE id = ? LIMIT 1");
					$validate->execute(array($_POST['id']));
					// SEND MAIL TO : $getusertovalidate->fetch()['email'];
					if(sendmail($getusertovalidate->fetch()['email'],"kill")){
						die(json_encode(array('status'=>'success','message'=>'Utilisateur supprimé.')));
					}else{
						die(json_encode(array('status'=>'error','message'=>"L'utilisateur a été supprimé mais le mail l'informant n'a pas pu être envoyé, veuillez le contacter manuellement.")));
					}
					
				}else{
					die(json_encode(array('status'=>'error','message'=>'Impossible d\'effectuer l\'action sur l\'utilisateur.')));
				}
			}else{
			//ERREUR
				die(json_encode(array('status'=>'error','message'=>'Action incorecte.')));
			}
		}else{
			die(json_encode(array('status'=>'error','message'=>'Identifiant incorect.')));
		}
		
	}
}else{
	//ERREUR PAS ASSEZ DE DROIT OU PAS CONNECTER
	die(json_encode(array('status'=>'error','message'=>'Vous n\'avez pas les droits nécessaire ou vous n\'êtes pas connecté.')));
}


?>