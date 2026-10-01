<?php

//Génération du password hash 

// Récupération du mdp dans le terminal
$password = $argv[1] ?? "";

// Fabrication du hash (bcrypt)
$hash = password_hash($password, PASSWORD_DEFAULT);



echo "Mot de passe : " . $password . "\n";
echo "Hash         : " . $hash . "\n";

?>