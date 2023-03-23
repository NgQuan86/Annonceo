<?php
require_once('include/init.php');

// recupere les annonces dans la base de donnees
$arrayAnnonce = '';
$afficheAnnonce = '';
$add = '';
// recupere les annonces dans la base de donnees
$afficheAnnonce3 = $pdo->query('SELECT * FROM categorie');
// Sélection des villes
$afficheAnnonce4 = $pdo->query('SELECT DISTINCT ville FROM annonce');
// Obtenir tous les membres
$afficheAnnonce5 = $pdo->query('SELECT * FROM membre');
// Obtenir toutes les annonces
$afficheAnnonce6 = $pdo->query('SELECT * FROM annonce');

// si l'annonce n'existe pas
if (isset($_GET['annonce']) && $_GET['annonce'] == "inexistant") {
    $erreur .= "<div class='col-md-6 mx-auto alert alert-danger text-center disparition'>
                        Annonce inexistante
                    </div>";
}


// AFFICHAGES DE L'ENSEMBLE DES ANNONCES  -->
$toutesAnnonces = array();

if ($_POST) {

    if (!empty($_POST['categorie'])) {
        $categorie = $_POST['categorie'];

        $toutesAnnonces[] = " categorie_id IN (" . $categorie . ") ";
    }
    if (!empty($_POST['region'])) {
        $text = "'";
        $region = $_POST['region'];

        $toutesAnnonces[] = " ville IN (" . $text . $region . $text . ") ";
    }
    if (!empty($_POST['membre'])) {
        $membre = $_POST['membre'];

        $toutesAnnonces[] = " membre_id IN (" . $membre . ") ";
    }
    if (!empty($_POST['prix'])) {
        $prix = $_POST['prix'];

        $toutesAnnonces[] = " prix < " . $prix . " ";
    }

    if (isset($_POST['ordre']) && $_POST['ordre'] == 'prix_ascendant') {
        $add = 'ORDER BY prix ASC';
    }
    if (isset($_POST['ordre']) && $_POST['ordre'] == 'prix_descendant') {
        $add = 'ORDER BY prix DESC';
    }
    if (isset($_POST['ordre']) && $_POST['ordre'] == 'date_ascendant') {
        $add = 'ORDER BY date_enregistrement ASC';
    }
    if (isset($_POST['ordre']) && $_POST['ordre'] == 'date_descendant') {
        $add = 'ORDER BY date_enregistrement ASC';
    }
}
$condition = "";
if(!empty($toutesAnnonces)) {
    $condition = ' AND ';
}
$allAnonnonces = 'SELECT * FROM annonce WHERE 1 ' . $condition . implode(' AND ', $toutesAnnonces) . $add;
$afficheAnnonce = $pdo->query($allAnonnonces);

require_once('include/affichage.php');
require_once('include/header.php');
?>

</div>
<!-- Rubrique des différents catégories -->
<div class="container-fluid d-flex">
		<div class="row justify-content-center mx-auto">
			<div class="col-md-12">
            <ul class="nav nav-pills ">
                <li class="nav-item">
                    <?php  $afficheMenuCategories = $pdo->query(' SELECT * FRom categorie') ?>
                <?php while($menuCategorie = $afficheMenuCategories->fetch(PDO::FETCH_ASSOC)): ?>
                    <a class="btn btn-dark my-2" href="<?= URL ?>?categorie=<?= $menuCategorie['id_categorie'] ?>"><?= $menuCategorie['titre'] ?></a>
                <?php endwhile; ?>
                </li>
            </ul>
			</div>
		</div>
	</div>
        <!-- Bandeau du site Annonceo -->
    <div class=" mx-auto my-5">
        <img class='img-fluid w-100' src="img/banniere_annonceo.png" alt="Bandeau de La Boutique" loading="lazy">
    </div>    

<?= $erreur ?>
<!-- Titre  -->    
<div class="py-5"> 
        <h2 class="text-center pb-5"> Découvrez toutes nos annonces !</h2>
</div>
<?php
while ($arrayAnnonce = $afficheAnnonce->fetch(PDO::FETCH_ASSOC)) :

    $afficheAnnonce2 = $pdo->prepare('SELECT prenom FROM membre WHERE id_membre = :id_membre');
    $afficheAnnonce2->bindValue(':id_membre', $arrayAnnonce['membre_id'], PDO::PARAM_INT);
    $afficheAnnonce2->execute();

    $arrayMembre = $afficheAnnonce2->fetch(PDO::FETCH_ASSOC);

    $allPhotos = $pdo->prepare('SELECT * FROM photo WHERE id_photo = :id_photo');
    $allPhotos->bindValue(':id_photo', $arrayAnnonce['photo_id'], PDO::PARAM_INT);
    $allPhotos->execute();

    $detail = $allPhotos->fetch(PDO::FETCH_ASSOC);

?>

<!-- AFFICHAGE DES ANNONCES  -->
<div class="container py-5">
<?php $afficheAnnonces = $pdo->query("SELECT * FROM annonce ORDER BY prix ") ?>
    <a class="btn border-bottom col-md-12 mt-1 mb-1  " href="fiche_annonce.php?id_annonce=<?= $arrayAnnonce['id_annonce'] ?>">
                    <div class="row  align-items-center col-md-10  ">
                        <!-- Image -->
                        <div class="col-sm-6 align-self-center ">
                            <?php if ($detail['photo1'] != "") :  ?>
                                <img class='w-100' src="img/<?= $detail['photo1'] ?>" alt="<?= $arrayAnnonce['titre'] ?>" title="<?= $arrayAnnonce['titre'] ?>">
                            <?php else :  ?>
                                <img class='w-50' src="img/" alt="" title="Image par défaut">
                            <?php endif;  ?>
                        </div>
                        <!-- Description -->
                        <div class="col-sm-6">
                            <h6 class="m-2 text-primary text-left"><?= $arrayAnnonce['titre'] ?></h6>
                            <p class="text-left"><?= $arrayAnnonce['description_courte'] ?></p>
                            <div class="row ">
                                <div class="col ">
                                    <p class="text-left"><?= $arrayMembre['prenom'] ?> <?= ($arrayAnnonce['membre_id']) ?><i class="bi bi-star-fill" style="color: #FFD700"></i></p>
                                </div>
                                <div class="col">
                                    <h6 class="m-2"><?= $arrayAnnonce['prix'] ?> €</h6>
                                </div>
                            </div>
                        </div>

                    </div>
                </a>
</div>
<?php endwhile;   ?>


<?php require_once('include/footer.php') ?>
