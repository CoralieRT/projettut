<?php
session_start (); // on démarre la session
$login=$_POST['login'];
$mdp=$_POST['mdp'];
include('bdd_connect.php');

//Première étape : Authentification d'un enseignant
$verif_login = $bdd->prepare('SELECT COUNT(*) FROM bdd_promo.personnel WHERE login = ?'); //On vérifie que le login existe dans la table
$verif_login->execute(array($login));
// Si le login rentré correspond à un login d'enseignant
if($verif_login->fetchColumn() != 0)  
{    
    // Sélection du password pour le login saisi
    $verif_mdp = $bdd->prepare('SELECT MDP FROM bdd_promo.personnel WHERE login = ? LIMIT 1'); // Préparation de la requête
    $verif_mdp->execute(array($login));                                           // Exécution
    if ($mdp == $verif_mdp->fetchColumn())                                        // Si le mot de passe correspond (enseignant)
    {
        //on stocke les variables dans les variables de session
        $_SESSION['login']=$_POST['login'];
        $_SESSION['mdp']=$_POST['mdp'];
        $verif_mdp->closeCursor(); // Termine le traitement de la requête mdp
        //direction vers la page d'administration
        if ($_SESSION['login']=='admin'){ 
            header('Location: administration.php');
        }
        else{
            $temps=time();
            $jour=date('d');
            $mois=date('m');
            /*Recherche du prof dans la bdd*/
            $rec=$bdd->prepare('SELECT `id_prof` FROM `personnel` WHERE `login` = ?');
            $rec->execute(array($_POST['login']));
            while($rep=$rec->fetch())
            {
                $idpr=$rep['id_prof'];
            }

            /*Recherche du cours dans la bdd*/
            $req_temps_ref = $bdd->prepare('SELECT * FROM cours WHERE id_prof = ?');
            $req_temps_ref -> execute(array($idpr));

            /*comparaison de l'heure pour trouver le bon cours de la semaine*/
            while($rep=$req_temps_ref->fetch()){

                $array_deb=array($rep['debut']);
                $array_fin=array($rep['fin']);
                $chaine_deb_ref=implode(" ", $array_deb);
                $chaine_fin_ref=implode(" ", $array_fin);
                /*découpe du jour*/
                $jourdeb=substr($chaine_deb_ref, 8, 2);
                $jourfin=substr($chaine_fin_ref, 8, 2);
                /*découpe du mois*/
                $moisdeb=substr($chaine_deb_ref, 5, 2);
                $moisfin=substr($chaine_fin_ref, 5, 2);
                /*découpe de l'heure et passage en seconde*/
                $chaine_deb_ref=substr($chaine_deb_ref, 11);
                $chaine_fin_ref=substr($chaine_fin_ref, 11);
                $deb_ref=strtotime($chaine_deb_ref);
                $fin_ref=strtotime($chaine_fin_ref);
                /*recherche de l'heure correspondante*/
                if($mois==$moisdeb){
                    if($jour==$jourdeb){
                        if($deb_ref<=$temps){
                            if($temps<=$fin_ref){
                                $idc=$rep['id_cours'];
                            }
                        }
                    }
                }
            }
            $_SESSION['id_cours']=$idc;                 //on stocke le cours dans une variable de session
            
            header('Location: pageprof.php');           //on dirige vers la page principale
        }
    }
    else
    {
        header('Location: index.php');            //On renvoie vers la page d'accueil
        $verif_mdp->closeCursor();                  // Termine le traitement de la requête login 
        $_SESSION['essais']=$_SESSION['essais']+1;
        
    }
}
//Si le login ne correspond pas à un login d'enseignant
else 
{
    //On vérifie que le login existe dans la table étudiant
    $verif_login = $bdd->prepare('SELECT COUNT(*) FROM bdd_promo.etudiant WHERE login = ?'); 
    $verif_login->execute(array($login));
    // Si le login existe dans la table étudiant
    if($verif_login->fetchColumn() !=0) 
    {    
        // Sélection du password pour le login saisi
        $verif_mdp = $bdd->prepare('SELECT MDP FROM bdd_promo.etudiant WHERE login = ? LIMIT 1'); // Préparation de la requête
        $verif_mdp->execute(array($login)); // Exécution
         //Si le mot correpond au mot de passe d'un étudiant
        if ($mdp == $verif_mdp->fetchColumn())
        {
             //on stocke les variables dans les variables de session
            $_SESSION['login']= $login;
            $_SESSION['mdp']=$mdp;
            $_SESSION['lat']=$_REQUEST['lat'];
            $_SESSION['lon']=$_REQUEST['lon'];
            $_SESSION['alt']=$_REQUEST['alt'];

            if(isset($_SESSION['v']))
            {                   
                $temps=time();
                $jour=date('d');
                $req_promo = $bdd->prepare('SELECT id_promo,Groupe FROM etudiant WHERE login = ?');
                $req_promo->execute(array($login));
                while($rep_promo=$req_promo->fetch())
                {
                    $id_promo = $rep_promo['id_promo'];
                    $gr = $rep_promo['Groupe'];
                }
                
                $req_cours=$bdd->prepare('SELECT * FROM cours WHERE id_promo = ?');
                $req_cours->execute(array($id_promo));
                while($rep_cours=$req_cours->fetch()){
                    $id_cours=$rep_cours['id_cours'];
                    /*traitement des horaires*/
                    $array_deb=array($rep_cours['debut']);                  //horaire de début
                    $array_fin=array($rep_cours['fin']);                    //horaire de fin
                    $chaine_deb_ref=implode(" ", $array_deb);
                    $chaine_fin_ref=implode(" ", $array_fin);
                    /*découpe du jour*/
                    $jourdeb=substr($chaine_deb_ref, 8, 2);
                    /*découpe de l'heure et passage en seconde*/
                    $heure_deb_ref=substr($chaine_deb_ref, 11);
                    $heure_fin_ref=substr($chaine_fin_ref, 11);
                    $deb_ref=strtotime($chaine_deb_ref);
                    $fin_ref=strtotime($chaine_fin_ref);
                    // Récupération du temps de génération du QR code
                    $req_temps_ref = $bdd->prepare('SELECT horaire FROM qrcode WHERE `id_cours` = ?');
                    $req_temps_ref->execute(array($id_cours));
                    while($array_temps_ref = $req_temps_ref->fetch()){
                        $chaine_temps_ref=implode(" ", $array_temps_ref);       //horaire du qr
                        $temps_ref=substr($chaine_temps_ref, 11,8);             //heure du qr
                        $temps_ref=strtotime($temps_ref);                       //passage en seconde
                        $jour=substr($chaine_temps_ref,8,2);                    //récupération du jour
                        if($jour==$jourdeb){
                            if($deb_ref<=$temps_ref){
                                if($temps_ref<=$fin_ref){
                                    $idc=$rep_cours['id_cours'];
                                    $salle=$rep_cours['salle'];
                                }
                            }
                        }
                    }
                }
                if(!isset($idc)){
                    $id_promo=substr($id_promo,0,3).'G'.$gr;
                    echo $id_promo;
                    $req_cours=$bdd->prepare('SELECT * FROM cours WHERE id_promo = ?');
                    $req_cours->execute(array($id_promo));
                    while($rep_cours=$req_cours->fetch()){
                        $id_cours=$rep_cours['id_cours'];
                        /*traitement des horaires*/
                        $array_deb=array($rep_cours['debut']);                  //horaire de début
                        $array_fin=array($rep_cours['fin']);                    //horaire de fin
                        $chaine_deb_ref=implode(" ", $array_deb);
                        $chaine_fin_ref=implode(" ", $array_fin);
                        /*découpe du jour*/
                        $jourdeb=substr($chaine_deb_ref, 8, 2);
                        /*découpe de l'heure et passage en seconde*/
                        $heure_deb_ref=substr($chaine_deb_ref, 11);
                        $heure_fin_ref=substr($chaine_fin_ref, 11);
                        $deb_ref=strtotime($chaine_deb_ref);
                        $fin_ref=strtotime($chaine_fin_ref);
                        // Récupération du temps de génération du QR code
                        $req_temps_ref = $bdd->prepare('SELECT horaire FROM qrcode WHERE `id_cours` = ?');
                        $req_temps_ref->execute(array($id_cours));
                        while($array_temps_ref = $req_temps_ref->fetch()){
                            $chaine_temps_ref=implode(" ", $array_temps_ref);       //horaire du qr
                            $temps_ref=substr($chaine_temps_ref, 11,8);             //heure du qr
                            $temps_ref=strtotime($temps_ref);                       //passage en seconde
                            $jour=substr($chaine_temps_ref,8,2);                    //récupération du jour
                            if($jour==$jourdeb){
                                if($deb_ref<=$temps_ref){
                                    if($temps_ref<=$fin_ref){
                                        $idc=$rep_cours['id_cours'];
                                        $salle=$rep_cours['salle'];
                                    }
                                }
                            }
                        }
                    }
                }
                //récupération de la variable du QR code
                $req_qr= $bdd->prepare('SELECT qr FROM qrcode, cours WHERE qrcode.id_cours=cours.id_cours AND cours.id_cours = ?');
                $req_qr->execute(array($idc));
                while($rep_qr=$req_qr->fetch())
                {
                    $qr_code = $rep_qr['qr'];
                }
                $temps=time();
                // si la date correspond (moins de 30 secondes après génération du QR code)
                if ($temps<=$temps_ref+300000)
                {
                    if ($_SESSION['v']==$qr_code)
                    {
                        $lat=$_SESSION['lat'];
                        $lon=$_SESSION['lon'];
                        $alt=$_SESSION['alt'];
                        //recherche dans la BDD les salles avec leurs coordonnees
                        $rep = $bdd->prepare('SELECT * FROM geoloc WHERE Salle=?');
                        $rep->execute(array($salle));
                        while($pos=$rep->fetch()){
                            //mise en place des coordonnees de la salle choisie dans des variables utilisables
                            $lat1=$pos['Latitude1'];
                            $lat2=$pos['Latitude2'];
                            $lat3=$pos['Latitude3'];
                            $lat4=$pos['Latitude4'];
                            $lon1=$pos['Longitude1'];
                            $lon2=$pos['Longitude2'];
                            $lon3=$pos['Longitude3'];
                            $lon4=$pos['Longitude4'];
                                if ((($lat1 <= $lat) && ($lat <= $lat2)) || (($lat4 <= $lat) && ($lat <= $lat3))){ //mise en place du périmètre pour la longitude
                                    if ((($lon1 <= $lon) && ($lon <= $lon3)) || (($lon4 <= $lon) && ($lon <= $lon3))){ //mise en place du périmètre pour la longitude               
                                        $req = $bdd->prepare('UPDATE bdd_promo.etudiant SET `presencetemp` = 1 WHERE login = ?');
                                        $req->execute(array($login));
                                        
                                        echo "<script> alert('Votre présence a bien été enregistrée (sous réserve de validation de l'enseignant')</script>";
                                        header('Location:pageetudiant.php');
                                    }
                                    else{ echo "<script> alert('Pas à la bonne position')</script>";}
                                }
                                else{ echo "<script> alert('Pas à la bonne position')</script>";}
                        }
                    }
                }
            }
            header('Location: pageetudiant.php'); //on dirige vers la page principale
        }
    }   
    else{
        header('Location: index.php'); // Si il manque login ou mdp, on renvoie vers la page d'accueil
        //$verif_mdp->closeCursor(); // Termine le traitement de la requête mdp
        $_SESSION['essais']=$_SESSION['essais']+1;
    }  
}
?>
