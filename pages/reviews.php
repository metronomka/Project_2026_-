<?php
include '../config/config.php';

$isLoggedIn = isset($_SESSION['user_id']);

$success_message = '';
$error_message = '';

// Обработка добавления отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id'])) {
        $error_message = 'Для добавления отзыва необходимо войти в аккаунт';
    } else {
        $user_id = (int)$_SESSION['user_id'];
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = e(trim($_POST['comment'] ?? ''));
        $booking_id = !empty($_POST['booking_id']) ? (int)$_POST['booking_id'] : 'NULL';

        if ($rating < 1 || $rating > 5) {
            $error_message = 'Оценка должна быть от 1 до 5';
        } elseif (empty($comment)) {
            $error_message = 'Введите текст отзыва';
        } else {
            if (query("INSERT INTO reviews (user_id, booking_id, rating, comment, is_featured) 
                       VALUES ($user_id, $booking_id, $rating, '$comment', 0)")) {
                $success_message = 'Спасибо за ваш отзыв!';
                $_POST = [];
            } else {
                $error_message = 'Ошибка при сохранении отзыва';
            }
        }
    }
}

// Получение featured-отзывов для слайдера
$featured_reviews = fetch_all(query("
    SELECT r.*, u.name as user_name, s.name as service_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    LEFT JOIN bookings b ON r.booking_id = b.id
    LEFT JOIN services s ON b.service_id = s.id
    WHERE r.is_featured = 1
    ORDER BY r.created_at DESC
    LIMIT 2
"));

// Получение всех отзывов
$all_reviews = fetch_all(query("
    SELECT r.*, u.name as user_name, s.name as service_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    LEFT JOIN bookings b ON r.booking_id = b.id
    LEFT JOIN services s ON b.service_id = s.id
    ORDER BY r.created_at DESC
"));

// Получение бронирований пользователя для формы
$user_bookings = [];
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $user_bookings = fetch_all(query("
        SELECT b.id, s.name as service_name, b.date
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        WHERE b.user_id = $uid AND b.status = 'completed'
    "));
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отзывы — МЕТРОНОМ</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/reviews.css">
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
        .review-slide { display: none; }
        .review-slide.active { display: block; }
        .star-filled { color: #EAB308; fill: #EAB308; }
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

    <div class="py-24 px-4 lg:px-8">
        <div class="container mx-auto">
            <div class="mb-16" data-aos="fade-down">
                <div class="flex items-center mb-6">
                    <div class="w-1 h-16 bg-accent mr-6"></div>
                    <h1 class="text-4xl md:text-5xl font-bold">Отзывы клиентов</h1>
                </div>
                <p class="text-lg text-muted-foreground max-w-3xl">
                    История нашей студии пишется вашими эмоциями. Спасибо, что доверяете нам свои важные моменты.
                </p>
            </div>

            <!-- Уведомления -->
            <?php if ($success_message): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded mb-8">
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded mb-8">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <!-- Слайдер с featured отзывами -->
            <section class="mb-32 bg-secondary p-8 md:p-16 relative" data-aos="fade-up">
                <div id="reviews-slider">
                    <?php if (empty($featured_reviews)): ?>
                        <!-- Заглушки, если нет отзывов -->
                        <div class="review-slide active animate-in fade-in duration-700">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                                <div class="relative aspect-square overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1720456485619-8ef428357cea?q=80&w=800" alt="Анна Смирнова" class="w-full h-full object-cover grayscale">
                                    <div class="absolute bottom-0 right-0 bg-accent p-6">
                                        <i data-lucide="quote" class="w-8 h-8 text-white"></i>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <div class="flex space-x-1">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <i data-lucide="star" class="w-5 h-5 star-filled"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="text-2xl md:text-3xl italic leading-relaxed text-gray-800">
                                        "Потрясающая фотосессия! Фотограф создал невероятно комфортную атмосферу, и результат превзошел все ожидания."
                                    </p>
                                    <div>
                                        <h3 class="text-xl font-bold">Анна Смирнова</h3>
                                        <p class="text-accent">Портретная съемка</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php $slide_index = 0; ?>
                        <?php foreach ($featured_reviews as $review): ?>
                            <div class="review-slide <?= $slide_index === 0 ? 'active' : '' ?> animate-in fade-in duration-700">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                                    <div class="relative aspect-square overflow-hidden">
                                        <img src="https://images.unsplash.com/photo-1720456485619-8ef428357cea?q=80&w=800" alt="<?= htmlspecialchars($review['user_name']) ?>" class="w-full h-full object-cover grayscale">
                                        <div class="absolute bottom-0 right-0 bg-accent p-6">
                                            <i data-lucide="quote" class="w-8 h-8 text-white"></i>
                                        </div>
                                    </div>
                                    <div class="space-y-6">
                                        <div class="flex space-x-1">
                                            <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                                <i data-lucide="star" class="w-5 h-5 star-filled"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="text-2xl md:text-3xl italic leading-relaxed text-gray-800">
                                            "<?= htmlspecialchars($review['comment']) ?>"
                                        </p>
                                        <div>
                                            <h3 class="text-xl font-bold"><?= htmlspecialchars($review['user_name']) ?></h3>
                                            <p class="text-accent"><?= htmlspecialchars($review['service_name'] ?? 'Клиент студии') ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $slide_index++; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="flex space-x-4 mt-12 lg:absolute lg:bottom-16 lg:right-16">
                    <button onclick="prevSlide()" class="p-4 border border-black hover:bg-black hover:text-white transition-all">
                        <i data-lucide="chevron-left"></i>
                    </button>
                    <button onclick="nextSlide()" class="p-4 border border-black hover:bg-black hover:text-white transition-all">
                        <i data-lucide="chevron-right"></i>
                    </button>
                </div>
            </section>

            <!-- Все отзывы -->
            <section>
                <h2 class="text-3xl font-bold mb-12">Все отзывы</h2>
                <?php if (empty($all_reviews)): ?>
                    <div class="text-center py-12 text-muted-foreground">
                        <p>Отзывов пока нет. Будьте первыми!</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php foreach ($all_reviews as $review): ?>
                            <div class="border border-border p-8 hover:border-accent transition-colors" data-aos="fade-up">
                                <div class="flex justify-between items-start mb-6">
                                    <div>
                                        <h4 class="font-bold"><?= htmlspecialchars($review['user_name']) ?></h4>
                                        <p class="text-xs text-muted-foreground">
                                            <?= htmlspecialchars($review['service_name'] ?? 'Клиент студии') ?>
                                        </p>
                                    </div>
                                    <div class="flex text-accent">
                                        <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="text-muted-foreground mb-6 text-sm leading-relaxed">
                                    <?= htmlspecialchars($review['comment']) ?>
                                </p>
                                <span class="text-[10px] text-gray-400 uppercase tracking-widest">
                                    <?= date('d.m.Y', strtotime($review['created_at'])) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <!-- Форма добавления отзыва -->
    <section class="py-24 px-4 lg:px-8 bg-secondary">
        <div class="container mx-auto max-w-2xl">
            <h2 class="text-3xl font-bold mb-8 text-center">Оставить отзыв</h2>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="" class="space-y-6">
                    <div>
                        <label class="block mb-2 text-sm font-semibold">Ваша оценка</label>
                        <div class="flex space-x-2" id="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <label class="cursor-pointer star-label" data-value="<?= $i ?>">
                                    <input type="radio" name="rating" value="<?= $i ?>" class="hidden peer" required>
                                    <i data-lucide="star" class="w-8 h-8 text-gray-300 transition-colors star-icon"></i>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-semibold">Текст отзыва</label>
                        <textarea name="comment" rows="5" required 
                                  class="w-full border px-4 py-3 focus:border-accent outline-none resize-none"
                                  placeholder="Расскажите о вашем опыте работы с нами..."><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
                    </div>
                    
                    <?php if (!empty($user_bookings)): ?>
                        <div>
                            <label class="block mb-2 text-sm font-semibold">Бронирование (необязательно)</label>
                            <select name="booking_id" class="w-full border px-4 py-3 focus:border-accent outline-none">
                                <option value="">Не указано</option>
                                <?php foreach ($user_bookings as $booking): ?>
                                    <option value="<?= $booking['id'] ?>">
                                        <?= htmlspecialchars($booking['service_name']) ?> — <?= date('d.m.Y', strtotime($booking['date'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    
                    <button type="submit" name="submit_review" 
                            class="w-full bg-accent text-white py-3 font-semibold hover:bg-accent/90 transition-colors">
                        Отправить отзыв
                    </button>
                </form>
            <?php else: ?>
                <div class="text-center py-8">
                    <p class="text-muted-foreground mb-4">Для добавления отзыва необходимо войти в аккаунт</p>
                    <a href="login.php" class="text-accent hover:underline font-semibold">Войти</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA секция -->
    <section class="py-24 px-4 lg:px-8 bg-black text-white text-center">
        <div class="container mx-auto max-w-3xl" data-aos="zoom-in">
            <h2 class="text-4xl font-bold mb-6">Станьте частью нашей истории</h2>
            <p class="text-gray-400 mb-10">Забронируйте фотосессию и поделитесь своими впечатлениями</p>
            <a href="booking.php" class="inline-block bg-accent text-white px-12 py-4 font-bold hover:bg-opacity-90 transition-all">
                Забронировать
            </a>
        </div>
    </section>

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
    <script src="../assets/js/reviews.js" defer></script>
    <script>
        lucide.createIcons();
        AOS.init({ once: true });
    </script>
</body>
</html>
