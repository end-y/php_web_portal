<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPWebPortal\User;

$user = new User();
$user->logout();

header("Location: /login");
exit;

