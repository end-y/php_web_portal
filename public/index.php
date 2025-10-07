<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Components/TaskColorList.php';
require_once __DIR__ . '/../src/Components/TaskOrderList.php';
require_once __DIR__ . '/../src/Components/TaskModal.php';

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

?>

<div class="bg-white rounded-lg shadow-md p-6">

    <!-- Modal butonu (tablonun dışında) -->
    <?php echo renderTaskModal(); ?>


    <!-- Arama Kutusu -->
    <form class="mb-4 flex items-center gap-2">
        <input
            name="search"
            value="<?php echo Utils::e($_GET['search'] ?? ''); ?>"
            type="text"
            id="searchInput"
            placeholder="Search tasks..."
            class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <input type="button" class="bg-red-500 cursor-pointer text-white px-4 py-2 rounded-md" value="Clear filters" onclick="window.location.href = '/';">
    </form>

    <!-- Görev Tablosu -->
    <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <?php echo renderTaskColorList($colorTasks); ?>
                    <?php echo renderTaskOrderList("title", "Task"); ?>
                    <?php echo renderTaskOrderList("description", "Title"); ?>
                </tr>
            </thead>
            <tbody id="taskTable">
                <?php foreach($tasks as $task): ?>
                    <?php $colorClass = TaskController::getColorClass(Utils::e($task["colorCode"])); ?>
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-4 <?php echo $colorClass; ?> py-3"><?php echo Utils::e($task["task"]); ?></td>
                        <td class="px-4 <?php echo $colorClass; ?> py-3"><?php echo Utils::e($task["title"]); ?></td>
                        <td class="px-4 <?php echo $colorClass; ?> py-3"><?php echo Utils::e($task["description"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/_layout.php';
?>