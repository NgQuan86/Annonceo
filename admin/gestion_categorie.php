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

$queryCate = $pdo->query(" SELECT COUNT(id_categorie) AS nbCate FROM categorie ");
$resultCate = $queryCate->fetch();
$nbCate = (int) $resultCate['nbCate'];
echo debug($nbCate);
$parPage = 5;
$nbPages = ceil($nbCate / $parPage);
$premierCate = ($pageCourante - 1) * $parPage;



                                // ****************** TRAITEMENT DES INFOS *******************
if (isset($_GET['action'])) {

                                                // Contrainte
    if ($_POST) {
        if (!isset($_POST['titre']) || iconv_strlen($_POST['titre']) < 3 || iconv_strlen($_POST['titre']) > 20) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format nom !</div>';
        }
        if (!isset($_POST['motscles']) || iconv_strlen($_POST['motscles']) < 3 || iconv_strlen($_POST['motscles']) > 100) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format prénom !</div>';
        }
        

                                          // ------ ACTION -------
        if (empty($erreur)) {
                                        //  procédure de modification
            if ($_GET['action'] == 'update') {
                $modifCate = $pdo->prepare(" UPDATE categorie SET id_categorie = :id_categorie , titre = :titre, motscles = :motscles WHERE id_categorie = :id_categorie ");
                $modifCate->bindValue(':id_categorie', $_POST['id_categorie'], PDO::PARAM_INT);
                $modifCate->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
                $modifCate->bindValue(':motscles', $_POST['motscles'], PDO::PARAM_STR);
                $modifCate->execute();
                header('Location:' . URL . "admin/gestion_categorie.php");
                exit();
            
                                            //  message de réussite
                $queryCate = $pdo->query(" SELECT titre FROM categorie WHERE id_categorie = '$_GET[id_categorie]' ");
                $nomTitre = $queryCate->fetch(PDO::FETCH_ASSOC);

                $content .= '<div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                        <strong>Félicitations !</strong> Modification de la categorie '. $nomTitre['titre'] .' réussie !
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } 

                                    //  procédure d'insertion en BDD
            else {                 
                $ajoutCate = $pdo->prepare(" INSERT INTO categorie (titre, motscles) VALUE (:titre, :motscles) ");
                $ajoutCate->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
                $ajoutCate->bindValue(':motscles', $_POST['motscles'], PDO::PARAM_STR);
                $ajoutCate->execute();
                header('Location:' . URL . "admin/gestion_categorie.php");
                exit();
            }
        }
    }


                                    // Afficher les infos du BDD dans modif  
    if ($_GET['action'] == 'update') {
        $tousCates = $pdo->query("SELECT * FROM categorie WHERE id_categorie = '$_GET[id_categorie]' ");
        $cateActuel = $tousCates->fetch(PDO::FETCH_ASSOC);
    }
        $id_categorie = (isset($cateActuel['id_categorie'])) ? $cateActuel['id_categorie'] : "";
        $titre = (isset($cateActuel['titre'])) ? $cateActuel['titre'] : "";
        $motscles = (isset($cateActuel['motscles'])) ? $cateActuel['motscles'] : "";
       

    if($_GET['action'] == 'delete'){
        $pdo->query(" DELETE FROM categorie WHERE id_categorie = '$_GET[id_categorie]' ");
    }
}
                            // ************************************************************** //


require_once('includeAdmin/header.php');
?>





                            <!-- ****************** AFFICHER DES INFOS ******************* -->

<?= $erreur ?>
<?= $content ?>
                
                                <!-- BDD pour tableau  -->

<?php $nbCates = $pdo->query("SELECT id_categorie FROM categorie"); ?>

                                <!-- nombre du categorie -->
<h2 class="py-4">Nombre de categorie en base de données: <?= $nbCates->rowCount() ?></h2>

                                <!-- Bouton d'Ajouter -->
<div class="row justify-content-center py-4">
    <a href='?action=add'>
        <button type="button" class="btn btn-sm btn-outline-dark shadow rounded">
            <i class="bi bi-plus-circle-fill"></i> Ajouter un categorie
        </button>
    </a>
</div>


                                    <!-- Tableau du categorie -->
