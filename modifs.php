<?php
    session_start();
    include('bdd_connect.php');
    /*modification du mot de passe*/
    if(isset($_POST['mdpmodif'])){
        $login=$_SESSION['login'];
        $mdp=crypt($_SESSION['mdp'],$login);
        $nvmdp = crypt($_POST['mdpmodif'],$login);

        $reqetu = $bdd->prepare('UPDATE bdd_promo.etudiant SET `MDP`= ? WHERE login = ? AND MDP = ?');
        $reqens = $bdd->prepare('UPDATE bdd_promo.personnel SET `MDP`= ? WHERE login = ? AND MDP = ?');

        if(crypt($_POST['mdp1'],$login)==$mdp)
        {
            $verif_login = $bdd->prepare('SELECT COUNT(*) FROM bdd_promo.personnel WHERE login = ?'); //On vérifie que le login existe dans la table
            $verif_login->execute(array($login));
            // Si le login rentré correspond à un login d'enseignant
            if($verif_login->fetchColumn() != 0)
            {
                $reqens->execute(array($nvmdp,$login,$_SESSION['mdp']));
            }

            $verif_login = $bdd->prepare('SELECT COUNT(*) FROM bdd_promo.etudiant WHERE login = ?'); 
            $verif_login->execute(array($login));
            // Si le login existe dans la table étudiant
            if($verif_login->fetchColumn() !=0) 
            {    
                $reqetu->execute(array($nvmdp,$login,$mdp));
            }
            ?>

            <script type="text/javascript">
                window.alert('Modification réussie');
            </script>
            <?php
        }
    }
    /*ajout d'une adresse mail*/
    //Pour les étudiants
    if (isset($_POST['etu'])){
        $login=$_POST['etu'];
        $email=$_POST['email'];
        $req = $bdd->prepare("UPDATE etudiant SET mail=? WHERE login=?");
        $req->execute(array($email,$login));
    }
    //Pour les professeurs/admin
    if (isset($_POST['prof'])){
        $login=$_POST['prof'];
        $email=$_POST['email'];
        $req = $bdd->prepare("UPDATE personnel SET mail=? WHERE login=?");
        $req->execute(array($email,$login));
    }
    //header('location:modifs.php');

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
        <form method="POST" action="modifs.php">
            <legend>Modification du mot de passe</legend>
            <input type="password" name="mdp1" placeholder="ancien mot de passe"><br>
            <input type="password" name="mdpmodif" placeholder="nouveau mot de passe"><br>
            <input type="submit" class="btn btn-success" value="envoyer" />
        </form>
        <br/>
        <?php 
        $login=$_SESSION['login'];
        //Changement étudiant
        $mail=$bdd->prepare('SELECT * FROM etudiant WHERE login=?');
        $mail->execute(array($login));
        while ($res=$mail->fetch()){
            $email=$res['mail'];
            if ($email!=NULL)
            echo "Vous avez défini l'adresse mail $email pour recevoir vos notifications d'absence.";
            ?>
            <form method='POST' action='modifs.php'>
            <br><legend>Ajout/changement de votre adresse mail :</legend>
                <input type='hidden' name='etu' value=$login>
                <input type='email' name='email' size='30'><br>
                <input type='submit' class='btn btn-success' value='envoyer' />
            </form><?php
        }
        //Changement enseignant
        $mail=$bdd->prepare('SELECT * FROM personnel WHERE login=?');
        $mail->execute(array($login));
        while ($res=$mail->fetch()){
            $email=$res['mail'];
            if ($email!=NULL)
            echo "Vous avez défini l'adresse mail $email pour recevoir vos notifications d'absence.";?>
            <form method='POST' action='modifs.php'>
            <br><legend>Ajout/changement de votre adresse mail :</legend>
                <input type='hidden' name='prof' value=$login>
                <input type='email' name='email' size='30'><br>
                <input type='submit' class='btn btn-success' value='envoyer' />
            </form><?php
        }
        ?>
        </fieldset> 
        <a class="btn-warning btn-outline" href="javascript:history.go(-1)" role="button">Retour</a>

    </body>
</html>
