<?php
	session_start();
	include('bdd_connect.php');
	
	if(!is_dir('justificatifs')){		//création du fichier s'il n'existe pas
		mkdir('justificatifs');
	}
	/*on récupère le fichier avec la variable globale $_FILES*/
	if ($_FILES['file']['error'] > 0) 
		echo "Erreur lors du transfert";

	$fichier = $_FILES['file']['name'];	//on récupère le nom du fichier pdf envoyé

	$extension = strtolower(  substr(  strrchr($fichier, '.')  ,1)  );			//on vérifie son extension
	if ( $extension!='pdf' ) 	//envoi du fichier seulement 
		echo "Extension incorrecte";
	
	/*téléchargement du fichier sur notre serveur*/

	$uploaddir = 'justificatifs\\';
	$uploadfile = $uploaddir . basename($_FILES['file']['name']);
	$name = basename($fichier);
	move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
	$loginetu=$_SESSION['login'];
	foreach($_POST['absence'] as $valeur){		//implémentation des justificatifs dans la bdd avec les différents paramètres
		$date=$valeur;
		$heure=substr($date,11,18);
		$date=substr($date,0,10);	
		if (($heure<="12:15:00") &&($heure>="08:00:00"))
			$date=$date." matin";
        if (($heure<="19:00:00") &&($heure>="13:45:00"))  
			$date=$date." après-midi";
		$heureact=date('H')+1;
		$dateact=date('Y-m-d ').$heureact.date(':i');
		$req = $bdd->prepare("INSERT INTO `justificatif` (`loginetu`, `dateabs`, `heureabs`, `filename`, `date`) VALUES (:logetu,:dt,:hr,:name,:dtact)"); 	
		$req->execute(array('logetu'=>$loginetu,'dt'=>$date,'hr'=>$heure,'name'=>$name,'dtact'=>$dateact));
		header('Location: justificatif.php');
	}
?>
