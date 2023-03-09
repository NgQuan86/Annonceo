<?php
require_once('include/init.php');

$pageTitle = "Connexion";


if(isset($_GET['action'])  && $_GET['action'] == 'deconnexion'){
    unset($_SESSION['membre']);
    header('location:' . URL . 'connexion.php');
    exit();
}


if(internauteConnecte()){
    header('location:' . URL . 'profil.php');
    exit();
}


if(isset($_GET['action']) && $_GET['action'] == 'validate' ){
    $validate .= '<div class="alert alert-primary alert-dismissible fade show mt-5" role="alert">
                        <strong>Félicitations !</strong> Votre inscription est réussie 😉, vous pouvez vous connecter !
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    }



    if($_POST){

        $verifPseudo = $pdo->prepare(" SELECT * FROM membre WHERE pseudo = :pseudo ");
        $verifPseudo->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
        $verifPseudo->execute();
                                    // PSEUDO
        if($verifPseudo->rowCount() == 1){
            $user = $verifPseudo->fetch(PDO::FETCH_ASSOC);

                                    // MOTS DE PASSE
            if(password_verify($_POST['mdp'], $user['mdp'])){
                foreach($user as $key => $value){
                    // on récupère toutes les infos en BDD sauf mdp
                    if($key != 'mdp'){
                                    // SESSION  
                        $_SESSION['membre'][$key] = $value;


                                    // vers Admin
                        if(internauteConnecteAdmin()){
                            header('location:' . URL . 'admin/index.php?action=validate' );
                                    // vers panier
                        }
                        elseif(isset($_GET['action']) && $_GET['action'] == 'acheter'){
                            header('location:' . URL . 'panier.php' );
                        }
                        else{
                                    // vers profil
                            header('location:' . URL . 'profil.php?action=validate' );
                        }
                        
                    }
                }
            }

            else{
                $erreur .= '<div class="alert alert-danger" role="alert">Erreur ce mot de passe ne correspond pas !</div>';
            }
        }

        else{
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur ce pseudo n\'existe pas, vérifiez !<br> Etes vous inscrit ?</div>';
        }
    
    }


    require_once('include/header.php');
?>

<?= $validate ?>

<!-- $erreur .= '<div class="alert alert-danger" role="alert">Erreur format adresse !</div>'; -->

<!-- $validate .= '<div class="alert alert-primary alert-dismissible fade show mt-5" role="alert">
                    <strong>Félicitations !</strong> Votre inscription est réussie 😉, vous pouvez vous connecter !
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>'; -->

<form class="my-5" method="POST" action="">

    <div class="col-md-4 offset-md-4 my-4">

    <label class="form-label" for="pseudo"><div class="badge badge-dark text-wrap">Pseudo</div></label>
    <input class="form-control btn btn-outline-primary mb-4" type="text" name="pseudo" id="pseudo" placeholder="Votre pseudo">

    <label class="form-label" for="mdp"><div class="badge badge-dark text-wrap">Mot de passe</div></label>
    <input class="form-control btn btn-outline-primary mb-4" type="password" name="mdp" id="mdp" placeholder="Votre mot de passe">

    <button type="submit" class="btn btn-lg btn-outline-primary offset-md-4 my-2">Connexion</button>

    </div>
   
</form>

<?php require_once('include/footer.php') ?>