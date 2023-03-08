<?php

// requete pour afficher les onglets public (enfant, femme etc... par ordre alphabétique) dans la barre de navigation
// DISTINCT permet de n'afficher qu'une seule fois l'onglet, sinon, il sera affiché pour autant de produits concernés par ce public
$afficheMenuPublics = $pdo->query(" SELECT DISTINCT public FROM produit ORDER BY public ASC ");

?>

<!-- $erreur .= '<div class="alert alert-danger" role="alert">Erreur pseudo inconnu !</div>'; -->

<!-- $validate .= '<div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                  Félicitations <strong>' . $_SESSION['membre']['pseudo'] .'</strong>, vous etes connecté(e) 😉 !
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>'; -->

<!DOCTYPE html>
<html lang="fr">

                                <!-- HEAD  -->
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

                    <!-- favicon -->
    <link rel="icon" type="image/png" href="logo.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

                    <!-- links pour les icon bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">

                    <!-- Mon CSS -->
    <link rel="stylesheet" href="style.css">

                  <!-- PAGE TITRE  -->
    <title><?= (isset($pageTitle) ? $pageTitle : "Annonce") ?></title>


</head>

                                <!-- BODY -->
<body>

<header>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
                                <!-- LOGO -->
        <a class="navbar-brand" href="<?= URL ?>">
            <img style="height: 60px;" src="<?= URL ?>img/logo">
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


                                    <!--Menu -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">

            <li class="nav-item mt-2">
                <a class="nav-link" href="<?= URL ?>">Annonceo</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link" href="<?= URL ?>">Qui Sommes Nous</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link" href="<?= URL ?>">Contact</a>
            </li>
                            
            </ul>

                                                <!-- Search  -->
            <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>




                                    <!-- **** Zone Espace Membre-Admin **** -->
            <ul class="navbar-nav ml-auto">


                                                <!-- Connecté -->
                <?php if(internauteConnecte()): ?>
            
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                            <button type="button" class="btn btn-outline-success">
                                Espace <strong>
                                            <?= $_SESSION['membre']['pseudo'] ?>
                                    </strong>
                            </button>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                            <a class="dropdown-item" href="<?= URL ?>profil.php">
                                Profil <?= $_SESSION['membre']['pseudo'] ?>
                            </a>
                            <a class="dropdown-item" href="<?= URL ?>panier.php">
                                Panier <?= $_SESSION['membre']['pseudo'] ?>
                            </a>
                            <a class="dropdown-item" href="<?= URL ?>connexion.php?action=deconnexion">
                                Déconnexion
                            </a>

                        </div>
                    </li>



                                            <!--Sans connexion  -->
                <?php else: ?>
            
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle mr-5" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <button type="button" class="btn btn-outline-success">Espace Membre</button>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="<?= URL ?>inscription.php"><button class="btn btn-outline-success">Inscription</button></a>
                        <a class="dropdown-item"><button class="btn btn-outline-success" data-toggle="modal" data-target="#connexionModal">
                            Connexion
                        </button></a>
                        <a class="dropdown-item" href="<?= URL ?>panier.php"><button class="btn btn-outline-success px-4">Panier</button></a>
                        </div>
                    </li>
                <?php endif; ?>
            
            
                                            <!-- Zone Admin -->
                <?php if(internauteConnecteAdmin()): ?>
                <li class="nav-item mr-5">
                    <a class="nav-link" href="admin/index.php">
                        <button type="button" class="btn btn-outline-success">Admin</button>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

</header>

<div class="container">

                                    <!-- Modal -->
          <div class="modal fade" id="connexionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

          
            <div class="modal-dialog" role="document">


              <div class="modal-content">

                                    <!-- Header -->
                <div class="modal-header">
                  <h3 class="modal-title" id="exampleModalLabel"><img src="<?= URL ?>img/logo">Annonceo</h3>    
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>

                                    <!-- Body -->
                <div class="modal-body text-center">

                    <form name="connexion" method="POST" action="">
                        <div class="row justify-content-around">
                            <div class="col-md-4 mt-4">
                                <label class="form-label" for="pseudo"><div class="badge badge-dark text-wrap">Pseudo</div></label>
                                <input class="form-control btn btn-outline-success" type="text" name="pseudo" id="pseudo" placeholder="Votre pseudo">
                            </div>
                        </div>

                        <div class="row justify-content-around">
                            <div class="col-md-6 mt-4">
                                <label class="form-label" for="mdp"><div class="badge badge-dark text-wrap">Mot de passe</div></label>
                                <input class="form-control btn btn-outline-success" type="password" name="mdp" id="mdp" placeholder="Votre mot de passe">
                            </div>
                        </div>
                        
                        <div class="row justify-content-center">
                            <button type="submit" name="connexion" class="btn btn-lg btn-outline-success mt-3">Connexion</button>
                        </div>
                    </form>

                </div>

                                    <!-- Footer -->
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                </div>

              </div>
            </div>
          </div>