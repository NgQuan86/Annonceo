<?php
require_once('../include/init.php');

if (!internauteConnecteAdmin()) {
    header('location:' . URL . 'connexion.php');
    exit();
}

if(isset($_GET['page']) && !empty($_GET['page'])){
   
    $pageCourante = (int) strip_tags($_GET['page']);
}else{
   
    $pageCourante = 1;
}
$queryAnnonces = $pdo->query(" SELECT COUNT(id_annonce) AS nombreAnnonces FROM annonce ");

$resultatAnnonces = $queryAnnonces->fetch();
$nombreAnnonces = (int) $resultatAnnonces['nombreAnnonces'];
$parPage = 10;
$nombrePages = ceil($nombreAnnonces / $parPage);
$premierAnnonce = ($pageCourante - 1) * $parPage;

// fin de pagination

if (isset($_GET['action'])) {

    if ($_POST) {

        if (!isset($_POST['titre']) || !preg_match('#^[a-zA-Z0-9]{4,200}$#', $_POST['titre'])) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format référence !</div>';
        }

        if (!isset($_POST['categorie']) || strlen($_POST['categorie']) < 3 || strlen($_POST['categorie']) > 20) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format categorie !</div>';
        }

        if (!isset($_POST['titre']) || strlen($_POST['titre']) < 3 || strlen($_POST['titre']) > 20) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format titre !</div>';
        }

        if (!isset($_POST['description']) || strlen($_POST['description']) < 3 || strlen($_POST['description']) > 50) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format description !</div>';
        }

        if (!isset($_POST['couleur']) || $_POST['couleur'] != 'bleu' && $_POST['couleur'] != 'rouge' && $_POST['couleur'] != 'vert' && $_POST['couleur'] != 'jaune' && $_POST['couleur'] != 'blanc' && $_POST['couleur'] != 'noir' && $_POST['couleur'] != 'marron') {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format couleur !</div>';
        }

        if (!isset($_POST['taille']) || $_POST['taille'] != 'small' && $_POST['taille'] != 'medium' && $_POST['taille'] != 'large' && $_POST['taille'] != 'xlarge') {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format taille !</div>';
        }

        if (!isset($_POST['public']) || $_POST['public'] != 'enfant' && $_POST['public'] != 'femme' && $_POST['public'] != 'homme' && $_POST['public'] != 'mixte') {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format public !</div>';
        }

        if (!isset($_POST['prix']) || !preg_match('#^[0-9]{1,5}$#', $_POST['prix'])) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format prix !</div>';
        }

        if (!isset($_POST['stock']) || !preg_match('#^[0-9]{1,5}$#', $_POST['stock'])) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format stock !</div>';
        }

        // traitement pour la photo
        // initialisation de la variable à vide
        $photo_bdd = "";

        // condition pour modifier une photo
        if($_GET['action'] == 'update'){
            // A mettre en relation avec la nouvelle photo que l'on veut insérer en BDD pour remplacer l'ancienne
            $photo_bdd = $_POST['photoActuelle'];
        }


        if(!empty($_FILES['photo']['name'])){
            // je donne un nom à la photo que je vais ajouter en concaténant le nom de la référence du annonce, avec le nom du fichier photo d'origine (les deux étant séparés d'un underscore ( _ ) )
            $photo_nom = $_POST['titre'] . '_' . $_FILES['photo']['name'];
            // utilisation de la variable photo_bdd pour lui affecter la valeur de photo_nom, sous forme de chaine de caractères (pour les bindValue)
            $photo_bdd = "$photo_nom";
            // declaration d'une variable qui va enregistrer le chemin ou uploader notre fichier (les photos vont aller dans le dossier img de notre projet, en local comme en ligne lorsque le site sera hébergé)
            $photo_dossier = RACINE_SITE . "img/$photo_nom";
            // processus d'envoi du fichier vers le dossier img, en passant par la fonction prédéfinie copy qui va donner un nom temporaire au fichier, avec de l'uploader dans le dossier img avec son nom définitif ($photo_nom)
            copy($_FILES['photo']['tmp_name'], $photo_dossier);
        }

        // fin traitement pour la photo

        if (empty($erreur)) {
            if ($_GET['action'] == 'update') {
                $modifAnnonce = $pdo->prepare(" UPDATE annonce SET id_annonce = :id_annonce , titre = :titre, categorie = :categorie, titre = :titre, description = :description, couleur = :couleur, taille = :taille, public = :public, photo = :photo, prix = :prix, stock = :stock WHERE id_annonce = :id_annonce ");
                $modifAnnonce->bindValue(':id_annonce', $_POST['id_annonce'], PDO::PARAM_INT);
                $modifAnnonce->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':public', $_POST['public'], PDO::PARAM_STR);
                $modifAnnonce->bindValue(':photo', $photo_bdd, PDO::PARAM_STR);
                $modifAnnonce->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
                $modifAnnonce->bindValue(':stock', $_POST['stock'], PDO::PARAM_INT);
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
                $inscrireAnnonce = $pdo->prepare(" INSERT INTO annonce (titre, categorie, titre, description, couleur, taille, public, photo, prix, stock) VALUES (:titre, :categorie, :titre, :description, :couleur, :taille, :public, :photo, :prix, :stock) ");
                $inscrireAnnonce->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
                $inscrireAnnonce->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
                $inscrireAnnonce->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
                $inscrireAnnonce->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
                $inscrireAnnonce->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
                $inscrireAnnonce->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
                $inscrireAnnonce->bindValue(':public', $_POST['public'], PDO::PARAM_STR);
                // pour le bindValue de la photo, on ne peut utiliser $_POST['photo'] pour le pointeur nommé :photo. On doit donner une chaine de caractères (affectée à $photo_bdd, voir plus en haut)
                $inscrireAnnonce->bindValue(':photo', $photo_bdd, PDO::PARAM_STR);
                $inscrireAnnonce->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
                $inscrireAnnonce->bindValue(':stock', $_POST['stock'], PDO::PARAM_INT);
                $inscrireAnnonce->execute();
            }
        }
    }

    if ($_GET['action'] == 'update') {
        $queryAnnonces = $pdo->query("SELECT * FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
        $annonceActuel = $queryAnnonces->fetch(PDO::FETCH_ASSOC);
    }

    $id_annonce = (isset($annonceActuel['id_annonce'])) ? $annonceActuel['id_annonce'] : "";
    $titre = (isset($annonceActuel['titre'])) ? $annonceActuel['titre'] : "";
    $categorie = (isset($annonceActuel['categorie'])) ? $annonceActuel['categorie'] : "";
    $titre = (isset($annonceActuel['titre'])) ? $annonceActuel['titre'] : "";
    $description = (isset($annonceActuel['description'])) ? $annonceActuel['description'] : "";
    $couleur = (isset($annonceActuel['couleur'])) ? $annonceActuel['couleur'] : "";
    $taille = (isset($annonceActuel['taille'])) ? $annonceActuel['taille'] : "";
    $public = (isset($annonceActuel['public'])) ? $annonceActuel['public'] : "";
    $photo = (isset($annonceActuel['photo'])) ? $annonceActuel['photo'] : "";
    $prix = (isset($annonceActuel['prix'])) ? $annonceActuel['prix'] : "";
    $stock = (isset($annonceActuel['stock'])) ? $annonceActuel['stock'] : "";

    if($_GET['action'] == 'delete'){
        $pdo->query(" DELETE FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
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

<!-- l'attribut enctype de la balise form permet l'envoi d'un fichier en upload, il est obligatoire, sinon on ne pourra envoyer le fichier image correspondant au annonce -->
<form id="monForm" class="my-5" method="POST" action="" enctype="multipart/form-data">

    <input type="hidden" name="id_annonce" value="<?= $id_annonce ?>">

    <div class="row mt-5">
        <div class="col-md-4">
            <label class="form-label" for="titre">
                <div class="badge badge-dark text-wrap">Référence</div>
            </label>
            <input class="form-control" type="text" name="titre" id="titre" placeholder="Référence" value="<?= $titre ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label" for="categorie">
                <div class="badge badge-dark text-wrap">Catégorie</div>
            </label>
            <input class="form-control" type="text" name="categorie" id="categorie" placeholder="Catégorie"  value="<?= $categorie ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label" for="titre">
                <div class="badge badge-dark text-wrap">Titre</div>
            </label>
            <input class="form-control" type="text" name="titre" id="titre" placeholder="Titre" value="<?= $titre ?>">
        </div>
    </div>

    <div class="row justify-content-around mt-5">
        <div class="col-md-6">
            <label class="form-label" for="description">
                <div class="badge badge-dark text-wrap">Description</div>
            </label>
            <textarea class="form-control" name="description" id="description" placeholder="Description" rows="5"><?= $description ?></textarea>
        </div>
    </div>

    <div class="row mt-5">

        <div class="col-md-4 mt-3">
            <label class="badge badge-dark text-wrap" for="couleur">Couleur</label>
            <select class="form-control" name="couleur" id="couleur">
                <option value="">Choisissez</option>
                <option class="bg-primary text-light" value="bleu" <?= ($couleur == 'bleu') ? 'selected' : '' ?>>Bleu</option>
                <option class="bg-danger text-light" value="rouge" <?= ($couleur == 'rouge') ? 'selected' : '' ?>>Rouge</option>
                <option class="bg-success text-light" value="vert" <?= ($couleur == 'vert') ? 'selected' : '' ?>>Vert</option>
                <option class="bg-warning text-light" value="jaune" <?= ($couleur == 'jaune') ? 'selected' : '' ?>>Jaune</option>
                <option class="bg-light text-dark" value="blanc" <?= ($couleur == 'blanc') ? 'selected' : '' ?>>Blanc</option>
                <option class="bg-dark text-light" value="noir" <?= ($couleur == 'noir') ? 'selected' : '' ?>>Noir</option>
                <option class="text-light" style="background:brown;" value="marron" <?= ($couleur == 'noir') ? 'selected' : '' ?>>Marron</option>
            </select>
        </div>

        <div class="col-md-4">
            <p>
            <div class="badge badge-dark text-wrap">Taille</div>
            </p>

            <input type="radio" name="taille" id="taille1" value="small" <?= ($taille == 'small') ? 'checked' : '' ?>>
            <label class="mx-1" for="taille1">Small</label>

            <input type="radio" name="taille" id="taille2" value="medium" <?= ($taille == 'medium') ? 'checked' : '' ?>>
            <label class="mx-1" for="public2">Medium</label>

            <input type="radio" name="taille" id="taille3" value="large" <?= ($taille == 'large') ? 'checked' : '' ?>>
            <label class="mx-1" for="taille3">Large</label>

            <input type="radio" name="taille" id="taille4" value="xlarge" <?= ($taille == 'xlarge') ? 'checked' : '' ?>>
            <label class="mx-1" for="taille4">XLarge</label>
        </div>

        <div class="col-md-4">
            <p>
            <div class="badge badge-dark text-wrap">Public</div>
            </p>

            <input type="radio" name="public" id="public1" value="enfant" <?= ($public == 'enfant') ? 'checked' : '' ?>>
            <label class="mx-1" for="public1">Enfant</label>

            <input type="radio" name="public" id="public2" value="femme" <?= ($public == 'femme') ? 'checked' : '' ?>>
            <label class="mx-1" for="public2">Femme</label>

            <input type="radio" name="public" id="public3" value="homme" <?= ($public == 'homme') ? 'checked' : '' ?>>
            <label class="mx-1" for="public3">Homme</label>

            <input type="radio" name="public" id="public4" value="mixte" <?= ($public == 'mixte') ? 'checked' : '' ?>>
            <label class="mx-1" for="public4">Mixte</label>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-4">
            <label class="form-label" for="photo">
                <div class="badge badge-dark text-wrap">Photo</div>
            </label>
            <input class="form-control" type="file" name="photo" id="photo" placeholder="Photo">
        </div>
        <!-- ----------------- -->
        <!-- si la variable $photo a trouvé une information en BDD, on exécute ce qui suit dans les accolades -->
        <?php if(!empty($photo)): ?>
        <div class="mt-4">
            <p>Vous pouvez changer d'image
                <img src="<?= URL . 'img/' . $photo ?>" width="50px">
            </p>
        </div>
        <?php endif; ?>
        <!-- pour modifier la photo existante par une nouvelle (voir ligne 56) -->
        <input type="hidden" name="photoActuelle" value="<?= $photo ?>">
        <!-- -------------------- -->
        <div class="col-md-4">
            <label class="form-label" for="prix">
                <div class="badge badge-dark text-wrap">Prix</div>
            </label>
            <input class="form-control" type="text" name="prix" id="prix" placeholder="Prix" value="<?= $prix ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label" for="stock">
                <div class="badge badge-dark text-wrap">Stock</div>
            </label>
            <input class="form-control" type="text" name="stock" id="stock" placeholder="Stock" value="<?= $stock ?>">
        </div>
    </div>

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
        <!-- dans la cas ou nous sommes sur la page 1, il ne faudra pas pouvoir cliquer sur l'onglet précedent, sinon, on sera expédié à la page 0 ! Il faut donc dans ce cas (voir la ternaire) si on est sur la page 1, que l'onglet soit non-cliquable, grace à la classe Bootstrap 'disabled' -->
        <li class="page-item <?= ($pageCourante == 1) ? 'disabled' : "" ?>">
        <!-- si on clique la fleche précédente, c'est pour aller à la page précédent
    dans ce cas, on soustrait à $pageCourante, la valeur de 1 (si pageCourante = 4, on retournera à la page 3) -->
            <a class="page-link text-dark" href="?page=<?= $pageCourante - 1 ?>" aria-label="Previous">
                <span aria-hidden="true">précédente</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>

        <!-- affiche le nb de pages pour cliquer sur celle que l'on veut -->
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