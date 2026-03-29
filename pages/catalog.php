<?php
include '../config/config.php';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог услуг — МЕТРОНОМ</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/catalog.css">
    <script src="../assets/js/main.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#EAB308',
                        secondary: '#F9FAFB',
                        border: '#E5E7EB',
                        'muted-foreground': '#6B7280',
                    }
                }
            }
        }
    </script>
    <style>
        .grayscale { filter: grayscale(100%); }
        .selected-ring { ring: 2px solid #EAB308; box-shadow: 0 0 0 2px #EAB308; }
        .service-active { border-color: #EAB308; background-color: rgba(234, 179, 8, 0.05); }
        .check-box-active { border-color: #EAB308; background-color: #EAB308; }
    </style>
</head>
<body class="bg-white text-gray-900 font-sans overflow-x-hidden">

    <!-- HEADER -->
    <header class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md z-50 border-b border-gray-100">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="../index.php" class="flex items-center space-x-2 group">
                    <div class="w-8 h-8 bg-accent flex items-center justify-center transition-transform group-hover:rotate-90">
                        <div class="w-1 h-6 bg-white"></div>
                    </div>
                    <span class="text-xl font-bold tracking-tighter uppercase">Метроном</span>
                </a>
                <nav class="hidden md:flex items-center space-x-8" id="main-nav">
                    <a href="catalog.php" class="nav-link text-sm font-medium hover:text-accent transition-colors">Услуги</a>
                    <a href="about.php" class="nav-link text-sm font-medium hover:text-accent transition-colors">О нас</a>
                    <a href="reviews.php" class="nav-link text-sm font-medium hover:text-accent transition-colors">Отзывы</a>
                    <a href="booking.php" class="text-sm font-medium text-accent border border-accent px-4 py-2 hover:bg-accent hover:text-white transition-all">Забронировать</a>
                </nav>
                <div class="flex items-center space-x-5">
                    <?php if ($isLoggedIn): ?>
                        <a href="profile.php" class="hover:text-accent transition-colors" title="Личный кабинет">
                            <i data-lucide="user" class="w-6 h-6"></i>
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="hidden sm:block text-sm font-semibold hover:text-accent transition-colors">Войти</a>
                    <?php endif; ?>
                    <button class="md:hidden" id="mobile-menu-btn">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-gray-100 absolute w-full left-0 px-4 py-6 space-y-4 shadow-xl">
            <a href="catalog.php" class="block text-lg font-semibold">Услуги</a>
            <a href="about.php" class="block text-lg font-semibold">О нас</a>
            <a href="reviews.php" class="block text-lg font-semibold">Отзывы</a>
            <a href="booking.php" class="block bg-accent text-white text-center py-3 font-bold">Забронировать</a>
        </div>
    </header>
    <div class="h-20"></div>
    <!-- /HEADER -->

    <?php
    // Получение фотографов из БД
    $photographers = fetch_all(query("SELECT * FROM photographers WHERE is_active = 1 ORDER BY base_price ASC"));

    // Получение услуг из БД
    $services = fetch_all(query("SELECT * FROM services WHERE is_active = 1 ORDER BY base_price ASC"));

    // Получение доп. услуг из БД
    $addons = fetch_all(query("SELECT * FROM addons WHERE is_active = 1 ORDER BY price ASC"));
    ?>

    <div class="min-h-screen py-24 px-4 lg:px-8">
        <div class="container mx-auto">
            <div class="mb-16" data-aos="fade-down">
                <div class="flex items-center mb-6">
                    <div class="w-1 h-16 bg-accent mr-6"></div>
                    <h1 class="text-4xl md:text-5xl font-bold">Каталог услуг</h1>
                </div>
                <p class="text-lg text-muted-foreground max-w-3xl">
                    Выберите фотографа и дополнительные услуги для создания идеальной фотосессии. Мы собрали лучших мастеров своего дела.
                </p>
            </div>

            <!-- Фотографы -->
            <div class="mb-16">
                <h2 class="text-2xl font-semibold mb-8" data-aos="fade-in">Наши фотографы</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="photographers-grid">
                    <?php foreach ($photographers as $photographer): ?>
                        <div class="photographer-card group cursor-pointer transition-all" 
                             data-id="<?= $photographer['id'] ?>" 
                             data-price="<?= $photographer['base_price'] ?>"
                             data-aos="fade-up" 
                             data-aos-delay="100">
                            <div class="relative overflow-hidden aspect-[3/4] mb-4">
                                <?php if (!empty($photographer['photo_url'])): ?>
                                    <img src="../<?= htmlspecialchars($photographer['photo_url']) ?>" 
                                         alt="<?= htmlspecialchars($photographer['name']) ?>"
                                         class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-500">
                                <?php else: ?>
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center grayscale group-hover:grayscale-0">
                                        <span class="text-6xl font-bold text-gray-400"><?= mb_substr($photographer['name'], 0, 1) ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="select-indicator absolute top-4 right-4 w-8 h-8 bg-accent hidden items-center justify-center">
                                    <i data-lucide="check" class="w-5 h-5 text-white"></i>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-xl font-semibold"><?= htmlspecialchars($photographer['name']) ?></h3>
                                <p class="text-sm text-muted-foreground"><?= htmlspecialchars($photographer['specialty']) ?></p>
                                <div class="flex items-center justify-between pt-2">
                                    <div class="flex items-center space-x-1 text-sm text-muted-foreground">
                                        <i data-lucide="award" class="w-4 h-4"></i>
                                        <span><?= $photographer['experience_years'] ?> лет опыта</span>
                                    </div>
                                    <span class="font-semibold text-lg">от <?= number_format($photographer['base_price'], 0, '.', ' ') ?> ₽</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Конструктор услуг -->
            <div id="constructor-section" class="hidden opacity-0 transition-opacity duration-500 bg-secondary p-6 md:p-8 mb-16 border border-border">
                <h2 class="text-2xl font-semibold mb-8">Конструктор услуг</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <?php foreach ($addons as $addon): ?>
                        <div class="service-item flex items-center justify-between p-4 border border-border bg-white cursor-pointer transition-colors" 
                             data-price="<?= $addon['price'] ?>">
                            <div class="flex items-center space-x-3">
                                <div class="check-box w-5 h-5 border-2 border-gray-300 flex items-center justify-center transition-all">
                                    <i data-lucide="plus" class="w-3 h-3 text-white hidden rotate-45"></i>
                                </div>
                                <span class="font-medium"><?= htmlspecialchars($addon['name']) ?></span>
                            </div>
                            <span class="text-muted-foreground text-sm">+<?= number_format($addon['price'], 0, '.', ' ') ?> ₽</span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center justify-between text-xl mb-6">
                        <span class="font-semibold">Предварительная стоимость:</span>
                        <span class="font-bold text-accent text-2xl" id="total-price">0 ₽</span>
                    </div>
                    <a href="booking.php" class="block w-full bg-accent text-white text-center px-8 py-4 font-semibold hover:bg-opacity-90 transition-all">
                        Перейти к бронированию
                    </a>
                </div>
            </div>

            <!-- Типы фотосессий -->
            <div class="mt-24">
                <h2 class="text-2xl font-semibold mb-8" data-aos="fade-in">Типы фотосессий</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($services as $service): ?>
                        <div class="border border-border p-6 hover:border-accent group transition-all" data-aos="fade-up">
                            <h3 class="text-xl font-semibold mb-2 group-hover:text-accent transition-colors">
                                <?= htmlspecialchars($service['name']) ?>
                            </h3>
                            <div class="flex items-center justify-between text-sm text-muted-foreground">
                                <div class="flex items-center space-x-2">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                    <span><?= $service['duration_minutes'] ?> мин</span>
                                </div>
                                <span class="font-semibold text-gray-900 text-base">
                                    от <?= number_format($service['base_price'], 0, '.', ' ') ?> ₽
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-gray-50 border-t border-gray-200 pt-16 pb-8 mt-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-6 h-6 bg-accent flex items-center justify-center"><div class="w-0.5 h-4 bg-white"></div></div>
                        <span class="text-lg font-bold uppercase">Метроном</span>
                    </div>
                    <p class="text-sm text-gray-500">Эстетика в каждом кадре.</p>
                </div>
                </div>
            <div class="border-t border-gray-200 pt-8 text-center text-[10px] text-gray-400 uppercase tracking-widest">
                © 2026 МЕТРОНОМ. Все права защищены.
            </div>
        </div>
    </footer>
    <!-- /FOOTER -->

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../assets/js/catalog.js" defer></script>
    <script>
        lucide.createIcons();
        AOS.init({ once: true });
    </script>
</body>
</html>
