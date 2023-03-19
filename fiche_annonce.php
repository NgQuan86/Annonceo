<?php
include_once('include/init.php');
// le code PHP du fichier se situera entre init et header

// la superglobale $_GET permet de récupérer les valeurs qui véhiculent dans les paramètres de l'URL
// ici on a un paramètre qui s'appelle id_produit
// sa valeur est définie dans le fichier produits.php : c'est l'id_produit de la table produit

//echo $_GET['id_produit']; // id_produit de la table produit

// pour récupérer les données de l'id_produit dans l'URL on crée la requête de sélection avec pour précision la valeur de l'id_produit qui circule dans l'URL
$arrayAdresse[] = '';

$pdoStatement = $pdoObject->prepare('SELECT * FROM annonce WHERE id_annonce = :id_annonce');
$pdoStatement->bindValue(":id_annonce", $_GET['id_annonce'], PDO::PARAM_INT);
$pdoStatement->execute();

// pour exploiter les valeurs on doit pointer sur la méthode fetch
$arrayannonce = $pdoStatement->fetch(PDO::FETCH_ASSOC);
// si on change la valeur du paramètre id_produit de l'URL et qu'on place un id_produit qui n'existe pas dans la table produit
// alors $arrayProduit sera vide
$pdoStatement2 = $pdoObject->prepare('SELECT * FROM membre INNER JOIN annonce ON membre_id=id_membre  WHERE id_annonce = :id_annonce');
$pdoStatement2->bindValue(":id_annonce", $_GET['id_annonce'], PDO::PARAM_INT);
$pdoStatement2->execute();
$arrayMembre1 = $pdoStatement2->fetch(PDO::FETCH_ASSOC);

$pdoStatement3 = $pdoObject->prepare('SELECT * FROM photo INNER JOIN annonce ON photo_id=id_photo  WHERE id_annonce = :id_annonce');
$pdoStatement3->bindValue(":id_annonce", $_GET['id_annonce'], PDO::PARAM_INT);
$pdoStatement3->execute();
$arrayphoto = $pdoStatement3->fetch(PDO::FETCH_ASSOC);


$pdoStatement4 = $pdoObject->prepare('SELECT * FROM annonce WHERE categorie_id = :categorie_id AND id_annonce != :id_annonce');
$pdoStatement4->bindValue(":categorie_id", $arrayannonce['categorie_id'], PDO::PARAM_INT);
$pdoStatement4->bindValue(":id_annonce", $_GET['id_annonce'], PDO::PARAM_INT);
$pdoStatement4->execute();


$pdoStatement1 = $pdoObject->prepare('SELECT * FROM note WHERE membre_id2 = :id_membre');
$pdoStatement1->bindValue(":id_membre", $arrayannonce['membre_id'], PDO::PARAM_INT);
$pdoStatement1->execute();

$arrayMembreNote = $pdoStatement1->fetch(PDO::FETCH_ASSOC);


$arrayAdresse[] = $arrayannonce['adresse'];
$arrayAdresse[] = $arrayannonce['cp'];
$arrayAdresse[] = $arrayannonce['ville'];


if (isset($_POST['envoyercommentaire']) && $_POST['envoyercommentaire']) {
    $pdoStatement5 = $pdoObject->prepare('INSERT INTO commentaire (membre_id, annonce_id, commentaire, date_enregistrement) VALUES (:membre_id, :annonce_id, :commentaire, :date_enregistrement)');
    $pdoStatement5->bindValue(":membre_id", $_SESSION['membre']['id_membre'], PDO::PARAM_INT);
    $pdoStatement5->bindValue(":annonce_id", $_GET['id_annonce'], PDO::PARAM_INT);
    $pdoStatement5->bindValue(":commentaire", $_POST['messageCommentaire'], PDO::PARAM_STR);
    $pdoStatement5->bindValue(":date_enregistrement", date("Y-m-d H:i:s"), PDO::PARAM_STR);
    // Utiliser DATE_FORMAT pour formater la date en format français
    //$pdoStatement5->bindValue(":date_enregistrement", date("d/m/Y H:i:s"), PDO::PARAM_STR);
    //$pdoStatement5->bindValue(":date_enregistrement", date("d F Y H:i:s"), PDO::PARAM_STR); // Exemple pour la date en format "jour mois année"
    $pdoStatement5->execute();
}

