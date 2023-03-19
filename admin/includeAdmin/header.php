<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>L'Annonceo Admin</title>
  <!-- Custom styles for this template -->
  <link href="../css/simple-sidebar.css" rel="stylesheet">

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- links pour les icon bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">

  <!-- {# links pour databaseTables #} -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css"/>

</head>

<body>

  <div class="d-flex">

    <div class="bg-dark border-right" >
      
      <div class="list-group list-group-flush">

        <a href="<?= URL ?>admin/gestion_membre.php" class="list-group-item list-group-item-action bg-dark text-light py-5">
            <button type="button" class="btn btn-outline-warning text-light">Gestion des membre</button>
        </a>

        <a href="<?= URL ?>admin/gestion_annonce.php" class="list-group-item list-group-item-action bg-dark text-light py-5">
            <button type="button" class="btn btn-outline-warning text-light">Gestion des annonces</button>
        </a>

        <a href="<?= URL ?>admin/gestion_commentaire.php" class="list-group-item list-group-item-action bg-dark text-light py-5">
            <button type="button" class="btn btn-outline-warning text-light">Gestion des commentaire</button>
        </a>

        <a href="<?= URL ?>admin/gestion_categorie.php" class="list-group-item list-group-item-action bg-dark text-light py-4">
            <button type="button" class="btn btn-outline-warning text-light">Gestion des categorie</button>
        </a>

        <a href="<?= URL ?>admin/gestion_note.php" class="list-group-item list-group-item-action bg-dark text-light py-5">
            <button type="button" class="btn btn-outline-warning text-light">Gestion des notes</button>
        </a>
    
      </div>
    </div>
    

        <!-- Page Content -->
        <div id="page-content-wrapper">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom">

  

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">

      <li class="nav-item">
        <a class="nav-link" href="<?= URL ?>index.php"><button type="button" class="btn btn-outline-warning text-light">Home Annonceo</button></a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="<?= URL ?>admin/index.php"><button type="button" class="btn btn-outline-warning text-light">Home Admin</button></a>
      </li>


    </ul>
  </div>
</nav>

<div class="container mb-5 mx-auto text-center">