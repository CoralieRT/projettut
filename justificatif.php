<!DOCTYPE html>

<html>
    <?php
        include('bdd_connect.php');
		session_start();
		$login=$_SESSION['login'];
    ?>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <link rel="shortcut icon" type="image/x-icon" href="logo3.png" />
        <title>Envoi d'un justificatif</title>
    </head>
    <body>
        <header> IUT de Saint-Malo </header>
        <h1>Statistiques individuelles d'absence</h1>
        
		<form method="post" action="justif_abs.php" enctype="multipart/form-data">		
			<input id="btn" value="1" type="checkbox" onchange="cocherdécocher(this.checked)" /> Tout sélectionner/désélectionenr<br/><br/>
			<?php $reponse = $bdd->prepare('SELECT * FROM absencesdemij WHERE loginetu=?');	//Sélection des absences correspondants à l'étudiant
				$reponse->execute(array($login));
				$abs=0;
				while ($res =$reponse->fetch()){	
					$abs=1;
					$heure=$res['heure'];
					$dateabs=$res['date'];
					$anneeabs=substr($dateabs,0,-6);
					$moisabs=substr($dateabs,5,-3);
					$ma=$moisabs."-".$anneeabs;
					$jourabs=substr($dateabs,8,10);
					$jouract=date('d');
					$dateact=date('m-Y');
					$difjour=$jouract-$jourabs;
					$difheure=date('H')+1;
					$date=$jourabs."-".$moisabs."-".$anneeabs;
					if ($res['j']==0){
						if (($dateact==$ma && $difjour<2) || ($testdate=$ma && ($difjour==2 && $difheure<=$heure))){		//Si l'absence date d'il y a moins de 2 jours possiblité d'envoyer un justificatif
							$date2="Absence du ".$date." à ".$heure;
							$date3=$date." ".$heure;
							echo "<input type='checkbox' name='absence[]' value='$date3'/><label>$date2</label><br/>";
						}
						else 	//Si les 2 jours sont passés
							$abs=2;
					}
				}
				if ($abs==2)
					echo "<br/>Ce n'est plus possible de justifier l'absence du $date.";
				
				echo "<input type='file' name='file' id='file' /><br>";
				echo "<input type='submit' name='submit' value='Envoyer'/>";
				if ($abs==0)	//S'il n'y a aucune absence à justifier
					echo "<br/>Vous n'avez aucune absence à justifier.";
				?>
    	</form>
		<script>
			function cocherdécocher(){
				var btn =document.getElementById('btn');	//on récupère le bouton cocher décocher
				var action = btn.value;
				var cases = document.getElementsByTagName('input');	// on recupere tous les INPUT
				for (var i = 0; i < cases.length; i++) {	//on parcourt les différentes input
					if (cases[i].type == 'checkbox'){		//si c'est une checkbox
						if (action==1){						
							cases[i].checked = true;		//on la coche si elle est décochée (elle est décochée de base)
							btn.value=0;
						}
						else{
							cases[i].checked = false;		//on la décoche si elle est cochée
							btn.value=1;
						}
					}	
				}
			}
		</script>
        <a class="btn-warning  btn-outline" href="modifs.php" role="button">Paramètres du compte</a>
        <a class="btn-warning btn-outline" href="faq.php" role="button">FAQ</a>
		<a class="btn-warning btn-outline" href="pageetudiant.php" role="button">Retour</a>

        <script>
                function Deconnexion ()
                {
                function RedirigeDeconnexion()
                {
                document.location.href="deconnect.php"; 
                }
                if (confirm("Etes-vous sûr de vouloir vous déconnecter ?")) 
                    {
                        RedirigeDeconnexion();
                    }
                }
        </script>
        <button class="btn-warning btn-outline" onclick="Deconnexion()">Deconnexion</button>
    </body>

</html>
