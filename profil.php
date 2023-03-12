<?php
require_once('include/init.php');

$pageTitle = "profil de" . $_SESSION['membre']['pseudo'] ;
                            // pas connexion
if(!internauteConnecte()){
    header('location' . URL . 'connexion.php');
    exit();
}
                            // connecté
if(isset($_GET['action']) && $_GET['action'] == 'validate') {

    $validate .= '<div class="alert alert-primary alert-dismissible fade show mt-5" role="alert">
                    Félicitations <strong>' . $_SESSION['membre']['pseudo'] .'</strong>, vous etes connecté 😉 !
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
}

require_once('include/header.php');
?>


<div class="bagde badge-dark text-wrap p-3">
    Bonjour <?= (internauteConnecteAdmin()) ? $_SESSION['membre']['pseudo'] . "vous etes admin du site" : $_SESSION['membre']['pseudo'] ?>
</div>


<?= $validate ?>


<div class="row justify-content-around py-5">
    <div class="col-md-3 text-center">
        <ul class="list-group">
            <li class="btn btn-outline-primary text-dark my-3 shadow bg-white rounded"><?=  $_SESSION['membre']['pseudo'] ?></li>
            <li class="btn btn-outline-primary text-dark my-3 shadow bg-white rounded"><?=  $_SESSION['membre']['nom'] ?></li>
            <li class="btn btn-outline-primary text-dark my-3 shadow bg-white rounded"><?=  $_SESSION['membre']['prenom'] ?></li>
            <li class="btn btn-outline-primary text-dark my-3 shadow bg-white rounded"><?=  $_SESSION['membre']['civilite'] ?></li>
            <li class="btn btn-outline-primary text-dark my-3 shadow bg-white rounded"><?=  $_SESSION['membre']['email'] ?></li>
            <li class="btn btn-outline-primary text-dark my-3 shadow bg-white rounded"><?=  $_SESSION['membre']['telephone'] ?></li>
        </ul>
    </div>
</div>


<?php require_once('include/footer.php'); ?>