<?php
require_once('../include/init.php');

if (!internauteConnecteAdmin()) {
    header('location:' . URL . 'connexion.php');
    exit();
}
                            // pagination

    if(isset($_GET['page']) && !empty($_GET['page'])){
        $pageCourante = (int) strip_tags($_GET['page']);
    }
    else{$pageCourante = 1;}
        $queryAnnonces = $pdo->query(" SELECT COUNT(id_annonce) AS nombreAnnonces FROM annonce ");
        $resultatAnnonces = $queryAnnonces->fetch();
        $nombreAnnonces = (int) $resultatAnnonces['nombreAnnonces'];
        $parPage = 10;
        $nombrePages = ceil($nombreAnnonces / $parPage);
        $premierAnnonce = ($pageCourante - 1) * $parPage;





                        //  ****************** TRAITEMENT DES INFOS *******************
if (isset($_GET['action'])) {

    if ($_POST) {
                                    // Contrainte
        if (!isset($_POST['categorie'])) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format categorie !</div>';
        }
        if (!isset($_POST['titre']) || strlen($_POST['titre']) < 3 || strlen($_POST['titre']) > 20) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format titre !</div>';
        }
        if (!isset($_POST['description_courte']) || strlen($_POST['description_courte']) < 3 || strlen($_POST['description_courte']) > 255) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format description_courte !</div>';
        }
        if (!isset($_POST['description_longue']) || strlen($_POST['description_longue']) < 3 || strlen($_POST['description_longue']) > 500) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format description_longue !</div>';
        }
        if (!isset($_POST['prix']) || !preg_match('#^[0-9]{1,5}$#', $_POST['prix'])) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format prix !</div>';
        }
        if (!isset($_POST['pays']) || strlen($_POST['pays']) < 3 || strlen($_POST['pays']) > 20) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format pays !</div>';
        }
        if (!isset($_POST['ville']) || strlen($_POST['ville']) < 3 || strlen($_POST['ville']) > 20) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format ville !</div>';
        }
        if (!isset($_POST['adresse']) || strlen($_POST['adresse']) < 3 || strlen($_POST['adresse']) > 20) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format adresse !</div>';
        }
        if (!isset($_POST['cp']) || !preg_match('#^[0-9]{1,5}$#', $_POST['cp'])) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format cp !</div>';
        }
        

                                // traitement pour la photo
    // photo principale
        $photo_bdd = "";
        if (!empty($_FILES['photo']['name'])) {
            $photo_nom = $_POST['titre'] . '_' . $_FILES['photo']['name'];
            $photo_bdd = "$photo_nom";
            $photo_dossier = RACINE_SITE . "img/$photo_nom";
            copy($_FILES['photo']['tmp_name'], $photo_dossier);
        }
        // autres photos
        $photo_bdd1 = "";
        $photo_bdd2 = "";
        $photo_bdd3 = "";
        $photo_bdd4 = "";
        $photo_bdd5 = "";
        if (!empty($_FILES['photo1']['name'])) {
            $photo_nom = $_POST['titre'] . '_' . $_FILES['photo1']['name'];
            $photo_bdd1 = "$photo_nom";
            $photo_dossier = RACINE_SITE . "img/$photo_nom";
            copy($_FILES['photo1']['tmp_name'], $photo_dossier);
        }
        if (!empty($_FILES['photo2']['name'])) {
            $photo_nom = $_POST['titre'] . '_' . $_FILES['photo2']['name'];
            $photo_bdd2 = "$photo_nom";
            $photo_dossier = RACINE_SITE . "img/$photo_nom";
            copy($_FILES['photo2']['tmp_name'], $photo_dossier);
        }
        if (!empty($_FILES['photo3']['name'])) {
            $photo_nom = $_POST['titre'] . '_' . $_FILES['photo3']['name'];
            $photo_bdd3 = "$photo_nom";
            $photo_dossier = RACINE_SITE . "img/$photo_nom";
            copy($_FILES['photo3']['tmp_name'], $photo_dossier);
        }
        if (!empty($_FILES['photo4']['name'])) {
            $photo_nom = $_POST['titre'] . '_' . $_FILES['photo4']['name'];
            $photo_bdd4 = "$photo_nom";
            $photo_dossier = RACINE_SITE . "img/$photo_nom";
            copy($_FILES['photo4']['tmp_name'], $photo_dossier);
        }
        if (!empty($_FILES['photo5']['name'])) {
            $photo_nom = $_POST['titre'] . '_' . $_FILES['photo5']['name'];
            $photo_bdd5 = "$photo_nom";
            $photo_dossier = RACINE_SITE . "img/$photo_nom";
            copy($_FILES['photo5']['tmp_name'], $photo_dossier);
        }


    if (empty($erreur)) {

            $inscrirePhoto = $pdo->prepare("INSERT INTO photo (photo1, photo2, photo3, photo4, photo5) VALUES (:photo1, :photo2, :photo3, :photo4, :photo5)");
            $inscrirePhoto->bindValue(':photo1', $photo_bdd1, PDO::PARAM_STR);
            $inscrirePhoto->bindValue(':photo2', $photo_bdd2, PDO::PARAM_STR);
            $inscrirePhoto->bindValue(':photo3', $photo_bdd3, PDO::PARAM_STR);
            $inscrirePhoto->bindValue(':photo4', $photo_bdd4, PDO::PARAM_STR);
            $inscrirePhoto->bindValue(':photo5', $photo_bdd5, PDO::PARAM_STR);
            $inscrirePhoto->execute();

            $photo_id = $pdo->lastInsertId();


        // initialisation de la variable à vide
        $photo_bdd = "";

        // condition pour modifier une photo
        if($_GET['action'] == 'update'){
            // A mettre en relation avec la nouvelle photo que l'on veut insérer en BDD pour remplacer l'ancienne
            $photo_bdd = $_POST['photoActuelle'];
        }


        if(!empty($_FILES['photo']['name'])){
            
            $photo_nom = $_POST['titre'] . '_' . $_FILES['photo']['name'];
           
            $photo_bdd = "$photo_nom";
            
            $photo_dossier = RACINE_SITE . "img/$photo_nom";
            
            copy($_FILES['photo']['tmp_name'], $photo_dossier);
        }

        // fin traitement pour la photo

        if (empty($erreur)) {
            if ($_GET['action'] == 'update') {
                $modifAnnonce = $pdo->prepare(" UPDATE annonce SET id_annonce = :id_annonce , titre = :titre, description_courte = :description_courte, description_longue = :description_longue, prix = :prix, photo = :photo, pays = :pays, adresse = :adresse, ville = :ville, cp= :cp  WHERE id_annonce = :id_annonce ");
                $modifAnnonce->bindValue(':id_annonce', $_POST['id_annonce'], PDO::PARAM_INT);
                $modifAnnonce->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':description_courte', $_POST['description_courte'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':description_longue', $_POST['description_longue'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
                $modifAnnonce->bindValue(':photo', $photo_bdd, PDO::PARAM_STR); 
                $modifAnnonce->bindValue(':pays', $_POST['pays'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':cp', $_POST['cp'], PDO::PARAM_INT);
                $modifAnnonce->execute();

                $queryAnnonce = $pdo->query(" SELECT titre FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
                $annonce = $queryAnnonce->fetch(PDO::FETCH_ASSOC);

                $content .= '<div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                        <strong>Félicitations !</strong> Modification du annonce '. $annonce['titre'] .' réussie !
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $inscrireAnnonce = $pdo->prepare(" INSERT INTO annonce (titre, description_courte, description_longue, prix, photo, pays, ville, adresse, cp) VALUES (:titre, :description_courte, :description_longue, :prix, :photo, :pays, :ville, :adresse, :cp) ");
                $inscrireAnnonce->bindValue(':id_annonce', $_POST['id_annonce'], PDO::PARAM_INT);
                $inscrireAnnonce->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
                $inscrireAnnonce->bindValue(':description_courte', $_POST['description_courte'], PDO::PARAM_STR);
                $inscrireAnnonce->bindValue(':description_longue', $_POST['description_longue'], PDO::PARAM_STR);
                $inscrireAnnonce->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
                $inscrireAnnonce->bindValue(':photo', $photo_bdd, PDO::PARAM_STR); 
                $inscrireAnnonce->bindValue(':pays', $_POST['pays'], PDO::PARAM_STR);
                $inscrireAnnonce->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
                $inscrireAnnonce->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
                $inscrireAnnonce->bindValue(':cp', $_POST['cp'], PDO::PARAM_INT);
                $inscrireAnnonce->execute();
            }
        }
    }


                    // 
    if ($_GET['action'] == 'update') {
        $queryAnnonces = $pdo->query("SELECT * FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
        $annonceActuel = $queryAnnonces->fetch(PDO::FETCH_ASSOC);
    }
    $id_annonce = (isset($annonceActuel['id_annonce'])) ? $annonceActuel['id_annonce'] : "";
    $categorie = (isset($annonceActuel['categorie'])) ? $annonceActuel['categorie'] : "";
    $titre = (isset($annonceActuel['titre'])) ? $annonceActuel['titre'] : "";
    $description_courte = (isset($annonceActuel['description_courte'])) ? $annonceActuel['description_courte'] : "";
    $description_longue = (isset($annonceActuel['description_longue'])) ? $annonceActuel['description_longue'] : "";
    $prix = (isset($annonceActuel['prix'])) ? $annonceActuel['prix'] : "";
    $photo = (isset($annonceActuel['photo'])) ? $annonceActuel['photo'] : "";
    $pays = (isset($annonceActuel['pays'])) ? $annonceActuel['pays'] : "";
    $ville = (isset($annonceActuel['ville'])) ? $annonceActuel['ville'] : "";
    $adresse = (isset($annonceActuel['adresse'])) ? $annonceActuel['adresse'] : "";
    $cp = (isset($annonceActuel['cp'])) ? $annonceActuel['cp'] : "";
    
    

    if($_GET['action'] == 'delete'){    
        $pdo->query(" DELETE FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
    }
}
}
require_once('includeAdmin/header.php');
?>

<!-- $erreur .= '<div class="alert alert-danger" role="alert">Erreur format mot de passe !</div>'; -->

<!-- $content .= '<div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                        <strong>Félicitations !</strong> Insertion du annonce réussie !
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>'; -->

<h1 class="text-center my-5">
    <div class="badge badge-warning text-wrap p-3">Gestion des annonces</div>
</h1>

<?= $erreur ?>
<?= $content ?>
<!-- utilisation de la fonction personnalisée debug pour savoir ce qui a été récupéré avec $_POST, pour comprendre en cas de problème, ou se situe le problème -->
<!-- <?= debug($_POST) ?> -->

<?php if (!isset($_GET['action']) && !isset($_GET['page'])) : ?>
<div class="blockquote alert alert-dismissible fade show mt-5 shadow border border-warning rounded" role="alert">
    <p>Gérez ici votre base de données des annonces</p>
    <p>Vous pouvez modifier leurs données, ajouter ou supprimer un annonce</p>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<?php if(isset($_GET['action'])): ?>
<h2 class="pt-5">Formulaire <?= ($_GET['action'] == 'add') ? "d'ajout" : "de modification" ?> des annonces</h2>




                    <!-- ********** Tableau de modification ************* -->

<form id="monForm" class="my-5" method="POST" action="" enctype="multipart/form-data">

                                <!-- id_annonce -->
    <input type="hidden" name="id_annonce" value="<?= $id_annonce ?>">


                                <!-- Titre & Prix -->
    <div class="row mt-5">
        <div class="col-md-4">
            <label class="form-label" for="titre">
                <div class="badge badge-dark text-wrap">Titre</div>
            </label>
            <input class="form-control" type="text" name="titre" id="titre" placeholder="Référence" value="<?= $titre ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label" for="prix">
                <div class="badge badge-dark text-wrap">Prix</div>
            </label>
            <input class="form-control" type="text" name="prix" id="prix" placeholder="Prix" value="<?= $prix ?>">
        </div>

       
    </div>


                            <!-- Déscription -->
    <div class="row justify-content-around mt-5">
        <div class="col-md-6">
            <label class="form-label" for="description_courte">
                <div class="badge badge-dark text-wrap">Votre commentaire</div>
            </label>
            <textarea class="form-control" name="description_courte" id="description_courte" placeholder="Description" rows="5"><?= $description_courte ?></textarea>
        </div>
    </div>

    <div class="row justify-content-around mt-5">
        <div class="col-md-6">
            <label class="form-label" for="description_longue">
                <div class="badge badge-dark text-wrap">Votre commentaire</div>
            </label>
            <textarea class="form-control" name="description_longue" id="description_longue" placeholder="Description" rows="5"><?= $description_longue ?></textarea>
        </div>
    </div>

                            <!-- photo -->
        <div class="col-md-4">
            <label class="form-label" for="photo">
                <div class="badge badge-dark text-wrap">Photo</div>
            </label>
            <input class="form-control" type="file" name="photo" id="photo" placeholder="Photo">
        </div>
                                        
        <?php if(!empty($photo)): ?>
        <div class="mt-4">
            <p>Vous pouvez changer d'image
                <img src="<?= URL . 'img/' . $photo ?>" width="50px">
            </p>
        </div>
        <?php endif; ?>
        <input type="hidden" name="photoActuelle" value="<?= $photo ?>">
        

                                <!-- Pay Ville cp -->
        <div class="row mt-5">
            <div class="col-md-4">
                <label class="form-label" for="pays">
                    <div class="badge badge-dark text-wrap">Référence</div>
            </label>
                <input class="form-control" type="text" name="pays" id="pays" placeholder="Pays" value="<?= $pays ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label" for="ville">
                    <div class="badge badge-dark text-wrap">Ville</div>
                </label>
                <input class="form-control" type="text" name="ville" id="ville" placeholder="Ville" value="<?= $ville ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label" for="cp">
                    <div class="badge badge-dark text-wrap">Code Postal</div>
                </label>
                <input class="form-control" type="text" name="cp" id="cp" placeholder="75000" value="<?= $cp ?>">
            </div>

        </div>

                                    <!--  Adresse   -->
            <div class="mx-5">
                <label class="form-label mx_auto" for="adresse">
                    <div class="badge badge-dark text-wrap">Adresse</div>
                </label>
                <input class="form-control" type="text" name="adresse" id="adresse" placeholder="Adresss" value="<?= $adresse ?>">
            </div>
                                    <!-- Valider -->
    <div class="col-md-1 mt-5">
        <button type="submit" class="btn btn-outline-dark btn-warning">Valider</button>
    </div>
    <div class="col-md-4">
            <label class="form-label" for="categorie">
                <div class="badge badge-dark text-wrap">Catégorie</div>
            </label>
            <!-- Mettre une balise select et faire une boucle While -->
            <select class="form-control"  name="categorie" id="categorie">
            <?php             
            while($categorie = $listeCategorie->fetch(PDO::FETCH_ASSOC)){
                echo "<option value='$categorie[id_categorie]'> $categorie[titre] </option> ";
            }
        
            ?>
            </select>
        </div>
    
</form>
<?php endif; ?>

<?php $queryAnnonces = $pdo->query(" SELECT id_annonce FROM annonce "); ?>
<h2 class="py-5">Nombre de annonces en base de données: <?= $queryAnnonces->rowCount() ?></h2>

<div class="row justify-content-center py-5">
    <a href='?action=add'>
        <button type="button" class="btn btn-sm btn-outline-dark shadow rounded">
            <i class="bi bi-plus-circle-fill"></i> Ajouter un annonce
        </button>
    </a>
</div>

<table class="table table-dark text-center">
    <!-- requete complétée pour n'afficher que 10 annonces dans le tableau, le OFFSET détermine quel annonce sera affiché en premier dans la nouvelle page -->
    <?php $afficheAnnonces = $pdo->query("SELECT * FROM annonce ORDER BY prix ASC LIMIT $parPage OFFSET $premierAnnonce ") ?>
    <thead>
        <tr>
            <?php for ($i = 0; $i < $afficheAnnonces->columnCount(); $i++) :
                $colonne = $afficheAnnonces->getColumnMeta($i) ?>
                <th><?= $colonne['name'] ?></th>
            <?php endfor; ?>
            <th colspan=2>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($annonce = $afficheAnnonces->fetch(PDO::FETCH_ASSOC)) : ?>
            <tr>
                <?php foreach ($annonce as $key => $value) : ?>
                    <?php if ($key == 'prix') : ?>
                        <td><?= $value ?> €</td>
                    <?php elseif ($key == 'photo') : ?>
                        <td><img class="img-fluid" src="<?= URL . 'img/' . $value ?>" width="50" loading="lazy"></td>
                    <?php else : ?>
                        <td><?= $value ?></td>
                    <?php endif; ?>
                <?php endforeach; ?>
                <td><a href='?action=update&id_annonce=<?= $annonce['id_annonce'] ?>'><i class="bi bi-pen-fill text-warning"></i></a></td>
                <td><a data-href="?action=delete&id_annonce=<?= $annonce['id_annonce'] ?>" data-toggle="modal" data-target="#confirm-delete"><i class="bi bi-trash-fill text-danger" style="font-size: 1.5rem;"></i></a></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

                            <!-- Début de pagination -->
<nav aria-label="">
    <ul class="pagination justify-content-end">
        <li class="page-item <?= ($pageCourante == 1) ? 'disabled' : "" ?>">
            <a class="page-link text-dark" href="?page=<?= $pageCourante - 1 ?>" aria-label="Previous">
                <span aria-hidden="true">précédente</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>
        <?php for($page = 1; $page <= $nombrePages; $page++): ?>
        <li class="mx-1 page-item">
            <a class="btn btn-outline-dark <?= ($pageCourante == $page ) ? 'active' : "" ?>" href="?page=<?= $page ?>"><?= $page ?></a>
        </li>
        <?php endfor; ?>
                            <!-- fin affichage nb de pages -->

        <li class="page-item <?= ($pageCourante == $nombrePages)? 'disabled' : '' ?>">
            <a class="page-link text-dark" href="?page=<?= $pageCourante + 1 ?>" aria-label="Next">
                <span aria-hidden="true">suivante</span>
                <span class="sr-only">Next</span>
            </a>
        </li>
    </ul>
</nav>
<!-- fin de pagination -->

<!-- <img class="img-fluid" src="" width="50"> -->

<!-- <td><a href=''><i class="bi bi-pen-fill text-warning"></i></a></td>-->
<!-- <td><a data-href="" data-toggle="modal" data-target="#confirm-delete"><i class="bi bi-trash-fill text-danger" style="font-size: 1.5rem;"></i></a></td> -->

<!-- modal suppression codepen https://codepen.io/lowpez/pen/rvXbJq -->

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Supprimer article
            </div>
            <div class="modal-body">
                Etes-vous sur de vouloir retirer cet article de votre panier ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                <a class="btn btn-danger btn-ok">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<!-- modal -->

<?php require_once('includeAdmin/footer.php'); ?>