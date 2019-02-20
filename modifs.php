<?php
    session_start();
?>

<!DOCTYPE html>

<html>
    <?php
        include('bdd_connect.php')
    ?>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <link rel="shortcut icon" type="image/x-icon" href="logo3.png" />
        <title>Paramètres de compte</title>
    </head>
    <body>
        <header> IUT de Saint-Malo </header>


        <fieldset>
        <form method="POST" action="update.php">
            <legend>Modification du mot de passe</legend>
            <input type="password" name="mdp1" placeholder="ancien mot de passe"><br>
            <input type="password" name="mdpmodif" placeholder="nouveau mot de passe"><br>
            <input type="submit" class="btn btn-success" value="envoyer" />
        </form>
		<br/>
		<?php 
		$login=$_SESSION['login'];

		$mail=$bdd->prepare('SELECT * FROM etudiant WHERE login=?');
		$mail->execute(array($login));
		while ($res=$mail->fetch()){
			$email=$res['mail'];
			if ($email!=NULL)
			echo "Vous avez défini l'adresse mail $email pour recevoir vos notifications d'absence.";
			echo "<form method='POST' action='mail.php'>";
			echo "<br><legend>Ajout/changement de votre adresse mail :</legend>";
				echo "<input type='hidden' name='etu' value=$login>";
				echo "<input type='email' name='email' size='30'><br>";
				echo "<input type='submit' class='btn btn-success' value='envoyer' />";
			echo "</form>";
		}

		$mail=$bdd->prepare('SELECT * FROM personnel WHERE login=?');
		$mail->execute(array($login));
		while ($res=$mail->fetch()){
			$email=$res['mail'];
			if ($email!=NULL)
			echo "Vous avez défini l'adresse mail $email pour recevoir vos notifications d'absence.";
			echo "<form method='POST' action='mail.php'>";
			echo "<br><legend>Ajout/changement de votre adresse mail :</legend>";
				echo "<input type='hidden' name='prof' value=$login>";
				echo "<input type='email' name='email' size='30'><br>";
				echo "<input type='submit' class='btn btn-success' value='envoyer' />";
			echo "</form>";
		}
		?>
		
		
        </fieldset> 
        <a class="btn-warning btn-outline" href="javascript:history.go(-1)" role="button">Retour</a>

    </body>
</html>
