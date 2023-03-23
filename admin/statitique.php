<?php

require_once('../include/init.php');

// Vérifier la connexion
if (!internauteConnecteAdmin()) {
    header('location:' . URL . 'connexion.php');
    exit();
}




// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Requête pour récupérer les 5 membres les mieux notés
$sql = "SELECT membre_id1, AVG(note) as note_moyenne
        FROM note
        GROUP BY membre_id1
        ORDER BY note_moyenne DESC
        LIMIT 5";

// Exécuter la requête
$resultat = mysqli_query($conn, $sql);

// Tableau pour stocker les données
$membres_notes = array();

// Ajouter les résultats au tableau
if (mysqli_num_rows($resultat) > 0) {
    while ($row = mysqli_fetch_assoc($resultat)) {
        $membres_notes[] = array(
            "id" => $row["membre_id1"],
            "note" => $row["note_moyenne"]
        );
    }
}

// Requête SQL pour récupérer les 5 annonces les plus anciennes
$sql = "SELECT *
        FROM annonce
        ORDER BY date_enregistrement ASC
        LIMIT 5;";
$result = $conn->query($sql);

// Tableau pour stocker les données
$annonces_anciennes = array();

// Ajouter les résultats au tableau
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $annonces_anciennes[] = array(
            "titre" => $row["titre"],
            "description_courte" => $row["description_courte"],
            "description_longue" => $row["description_longue"],
            "prix" => $row["prix"],
            "photo" => $row["photo"],
            "pays" => $row["pays"],
            "ville" => $row["ville"],
            "adresse" => $row["adresse"],
            "cp" => $row["cp"],
            "membre_id" => $row["membre_id"],
            "photo_id" => $row["photo_id"],
            "categorie_id" => $row["categorie_id"],
            "date_enregistrement" => $row["date_enregistrement"]
        );
    }
}

// Requête SQL pour récupérer les 5 membres les plus actifs
$sql = "SELECT m.id_membre, m.pseudo, COUNT(a.id_annonce) as nb_annonces
        FROM membre m
        LEFT JOIN annonce a ON m.id_membre = a.membre_id
        GROUP BY m.id_membre
        ORDER BY nb_annonces DESC
        LIMIT 5;";
$result = $conn->query($sql);

// Tableau pour stocker les données
$membres_actifs = array();

// Ajouter les résultats au tableau
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $membres_actifs[] = array(
            "id" => $row["id_membre"],
            "pseudo" => $row["pseudo"],
            "nb_annonces" => $row["nb_annonces"]
        );
    }
}



// Requête SQL pour récupérer le top 5 des catégories contenant le plus d'annonces
$sql = "SELECT categorie_id, COUNT(*) AS nb_annonces 
        FROM annonce 
        GROUP BY categorie_id 
        ORDER BY nb_annonces DESC 
        LIMIT 5";
$result = $conn->query($sql);

// Vérification des résultats et stockage des données dans un tableau
$categories = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categorie_id = $row["categorie_id"];
        $nb_annonces = $row["nb_annonces"];
        
        // Requête SQL pour récupérer le nom de la catégorie
        $sql_categorie = "SELECT titre FROM categorie WHERE id_categorie = $categorie_id";
        $result_categorie = $conn->query($sql_categorie);
        $row_categorie = $result_categorie->fetch_assoc();
        $titre_categorie = $row_categorie["titre"];
        
        // Stockage des données dans un tableau
        $categories[] = array(
            "categorie" => $titre_categorie,
            "nb_annonces" => $nb_annonces
        );
    }
} else {
    echo "0 résultats";
}

// Fermeture de la connexion à la base de données
$conn->close();

require_once('includeAdmin/header.php');
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Membres les mieux notés</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Note moyenne</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($membres_notes as $membre_note) : ?>
                        <tr>
                            <td><?= $membre_note['id'] ?></td>
                            <td><?= $membre_note['note'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-12 table-responsive">
            <h2>Annonces les plus anciennes</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Description courte</th>
                        <th>Description longue</th>
                        <th>Prix</th>
                        <th>Photo</th>
                        <th>Pays</th>
                        <th>Ville</th>
                        <th>Adresse</th>
                        <th>Code postal</th>
                        <th>Membre ID</th>
                        <th>Photo ID</th>
                        <th>Catégorie ID</th>
                        <th>Date d'enregistrement</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($annonces_anciennes as $annonce) : ?>
                        <tr>
                            <td><?= $annonce['titre'] ?></td>
                            <td><?= $annonce['description_courte'] ?></td>
                            <td><?= $annonce['description_longue'] ?></td>
                            <td><?= $annonce['prix'] ?></td>
                            <td><?= $annonce['photo'] ?></td>
                            <td><?= $annonce['pays'] ?></td>
                            <td><?= $annonce['ville'] ?></td>
                            <td><?= $annonce['adresse'] ?></td>
                            <td><?= $annonce['cp'] ?></td>
                            <td><?= $annonce['membre_id'] ?></td>
                            <td><?= $annonce['photo_id'] ?></td>
                            <td><?= $annonce['categorie_id'] ?></td>
                            <td><?= $annonce['date_enregistrement'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2>Membres les plus actifs</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pseudo</th>
                        <th>Nombre d'annonces</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($membres_actifs as $membre_actif) : ?>
                        <tr>
                            <td><?= $membre_actif['id'] ?></td>
                            <td><?= $membre_actif['pseudo'] ?></td>
                            <td><?= $membre_actif['nb_annonces'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<div class="col-md-12 table-responsive">
            <h2>Catégories avec le plus d'annonces</h2>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Catégorie</th>
                        <th>Nombre d'annonces</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $categorie) : ?>
                        <tr>
                            <td><?= $categorie["categorie"] ?></td>
                            <td><?= $categorie["nb_annonces"] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
<?php require_once('includeAdmin/footer.php'); ?>