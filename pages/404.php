<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Страница не найдена</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
    <script src="../assets/js/main.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#EAB308',
                        'muted-foreground': '#6B7280',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white text-gray-900">

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="text-center max-w-2xl mx-auto">
            
            <div class="flex items-center justify-center space-x-4 mb-12">
                <div class="w-1 h-24 md:h-32 bg-accent opacity-80"></div>
                <div class="text-7xl md:text-9xl font-extrabold tracking-tighter">404</div>
                <div class="w-1 h-24 md:h-32 bg-accent opacity-80"></div>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold mb-4 uppercase tracking-tight">
                Страница не найдена
            </h1>
            <p class="text-lg text-muted-foreground mb-12 max-w-md mx-auto leading-relaxed">
                К сожалению, запрашиваемая страница не существует, была перемещена или находится в разработке.
            </p>

            <a href="../index.php" class="inline-flex items-center space-x-3 bg-accent text-white px-10 py-4 font-bold hover:bg-opacity-90 transition-all group">
                <i data-lucide="home" class="w-5 h-5 transition-transform group-hover:-translate-y-0.5"></i>
                <span>Вернуться на главную</span>
            </a>
            
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>