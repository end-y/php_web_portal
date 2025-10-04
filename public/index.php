<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPWebPortal\User;
use PHPWebPortal\Task;

$user = new User();
$isTokenValid = $user->isTokenValid();
if(!$isTokenValid) {
    header("Location: /login");
    exit;
}

$task = new Task($user);
$tasks = $task->getTasks();
$colorTasks = $task->getColorTasks();
// Layout değişkenleri
$title = 'Görev Listesi';
$showNav = true;
$isLoggedIn = true;
function getColor(string $colorCode): string {
    $text = $colorCode ? "#ffffff" : "#000000";
    return "text-[$text] bg-[$colorCode]";
}
// İçerik buffer'ını başlat
ob_start();

?>

<div class="bg-white rounded-lg shadow-md p-6">

    <!-- Arama Kutusu -->
    <div class="mb-4">
        <input
            type="text"
            id="searchInput"
            placeholder="Görevlerde ara..."
            class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
    </div>

    <!-- Görev Tablosu -->
    <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">
                        <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 text-center inline-flex items-center border border-gray-300" type="button">
                            Tasks <svg class="w-2.5 h-2.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                            </svg>
                        </button>
                        <div id="dropdown" class="absolute z-10 mt-2 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-lg w-32 border border-gray-200">
                            <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownDefaultButton">
                                <?php if(empty($colorTasks)): ?>
                                    <li>
                                        <span class="block px-4 py-2 text-gray-500">Henüz renk verisi yok</span>
                                    </li>
                                <?php else: ?>
                                    <?php foreach($colorTasks as $colorTask): ?>
                                        <?php if(!empty($colorTask["colorCode"])): ?>
                                            <li>
                                                <a href="#" class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                                    <div class="w-4 h-4 rounded-md border border-gray-300" style="background-color: <?php echo htmlspecialchars($colorTask["colorCode"]); ?>;"></div>
                                                    <span class="font-medium">Tasks</span>
                                                    <span class="ml-auto text-xs bg-gray-100 px-2 py-1 rounded-full"><?php echo (int)$colorTask["amount"]; ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Görev</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Başlık</th>
                </tr>
            </thead>
            <tbody id="taskTable">
                <?php foreach($tasks as $task): ?>
                    <?php $colorClass = getColor(htmlspecialchars($task["colorCode"])); ?>
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-4 <?php echo $colorClass; ?> py-3"><?php echo htmlspecialchars($task["task"]); ?></td>
                        <td class="px-4 <?php echo $colorClass; ?> py-3"><?php echo htmlspecialchars($task["title"]); ?></td>
                        <td class="px-4 <?php echo $colorClass; ?> py-3"><?php echo htmlspecialchars($task["description"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Dropdown toggle functionality
document.addEventListener('DOMContentLoaded', function () {
    const dropdownButtons = document.querySelectorAll('[data-dropdown-toggle]');

    dropdownButtons.forEach(button => {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-dropdown-toggle');
            const dropdown = document.getElementById(targetId);

            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (event) {
        dropdownButtons.forEach(button => {
            const targetId = button.getAttribute('data-dropdown-toggle');
            const dropdown = document.getElementById(targetId);

            if (dropdown && !button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    });
});

// Arama fonksiyonu
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#taskTable tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/_layout.php';
?>