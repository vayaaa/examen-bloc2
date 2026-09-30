<?php
// define("HOST", "http://localhost/telefoot-with-mvc/public/");

//Constantes pour stocker les chemins vers nos dossiers du projet 
define("DIR_PUBLIC",__DIR__ . "/../public/");
define("DIR_APPLICATION", __DIR__ . "/../src/");
define("DIR_TEMPLATES", __DIR__ . "/../templates/");

define("DIR_MODEL", DIR_APPLICATION . "models/");
define("DIR_CONTROLLER", DIR_APPLICATION . "controllers/");
define("DIR_VIEW", DIR_APPLICATION . "views/");



//Appel aux constantes dans le .env
$env = parse_ini_file(__DIR__ . "/../.env" );

if($env === false){
    die("Fichier .env introuvable");
}

define("DB_HOSTNAME", $env["DB_HOSTNAME"]);
define("DB_USERNAME", $env["DB_USERNAME"]);
define("DB_PASSWORD", $env["DB_PASSWORD"]);
define("DB_DATABASE", $env["DB_DATABASE"]);


?>