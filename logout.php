<?php
session_start();
session_destroy();
Header("Location: login.php");
die("Vous avez été déconnecté");
?>