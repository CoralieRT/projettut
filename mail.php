<?php 
include('bdd_connect.php');

//Pour les étudiants
if (isset($_POST['etu'])){
$login=$_POST['etu'];echo $login;
$email=$_POST['email'];echo $email;
$req = $bdd->prepare("UPDATE etudiant SET mail=? WHERE login=?");
$req->execute(array($email,$login));
}

//Pour les professeurs/admin
if (isset($_POST['prof'])){
$login=$_POST['prof'];echo $login;
$req = $bdd->prepare("UPDATE personnel SET mail=? WHERE login=?");
$req->execute(array($email,$login));
}
header('location:modifs.php');
?>
