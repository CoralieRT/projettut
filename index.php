<!doctype html>
<html lang="fr">
<?php
	session_start();
	include('bdd_connect.php');
?>
<head>
		<meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <link rel="shortcut icon" type="image/x-icon" href="logo3.png" />
        <title>Gestion des absences : authentification</title>
</head>
<body>

<!-- Un élément HTML pour recueillir l’affichage -->
<div id="maposition"></div>
<!-- Chargement de l'API Google maps -->
<script src="https://maps.google.com/maps/api/js?sensor=false"></script>
<script src="jquery-3.3.1.js"></script>
<?php if(isset($_GET['v'])){
	$_SESSION['v']=$_GET['v'];?>
<script>

	//Récupération des données de géolocalisation
	function affichePosition(position) {
	
		var infopos = "Position déterminée : <br>";
		infopos += "Latitude : "+position.coords.latitude +"<br>";
		infopos += "Longitude: "+position.coords.longitude+"<br>";
		infopos += "Altitude : "+position.coords.altitude +"<br>";
		document.getElementById("maposition").innerHTML = infopos;		//affichage des données
		console.log(position.coords.latitude);
		
		// On envoie les valeurs de position en REQUEST
		$("#lat").val(position.coords.latitude); 
		$("#lon").val(position.coords.longitude);
		$("#alt").val(position.coords.altitude);
	}

	// Gestion des erreurs
	function erreurPosition(error) {
		var info = "Erreur lors de la géolocalisation : ";
		switch(error.code) {
		case error.TIMEOUT:
			info += "Timeout !";
		break;
		case error.PERMISSION_DENIED:
			info += "Vous n’avez pas donné la permission";
		break;
		case error.POSITION_UNAVAILABLE:
			info += "La position n’a pu être déterminée";
		break;
		case error.UNKNOWN_ERROR:
			info += "Erreur inconnue";
		break;
		}
		document.getElementById("maposition").innerHTML = info;
	}

	if(navigator.geolocation) {				//Si géolocalisation activée
		navigator.geolocation.getCurrentPosition(affichePosition,erreurPosition);	
	}

	else {									//Si géolocalisation désactivée
		alert("Ce navigateur ne supporte pas la géolocalisation");
	}

</script>
<?php }?>
<header> Département R&T FI1A </header>
<h1>Gestion des absences</h1>
<div id="maposition"></div>

 <!--formulaire qui envoie les information vers le script d'authentification : géoloc, login et mdp-->
    <form method="post" action="connect.php">
    	<fieldset>
			<input type="hidden" name="lat" id="lat" />
			<input type="hidden" name="lon" id="lon" /> 
            <input type="text" name="login" placeholder="N° SESAME"/> <br/> 
            <input type="password" name="mdp" placeholder="Mot de passe" /> <br/>
            <br/>
            <input type="submit" value="Valider"/>
        </fieldset>
    </form>
</body>
</html>
