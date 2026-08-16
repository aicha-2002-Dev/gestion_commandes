<?php

require_once dirname(__DIR__) . '/app/Controller/POSController.php';

require_once dirname(__DIR__) . '/app/Core/Routeur.php';

$routeur = new Routeur();
$routeur->distribuer();