if (isset($_POST['envoyernote']) && $_POST['envoyernote']) {
    $pdoStatement6 = $pdoObject->prepare('INSERT INTO note (membre_id1, membre_id2, note, avis, date_enregistrement) VALUES (:membre_id1, :membre_id2, :note, :avis, :date_enregistrement)');
    $pdoStatement6->bindValue(":membre_id1", $_SESSION['membre']['id_membre'], PDO::PARAM_INT);
    $pdoStatement6->bindValue(":membre_id2", $arrayMembre1['id_membre'], PDO::PARAM_INT);
    $pdoStatement6->bindValue(":note", $_POST['note'], PDO::PARAM_INT);
    $pdoStatement6->bindValue(":avis", $_POST['avis'], PDO::PARAM_STR);
    $pdoStatement6->bindValue(":date_enregistrement", date('Y-m-d H:i:s'), PDO::PARAM_STR);
     // Utiliser DATE_FORMAT pour formater la date en format français
    //$pdoStatement5->bindValue(":date_enregistrement", date("d/m/Y H:i:s"), PDO::PARAM_STR);
    //$pdoStatement5->bindValue(":date_enregistrement", date("d F Y H:i:s"), PDO::PARAM_STR); // Exemple pour la date en format "jour mois année"
    $pdoStatement6->execute();
    
}


$pdoStatement7 = $pdoObject->prepare('SELECT * FROM commentaire WHERE annonce_id = :annonce_id');
$pdoStatement7->bindValue(":annonce_id", $_GET['id_annonce'], PDO::PARAM_INT);
$pdoStatement7->execute();


include_once('include/header.php');
?>

