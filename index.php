<?php

require_once('include/init.php');

require_once('include/affichage.php');

require_once('include/header.php');

?>

<div class="container-fluid">
    <div class="row my-5">

        <div class="col-md-2">


                                 <!--****** SELECT OPTION **********-->

            <div class="list-group text-center">

                                            <!-- Catégorie  -->
                <div class="my-4">
                    <h5>Catégorie</h5>
                    <select class="form-select form-select-sm" aria-label=".form-select-sm example">
                        <option selected>Toutes les catégorie</option>
                        <?php while($formCategorie = $afficheFormCategories->fetch(PDO::FETCH_ASSOC)): ?>

                            <option value="#">
                                <a class="btn" href="<?= URL ?>?titre=<?= $formCategorie['titre'] ?>">
                                    <?= $formCategorie['titre'] ?>
                                </a>
                            </option>
                        <?php endwhile; ?>  
                    </select>          
                </div>            
                        
                
                                            <!-- Région  -->
                <div class="my-4">
                    <h5>Région</h5>
                    <select class="form-select form-select-sm" aria-label=".form-select-sm example">
                        <option selected>Toutes les régions</option>
                        <?php while($formVille = $afficheFormVilles->fetch(PDO::FETCH_ASSOC)): ?>

                            <option value="#">
                                <a class="btn" href="<?= URL ?>?ville=<?= $formVille['ville'] ?>">
                                    <?= $formVille['ville'] ?>
                                </a>
                            </option>
                        <?php endwhile; ?>  
                    </select>
                </div>


                                            <!-- Membre  -->
                <div class="my-4">
                    <h5>Membre</h5>
                    <select class="form-select form-select-sm" aria-label=".form-select-sm example">
                        <option selected>Toutes les membres</option>
                        <?php while($formMembre = $afficheFormMembres->fetch(PDO::FETCH_ASSOC)): ?>

                            <option value="#">
                                <a class="btn" href="<?= URL ?>?pseudo=<?= $formMembre['pseudo'] ?>">
                                    <?= $formMembre['pseudo'] ?>
                                </a>
                            </option>
                        <?php endwhile; ?>  
                </div>
            </div>  

        </div>

        <!-- --------------------------------------------------------------------------------- -->
                    <div class="row justify-content-around">
                            <!-- SELECT OPTION TRIER -->
                    </div>

        <!-- pour afficher les annonces par catégories-titre -->
        <?php if(isset($_GET['titre'])): ?>
        
            <div class="row justify-content-around text-center">
                
                <?php while($annonce = $afficheAnnonces->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="mx-3 shadow p-3 mb-5 bg-white rounded" style="width: 18rem;">

                    <a href="fiche_annonce.php?id_annonce=<?= $annonce['id_annonce'] ?>">
                        <img src="<?= URL . 'img/' . $annonce['photo'] ?>" class="mw-100" alt="...">
                    </a>
                    
                    <div class="#">
                                            <!-- titre -->
                        <h3 class="#"><?= $annonce['titre'] ?></h3>
                                            <!-- description -->
                        <p class="#"><?= $annonce['description'] ?></p>
                        <div class="d-flex">
                                            <!-- Nom -->
                            <?php while($annonce = $afficheAnnonces->fetch(PDO::FETCH_ASSOC)): ?>
                                <a href="membre.php?id_annonce=<?= $membre['id_membre'] ?>" class="btn btn-outline-success"><?= $membre['nom'] ?></a>
                            <?php endwhile; ?>
                                            <!-- Note -->
                            <?php while($annonce = $afficheAnnonces->fetch(PDO::FETCH_ASSOC)): ?>
                                <p><?= $note['note'] ?></p>
                            <?php endwhile; ?>
                                            <!-- prix -->
                                <p class="badge badge-dark text-wrap"><?= $annonce['prix'] ?> €</p>
                        </div>
                        
                    </div>
                </div>
                <?php endwhile; ?>
            </div>


                        <!-- Previous - - Next -->
            <nav aria-label="">
                <ul class="pagination justify-content-end">
                    <li class="mx-1 page-item  ">
                        <a class="page-link text-success" href="" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                    <!--  -->
                    <li class="mx-1 page-item ">
                        <a class="btn btn-outline-success " href=""></a>
                    </li>
                    <!--  -->
                    <li class="mx-1 page-item ">
                        <a class="page-link text-success" href="" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                </ul>
            </nav>

        </div>

        <!-- ----------------------- -->
        <!-- pour afficher les annonces  par ville -->
        <?php elseif(isset($_GET['ville'])): ?>

        <div class="col-md-8">

            <div class="row justify-content-around text-center">
                    
                    <?php while($annonce = $afficheAnnonces->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="mx-3 shadow p-3 mb-5 bg-white rounded" style="width: 18rem;">
                        <a href="fiche_annonce.php?id_annonce=<?= $annonce['id_annonce'] ?>">
                            <img src="<?= URL . 'img/' . $annonce['photo'] ?>" class="mw-100" alt="...">
                        </a>
                        <div class="#">
                            <h3 class="#"><?= $annonce['titre'] ?></h3>
                            <p class="#"><?= $annonce['description'] ?></p>
                            <div class="d-flex">
                                    <p><?= $annonce['description'] ?></p>
                                    <p><?= $annonce['note'] ?></p>
                                    <p class="badge badge-dark text-wrap"><?= $annonce['prix'] ?> €</p>
                            </div>
                            <a href="fiche_annonce.php?id_annonce=<?= $annonce['id_annonce'] ?>" class="btn btn-outline-success"><i class='bi bi-search'></i> Voir Produit</a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <!-- Previous - - Next -->
            <nav aria-label="">
                <ul class="pagination justify-content-end">
                    <li class="mx-1 page-item  ">
                        <a class="page-link text-success" href="" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                    <!--  -->
                    <li class="mx-1 page-item ">
                        <a class="btn btn-outline-success " href=""></a>
                    </li>
                    <!--  -->
                    <li class="mx-1 page-item ">
                        <a class="page-link text-success" href="" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

            <!-- pour afficher les annonces  par ville -->
        <?php elseif(isset($_GET['ville'])): ?>

        <div class="col-md-8">

        <div class="row justify-content-around text-center">
            
            <?php while($annonce = $afficheAnnonces->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="mx-3 shadow p-3 mb-5 bg-white rounded" style="width: 18rem;">
                <a href="fiche_annonce.php?id_annonce=<?= $annonce['id_annonce'] ?>">
                    <img src="<?= URL . 'img/' . $annonce['photo'] ?>" class="mw-100" alt="...">
                </a>
                <div class="#">
                    <h3 class="#"><?= $annonce['titre'] ?></h3>
                    <p class="#"><?= $annonce['description'] ?></p>
                    <div class="d-flex">
                            <p><?= $annonce['description'] ?></p>
                            <p><?= $annonce['note'] ?></p>
                            <p class="badge badge-dark text-wrap"><?= $annonce['prix'] ?> €</p>
                    </div>
                    <a href="fiche_annonce.php?id_annonce=<?= $annonce['id_annonce'] ?>" class="btn btn-outline-success"><i class='bi bi-search'></i> Voir Produit</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

            <nav aria-label="">
                <!-- dans les 3 <a href> je dois faire référence à la catégorie, en plus de la page, sinon cela ne fonctionnera pas -->
                <ul class="pagination justify-content-end">
                    <li class="mx-1 page-item  ">
                        <a class="page-link text-success" href="" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>

                    <li class="mx-1 page-item ">
                        <a class="btn btn-outline-success " href=""></a>
                    </li>

                    <li class="mx-1 page-item ">
                        <a class="page-link text-success" href="" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                </ul>
            </nav>

        </div>

        </div>
        <?php endif; ?>

    </div>

</div>


<?php require_once('include/footer.php') ?>