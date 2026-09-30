<?php
session_start();

require_once("../config/config.php");


//Connexion à la bdd
$dsn = "mysql:host=" . DB_HOSTNAME . ";dbname=" . DB_DATABASE;
$db = new PDO($dsn, DB_USERNAME, DB_PASSWORD);




$page = "login";
if (isset($_GET["page"]) && !empty($_GET["page"])) {
    $page = $_GET["page"];
}

//Pages
$pages = array(
    "login" => array(
        "model" => "LoginModel",
        "controller" => "LoginController",
        "view" => "LoginView",
    ),
    "admin" => array(
        "model" => "AdminModel",
        "controller" => "AdminController",
        "view" => "AdminView",
    ),
    "prep" => array(
        "model" => "PrepModel",
        "controller" => "PrepController",
        "view" => "PrepView",
    ),
    "accueil" => array(
        "model" => "AccueilModel",
        "controller" => "AccueilController",
        "view" => "AccueilView",
    )
);


$find = false;
foreach ($pages as $key => $value) {
    if ($page === $key) {
        $find = true;

        $model = $value["model"];
        $controller = $value["controller"];
        $view = $value["view"];

        break;
    }
}

if ($find) {
    //On importe des diff classes
    require(DIR_MODEL . $page . ".php");
    require(DIR_CONTROLLER . $page . ".php");
    require(DIR_VIEW . $page . ".php");

    //On instancie les classes importer
    $pageModel = new $model($db);
    $pageController = new $controller($pageModel);
    $pageView = new $view($pageController);

    // Le contrôleur traite le formulaire AVANT l'affichage
    if (method_exists($pageController, "handleRequest")) {
        $pageController->handleRequest();
    }

    $pageView->render();
}

?>