<div class="d-flex justify-content-between">
    <div>
        <h2><?php echo $arrayannonce['titre'] ?></h2>
    </div>
    <!-- Button trigger modal -->
    <div>
        <button type="button" class="btn-dark btn-lg m-2" data-toggle="modal" data-target="#bouton_contacter"><i class="p-2 bi bi-telephone"></i>Contacter <?php echo $arrayMembre1['prenom'] ?></button>

        <!-- Modal -->
        <div class="modal fade" id="bouton_contacter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Contacter <?php echo $arrayMembre1['prenom'] ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php if ((membreConnecte() && $_SESSION['membre']['statut'] == 1) || (adminConnecte())) :
                            if (isset($_POST['envoyer'])) {
                                $to = $arrayMembre1['email'];  // verifier spams si pas recu
                                $from = $_SESSION['membre']['email'];
                                $first_name = $_SESSION['membre']['prenom'];
                                $last_name = $_SESSION['membre']['nom'];
                                $subject = "Form submission";
                                $message = $first_name . " " . $last_name . " wrote the following:" . "\n\n" . $_POST['message'];

                                $headers = "From:" . $from;
                                mail($to, $subject, $message, $headers);
                                echo "<br><div class='text-center text-success'>Message envoyé. Merci " . $first_name . ", Nous vous contacterons sous peu.</div>";
                            }
                        ?>
                            <h3 class="text-center">Numéro de téléphone</h3>

                            <div class="d-flex justify-content-center">
                                <p>

                                    <i class="fas fa-phone"></i>
                                    <a href="tel:<?php echo $arrayMembre1['telephone'] ?>"> <?php echo $arrayMembre1['telephone'] ?></a>
                                </p>
                            </div>
                            <form method="post" class="col-md-12 mx-auto" name="form_envoyer_message">
                                <br>
                                <h3 class="text-center">Envoyer un message</h3>
                                <div class="form-group m-3">
                                    <label for="message"></label>
                                    <textarea class="form-control" name="message" id="message" rows="3" placeholder="Votre message"></textarea>
                                </div>


                                <div class="form-group m-3">
                                    <input type="submit" name="envoyer" class="btn btn-primary col-md-12 mt-3 mb-5" value='Envoyer'>
                                </div>

                            </form>
                        <?php else : ?>
                            <?php
                            if (isset($_POST['envoyer'])) {
                                $to = $arrayMembre1['email'];  // verifier spams si pas recu
                                $from = $_POST['email'];
                                $first_name = $_POST['prenom'];
                                $last_name = $_POST['nom'];
                                $subject = "Form submission";
                                $message = $first_name . " " . $last_name . " wrote the following:" . "\n\n" . $_POST['message'];

                                $headers = "From:" . $from;
                                mail($to, $subject, $message, $headers);
                                echo "<br><div class='text-center text-success'>Message envoyé. Merci " . $first_name . ", Nous vous contacterons sous peu.</div>";
                            }
                            ?>
                            <h3 class="text-center">Numéro de téléphone</h3>

                            <div class="d-flex justify-content-center">
                                <p>
                                    <i class="bi bi-telephone"></i>
                                    <a href="tel:<?php echo $arrayMembre1['telephone'] ?>"> <?php echo $arrayMembre1['telephone'] ?></a>
                                </p>
                            </div>
                            <form method="post" class="col-md-12 mx-auto" name="form_envoyer_message">
                                <br>

                                <h3 class="text-center">Envoyer un message</h3>

                                <div class="form-group m-3">
                                    <label for="prenom"></label>
                                    <input type="text" class="form-control bg-white" id='prenom' name="prenom" placeholder="Votre prénom">
                                </div>

                                <div class="form-group m-3">
                                    <label for="nom"></label>
                                    <input type="text" class="form-control bg-white" id='nom' name="nom" placeholder="Votre nom">
                                </div>


                                <div class="form-group m-3">
                                    <label for="email"></label>
                                    <input type="email" class="form-control bg-white" id='email' name="email" placeholder="Votre email">
                                </div>
                                <div class="form-group m-3">
                                    <label for="message"></label>
                                    <textarea class="form-control" name="message" id="message" rows="3" placeholder="Votre message"></textarea>
                                </div>


                                <div class="form-group m-3">
                                    <input type="submit" name="envoyer" class="btn btn-primary col-md-12 mt-3 mb-5" value='Envoyer'>
                                </div>

                            </form>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<br><br>
<div class="row ">
    <div class="col">
        <!-- carrousel photos -->
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="4"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="5"></li>
            </ol>
            <div class="carousel-inner ">

                <div class="carousel-item active">
                    <img class="d-block w-100" src="images/imagesUpload/<?= $arrayphoto['photo1'] ?>" alt="Second slide">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="images/imagesUpload/<?= $arrayphoto['photo2'] ?>" alt="Second slide">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="images/imagesUpload/<?= $arrayphoto['photo3'] ?>" alt="Second slide">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="images/imagesUpload/<?= $arrayphoto['photo4'] ?>" alt="Second slide">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="images/imagesUpload/<?= $arrayphoto['photo5'] ?>" alt="Second slide">
                </div>

            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>

    </div>

