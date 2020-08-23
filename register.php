<?php
session_start();
include("inc/config.php");

if(isset($_POST['email']) AND isset($_POST['password1']) AND isset($_POST['password2']) AND isset($_POST['username']) AND !empty($_POST['email']) AND !empty($_POST['password1']) AND !empty($_POST['password2']) AND !empty($_POST['username'])) {
	$email = htmlspecialchars($_POST['email']);
	$getmatchinguser = $bdd->query("SELECT * FROM users WHERE `email` = '$email'");
	if($getmatchinguser->rowCount() == 0){
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$username = $_POST['username'];
			$getmatchinguserbyusername = $bdd->query("SELECT * FROM users WHERE `username` = '$username'");
			if($getmatchinguserbyusername->rowCount() == 0){
				if(preg_match('%^([a-zA-Z\_0-9]+)$%isU', $_POST['username'])){
					if($_POST['password1'] == $_POST['password2']){
						$adduser = $bdd->prepare("INSERT INTO users (email,password,username) VALUES(?,?,?)");
						$adduser->execute(array($email, password_hash($_POST['password1'], PASSWORD_DEFAULT), $_POST['username']));
						if($adduser){
							$success = "Compte créé ! Un administrateur doit désormais valider votre compte.";
						}else{
							var_dump($bdd->errorInfo());
						}
					}else{
						$error = "Les mots de passe ne correspondent pas.";
					}
				}else{
					$error = "Nom d'utilisateur invalide, vérifiez qu'il comporte uniquement des lettres, chiffres et le caractère \"_\".";
				}
			}else{
				$error = "Ce pseudo est déjà utilisé :(";
			}
		}
		else {
			$error = "Adresse email incorecte.";
		}
		
	}else{
		$error = "Cette adresse email est déjà utilisée.";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=$sitename;?> - Inscription</title>

	<link href="main.css" rel="stylesheet">

<!--[if lt IE 9]>
<script src="js/html5shiv.js"></script>
<script src="js/respond.min.js"></script>
<![endif]-->

</head>
<body>
	<div class="row d-flex justify-content-center" style="margin-top:5%;">
		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
			<?php
			if(isset($error) AND !empty($error)){
				?>
				<div class="alert alert-warning" role="alert">
					<svg class="glyph stroked flag"><use xlink:href="#stroked-flag"></use></svg> <?=$error;?> <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
				</div>
				<br />
			<?php } ?>
			<?php
			if(isset($success) AND !empty($success)){
				?>
				<div class="alert alert-success" role="alert">
					<svg class="glyph stroked flag"><use xlink:href="#stroked-flag"></use></svg> <?=$success;?> <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
				</div>
				<br />
			<?php } ?>
			<div class="card-login mb-3 card">
				<div class="card-header-tab card-header-tab-animation card-header">
					<div class="card-header-title">
						Inscription
					</div>
				</div>
				<div class="card-body">
					<form role="form" method="POST">
						<b>Attention :</b> votre compte devra être validé par un administrateur avant d'être fonctionnel !
						<br /><br />
						<fieldset>
							<div class="form-group">
								<label>Nom d'utilisateur</label>
								<input class="form-control" placeholder="Nom d'utilisateur" value="<?php if(isset($_POST['username'])){echo $_POST['username'];}?>" name="username" type="text" autofocus="">
							</div>
							<div class="form-group">
								<label>Adresse e-mail</label>
								<input class="form-control" placeholder="Adresse e-mail" name="email" value="<?php if(isset($_POST['email'])){echo $_POST['email'];}?>" type="email" autofocus="">
							</div>
							<div class="form-group">
								<label>Mot de passe</label>
								<input class="form-control" placeholder="Mot de passe" name="password1" type="password" value="">
							</div>
							<div class="form-group">
								<label>Confirmez votre mot de passe</label>
								<input class="form-control" placeholder="Confirmer le mot de passe" name="password2" type="password" value="">
							</div>
							<button type="submit" class="btn btn-primary">Créer mon compte !</a>
							</fieldset>
						</form>
						<br />
						<small><a href="login.php">J'ai déjà un compte</a></small>
					</div>
				</div>
			</div><!-- /.col-->
		</div><!-- /.row -->	



		<script src="js/jquery-1.11.1.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/chart.min.js"></script>
		<script src="js/chart-data.js"></script>
		<script src="js/easypiechart.js"></script>
		<script src="js/easypiechart-data.js"></script>
		<script src="js/bootstrap-datepicker.js"></script>
		<script>
			!function ($) {
				$(document).on("click","ul.nav li.parent > a > span.icon", function(){		  
					$(this).find('em:first').toggleClass("glyphicon-minus");	  
				}); 
				$(".sidebar span.icon").find('em:first').addClass("glyphicon-plus");
			}(window.jQuery);

			$(window).on('resize', function () {
				if ($(window).width() > 768) $('#sidebar-collapse').collapse('show')
			})
			$(window).on('resize', function () {
				if ($(window).width() <= 767) $('#sidebar-collapse').collapse('hide')
			})
	</script>	
</body>

</html>
