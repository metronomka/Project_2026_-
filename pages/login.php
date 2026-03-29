<?php
include '../config/config.php';

$errors = [];
$is_login = true;

// Если уже авторизован — редирект в профиль
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Регистрация
    if (isset($_POST['register'])) {
        $is_login = false;
        $name = trim($_POST['name'] ?? '');

        // Валидация
        if (empty($name)) $errors[] = 'Введите имя';
        if (empty($email)) $errors[] = 'Введите email';
        if (empty($password)) $errors[] = 'Введите пароль';
        if (strlen($password) < 6) $errors[] = 'Пароль должен быть не менее 6 символов';

        // Проверка на существующий email
        if (!empty($email)) {
            $email_e = e($email);
            $result = query("SELECT id FROM users WHERE email = '$email_e'");
            if (num_rows($result) > 0) {
                $errors[] = 'Email уже зарегистрирован';
            }
        }

        // Если нет ошибок — регистрируем
        if (empty($errors)) {
            $name_e = e($name);
            $email_e = e($email);
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            
            query("INSERT INTO users (name, email, password) VALUES ('$name_e', '$email_e', '$hashed')");
            
            // Автоматический вход после регистрации
            $_SESSION['user_id'] = last_id();
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            header('Location: profile.php');
            exit;
        }

    // Вход
    } elseif (isset($_POST['login'])) {
        if (empty($email)) $errors[] = 'Введите email';
        if (empty($password)) $errors[] = 'Введите пароль';

        if (empty($errors)) {
            $email_e = e($email);
            $user = fetch_one(query("SELECT id, name, email, password FROM users WHERE email = '$email_e'"));

            if ($user && password_verify($password, $user['password'])) {
                // Успешный вход
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                header('Location: profile.php');
                exit;
            } else {
                $errors[] = 'Неверный email или пароль';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — МЕТРОНОМ</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="../assets/js/main.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#EAB308',
                        border: '#E5E7EB',
                        muted: '#F3F4F6',
                        'muted-foreground': '#6B7280'
                    }
                }
            }
        }
        
        // Передаём режим в JS
        const isLoginMode = <?= $is_login ? 'true' : 'false' ?>;
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hidden { display: none; }
    </style>
</head>
<body class="bg-white text-gray-900">

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

    <div class="min-h-screen flex items-center justify-center px-4 py-24">
        <div class="w-full max-w-md">

            <div class="text-center mb-8">
                <div class="inline-flex items-center space-x-2 mb-6">
                    <div class="w-8 h-8 bg-accent flex items-center justify-center">
                        <div class="w-1 h-6 bg-white"></div>
                    </div>
                    <span class="text-2xl font-bold tracking-tight">МЕТРОНОМ</span>
                </div>
                <h1 id="form-title" class="text-3xl font-bold mb-2">Вход в аккаунт</h1>
                <p id="form-subtitle" class="text-muted-foreground">Войдите, чтобы управлять бронированиями</p>
            </div>

            <!-- Вывод ошибок -->
            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside text-sm">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="relative mb-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-border"></div></div>
                <div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-muted-foreground">или</span></div>
            </div>

            <form id="auth-form" class="space-y-4" method="POST">
                <div id="name-field" class="hidden">
                    <label class="block mb-2 text-sm">Имя</label>
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <input type="text" name="name" placeholder="Введите ваше имя" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" class="w-full border border-border bg-white pl-12 pr-4 py-3 focus:outline-none focus:border-accent transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm">Email</label>
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <input type="email" name="email" required placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" class="w-full border border-border bg-white pl-12 pr-4 py-3 focus:outline-none focus:border-accent transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm">Пароль</label>
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full border border-border bg-white pl-12 pr-4 py-3 focus:outline-none focus:border-accent transition-colors">
                    </div>
                </div>

                <div id="remember-me" class="flex items-center justify-between text-sm">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-accent focus:ring-accent">
                        <span>Запомнить меня</span>
                    </label>
                    <a href="#" class="text-accent hover:underline">Забыли пароль?</a>
                </div>

                <button type="submit" name="login" id="submit-btn" class="w-full bg-accent text-white py-3 hover:bg-accent/90 transition-colors font-medium">
                    Войти
                </button>
            </form>

            <div class="mt-6 text-center text-sm">
                <span id="toggle-text" class="text-muted-foreground">Нет аккаунта?</span>
                <button id="toggle-btn" class="text-accent hover:underline font-medium">Зарегистрироваться</button>
            </div>

            <div class="mt-8 text-center">
                <a href="../index.php" class="text-sm text-muted-foreground hover:text-accent transition-colors">
                    ← Вернуться на главную
                </a>
            </div>
        </div>
    </div>

    <script>
        // Используем режим из PHP
        let isLogin = isLoginMode;

        const toggleBtn = document.getElementById('toggle-btn');
        const toggleText = document.getElementById('toggle-text');
        const formTitle = document.getElementById('form-title');
        const formSubtitle = document.getElementById('form-subtitle');
        const nameField = document.getElementById('name-field');
        const rememberMe = document.getElementById('remember-me');
        const submitBtn = document.getElementById('submit-btn');
        const authForm = document.getElementById('auth-form');

        // Инициализация состояния при загрузке
        function updateFormState() {
            formTitle.textContent = isLogin ? 'Вход в аккаунт' : 'Регистрация';
            formSubtitle.textContent = isLogin ? 'Войдите, чтобы управлять бронированиями' : 'Создайте аккаунт для бронирования';
            toggleText.textContent = isLogin ? 'Нет аккаунта?' : 'Уже есть аккаунт?';
            toggleBtn.textContent = isLogin ? 'Зарегистрироваться' : 'Войти';
            submitBtn.textContent = isLogin ? 'Войти' : 'Зарегистрироваться';
            submitBtn.setAttribute('name', isLogin ? 'login' : 'register');
            
            // Переключение полей
            nameField.classList.toggle('hidden', isLogin);
            rememberMe.classList.toggle('hidden', !isLogin);
        }

        // Запуск при загрузке
        updateFormState();

        toggleBtn.addEventListener('click', () => {
            isLogin = !isLogin;
            updateFormState();
        });

        // Форма отправляется на сервер (не предотвращаем)
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