<!-- PARTIE DESCRIPTION ET PROFIL -->
    <div class="col ">
        <p class="h5 "><?= $arrayannonce['description_longue'] ?></p>
        <div class="justify-content-center  ">
            <br><br>
            <div class="">
                <p><i class="bi bi-calendar"></i> Date de publication: <?= $arrayannonce['date_enregistrement'] ?></p>
                <div>
                    <p><i class="bi bi-person-square"></i> <a class="link" role="button" data-toggle="modal" data-target="#membre"><?= $arrayMembre1['prenom'] ?></a> <?php echo Note($arrayMembre1['id_membre']) ?> <i class="bi bi-star-fill" style="color: #FFD700"></i></p>
                    <div>
                        <p><i class="bi bi-tag"></i><?php echo $arrayannonce['prix'] ?> €</p>
                    </div>
                    <div>
                        <p><i class="bi bi-geo-fill"></i> Adresse: <?php echo $arrayannonce['adresse'] . ", ";
                                                                    echo $arrayannonce['cp'] . ", ";
                                                                    echo $arrayannonce['ville'] . " " ?></p>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <!--  FICHE DE MEMBRE Modal -->
    <div class="modal fade" id="membre" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Membre</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h1 class="text-center m-4">Fiche Membre</h1>
                    <br>
                    <h3 class="text-center m-4">Membre inscrit le : <?php echo $arrayMembre1['date_enregistrement'] ?>


                        <br><br>
                        <br><br>
                        Pseudo : <?php echo $arrayMembre1['pseudo'] ?>
                        <br><br> Prénom : <?php echo $arrayMembre1['prenom'] ?>

                        <br><br> Téléphone : <?php echo $arrayMembre1['telephone'] ?>
                        <br><br> Email : <?php echo $arrayMembre1['email'] ?>


                        <br><br> Note : <?php echo Note($arrayMembre1['id_membre']) ?><i class="bi bi-star-fill" style="color: #FFD700"></i>


                        <br><br>
                        <h3 class="text-center">Avis : </h3>
                        <?php while ($arrayMembreNote = $pdoStatement1->fetch(PDO::FETCH_ASSOC)) : ?>

                            <br>
                            <p class="text-center text-primary"><?php echo $arrayMembreNote['avis'] ?></p>
                            <br>

                        <?php endwhile; ?>

                    </h3>
                </div>

            </div>
        </div>
    </div>

</div>
<div class="d-flex justify-content-center">
    <div>
<!-- GOOGLE MAP -->
        <div class="mapouter">
            <div class="gmap_canvas d-flex justify-content-center"><iframe width="1080" height="221" id="gmap_canvas" src="https://maps.google.com/maps?q=<?php echo implode(',', $arrayAdresse) ?>&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a href="https://yt2.org"></a><br>
                <style>
                    .mapouter {
                        position: relative;
                        text-align: right;
                        height: 221px;
                        width: 90vw;
                    }
                </style>
                <style>
                    .gmap_canvas {
                        overflow: hidden;
                        background: none !important;
                        height: 221px;
                        width: 90vw;
                    }
                </style>
            </div>
        </div>
    </div>
</div>
<!-- ZONE DE COMMENTAIRE -->
<?php if ($pdoStatement->rowCount() > 0) : ?>
    <div class=" pt-2 col-12 ">

        <?php while ($arrayCommentaire = $pdoStatement7->fetch(PDO::FETCH_ASSOC)) : ?>

            <h6 class="pt-2">Commentaire du : <?php echo $arrayCommentaire['date_enregistrement'];  ?></h6>
            <div class="bg-light d-flex justify-content-center border border-dark rounded">
                <p class="text-center text-dark"><?php echo $arrayCommentaire['commentaire'];  ?></p>
            </div>



        <?php endwhile; ?>
    </div>
<?php else : ?>
<?php endif; ?>

<br>
<h5>Annonces similaires</h5>
<br>
<div class="row ">
    <?php
    while ($arraysimilaire = $pdoStatement4->fetch(PDO::FETCH_ASSOC)) :
    ?>
        <div class="col-sm-2 align-self-center border border-primary rounded mr-4">
            <a class="btn border-top border-bottom col-md-12 mt-1 mb-1" href="ficheAnnonce.php?id_annonce=<?= $arraysimilaire['id_annonce'] ?>">
                <?php if ($arraysimilaire['photo'] != "") :  ?>
                    <img class='img-fluid rounded' style='width:100px' src="images/imagesUpload/<?= $arraysimilaire['photo'] ?>" alt="<?= $arraysimilaire['titre'] ?>" title="<?= $arraysimilaire['titre'] ?>">
                <?php else :  ?>
                    <img class='img-fluid rounded' style='width:100px' src="images/default.jpg" alt="" title="Image par défaut">
                <?php endif;  ?>
            </a>
        </div>
    <?php endwhile;  ?>
