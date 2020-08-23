<?php
function getuserinfo($id){
	global $bdd;
	if(is_numeric($id)){
		$getuserinfos = $bdd->query("SELECT * FROM users WHERE id = $id");
		if($getuserinfos->rowCount()==1){
			return $getuserinfos->fetch();
		}else{
			return "Aucun utilisateur trouvé.";
		}
	}
	
}
function getgroupinfo($id){
	global $bdd;
	if(is_numeric($id)){
		$getgroupinfos = $bdd->query("SELECT * FROM ugroups WHERE id = $id");
		if($getgroupinfos->rowCount()==1){
			return $getgroupinfos->fetch();
		}else{
			return "Aucun groupe trouvé.";
		}
	}
	
}
function getvoteinfos($id){
	global $bdd;
	if(is_numeric($id)){
		$getvoteinfos = $bdd->query("SELECT * FROM tovote WHERE id = $id");
		if($getvoteinfos->rowCount()==1){
			return $getvoteinfos->fetch();
		}else{
			return "Aucun vote trouvé.";
		}
	}
}
function array_search_result($array,$key,$value){
	$result =0;
	if(is_array($array)){
		foreach($array as $k=>$v)
		{
			if($k==$key AND $v==$value){
				$result++;
			}			        
		}
		return $result;
	}else{
		return 'Erreur lors de la vérification de la présence d\'un élément dans le tableau.';
	}
	
	return $result;
}
function human_readable_rank($rank){
	if($rank==0){
		return "Peut voter";
	}elseif($rank==1){
		return "Peut voter et créer des votes privés";
	}elseif($rank==2){
		return "Peut voter et créer tous types de votes";
	}elseif(in_array($rank,array(3,4))){
		return "Indéfini pour le moment";
	}elseif($rank==5){
		return "Administrateur";
	}else{
		return "ERREUR";
	}
}
function human_readable_groups($idu){
	global $bdd;
	$user = getuserinfo($idu);
	if(!empty($user['groups'])){
		$groups = json_decode($user['groups'],1);
		$tmpreturn = "";
		foreach ($groups as $value) {
			$tmpreturn.="<span class='badge badge-primary' style='margin-right:5px;'>".getgroupinfo($value)['name']."</span>";
		}
		return $tmpreturn;
	}
	
}
?>