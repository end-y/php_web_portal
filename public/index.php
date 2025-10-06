<?php
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
            placeholder="Görevlerde ara..."
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
                    <?php echo renderTaskOrderList("title", "Görev"); ?>
                    <?php echo renderTaskOrderList("description", "Başlık"); ?>
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

// Arama fonksiyonu - güvenli: debounce, history.replaceState, fetch ile JSON al
(function() {
    const input = document.getElementById('searchInput');
    const tableBody = document.getElementById('taskTable');
    let timer = null;
    const DEBOUNCE_MS = 300;

    function buildUrl(search) {
        const url = new URL(window.location.href);
        if (search && search.length) url.searchParams.set('search', search);
        else url.searchParams.delete('search');
        return url;
    }

    function renderTasks(tasks) {
        while (tableBody.firstChild) tableBody.removeChild(tableBody.firstChild);
        tasks.forEach(task => {
            const tr = document.createElement('tr');
            tr.className = 'border-b hover:bg-gray-50 transition';

            const td1 = document.createElement('td');
            td1.className = 'px-4 ' + "text-[" + (task.colorCode ? "white" : "black") + "] " + (task.colorCode ? 'bg-[' + task.colorCode + ']' : '') + ' py-3';
            td1.textContent = task.task || '';

            const td2 = document.createElement('td');
            td2.className = 'px-4 ' + "text-[" + (task.colorCode ? "white" : "black") + "] " + (task.colorCode ? 'bg-[' + task.colorCode + ']' : '') + ' py-3';
            td2.textContent = task.title || '';

            const td3 = document.createElement('td');
            td3.className = 'px-4 ' + "text-[" + (task.colorCode ? "white" : "black") + "] " + (task.colorCode ? 'bg-[' + task.colorCode + ']' : '') + ' py-3';
            td3.textContent = task.description || '';

            tr.appendChild(td1);
            tr.appendChild(td2);
            tr.appendChild(td3);
            tableBody.appendChild(tr);
        });
    }

    async function fetchAndUpdate(search) {
        const url = new URL(window.location.origin + window.location.pathname);
        if (search && search.length) url.searchParams.set('search', search);
        url.searchParams.set('partial', '1');
        const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) return;
        const data = await res.json();
        renderTasks(Object.values(data.tasks) || []);
    }
    function updateUrl(search) {
        const urls = document.querySelectorAll("#urls");
        
        urls.forEach(url => {
            const urlObject = new URL(url.href);
            if(search.length === 0) {
                urlObject.searchParams.delete("search");
                url.href = urlObject.href;
                return;
            }
            urlObject.searchParams.set("search", search);
            url.href = urlObject.href;
        });
    }
    input.addEventListener('input', function(e) {
        const q = e.target.value;
        clearTimeout(timer);
        timer = setTimeout(() => {
            const newUrl = buildUrl(q);
            history.replaceState(null, '', newUrl.toString());
            updateUrl(q);
            fetchAndUpdate(q).catch(console.error);
        }, DEBOUNCE_MS);
    });
})();
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/_layout.php';
?>