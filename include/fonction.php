<?php

function debug($var, $mode = 1){

    
    $trace = debug_backtrace();
   
    $trace = array_shift($trace);

    echo "Debug demandé à la ligne <strong> $trace[line]</strong>,dans le fichier <strong> $trace[file] </strong> ";
        if($mode == 1){
            echo "<pre>"; print_r($var); echo "</pre>";
        }else{
            echo "<pre>"; var_dump($var); echo "</pre>";
        }

}


function internauteConnecte(){
    if(!isset($_SESSION['membre'])){
        return FALSE;
    }
    else {return TRUE;}
}
function internauteConnecteAdmin() {
    if(internauteConnecte() && $_SESSION['membre']['statut'] == 1 ){
        return TRUE;
    }
    else {return FALSE;}
}

?>