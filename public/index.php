<?php
/*
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/components/TaskColorList.php';
require_once __DIR__ . '/../src/components/TaskOrderList.php';
require_once __DIR__ . '/../src/components/TaskModal.php';

use PHPWebPortal\Controllers\TaskController;
use PHPWebPortal\Utils;

$controller = new TaskController();
$data = $controller->index();
extract($data);

// Filtreleri uygula
$tasks = Utils::applyFiltersAndSort($controller, $tasks);

// Eğer AJAX partial isteği geliyorsa sadece JSON döndür
if (isset($_GET['partial']) && $_GET['partial'] === '1') {
    header('Content-Type: application/json');
    echo json_encode(['tasks' => $tasks]);
    exit;
}
ob_start();
*/
?>

<?php
echo "Hello World";
?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/_layout.php';
?>