<?php

// affichage des catégories dans la form SELECT OPTION
$afficheFormCategories = $pdo->query(" SELECT DISTINCT titre FROM categorie ORDER BY titre ASC ");

// affichage des région dans la form SELECT OPTION
$afficheFormVilles = $pdo->query(" SELECT DISTINCT ville FROM annonce ORDER BY ville ASC ");

// affichage des membre dans la form SELECT OPTION
$afficheFormMembres = $pdo->query(" SELECT DISTINCT pseudo FROM membre ORDER BY pseudo ASC ");

// affichage des membre dans la form 
$afficheFormPrix = $pdo->query(" SELECT DISTINCT prix FROM annonce ORDER BY prix ASC "); 

// affichage des trier dans la form SELECT OPTION
// $afficheFormTrier = $pdo->query(" SELECT DISTINCT  FROM  ORDER BY  ASC "); 


if(isset($_GET['id_annonce'])){
    $affiAnnonce = $pdo->query(" SELECT * FROM annonce ");
    $affi = $affiAnnonce ->fetch(PDO::FETCH_ASSOC);
}

                            // L'AFFICHAGE DU TABLEAU D'ANNONCE
// 01-tout l'affichage par categorie
    if(isset($_GET['categorie_id'])){
        
        // affichage de tous les annonces concernés par une categorie
        $affiAnnoCate = $pdo->query(" SELECT * FROM annonce WHERE categorie_id = '$_GET[categorie_id]' ORDER BY prix ASC ");

        // affichage de tous les notes concernés par une categorie
        $affiNoteCate = $pdo->query(" SELECT * FROM note WHERE categorie_id = '$_GET[categorie_id]' ");

        // affichage de tous les membre concernés par une categorie
        $affiMembreCate = $pdo->query(" SELECT * FROM membre WHERE membre_id = '$_GET[categorie_id]' ");


        // pour les onglets categories
        $pageTitle = "Annonce de " . $_GET['categorie_id'];
    }

// 02-tout l'affichage par Région
    if(isset($_GET['ville'])){
        
        // affichage de tous les annonces concernés par une ville
        $affiAnnoVille = $pdo->query(" SELECT * FROM annonce WHERE ville = '$_GET[ville]' ORDER BY prix ASC ");

        // affichage de tous les notes concernés par une ville
        $affiNoteVille = $pdo->query(" SELECT * FROM note WHERE categorie_id = '$_GET[ville]' ");

        // affichage de tous les membre concernés par une ville
        $affiMembreVille = $pdo->query(" SELECT * FROM membre WHERE membre_id = '$_GET[ville]' ");

        // pour les onglets villes
        $pageTitle = "Annonce de " . $_GET['ville'];
    }

// 03-tout l'affichage par membre
    if(isset($_GET['membre'])){
        
        // affichage de tous les annonces concernés par une membre
        $affiAnnoMembre = $pdo->query(" SELECT * FROM annonce WHERE membre = '$_GET[membre]' ORDER BY prix ASC ");

        // affichage de tous les notes concernés par une membre
        $affiNoteMembre = $pdo->query(" SELECT * FROM note WHERE membre_id = '$_GET[membre]' ");

        // affichage de tous les membre concernés par une membre
        $affiMembreMe = $pdo->query(" SELECT * FROM membre WHERE membre_id = '$_GET[membre]' ");

        // pour les onglets membres
        $pageTitle = "Annonce de " . $_GET['membre'];
    }

// tout l'affichage par prix

// if(isset($_GET['prix'])){
   
// }

// ---------------------------------------------------------------------------------------
// Tout ce qui concerne la fiche_annonce

// affichage d'une annonce
if(isset($_GET['id_annonce'])){
    $detailAnnonce = $pdo->query(" SELECT * FROM annonce WHERE id_annonce = '$_GET[id_annonce]' ");
    
    if($detailAnnonce->rowCount() <= 0){
        header('location:' . URL);
        exit;
    }
    
    $detail = $detailAnnonce->fetch(PDO::FETCH_ASSOC);
}
?>