<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo $title ?? 'PHP Web Portal'; ?></title>
    <link rel="stylesheet" href="/css/output.css">
</head>
<body class="bg-gray-50">
    <?php if(isset($showNav) && $showNav): ?>
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-md mb-6">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="text-xl font-bold text-blue-600">PHP Web Portal</a>
            <div class="flex gap-4">
                <a href="/" class="text-gray-600 hover:text-blue-600">Görevler</a>
                <?php if(isset($isLoggedIn) && $isLoggedIn): ?>
                    <form action="/logout" method="post" class="inline">
                        <button type="submit" class="text-gray-600 hover:text-red-600">Çıkış Yap</button>
                    </form>
                <?php else: ?>
                    <a href="/login" class="text-gray-600 hover:text-blue-600">Giriş Yap</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    
    <main class="container mx-auto px-4">
        <?php echo $content; ?>
    </main>
    <?php if(isset($includeScripts) && is_array($includeScripts)): ?>
        <?php foreach($includeScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>