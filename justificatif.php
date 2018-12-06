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
			<?php $reponse = $bdd->prepare('SELECT * FROM absencesdemij WHERE loginetu=?');
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
					if (($dateact==$ma && $difjour<2) || ($testdate=$ma && ($difjour==2 && $difheure<=$heure))){
						
						$date2="Absence du ".$date." à ".$heure;
						echo "<select name='absence'>";
						echo "<option value='$date $heure'>$date2</option>";
						echo "</select>";
					}
					else 
						$abs=2;
						
				}
				if ($abs==2)
					echo "Ce n'est plus possible de justifier l'absence du $date.";
				
				if ($abs!=0 && $abs!=2){
					echo "<input type='file' name='file' id='file' /><br>";
					echo "<input type='submit' name='submit' value='Envoyer'/>";
				}
				if ($abs==0)
					echo "Vous n'avez aucune absence à justifier.";
				
				?>
    	</form>
		
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
