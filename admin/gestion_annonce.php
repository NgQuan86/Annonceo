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

    $photo1 = (isset($_POST['photo1'])) ? $_POST['photo1'] : "";
    $photo2 = (isset($_POST['photo2'])) ? $_POST['photo2'] : "";
    $photo3 = (isset($_POST['photo3'])) ? $_POST['photo3'] : "";
    $photo4 = (isset($_POST['photo4'])) ? $_POST['photo4'] : "";
    $photo5 = (isset($_POST['photo5'])) ? $_POST['photo5'] : "";

    

                        //  ****************** TRAITEMENT DES INFOS *******************
if (isset($_GET['action'])) {

    if ($_POST) {
                                    // **** CONTRAINTE ****
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
        $photo_bdd1 = "";
        $photo_bdd2 = "";
        $photo_bdd3 = "";
        $photo_bdd4 = "";
        $photo_bdd5 = "";
        $photo_id ="";
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
                                // **** FIN CONTRAINTE ****

    // ---------------------------------------------------------------------------------------------------------------

                                            // ACTION
        if (empty($erreur)) {
            if ($_GET['action'] == 'update') {
               

                if(isset($_GET['id_photo'])){
                    $detailPhoto = $pdo->query(" SELECT * FROM photo WHERE id_photo = '$_GET[id_photo]' ");
                    $detail = $detailPhoto->fetch(PDO::FETCH_ASSOC);
                    }
                $modifPhoto = $pdo->prepare("UPDATE photo SET  photo1 = :photo1, photo2 = :photo2, photo3 = :photo3, photo4 = :photo4, photo5 = :photo5 ");
                // $modifPhoto->bindValue(':id_photo', $photo_id, PDO::PARAM_STR);
                $modifPhoto->bindValue(':photo1', $photo_bdd1, PDO::PARAM_STR);
                $modifPhoto->bindValue(':photo2', $photo_bdd2, PDO::PARAM_STR);
                $modifPhoto->bindValue(':photo3', $photo_bdd3, PDO::PARAM_STR);
                $modifPhoto->bindValue(':photo4', $photo_bdd4, PDO::PARAM_STR);
                $modifPhoto->bindValue(':photo5', $photo_bdd5, PDO::PARAM_STR);
                
                $modifPhoto->execute();

                 $modifAnnonce = $pdo->prepare(" UPDATE annonce SET id_annonce = :id_annonce , titre = :titre, description_courte = :description_courte, description_longue = :description_longue, prix = :prix, photo = :photo, pays = :pays, ville = :ville, adresse = :adresse, cp= :cp  WHERE id_annonce = :id_annonce ");
                $modifAnnonce->bindValue(':id_annonce', $_POST['id_annonce'], PDO::PARAM_INT);
                $modifAnnonce->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':description_courte', $_POST['description_courte'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':description_longue', $_POST['description_longue'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
                $modifAnnonce->bindValue(':photo',$photo_bdd1, PDO::PARAM_STR);
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

                $inscrirePhoto = $pdo->prepare("INSERT INTO photo (photo1, photo2, photo3, photo4, photo5) VALUES (:photo1, :photo2, :photo3, :photo4, :photo5)");
                $inscrirePhoto->bindParam(':photo1', $photo_bdd1, PDO::PARAM_STR);
                $inscrirePhoto->bindParam(':photo2', $photo_bdd2, PDO::PARAM_STR);
                $inscrirePhoto->bindParam(':photo3', $photo_bdd3, PDO::PARAM_STR);
                $inscrirePhoto->bindParam(':photo4', $photo_bdd4, PDO::PARAM_STR);
                $inscrirePhoto->bindParam(':photo5', $photo_bdd5, PDO::PARAM_STR);
                $inscrirePhoto->execute();
                

                $photo_id = $pdo->lastInsertId();

                $inscrire_Annonces = $pdo->prepare(" INSERT INTO annonce (titre, description_courte, description_longue, prix, photo, pays, ville, adresse, cp, membre_id, categorie_id, date_enregistrement, photo_id) VALUES (:titre, :description_courte, :description_longue, :prix, :photo, :pays, :ville, :adresse, :cp, :membre_id, :categorie, NOW(), :photo_id )");
            $inscrire_Annonces->bindValue(':membre_id', $_SESSION['membre']['id_membre'], PDO::PARAM_STR);
            $inscrire_Annonces->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
            $inscrire_Annonces->bindValue(':description_courte', $_POST['description_courte'], PDO::PARAM_STR);
            $inscrire_Annonces->bindValue(':description_longue', $_POST['description_longue'], PDO::PARAM_STR);
            $inscrire_Annonces->bindValue(':prix', $_POST['prix'], PDO::PARAM_STR);
            $inscrire_Annonces->bindValue(':photo', $photo_bdd1, PDO::PARAM_STR);
            $inscrire_Annonces->bindValue(':pays', $_POST['pays'], PDO::PARAM_STR);
            $inscrire_Annonces->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
            $inscrire_Annonces->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
            $inscrire_Annonces->bindValue(':cp', $_POST['cp'], PDO::PARAM_STR);
            $inscrire_Annonces->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
            $inscrire_Annonces->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
            $inscrire_Annonces->execute();

            }
        }
    }
    
                                    // AFFICHER DES BDD
    if ($_GET['action'] == 'update') {
        $queryAnnonces = $pdo->query("SELECT * FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
        $annonceActuel = $queryAnnonces->fetch(PDO::FETCH_ASSOC);

        $queryPhoto = $pdo->query("SELECT * FROM photo");
        $photoActuel = $queryPhoto->fetch(PDO::FETCH_ASSOC);
    }
    $id_annonce = (isset($annonceActuel['id_annonce'])) ? $annonceActuel['id_annonce'] : "";
    $categorie = (isset($annonceActuel['categorie'])) ? $annonceActuel['categorie'] : "";
    $titre = (isset($annonceActuel['titre'])) ? $annonceActuel['titre'] : "";
    $description_courte = (isset($annonceActuel['description_courte'])) ? $annonceActuel['description_courte'] : "";
    $description_longue = (isset($annonceActuel['description_longue'])) ? $annonceActuel['description_longue'] : "";
    $prix = (isset($annonceActuel['prix'])) ? $annonceActuel['prix'] : "";   
    $pays = (isset($annonceActuel['pays'])) ? $annonceActuel['pays'] : "";
    $ville = (isset($annonceActuel['ville'])) ? $annonceActuel['ville'] : "";
    $adresse = (isset($annonceActuel['adresse'])) ? $annonceActuel['adresse'] : "";
    $cp = (isset($annonceActuel['cp'])) ? $annonceActuel['cp'] : ""; 

                                    // Affichage des photos
    $photo_bdd1 = (isset($photoActuel['photo1'])) ? $photoActuel['photo1'] : "";
    $photo_bdd2 = (isset($photoActuel['photo2'])) ? $photoActuel['photo2'] : "";
    $photo_bdd3 = (isset($photoActuel['photo3'])) ? $photoActuel['photo3'] : "";
    $photo_bdd4 = (isset($photoActuel['photo4'])) ? $photoActuel['photo4'] : "";
    $photo_bdd5 = (isset($photoActuel['photo5'])) ? $photoActuel['photo5'] : "";
    


                                        // DELETE
    if($_GET['action'] == 'delete'){    
        $pdo->query(" DELETE FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
    }
}
require_once('includeAdmin/header.php');
?>

<?= $erreur ?>
<?= $content ?>
<!-- utilisation de la fonction personnalisée debug pour savoir ce qui a été récupéré avec $_POST, pour comprendre en cas de problème, ou se situe le problème -->
<!-- <?= debug($_POST) ?> -->


<?php if(isset($_GET['action'])): ?>

                    <!-- ********** Tableau de modification ************* -->

<form id="monForm" class="my-5" method="POST" action="" enctype="multipart/form-data">

                                        <!-- id_annonce -->
    <input type="hidden" name="id_annonce" value="<?= $id_annonce ?>">


                                <!-- -----Titre-Prix-categorie----- -->
    <div class="row mt-5">
                                        <!-- *** -->
        <div class="col-md-4">
            <label class="form-label" for="titre">
                <div class="badge badge-dark text-wrap">Titre</div>
            </label>
            <input class="form-control" type="text" name="titre" id="titre" placeholder="Titre" value="<?= $titre ?>">
        </div>
                                        <!-- *** -->
        <div class="col-md-4">
            <label class="form-label" for="prix">
                <div class="badge badge-dark text-wrap">Prix</div>
            </label>
            <input class="form-control" type="text" name="prix" id="prix" placeholder="Prix" value="<?= $prix ?>">
        </div>
                                        <!-- *** -->
        <div class="col-md-4">
            <label class="form-label" for="categorie">
                <div class="badge badge-dark text-wrap">Catégorie</div>
            </label>
           
            <select class="form-control"  name="categorie" id="categorie">
                <?php  
                    $listeCategorie = $pdo->query('SELECT * FROM categorie' );         
                    while($categorie = $listeCategorie->fetch(PDO::FETCH_ASSOC)){
                    echo "<option value='$categorie[id_categorie]'> $categorie[titre] </option> ";
                }?>
            </select>
        </div>
    </div>


                                        <!-- Déscription -->
    <div>
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
    </div>
    

                                            <!-- photo -->
    <div class="row">
                                        <!-- 1 -->
        <div class="col-md-4">
            <label class="form-label" for="photo1">
                <div class="badge badge-dark text-wrap">Photo 1</div>
            </label>
            <input class="form-control" type="file" name="photo1" id="photo1" placeholder="Photo1" >
        </div>
                                        
        <?php if(!empty($photo_bdd1)): ?>
        <div class="mt-4">
                <img src="<?= URL . 'img/' . $photo_bdd1 ?>" width="50px">
           
        </div>
        <?php endif; ?>
        <input type="hidden" name="photoActuelle" value="<?= $photo_bdd1 ?>">

                                        <!-- 2 -->
        <div class="col-md-4">
            <label class="form-label" for="photo1">
                <div class="badge badge-dark text-wrap">Photo 2</div>
            </label>
            <input class="form-control" type="file" name="photo2" id="photo2" placeholder="Photo2" value='<?= $photo_bdd2 ?>' >
        </div>
                                        
        <?php if(!empty($photo_bdd2)): ?>
        <div class="mt-4">
                <img src="<?= URL . 'img/' . $photo_bdd2 ?>" width="50px">
        </div>
        <?php endif; ?>
        <input type="hidden" name="photoActuelle" value="<?= $photo_bdd2 ?>">

                                        <!-- 3 -->
        <div class="col-md-4">
            <label class="form-label" for="photo1">
                <div class="badge badge-dark text-wrap">Photo 3</div>
            </label>
            <input class="form-control" type="file" name="photo3" id="photo3" placeholder="Photo3" value='<?= $photo_bdd3 ?>'>
        </div>
                                        
        <?php if(!empty($photo_bdd3)): ?>
        <div class="mt-4">
                <img src="<?= URL . 'img/' . $photo_bdd3 ?>" width="50px">
            
        </div>
        <?php endif; ?>
        <input type="hidden" name="photoActuelle" value="<?= $photo_bdd3 ?>">

                                        <!-- 4 -->
        <div class="col-md-4">
            <label class="form-label" for="photo4">
                <div class="badge badge-dark text-wrap">Photo 4</div>
            </label>
            <input class="form-control" type="file" name="photo4" id="photo4" placeholder="Photo4" value='<?= $photo_bdd4 ?>'>
        </div>
                                        
        <?php if(!empty($photo_bdd4)): ?>
        <div class="mt-4">
                <img src="<?= URL . 'img/' . $photo_bdd4 ?>" width="50px">           
        </div>
        <?php endif; ?>
        <input type="hidden" name="photoActuelle" value="<?= $photo4 ?>">

                                        <!-- 5 -->
        <div class="col-md-4">
            <label class="form-label" for="photo1">
                <div class="badge badge-dark text-wrap">Photo 5</div>
            </label>
            <input class="form-control" type="file" name="photo5" id="photo5" placeholder="Photo5" value='<?= $photo_bdd5 ?>'>
        </div>
                                        
        <?php if(!empty($photo_bdd5)): ?>
        <div class="mt-4">
                <img src="<?= URL . 'img/' . $photo_bdd5 ?>" width="50px">
        </div>
        <?php endif; ?>
        <input type="hidden" name="photoActuelle" value="<?= $photo_bdd5 ?>">
       
    </div>
    
                                        <!-- Pay Ville cp -->
    <div class="row mt-5">
            <div class="col-md-4">                                                                          
                <label class="form-label" for="pays">
                    <div class="badge badge-dark text-wrap">Pays</div>
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
            <th colspan=3>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($annonce = $afficheAnnonces->fetch(PDO::FETCH_ASSOC)) : ?>
            <tr>
                <?php foreach ($annonce as $key => $value) : ?>
                    <?php if ($key == 'prix') : ?>
                        <td><?= $value ?> €</td>
                    <?php elseif ($key == 'photo') : ?>
                        <td>
                        <img class="img-fluid" src="<?= URL . 'img/' . $value?>" loading="lazy">
                            
                        </td>
                    <?php else : ?>
                        <td><?= $value ?></td>
                    <?php endif; ?>
                <?php endforeach; ?>

                <td><a href='?action=update&id_annonce=<?= $annonce['id_annonce'] ?>'><i class="bi bi-pen-fill text-warning"></i></a></td>

                <td><a href=' location: . URL . /fiche_annonce/id_annonce=<?= $annonce['id_annonce'] ?>'><i class="bi bi-zoom-in"></i></a></td>
                
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