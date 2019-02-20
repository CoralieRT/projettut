<?php
include('bdd_connect.php');
$envoi=$bdd->query('SELECT * FROM absencesdemij,etudiant WHERE absencesdemij.j="0" AND absencesdemij.nj="1" AND absencesdemij.loginetu=etudiant.login');
while ($res=$envoi->fetch()){
	//echo $res['mail'];
	$mail=$res['mail'];
	$abs=$res['date'];
	$heure=$res['heure'];
	$anneeabs=substr($abs,0,-6);
	$moisabs=substr($abs,5,-3);
	$jourabs=substr($abs,8,10);
	$date=$jourabs."-".$moisabs."-".$anneeabs;
	
	//Sujet
	$sujet = "Absence du $date à $heure";
	
	//messages au format texte et au format HTML
	$message_txt = "Bonjour, vous avez une absence injustifiée le $date à $heure, Veuillez envoyer un justificatif sous 48h.";
	$message_html = "<html><head></head><body><b>Bonjour</b>, vous avez une absence injustifiée le $date à $heure, Veuillez envoyer un justificatif sous 48h.</body></html>";
	
	//création de la boundary
	$boundary = "-----=".md5(rand());
	
	//header de l'email
	$test=$bdd->query('SELECT mail FROM personnel WHERE login="admin"')->fetch();
	echo $test[0];
	$header = "From: \"Administration\"<$test[0]>\n";
	$header.= "Reply-to: \"Administration\" <$test[0]>\n";
	$header.= "MIME-Version: 1.0\n";
	$header.= "Content-Type: multipart/alternative;\n boundary=\"$boundary\"\n";
	
	$message = "\n--".$boundary."\n";

	//message au format texte
	$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"\n";
	$message.= "Content-Transfer-Encoding: 8bit\n";
	$message.= "\n".$message_txt."\n";

	$message.= "\n"."--".$boundary."\n";

	//message au format HTML
	$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"\n";
	$message.= "Content-Transfer-Encoding: 8bit\n";
	$message.= "\n".$message_html."\n";

	
	$message.= "\n"."--".$boundary."--"."\n";
	$message.= "\n"."--".$boundary."--"."\n";
	
	//envoi de l'e-mail
	mail($mail,$sujet,$message,$header);
}
?>

