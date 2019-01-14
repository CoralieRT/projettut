<?php
    session_start();
	ob_start();
    include('bdd_connect.php');

    if (isset($_POST['nom']) && isset($_POST['prénom']) && isset($_POST['log']) && isset($_POST['mdp'])){
    	$req = $bdd->prepare("INSERT INTO `personnel` (`Nom`, `Prénom`, `login`,`mdp`) VALUES (:nom,:prenom,:log,:mdp)");
		$req->execute(array('nom'=>$_POST['nom'],'prenom'=>$_POST['prénom'],'log'=>$_POST['log'],'mdp'=>$_POST['mdp']));
    }
    if (isset($_POST['enseignant'])){
        /*va savoir pourquoi mais on récupère pas le prénom du prof encore sinon c ok*/
    	$k=explode(' ',$_POST['enseignant']);
    	$temps=time();
        $jour=date('d');

        if (!isset($k[0]) || !isset($k[1])){
            //$k[0]=0;
            $k[1]=0;
        }
        /*Recherche du prof dans la bdd*/
        $rec=$bdd->prepare('SELECT `id_prof` FROM `personnel` WHERE `Nom` = ?');//AND `Prénom`=?');
        $rec->execute(array($k[0]));
        while($rep=$rec->fetch())
        {
            $idpr=$rep['id_prof'];
        }

        /*Recherche du cours dans la bdd*/
        $req_temps_ref = $bdd->prepare('SELECT * FROM cours WHERE id_prof = ? OR id_prof2 = ?');
        $req_temps_ref -> execute(array($idpr,$idpr));

        /*comparaison de l'heure pour trouver le bon cours de la semaine*/
        while($rep=$req_temps_ref->fetch()){

            $array_deb=array($rep['debut']);
            $array_fin=array($rep['fin']);
            $chaine_deb_ref=implode(" ", $array_deb);
            $chaine_fin_ref=implode(" ", $array_fin);
            /*découpe du jour*/
            $jourdeb=substr($chaine_deb_ref, 8, 2);
            $jourfin=substr($chaine_fin_ref, 8, 2);
            /*découpe de l'heure et passage en seconde*/
            $chaine_deb_ref=substr($chaine_deb_ref, 11);
            $chaine_fin_ref=substr($chaine_fin_ref, 11);
            $deb_ref=strtotime($chaine_deb_ref);
            $fin_ref=strtotime($chaine_fin_ref);
            /*recherche de l'heure correspondante*/
            if($jour==$jourdeb){
                if($deb_ref<=$temps){
                    if($temps<=$fin_ref){
                        $idc=$rep['id_cours'];
                    }
                }
            }
        }
        if(isset($idc)){
            $reqcours = $bdd->prepare('SELECT * FROM `cours` WHERE `id_cours`=?');
            $reqcours->execute(array($idc));
            while($repcours=$reqcours->fetch()){ $cours=1; $lecours=$repcours['Matière'];}
        }
        else{ $pascours=1;}
        
    }
?>
<!DOCTYPE html>

<html>
     <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <link rel="shortcut icon" type="image/x-icon" href="logo3.png" />
        <title>Administration</title>
    </head>
    <body>
    	<header> IUT de Saint-Malo </header>
        <h1>Administration</h1>
        <!-- lien vers l'edt -->
        <h4><a href="emploidutemps.php">Mettre à jour l'emploi du temps</a></h4>
        <!-- afficher le cours actuel d'un enseignant -->
        <form method="post" action="administration.php">
        	<select name="enseignant">
        		<?php 
        			$req = $bdd->query('SELECT * FROM `personnel`');
        			while($rep=$req->fetch()){
                        $nom=$rep['Nom'].' '.$rep['Prénom'];
        				echo '<option value='.$nom.'/>'.$nom.'</option>';
        			}
        		?>
        	</select>
            <?php 
                if ((isset($pascours)) && ($pascours==1)){ echo $k[0]." n'est pas en cours.";}
                else if((isset($cours)) && ($cours==1)){echo $k[0].' a cours de '.$lecours;}
             ?>
        	<input type="submit" name="submit" value="Rechercher"/>
        </form>
        <!-- upload du fichier étudiants pour remplir la bdd -->
    	<h4>Liste des étudiants au format CSV :</h4>
    	<form method="post" action="excel.php" enctype="multipart/form-data">
    		<input type="file" name="file" id="file" /><br>
    		<input type="submit" name="submit" value="Envoyer"/>
    	</form>
    	<br><br>
    	<!-- ajout d'un enseignant dans la bdd -->
    	<h4>Ajouter un intervenant :</h4>
    	<fieldset>
    	<form method="post" action="administration.php">
    		Nom : <input type="text" name="nom" /> <br>
    		Prénom : <input type="text" name="prénom" /> <br>
    		Login : <input type="text" name="log" /> <br>
    		Mot de passe : <input type="password" name="mdp" /> <br>
    		<input type="submit" name="submit" value="Envoyer"/>
    	</form>
    	</fieldset>
		<!-- récupération des justificatifs étudiants -->
		<?php 
		
            $abs=$bdd->query('SELECT DISTINCT filename FROM justificatif,etudiant WHERE etudiant.login=justificatif.loginetu');
            echo "<h2>Récupérer les justificatifs des élèves</h2><br/>";
            //Ouvre le répertoire
            if(!is_dir('justificatifs')){
                mkdir('justificatifs');
            }
            $rep= opendir("justificatifs");
            echo "<center><table id='justif'>\n";
			while($fichierex = readdir($rep)){
                if ($fichierex!="." && $fichierex!=".."){
					while ($justif=$abs->fetch()){
						$fichier=$justif['filename'];
							echo "<tr>";
							echo "<td id='test' ><a href='justificatifs/", $fichier ,"' target='_blank'>Télécharger le justificatif $fichier</a></td>";
							$abs2=$bdd->prepare('SELECT * FROM justificatif,etudiant WHERE filename=? AND etudiant.login=justificatif.loginetu');
							$abs2->execute(array($fichier));
							while ($justif2=$abs2->fetch()){
								$nom=$justif2['Nom'];
								$prenom=$justif2['Prénom'];
								$date=$justif2['dateabs'];
								$heure=substr($date,10,18);
								$date=substr($date,0,-9);
								echo "<td><p>Absence de $prenom $nom le $date à $heure</p></td>";
							
							}	echo "</tr>";
							
					}
				}
			}
            echo "</table></center>\n";
            closedir($rep);
        ?>
		<br/><br/><br/><br/><br/>

		<!-- FIN, déconnexion -->
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
    	<button class="btn-warning btn-outline" href="deconnect.php" onclick="Deconnexion()">Déconnexion</button>

        <a class="btn-warning btn-outline" href="faq.php" role="button">FAQ</a>

        <a class="btn-warning btn-outline" href="modifs.php" role="button">Paramètres du compte</a>
    </body>
<?php ob_end_flush(); ?>	
</html>
