<?php
 session_start (); // on démarre la session
 if(isset($_GET['v'])){ $_SESSION['v']=$_GET['v'];}
 ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <link rel="shortcut icon" type="image/x-icon" href="logo3.png" />
        <title>Gestion des absences : authentification</title>
    </head>
    <body>
        <header> Département R&T FI1A </header>
        <h1>Gestion des absences</h1>
		<body><div id="maposition"></div>

			<script src="https://maps.google.com/maps/api/js?sensor=false"></script>
			<script src="jquery-3.3.1.js"></script>
			<script>
				// Position par défaut
				var centerpos = new google.maps.LatLng(48.657393,-1.969133);

				if(navigator.geolocation) {
							
					// Fonction de callback en cas de succès
					function affichePosition(position) {					
						var infopos = "Position déterminée : <br>";
						infopos += "Latitude : "+position.coords.latitude +"<br>";
						infopos += "Longitude: "+position.coords.longitude+"<br>";
						infopos += "Altitude : "+position.coords.altitude +"<br>";
						document.getElementById("maposition").innerHTML = infopos;
						console.log(position.coords.latitude);
						
						// On instancie un nouvel objet LatLng pour Google Maps
						var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
						$("#lat").val(position.coords.latitude); 
						$("#lon").val(position.coords.longitude);

						// Fonction de callback en cas d’erreur
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

					navigator.geolocation.getCurrentPosition(affichePosition,erreurPosition);
				} 
				else {

					alert("Ce navigateur ne supporte pas la géolocalisation");
				}
			</script>
         <!--formulaire qui envoie les information vers le script d'authentification : géoloc, login et mdp-->
		    <form method="post" action="connect.php">
            <fieldset>
			<input type="hidden" name="lat" id="lat" />
			<input type="hidden" name="lon" id="lon" />
            <input type="text" name="login" placeholder="N° SESAME"/> <br/> 
            <input type="password" name="mdp" placeholder="Mot de passe" /> <br/>
            <br/>
            <input type="submit" value="Valider" class="btn-warning btn-input" />    
            </fieldset>   
        </form> 
    </body>
</html>
