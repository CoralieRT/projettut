<?php 
	session_start();
	include('bdd_connect.php');

	$login=$_SESSION['login'];
	$mdp=$_SESSION['mdp'];
	$nvmdp = $_POST['mdpmodif'];

	$reqetu = $bdd->prepare('UPDATE etudiant SET `MDP`= ? WHERE login = ? AND MDP = ?');
	$reqens = $bdd->prepare('UPDATE personnel SET `MDP`= ? WHERE login = ? AND MDP = ?');

	if($_POST['mdp1']=$mdp)
	{
		$verif_login = $bdd->prepare('SELECT COUNT(*) FROM personnel WHERE login = ?'); //On vérifie que le login existe dans la table
	    $verif_login->execute(array($login));
	    // Si le login rentré correspond à un login d'enseignant
	    if($verif_login->fetchColumn() != 0)
	    {
	    	$reqens->execute(array($nvmdp,$login,$mdp));
	    }

	    $verif_login = $bdd->prepare('SELECT COUNT(*) FROM etudiant WHERE login = ?'); 
        $verif_login->execute(array($login));
        // Si le login existe dans la table étudiant
        if($verif_login->fetchColumn() !=0) 
        {    
			$reqetu->execute(array($nvmdp,$login,$mdp));
		}
		?>

		<script type="text/javascript">
			window.alert('Modification réussie');
			location="modifs.php";
		</script>
		<?php
	}
?>