<table class="table table-dark text-center">
    <?php $afficheCates = $pdo->query("SELECT * FROM categorie ORDER BY id_categorie ASC LIMIT $parPage OFFSET $premierCate "); ?>
    
    <thead>
        <tr>
            <?php for ($i = 0; $i < $afficheCates->columnCount(); $i++) :
                $colonne = $afficheCates->getColumnMeta(($i)) ?>
                    <th><?= $colonne['name'] ?></th>
            <?php endfor; ?>
            <th colspan=3>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php while ($categorie = $afficheCates->fetch(PDO::FETCH_ASSOC)) : ?>
            <tr>
                <?php foreach ($categorie as $key => $value) : ?>                 
                        <td><?= $value ?></td>
                <?php endforeach; ?>

                                            <!-- Action mod-search-del -->
                <td>
                    <a href='?action=update&id_categorie=<?= $categorie['id_categorie'] ?>'><i class="bi bi-pen-fill text-warning"></i></a>
                </td>

                <td>
                    <a href='?action=update&id_categorie=<?= $categorie['id_categorie'] ?>'><i class="bi bi-zoom-in"></i></a>
                </td>

                <td>
                    <a data-href="?action=delete&id_categorie=<?= $categorie['id_categorie'] ?>" data-toggle="modal" data-target="#confirm-delete"><i class="bi bi-trash-fill text-danger" style="font-size: 1.5rem;"></i></a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>


                                        <!-- Previous Next  -->
                                     
<nav>

    <ul class="pagination justify-content-end">
        <li class="page-item <?= ($pageCourante == 1) ? 'disabled' : "" ?>">
            <a class="page-link text-dark" href="?page=<?= $pageCourante - 1 ?>" aria-label="Previous">
                <span aria-hidden="true">précédente</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>
                
        <?php for($page = 1; $page <= $nbPages; $page++): ?>
        <li class="mx-1 page-item <?= ($pageCourante == $page ) ? 'active' : "" ?>">
            <a class="btn btn-outline-dark " href="?page=<?= $page ?>"><?= $page ?></a>
        </li>
        <?php endfor; ?>
        <li class="page-item <?= ($pageCourante == $nbPages) ? 'disabled' : "" ?>">
            <a class="page-link text-dark" href="?page=<?= $pageCourante + 1 ?>" aria-label="Next">
                <span aria-hidden="true">suivante</span>
                <span class="sr-only">Next</span>
            </a>
        </li>
    </ul>

</nav>


                                    <!-- ADD & MODIFICATION -->
<?php if (isset($_GET['action'])) : ?>

                                            <!-- Form  -->
    <form class="my-5" method="POST" action="">

        <input type="hidden" name="id_categorie" value="<?= $id_categorie ?>">

                                <!-- * ligne 1 * -->
        <div class="row">
            <div class="col-md-4 mt-5">
                <label class="form-label" for="titre">
                    <div class="badge badge-dark text-wrap">Titre</div>
                </label>
                <input class="form-control" type="text" name="titre" id="titre" placeholder="Titre" value="<?= $titre ?>">
            </div>

            <div class="col-md-4 mt-5">
                <label class="form-label" for="motscles">
                    <div class="badge badge-dark text-wrap">Mots clés</div>
                </label>
                <input class="form-control" type="motscles" name="motscles" id="motscles" placeholder="Mots clés" value="<?= $motscles ?>">
            </div>
        </div>

        <div class="col-md-1 mt-5">
            <button type="submit" class="btn btn-outline-dark btn-warning">Valider</button>
        </div>

    </form>
<?php endif; ?>

                                        <!-- Modal de supprimer -->
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Supprimer Categorie
            </div>
            <div class="modal-body">
                Etes-vous sur?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                <a class="btn btn-danger btn-ok">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<!-- modal -->

<!-- pour empecher la modale de s'ouvrir à chaque rafraichissement de page, le temps de terminer de coder cette page -->
<?php if (!isset($_GET['action']) && !isset($_GET['page'])) : ?>
    <!-- modal infos -->
    <div class="modal fade" id="myModalCates" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-warning" id="exampleModalLabel">Gestion des catégorie</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Gérez ici votre base de données des catégorie</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-warning text-dark" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    <!-- modal -->
<?php endif; ?>

<?php require_once('includeAdmin/footer.php'); ?>