<?php
require_once('include/init.php');
require_once('include/affichage.php');


// $pageTitle = "Profil de " . $_SESSION['membre']['pseudo'];

// si le user n'est PAS connecté, alors on lui interdit l'accès à la page profil (redirection vers la page connexion ou autre selon reflexion)

if(!internauteConnecte()){
    header('location' . URL . 'connexion.php');
    exit();
}
if(isset($_GET['action']) && $_GET['action'] == 'validate') {

    $validate .= '<div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                    Félicitations, vous etes connecté 😉 !
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
}

$content .= '<div class="alert alert-success alert-dismissible fade show
mt-5" role="alert">
<strong>Félicitations !</strong> Votre profil a été modifier avec succès 😉 !
<button type="button" class="close" data-dismiss="alert" arialabel="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>';  

require_once('include/header.php');
?>

<?= $validate ?>

<!-- FORM PROFIL UTILISATEUR -->
<div class="container my-5">
		<div class="row justify-content-center">
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<h1 class="text-center text-wrap">Bonjour <?= (internauteConnecteAdmin()) ? $_SESSION['membre']['pseudo'] .' ! <br> ' . "Vous êtes admin du site" : $_SESSION['membre']['pseudo'] ?></h1>
					</div>
					<div class="card-body">
						<div class="row justify-content-around py-5">
							<div class="col-md-8">
							<form action="profil_modif.php" method="POST">
                        <div class="mb-3">
                            <label for="nouveau_pseudo" class="form-label">Pseudo</label>
                            <input type="text" class="form-control" id="nouveau_pseudo" name="nouveau_pseudo" value="<?= $_SESSION['membre']['pseudo'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="nouveau_nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nouveau_nom" name="nouveau_nom" value="<?= $_SESSION['membre']['nom'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="nouveau_prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="nouveau_prenom" name="nouveau_prenom" value="<?= $_SESSION['membre']['prenom'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="nouveau_mail" class="form-label">Adresse email</label>
                            <input type="email" class="form-control" id="nouveau_email" name="nouveau_email" value="<?= $_SESSION['membre']['email'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="nouveau_telephone" class="form-label">Numéro de téléphone</label>
                            <input type="tel" class="form-control" id="nouveau_telephone" name="nouveau_telephone" value="<?= $_SESSION['membre']['telephone'] ?>">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark btn-outline-primary">Enregistrer les modifications</button>
                        </div>
                    </form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    </div>
	<!-- Bouton pour déposer une annonce -->
    <div class="container my-5 ">
        <div class="row justify-content-center"><a href="<?= URL ?>depose_annonce.php"><button class="shadow btn btn-dark btn-outline-primary btn-16 px-4  ">Déposez votre annonce</button></a></div>
    </div>
    
	<?php
// Récupération des annonces du membre
$id_membre = $_SESSION['membre']['id_membre'];
$recup = $pdo->prepare("SELECT * FROM annonce WHERE membre_id = ?");
$recup->execute([$id_membre]);
$annonces = $recup->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Affiches des annonces du membre -->

<div class="row justify-content-center py-5">
    <div class="col-md-6">
        <h2 class="mb-4">Vos annonces</h2>
        <?php foreach ($annonces as $annonce) { ?>
            <div class="card mb-2 ">
                <div class="card-body ">
                    <h5 class="card-title"><?= $annonce['titre'] ?></h5>
                    <?php if (!empty($annonce['photo'])) { ?>
                        <img src="<?= URL . 'img/' . $annonce['photo'] ?>" class="card-img-top mb-3" alt="Photo de l'annonce">
                    <?php } ?>
                    <p class="card-text"><?= $annonce['description_longue'] ?></p>
                    <p class="card-text"><small class="text-muted">Publiée le <?= $annonce['date_enregistrement'] ?></small></p>
					<!-- Bouton pour voir ou supprimer l'annonce -->
					<a href="fiche_annonce.php?id_annonce=<?= $annonce['id_annonce']?>" class="btn btn-sm btn-primary">Voir l'annonce</a>
                    <a data-href="?action=delete&id_annonce=<?= $annonce['id_annonce'] ?>" class="btn btn-sm btn-danger">Supprimer</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>



<?php require_once('include/footer.php'); ?>