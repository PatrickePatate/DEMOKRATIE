<?php
session_start();
include("inc/config.php");

if(isset($_POST['email']) AND isset($_POST['password']) AND !empty($_POST['email']) AND !empty($_POST['password'])) {
	$email = htmlspecialchars($_POST['email']);
	$getmatchinguser = $bdd->query("SELECT * FROM users WHERE `email` = '$email'");
	if($getmatchinguser->rowCount() == 1){
		$user = $getmatchinguser->fetch();
		if($user['validate'] == 1){
			if(password_verify($_POST['password'], $user['password'])){
				$_SESSION['id'] = $user['id'];
				$_SESSION['email'] = $user['email'];
				$_SESSION['rank'] = $user['rank'];
				$_SESSION['bio'] = $user['bio'];
				$_SESSION['username'] = $user['username'];
				$_SESSION['imgpp'] = $user['imgpp'];
				$_SESSION['groups'] = $user['groups'];

				if(!empty($_GET['uri'])){
					Header('Location: '.urldecode($_GET['uri']));
				}else{
					Header("Location: index.php");
				}
			}else{
				$error = "Mot de passe incorect.";
			}
		}else{
			$error = "Votre compte n'a pas été validé par un administrateur pour le moment.";
		}
	}else{
		$error = "Aucun utilisateur correspondant.";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=$sitename;?> - Connexion</title>

	<link href="main.css" rel="stylesheet">

<!--[if lt IE 9]>
<script src="js/html5shiv.js"></script>
<script src="js/respond.min.js"></script>
<![endif]-->
</head>
<body>
	<div class="row d-flex justify-content-center" style="margin-top:10%;">
		<div class="center col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
			<?php
			if(isset($error) AND !empty($error)){
				?>
				<div class="alert alert-warning" role="alert">
					<svg class="glyph stroked flag"><use xlink:href="#stroked-flag"></use></svg> <?=$error;?> <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
				</div>
				<br />
			<?php } ?>
			<div class="card-login mb-3 card">
				<div class="card-header-tab card-header-tab-animation card-header">
					<div class="card-header-title">
						Connexion
					</div>
				</div>
				<div class="card-body">
					<form role="form" method="POST">
						<fieldset>
							<div class="form-group">
								<input class="form-control" placeholder="Adresse e-mail" name="email" type="email" autofocus="">
							</div>
							<div class="form-group">
								<input class="form-control" placeholder="Mot de passe" name="password" type="password" value="">
							</div>
							<button type="submit" class="btn btn-primary">Se connecter</a>
							</fieldset>
						</form>
						<br />
						<small><a href="register.php">Je n'ai pas de compte</a></small>
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
