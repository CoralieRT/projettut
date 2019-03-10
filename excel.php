<?php
	include('bdd_connect.php');
	if(!is_dir('listes')){
		mkdir('listes');
	}
	/*on récupère le fichier avec la variable globale $_FILES*/
	if ($_FILES['file']['error'] > 0) $erreur = "Erreur lors du transfert";
	
	$fichier = "listes\\".$_FILES['file']['name'];			 								//on récupère le nom du fichier csv envoyé
	$extension = strtolower(  substr(  strrchr($fichier, '.')  ,1)  );						//on vérifie son extension
	if ( $extension!='csv' ) echo "Extension incorrecte";
	/*téléchargement du fichier sur notre serveur*/
	$uploaddir = 'listes\\';
	$uploadfile = $uploaddir . basename($_FILES['file']['name']);
	$name = basename($fichier);
	move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
	
	/*on ouvre le fichier*/
	if (file_exists($fichier)){
	 $fp = file($fichier); 
	}
 	else   		/*s'il n'existe pas*/
    { 
       echo "Fichier introuvable !<br>Importation stoppée.";
       exit();
    }
	/*ouverture du fichier de recap mdp*/
	$f=fopen('etudiants.txt',"a+");
	/*lecture du fichier*/
	foreach($fp as $ligne){
		/*traitement des données et préparation des infos étudiants*/	
		$k = explode(",",$ligne);
		$nom = $k[0];
		$prenom = $k[1];
		$promo = $k[2];
		$photo = strtolower($prenom).substr(strtolower(ucfirst($nom)),0,3);
		$login = $photo;
		$char = 'abcdefghijklmnopqrstuvwxyz0123456789'; 
		$mdp = substr(str_shuffle($char),26);
		$txt=$login.", ".$mdp."\r";
		/*hash du mot de passe, salé avec le login*/
		$mdpcr = crypt($mdp,$login);
		$gr = $k[3];
		/*on vérifie si l'étu est déjà dans la bdd*/
		$ajout = $bdd->prepare("SELECT * FROM `etudiant` WHERE Nom = ? AND Prénom = ? AND id_promo = ? AND Groupe = ?");
		$ajout->execute(array($nom,$prenom,$promo,$gr));
		while($rep=$ajout->fetch()){
			$init=1;
		}
		/*on entre les infos dans la bdd*/
		if(!isset($init)){
			fwrite($f,$txt);
			$req = $bdd->prepare("INSERT INTO `etudiant` (`Nom`, `Prénom`, `id_promo`,`photo`,`login`,`MDP`,`Groupe`,`presencetemp`) VALUES (:nom,:prenom,:promo,:photo,:log,:mdp,:gr,'0') ");
			$req->execute(array('nom'=>$nom,'prenom'=>$prenom,'promo'=>$promo,'photo'=>$photo,'log'=>$login,'mdp'=>$mdpcr,'gr'=>$gr));
		}
	}
	fclose($f);
	header('Location: administration.php');
?>
