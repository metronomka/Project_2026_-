<?php
include '../config/config.php';

// Проверка: авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$isLoggedIn = true;

// Получение актуальных данных пользователя из БД
$user = fetch_one(query("SELECT name, email, phone FROM users WHERE id = $user_id"));

// Получение бронирований пользователя
$bookings_result = query("
    SELECT b.*, s.name as service_name, p.name as photographer_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN photographers p ON b.photographer_id = p.id
    WHERE b.user_id = $user_id
    ORDER BY b.date DESC, b.time_slot DESC
");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет — МЕТРОНОМ</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
    <script src="../assets/js/main.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#EAB308',
                        border: '#E5E7EB',
                        secondary: '#F3F4F6',
                        'muted-foreground': '#6B7280'
                    }
                }
            }
        }
    </script>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-link.active { border-color: #EAB308; color: #000; font-weight: 600; }
        .grayscale { filter: grayscale(100%); }
        .grayscale:hover { filter: grayscale(0%); }
    </style>
</head>
<body class="bg-white text-gray-900 font-sans">

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

    <div class="min-h-screen py-24 px-4 lg:px-8">
        <div class="container mx-auto max-w-6xl">

            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-12 pb-12 border-b border-border">
                <div class="flex items-center space-x-6 mb-6 md:mb-0">
                    <div class="w-24 h-24 bg-secondary flex items-center justify-center border border-border">
                        <i data-lucide="user" class="w-12 h-12 text-muted-foreground"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold"><?= htmlspecialchars($user['name']) ?></h1>
                        <p class="text-muted-foreground"><?= htmlspecialchars($user['email']) ?></p>
                        <div class="mt-2 flex space-x-4">
                            <button class="text-sm text-accent hover:underline flex items-center">
                                <i data-lucide="edit-2" class="w-3 h-3 mr-1"></i> Редактировать
                            </button>
                            <a href="../logout.php" class="text-sm text-red-500 hover:underline flex items-center">
                                <i data-lucide="log-out" class="w-3 h-3 mr-1"></i> Выйти
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex space-x-8 mb-8 border-b border-border">
                <button onclick="switchTab('bookings')" id="tab-link-bookings" class="tab-link active pb-4 border-b-2 border-transparent transition-all">
                    Мои бронирования
                </button>
                <button onclick="switchTab('photos')" id="tab-link-photos" class="tab-link pb-4 border-b-2 border-transparent transition-all">
                    Мои фотографии
                </button>
            </div>

            <div id="tab-bookings" class="tab-content active">
                <div class="space-y-6">
                    <?php if (mysqli_num_rows($bookings_result) === 0): ?>
                        <div class="text-center py-12 text-muted-foreground">
                            <i data-lucide="calendar" class="w-16 h-16 mx-auto mb-4 opacity-50"></i>
                            <p>У вас пока нет бронирований</p>
                            <a href="booking.php" class="text-accent hover:underline mt-2 inline-block">Забронировать фотосессию</a>
                        </div>
                    <?php else: ?>
                        <?php while ($booking = mysqli_fetch_assoc($bookings_result)): ?>
                            <?php
                            // Статус бронирования
                            $status_class = match($booking['status']) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'confirmed' => 'bg-green-100 text-green-700',
                                'completed' => 'bg-gray-100 text-gray-600',
                                'cancelled' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-600'
                            };
                            $status_name = match($booking['status']) {
                                'pending' => 'Ожидает подтверждения',
                                'confirmed' => 'Подтверждено',
                                'completed' => 'Завершено',
                                'cancelled' => 'Отменено',
                                default => $booking['status']
                            };
                            
                            // Форматирование даты
                            $date_obj = new DateTime($booking['date']);
                            $date_formatted = $date_obj->format('d.m.Y');
                            ?>
                            
                            <div class="border border-border p-6 flex flex-col md:flex-row justify-between items-start md:items-center group hover:border-accent transition-colors">
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-accent/10 flex items-center justify-center text-accent">
                                        <i data-lucide="calendar"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-3 mb-1">
                                            <h3 class="text-xl font-semibold"><?= htmlspecialchars($booking['service_name']) ?></h3>
                                            <span class="px-2 py-1 <?= $status_class ?> text-[10px] font-bold uppercase tracking-wider">
                                                <?= $status_name ?>
                                            </span>
                                        </div>
                                        <p class="text-sm text-muted-foreground">Фотограф: <?= htmlspecialchars($booking['photographer_name']) ?></p>
                                        <p class="text-sm font-medium mt-2"><?= $date_formatted ?> • <?= substr($booking['time_slot'], 0, 5) ?></p>
                                    </div>
                                </div>
                                <div class="mt-4 md:mt-0 text-right">
                                    <div class="text-xl font-bold mb-2"><?= number_format($booking['total_price'], 0, '.', ' ') ?> ₽</div>
                                    <?php if ($booking['status'] === 'pending'): ?>
                                        <button class="text-sm border border-black px-4 py-2 hover:bg-black hover:text-white transition-colors"
                                                onclick="cancelBooking(<?= $booking['id'] ?>)">
                                            Отменить
                                        </button>
                                    <?php elseif ($booking['status'] === 'completed'): ?>
                                        <button class="text-sm text-accent font-semibold hover:underline"
                                                onclick="leaveReview(<?= $booking['id'] ?>)">
                                            Оставить отзыв
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div id="tab-photos" class="tab-content">
                <div class="grid grid-cols-1 gap-12">
                    <div class="border border-border p-8">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                            <div>
                                <h3 class="text-2xl font-bold mb-1">Портретная съемка - 25 фев 2026</h3>
                                <p class="text-muted-foreground">25 фотографий в высоком качестве</p>
                            </div>
                            <button class="mt-4 md:mt-0 bg-accent text-white px-8 py-3 flex items-center space-x-2 hover:bg-accent/90 transition-colors">
                                <i data-lucide="download" class="w-4 h-4"></i>
                                <span>Скачать всё (.zip)</span>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div class="aspect-square bg-gray-200 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1706824258534-c3740a1ae96b?w=400" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-500 cursor-pointer">
                            </div>
                            <div class="aspect-square bg-gray-200 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-500 cursor-pointer">
                            </div>
                            <div class="aspect-square bg-gray-200 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-500 cursor-pointer">
                            </div>
                            <div class="aspect-square bg-gray-200 overflow-hidden flex items-center justify-center border-2 border-dashed border-border text-muted-foreground">
                                +22 фото
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="../assets/js/profile.js" defer></script>
    <script>
        lucide.createIcons();
    </script>

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
</body>
</html>