</div>
<br><br>
<!-- MODAL DE CONNEXION  -->
<div class="d-flex justify-content-between">
    <?php if ((membreConnecte() && $_SESSION['membre']['statut'] == 1) || (adminConnecte())) : ?>
        <div>
            <a class="nav-link" role="button" data-toggle="modal" data-target="#commentaire"><i class="bi bi-file-earmark-post-fill"></i> Deposer un commentaire</a>
        </div>
        <div>
            <a class="nav-link" role="button" data-toggle="modal" data-target="#note">
                <i class="bi bi-list-stars"></i> Deposer une note
            </a>
        </div>
        <div>
            <a href="index.php" class="nav-link" role="button">
                <i class="bi bi-arrow-bar-right"></i>Retour vers les annonces 
            </a>
        </div>
        <!-- Modal commentaire -->
        <div class="modal fade" id="commentaire" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Laisser un commentaire</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" class="col-md-12 mx-auto" name="form_envoyer_commentaire">
                            <br>
                            <h1 class="text-center">Laisser un commentaire</h1>

                            <div class="form-group m-3">
                                <label for="messageCommentaire"></label>
                                <textarea class="form-control" name="messageCommentaire" id="messageCommentaire" rows="3" placeholder="Votre commentaire"></textarea>
                            </div>

                            <div class="form-group m-3">
                                <input type="submit" name="envoyercommentaire" class="btn btn-primary col-md-12 mt-3 mb-5" value='Envoyer'>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!-- Modal note-->
        <div class="modal fade" id="note" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Laisser une note</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" class="col-md-12 mx-auto" name="form_envoyer_note">
                            <br>
                            <h1 class="text-center">Laisser une note</h1>

                            <div class="form-group m-3 d-flex justify-content-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="note" id="inlineRadio1" value="1">
                                    <label class="form-check-label" for="inlineRadio1"><i class="bi bi-star-fill" style="color: #FFD700"></i>1</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="note" id="inlineRadio2" value="2">
                                    <label class="form-check-label" for="inlineRadio2"><i class="bi bi-star-fill" style="color: #FFD700"></i>2</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="note" id="inlineRadio3" value="3">
                                    <label class="form-check-label" for="inlineRadio3"><i class="bi bi-star-fill" style="color: #FFD700"></i>3</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="note" id="inlineRadio4" value="4">
                                    <label class="form-check-label" for="inlineRadio4"><i class="bi bi-star-fill" style="color: #FFD700"></i>4</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="note" id="inlineRadio5" value="5">
                                    <label class="form-check-label" for="inlineRadio5"><i class="bi bi-star-fill" style="color: #FFD700"></i>5</label>
                                </div>

                            </div>
                            <div class="form-group m-3">
                                <label for="avis"></label>
                                <textarea class="form-control" name="avis" id="avis" rows="3" placeholder="Votre avis"></textarea>
                            </div>


                            <div class="form-group m-3">
                                <input type="submit" name="envoyernote" class="btn btn-primary col-md-12 mt-3 mb-5" value='Envoyer'>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>

    <?php else : ?>

        <div class="d-flex justify-content-between">
            <a class="nav-link" data-toggle="modal" role="button" data-target="#connectez_vous">
                <i class="bi bi-box-arrow-in-right"></i> Connectez-vous pour déposer un commentaire ou une note
            </a>
            <a href="index.php" class="nav-link">
                <i class="bi bi-arrow-bar-right"></i> Retour vers les annonces
            </a>
        </div>

        <!-- Modal connexion-->
        <div class=" rounded modal fade" id="connectez_vous" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h5 class="modal-title text-white" id="exampleModalLabel">Connexion</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php
                        include_once('include/init.php');
                      
                        // le code PHP du fichier se situera entre init et header

                        // Sécurité
                        // la page connexion est accessible seulement si un utilisateur n'est pas connecté
                        if (membreConnecte()) {
                            header("Location:" . URL . "erreur.php?acces=interdit");
                            exit;
                        }


                        // s'il y a le paramètre "compte" dans l'url et que ce paramètre soit égal à "enregistre" : je rentre dans la condition
                        if (isset($_GET['compte']) && ($_GET['compte'] == "enregistre")) {
                            $notification .= "<div class='col-md-6 mx-auto alert alert-success text-center'>
                            Votre inscription a été enregistrée
                        </div>";
                        }


                  
                       
                        if (isset($_POST['connexion']) && $_POST['connexion']) {
                            
                            echo "<meta http-equiv='refresh' content='0'>";
                            if (!empty($_POST['pseudo'])) {

                                // Vérification de l'existance du pseudo

                                //1e étape 
                                $pdoStatement = $pdoObject->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");

                                // 2e étape 
                                $pdoStatement->bindValue(":pseudo", $_POST['pseudo'], PDO::PARAM_STR);

                                // 3e étape 
                                $pdoStatement->execute();

                                $membreArray = $pdoStatement->fetch(PDO::FETCH_ASSOC);



                                // 1e condition pour la connexion : vérifier que l'email existe en bdd
                                if (!empty($membreArray)) // si le tableau $membreArray n'est pas vide
                                {
                                    // Comparer les mdp 

                                    if (password_verify($_POST['mdp'], $membreArray['mdp'])) {
                                        // pour être connecté, il faut rajouter le tableau $membreArray (c'est-à-dire les infos personnelles de l'utilisateur) dans un tableau qui se trouve dans la superglobale $_SESSION

                                        foreach ($membreArray as $key => $value) {
                                            $_SESSION['membre'][$key] = $value;
                                        }

                                        //client
                                        if ($_SESSION['membre']['statut'] == 1) {
                                            header("Location:" . URL . "profil.php");
                                            exit;
                                        } else //admin
                                        {
                                            header("Location:" . URL . "admin/admin.php");
                                            exit;
                                        }
                                    } else //  le mdp ne correspond pas
                                    {
                                        $erreur .= "<div class='col-md-6 mx-auto alert alert-danger text-center'>
                                    Mot de passe incorrect
                                </div>";
                                    }
                                } else //pas de pseudo en bdd
                                {
                                    $erreur .= "<div class='col-md-6 mx-auto alert alert-danger text-center'>
                                L'email " . $_POST['pseudo']  .  " n'est pas associé à un compte <br>
                                Veuillez vous inscrire 
                            </div>";
                                }
                            } else //input pseudo VIDE
                            {
                                $erreur .= "<div class='col-md-6 mx-auto alert alert-danger text-center'>
                            Veuillez saisir votre Pseudo
                        </div>";
                            }
                        } //
                        ?>
                        <?= $erreur ?>
                        <?= $notification ?>

                        <form method="post" class=" rounded col-md-12 mx-auto bg-secondary">
                            <h1 class="text-center m-3 "></h1>
                            <div class="form-group m-3 ">
                                <label for="pseudo"></label>
                                <input type="text" class="form-control bg-white" id='pseudo' name="pseudo" placeholder="Votre pseudo">
                            </div>

                            <div class="form-group m-3">
                                <label for="mdp"></label>
                                <input type="text" class="form-control bg-white" id='mdp' name="mdp" placeholder="Votre mot de passe">
                            </div>

                            <div class="form-group m-3">
                                <input type="submit" class="btn btn-primary col-md-12 mt-4 mb-5" name="connexion" value='Connexion'>
                            </div>
                        </form>

                        <?php
                        include_once('include/footer.php'); ?>
                    </div>

                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<br><br><br><br>

<?php
include_once('include/footer.php');
