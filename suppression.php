<?php 
include('bdd_connect.php');
$fichier=$_POST['bouton'];
echo $fichier;
$test=$bdd -> prepare('SELECT * FROM justificatif WHERE filename= ?');		//recupère toutes les absences où login=loginetu
$test->execute(array($fichier));
while($justif=$test->fetch()){
	$date=$justif['dateabs'];
	$date=substr($date,0,10);
	$date1=substr($date,0,2);echo $date1;
	$date2=substr($date,3,2);echo $date2;
	$date3=substr($date,6,9);echo $date3;
	$date=$date3."-".$date2."-".$date1;//echo $date;
	$heure=$justif['heureabs'];//echo $heure;
	$loginetu=$justif['loginetu'];//echo $loginetu;
	$modif = $bdd->prepare('UPDATE absencesdemij SET `j`=1,`nj`=0 WHERE `date` = ? AND `heure` = ? AND `loginetu` = ?');
    $modif->execute(array($date,$heure,$loginetu));
	$modif2 = $bdd->prepare('UPDATE absences_totales SET `j`=1,`nj`=0 WHERE `date` = ? AND `heure` = ? AND `loginetu` = ?');
    $modif2->execute(array($date,$heure,$loginetu));
	$del = $bdd->prepare('DELETE FROM justificatif WHERE `filename`= ?');
	$del->execute(array($fichier));
}
if(file_exists('justificatifs\\'.$fichier)){ 
	unlink('justificatifs\\'.$fichier);
}
header('location:administration.php');
?>
