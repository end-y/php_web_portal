<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPWebPortal\Controllers\LoginController;
use PHPWebPortal\Utils;

$controller = new LoginController();
$data = $controller->index();
extract($data);
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $controller->login();
    extract($data);
}

// İçerik buffer'ını başlat
ob_start();
?>

<div class="flex justify-center items-center min-h-screen py-12">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">PHP Web Portal</h1>
            <p class="text-gray-600 mt-2">Devam etmek için giriş yapın</p>
        </div>

        <?php if(isset($error) && $error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo Utils::e($error); ?>
            </div>
        <?php endif; ?>

        <form action="/login" method="post" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                    Name
                </label>
                <input 
                    type="text" 
                    name="username" 
                    id="username"
                    placeholder="Kullanıcı adınızı girin" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input 
                    type="password" 
                    name="password" 
                    id="password"
                    placeholder="Şifrenizi girin" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200"
            >
                Login
            </button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/_layout.php';
?>