<?php
include '../config/config.php';

$isLoggedIn = isset($_SESSION['user_id']);

// Получение данных пользователя если авторизован
$user_phone = '';
if (isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $user_data = fetch_one(query("SELECT phone FROM users WHERE id = $user_id"));
    $user_phone = $user_data['phone'] ?? '';
}

$success_message = '';
$error_message = '';

// Обработка POST-запроса (сохранение бронирования)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        $error_message = 'Для бронирования необходимо войти в аккаунт.';
    } else {
        $user_id = (int)$user_id;
        $service_id = (int)($_POST['service_id'] ?? 0);
        $photographer_id = (int)($_POST['photographer_id'] ?? 0);
        $date = e($_POST['date'] ?? '');
        $time_slot = e($_POST['time_slot'] ?? '');
        $comment = e(trim($_POST['comment'] ?? ''));
        $addons = $_POST['addons'] ?? [];

        // Валидация
        if (empty($service_id) || empty($photographer_id) || empty($date) || empty($time_slot)) {
            $error_message = 'Заполните все обязательные поля';
        } else {
            // Проверка занятости слота
            $result = query("SELECT id FROM bookings WHERE photographer_id = $photographer_id AND date = '$date' AND time_slot = '$time_slot' AND status != 'cancelled'");

            if (num_rows($result) > 0) {
                $error_message = 'Это время уже забронировано. Выберите другое.';
            }

            // Если нет ошибок — сохраняем
            if (empty($error_message)) {
                // Получение цены услуги
                $service = fetch_one(query("SELECT base_price FROM services WHERE id = $service_id"));
                $total_price = $service['base_price'] ?? 0;

                // Сохранение бронирования
                query("INSERT INTO bookings (user_id, service_id, photographer_id, date, time_slot, total_price, comment) 
                       VALUES ($user_id, $service_id, $photographer_id, '$date', '$time_slot', $total_price, '$comment')");

                $booking_id = last_id();

                // Сохранение доп. услуг
                if (!empty($addons)) {
                    foreach ($addons as $addon_id) {
                        $addon_id = (int)$addon_id;
                        $addon = fetch_one(query("SELECT price FROM addons WHERE id = $addon_id"));
                        if ($addon) {
                            $total_price += $addon['price'];
                            query("INSERT INTO booking_addons (booking_id, addon_id, price_at_booking) 
                                   VALUES ($booking_id, $addon_id, {$addon['price']})");
                        }
                    }
                }

                // Обновление итоговой цены
                query("UPDATE bookings SET total_price = $total_price WHERE id = $booking_id");

                $success_message = 'Бронирование успешно создано!';
                $_POST = [];
            }
        }
    }
}

// Получение данных для формы
$services = fetch_all(query("SELECT * FROM services WHERE is_active = 1 ORDER BY base_price ASC"));
$photographers = fetch_all(query("SELECT * FROM photographers WHERE is_active = 1 ORDER BY base_price ASC"));
$addons = fetch_all(query("SELECT * FROM addons WHERE is_active = 1 ORDER BY price ASC"));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование — МЕТРОНОМ</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/booking.css">
    <script src="../assets/js/main.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#EAB308',
                        secondary: '#F3F4F6',
                        'muted-foreground': '#6B7280',
                        border: '#E5E7EB'
                    }
                }
            }
        }
    </script>
    <style>
        .step-content { display: none; }
        .step-content.active { display: block; }
        .card-selected { border-color: #EAB308; ring: 2px; --tw-ring-color: #EAB308; }
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
            <div class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="w-1 h-16 bg-accent mr-6"></div>
                    <h1 class="text-4xl md:text-5xl font-bold">Бронирование</h1>
                </div>
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

            <div class="mb-12">
                <div class="flex items-center justify-between max-w-3xl mx-auto" id="progress-bar">
                </div>
            </div>

            <form method="POST" action="" id="booking-form">

                <!-- Шаг 1: Выбор услуги -->
                <div id="step-1" class="step-content active max-w-4xl mx-auto">
                    <h2 class="text-2xl font-semibold mb-8">Выберите услугу</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" id="services-grid">
                        <?php foreach ($services as $service): ?>
                            <div onclick="selectService(<?= $service['id'] ?>)" 
                                 class="border p-6 cursor-pointer transition-all hover:border-accent <?= (($_POST['service_id'] ?? 0) == $service['id']) ? 'ring-2 ring-accent border-accent' : '' ?>">
                                <h3 class="text-lg font-semibold mb-2"><?= htmlspecialchars($service['name']) ?></h3>
                                <p class="text-sm text-muted-foreground mb-4">⏱ <?= $service['duration_minutes'] ?> мин</p>
                                <div class="text-xl font-bold"><?= number_format($service['base_price'], 0, '.', ' ') ?> ₽</div>
                                <input type="radio" name="service_id" value="<?= $service['id'] ?>" class="hidden" 
                                       <?= (($_POST['service_id'] ?? 0) == $service['id']) ? 'checked' : '' ?>>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Шаг 2: Выбор фотографа и доп. услуг -->
                <div id="step-2" class="step-content max-w-4xl mx-auto">
                    <h2 class="text-2xl font-semibold mb-8">Выберите фотографа</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" id="photographers-grid">
                        <?php foreach ($photographers as $photographer): ?>
                            <div onclick="selectPhotographer(<?= $photographer['id'] ?>)" 
                                 class="border p-6 cursor-pointer text-center hover:border-accent <?= (($_POST['photographer_id'] ?? 0) == $photographer['id']) ? 'ring-2 ring-accent border-accent' : '' ?>">
                                <div class="w-16 h-16 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center font-bold text-xl">
                                    <?= mb_substr($photographer['name'], 0, 1) ?>
                                </div>
                                <h3 class="font-semibold"><?= htmlspecialchars($photographer['name']) ?></h3>
                                <p class="text-xs text-muted-foreground"><?= htmlspecialchars($photographer['specialty']) ?></p>
                                <input type="radio" name="photographer_id" value="<?= $photographer['id'] ?>" class="hidden"
                                       <?= (($_POST['photographer_id'] ?? 0) == $photographer['id']) ? 'checked' : '' ?>>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="bg-secondary p-6">
                        <h3 class="text-lg font-semibold mb-4">Дополнительные услуги</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="addons-grid">
                            <?php foreach ($addons as $addon): ?>
                                <div onclick="toggleAddon('<?= $addon['id'] ?>')" 
                                     class="border p-4 cursor-pointer flex justify-between hover:border-accent <?= in_array($addon['id'], $_POST['addons'] ?? []) ? 'bg-accent/10 border-accent' : '' ?>">
                                    <span><?= htmlspecialchars($addon['name']) ?></span>
                                    <span class="text-muted-foreground">+<?= number_format($addon['price'], 0, '.', ' ') ?> ₽</span>
                                    <input type="checkbox" name="addons[]" value="<?= $addon['id'] ?>" class="hidden"
                                           <?= in_array($addon['id'], $_POST['addons'] ?? []) ? 'checked' : '' ?>>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Шаг 3: Дата и время -->
                <div id="step-3" class="step-content max-w-4xl mx-auto">
                    <h2 class="text-2xl font-semibold mb-8">Выберите дату и время</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <h3 class="font-semibold mb-4">Дата съемки</h3>
                            <input type="date" name="date" id="date-input" 
                                   value="<?= htmlspecialchars($_POST['date'] ?? '') ?>"
                                   min="<?= date('Y-m-d') ?>"
                                   class="w-full border p-4 focus:border-accent outline-none">
                        </div>
                        <div>
                            <h3 class="font-semibold mb-4">Время съемки</h3>
                            <div class="grid grid-cols-3 gap-3" id="time-slots">
                                <?php
                                $timeSlots = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];
                                foreach ($timeSlots as $time):
                                ?>
                                    <button type="button" onclick="selectTime('<?= $time ?>')" 
                                            class="border py-3 hover:border-accent <?= (($_POST['time_slot'] ?? '') === $time) ? 'bg-accent text-white border-accent' : '' ?>">
                                        <?= $time ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Шаг 4: Подтверждение -->
                <div id="step-4" class="step-content max-w-4xl mx-auto">
                    <h2 class="text-2xl font-semibold mb-8">Подтверждение бронирования</h2>
                    <div class="bg-secondary p-8 mb-8">
                        <div class="space-y-6" id="summary-content">
                            <!-- Заполняется через JS -->
                        </div>
                    </div>

                    <div class="border border-border p-6 mb-8">
                        <h3 class="font-semibold mb-4">Контактная информация</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="name" placeholder="Ваше имя" required 
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                                   class="border px-4 py-3 focus:border-accent outline-none">
                            <input type="tel" name="phone" placeholder="Телефон" required 
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                                   class="border px-4 py-3 focus:border-accent outline-none">
                            <input type="email" name="email" placeholder="Email" required 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   class="border px-4 py-3 focus:border-accent outline-none col-span-full">
                        </div>
                        <textarea name="comment" placeholder="Комментарий к заказу" rows="3" 
                                  class="w-full border px-4 py-3 mt-4 focus:border-accent outline-none"><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
                    </div>

                    <!-- Скрытые поля для отправки данных -->
                    <input type="hidden" name="service_id" id="hidden-service-id" value="<?= (int)($_POST['service_id'] ?? 0) ?>">
                    <input type="hidden" name="photographer_id" id="hidden-photographer-id" value="<?= (int)($_POST['photographer_id'] ?? 0) ?>">
                    <input type="hidden" name="date" id="hidden-date" value="<?= htmlspecialchars($_POST['date'] ?? '') ?>">
                    <input type="hidden" name="time_slot" id="hidden-time-slot" value="<?= htmlspecialchars($_POST['time_slot'] ?? '') ?>">

                    <button type="submit" name="submit_booking" class="w-full bg-accent text-white py-4 text-lg hover:bg-accent/90 transition-colors">
                        Подтвердить бронирование
                    </button>
                </div>

                <!-- Навигация -->
                <div class="flex justify-between mt-12 max-w-4xl mx-auto">
                    <button type="button" id="prev-btn" class="px-8 py-3 border border-foreground hover:bg-foreground hover:text-white disabled:opacity-50 disabled:cursor-not-allowed">
                        Назад
                    </button>
                    <button type="button" id="next-btn" class="px-8 py-3 bg-accent text-white hover:bg-accent/90 disabled:opacity-50">
                        Далее
                    </button>
                </div>
            </form>
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

    <script>
        // Передаём данные из PHP в JavaScript
        window.bookingData = {
            data: {
                services: <?= json_encode($services) ?>,
                photographers: <?= json_encode($photographers) ?>,
                addons: <?= json_encode($addons) ?>
            },
            state: {
                service: <?= (int)($_POST['service_id'] ?? 0) ?>,
                photographer: <?= (int)($_POST['photographer_id'] ?? 0) ?>,
                addons: <?= json_encode($_POST['addons'] ?? []) ?>,
                date: '<?= htmlspecialchars($_POST['date'] ?? '') ?>',
                time: '<?= htmlspecialchars($_POST['time_slot'] ?? '') ?>'
            },
            user: {
                name: <?= isset($_SESSION['user_name']) ? json_encode($_SESSION['user_name']) : '""' ?>,
                email: <?= isset($_SESSION['user_email']) ? json_encode($_SESSION['user_email']) : '""' ?>,
                phone: <?= json_encode($user_phone) ?>
            }
        };
    </script>
    <script src="../assets/js/booking.js" defer></script>
</body>
</html>
