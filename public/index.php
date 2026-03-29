<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>МЕТРОНОМ — Профессиональная фотостудия</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="main.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#EAB308', // Цвет "accent" из кода (желтый/золотой)
                        secondary: '#F3F4F6',
                        'muted-foreground': '#6B7280',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        .grayscale { filter: grayscale(100%); }
        .group:hover .grayscale-0 { filter: grayscale(0%); }
    </style>
</head>
<body class="bg-white text-gray-900 font-sans overflow-x-hidden">

    <!-- HEADER -->
    <header class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md z-50 border-b border-gray-100">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="index.php" class="flex items-center space-x-2 group">
                    <div class="w-8 h-8 bg-accent flex items-center justify-center transition-transform group-hover:rotate-90">
                        <div class="w-1 h-6 bg-white"></div>
                    </div>
                    <span class="text-xl font-bold tracking-tighter uppercase">Метроном</span>
                </a>
                <nav class="hidden md:flex items-center space-x-8" id="main-nav">
                    <a href="pages/catalog.php" class="nav-link text-sm font-medium hover:text-accent transition-colors">Услуги</a>
                    <a href="pages/about.php" class="nav-link text-sm font-medium hover:text-accent transition-colors">О нас</a>
                    <a href="pages/reviews.php" class="nav-link text-sm font-medium hover:text-accent transition-colors">Отзывы</a>
                    <a href="pages/booking.php" class="text-sm font-medium text-accent border border-accent px-4 py-2 hover:bg-accent hover:text-white transition-all">Забронировать</a>
                </nav>
                <div class="flex items-center space-x-5">
                    <?php if ($isLoggedIn): ?>
                        <a href="pages/profile.php" class="hover:text-accent transition-colors" title="Личный кабинет">
                            <i data-lucide="user" class="w-6 h-6"></i>
                        </a>
                    <?php else: ?>
                        <a href="pages/login.php" class="hidden sm:block text-sm font-semibold hover:text-accent transition-colors">Войти</a>
                    <?php endif; ?>
                    <button class="md:hidden" id="mobile-menu-btn">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-gray-100 absolute w-full left-0 px-4 py-6 space-y-4 shadow-xl">
            <a href="pages/catalog.php" class="block text-lg font-semibold">Услуги</a>
            <a href="pages/about.php" class="block text-lg font-semibold">О нас</a>
            <a href="pages/reviews.php" class="block text-lg font-semibold">Отзывы</a>
            <a href="pages/booking.php" class="block bg-accent text-white text-center py-3 font-bold">Забронировать</a>
        </div>
    </header>
    <div class="h-20"></div>
    <!-- /HEADER -->

    <section class="relative h-screen flex items-center justify-center overflow-hidden bg-black">
        <div class="absolute inset-0 bg-cover bg-center grayscale opacity-60 scale-110 transition-transform duration-[1500ms]" 
             id="hero-bg"
             style="background-image: url('https://images.unsplash.com/photo-1758613655205-d9bcdba2404d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxwcm9mZXNzaW9uYWwlMjBwaG90b2dyYXBoeSUyMHN0dWRpbyUyMGJsYWNrJTIwd2hpdGV8ZW58MXx8fHwxNzcyNTE0MzUwfDA&ixlib=rb-4.1.0&q=80&w=1080');">
        </div>
        <div class="absolute inset-0 bg-black/50"></div>
        
        <div class="relative z-10 text-center px-4">
            <div class="inline-flex items-center space-x-4 mb-6" data-aos="fade-down" data-aos-duration="800">
                <div class="w-1 h-24 bg-accent origin-top transition-all duration-600 delay-500"></div>
                <h1 class="text-6xl md:text-8xl font-bold text-white tracking-tight">
                    МЕТРОНОМ
                </h1>
                <div class="w-1 h-24 bg-accent origin-top transition-all duration-600 delay-500"></div>
            </div>
            <p class="text-xl md:text-2xl text-white/90 mb-12 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="800">
                Профессиональная фотостудия, где каждый кадр — в идеальном ритме
            </p>
            <div data-aos="fade-up" data-aos-delay="1000">
                <a href="pages/booking.php" class="inline-block bg-accent text-white px-12 py-4 text-lg hover:bg-opacity-90 transition-colors">
                    Забронировать съемку
                </a>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 right-0 h-1 bg-accent origin-left" data-aos="stretch-x" data-aos-delay="1200"></div>
    </section>

    <section class="py-24 px-4 lg:px-8">
        <div class="container mx-auto">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center mb-8" data-aos="fade-right">
                    <div class="w-1 h-16 bg-accent mr-6"></div>
                    <h2 class="text-4xl md:text-5xl font-bold">О нас</h2>
                </div>
                <p class="text-lg text-muted-foreground leading-relaxed mb-8" data-aos="fade-up" data-aos-delay="200">
                    Метроном — это не просто фотостудия. Это пространство, где профессионализм встречается с 
                    творчеством, а современное оборудование служит искусству. Мы создаем атмосферу, в которой 
                    каждый кадр становится произведением искусства.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                    <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                        <div class="text-4xl font-bold text-accent mb-2">5+</div>
                        <div class="text-muted-foreground">Лет опыта</div>
                    </div>
                    <div class="text-center" data-aos="fade-up" data-aos-delay="400">
                        <div class="text-4xl font-bold text-accent mb-2">1000+</div>
                        <div class="text-muted-foreground">Довольных клиентов</div>
                    </div>
                    <div class="text-center" data-aos="fade-up" data-aos-delay="500">
                        <div class="text-4xl font-bold text-accent mb-2">10+</div>
                        <div class="text-muted-foreground">Профессионалов</div>
                    </div>
                </div>
                <div class="mt-8" data-aos="fade-in" data-aos-delay="600">
                    <a href="pages/about.php" class="inline-block border border-black text-black px-8 py-3 hover:bg-black hover:text-white transition-colors">
                        Узнать больше
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 px-4 lg:px-8 bg-gray-100">
        <div class="container mx-auto">
            <div class="flex items-center mb-12" data-aos="fade-right">
                <div class="w-1 h-16 bg-accent mr-6"></div>
                <h2 class="text-4xl md:text-5xl font-bold">Популярные услуги</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="group" data-aos="fade-up" data-aos-delay="100">
                    <a href="pages/catalog.php" class="block">
                        <div class="relative overflow-hidden aspect-[3/4] mb-4">
                            <img src="https://images.unsplash.com/photo-1706824258534-c3740a1ae96b?q=80&w=1080" alt="Портретная съемка" 
                                 class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-500">
                            <div class="absolute inset-0 border-2 border-transparent group-hover:border-accent transition-colors"></div>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-semibold group-hover:text-accent transition-colors">Портретная съемка</h3>
                            <div class="flex items-center justify-between text-sm text-muted-foreground">
                                <div class="flex items-center space-x-1">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                    <span>1 час</span>
                                </div>
                                <span class="font-semibold text-black">от 5 000 ₽</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="group" data-aos="fade-up" data-aos-delay="200">
                    <a href="pages/catalog.php" class="block">
                        <div class="relative overflow-hidden aspect-[3/4] mb-4">
                            <img src="https://images.unsplash.com/photo-1758613654707-8bdab92f711d?q=80&w=1080" alt="Fashion съемка" 
                                 class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-500">
                            <div class="absolute inset-0 border-2 border-transparent group-hover:border-accent transition-colors"></div>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-semibold group-hover:text-accent transition-colors">Fashion съемка</h3>
                            <div class="flex items-center justify-between text-sm text-muted-foreground">
                                <div class="flex items-center space-x-1">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                    <span>2 часа</span>
                                </div>
                                <span class="font-semibold text-black">от 8 000 ₽</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="group" data-aos="fade-up" data-aos-delay="300">
                    <a href="pages/catalog.php" class="block">
                        <div class="relative overflow-hidden aspect-[3/4] mb-4">
                            <img src="https://images.unsplash.com/photo-1593968007877-b351c54bef61?q=80&w=1080" alt="Семейная фотосессия" 
                                 class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-500">
                            <div class="absolute inset-0 border-2 border-transparent group-hover:border-accent transition-colors"></div>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-semibold group-hover:text-accent transition-colors">Семейная фотосессия</h3>
                            <div class="flex items-center justify-between text-sm text-muted-foreground">
                                <div class="flex items-center space-x-1">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                    <span>1.5 часа</span>
                                </div>
                                <span class="font-semibold text-black">от 7 000 ₽</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="mt-12 text-center" data-aos="fade-up">
                <a href="pages/catalog.php" class="inline-block bg-accent text-white px-10 py-3 hover:bg-opacity-90 transition-colors">
                    Смотреть все услуги
                </a>
            </div>
        </div>
    </section>

    <section class="py-24 px-4 lg:px-8">
        <div class="container mx-auto">
            <div class="flex items-center mb-12" data-aos="fade-right">
                <div class="w-1 h-16 bg-accent mr-6"></div>
                <h2 class="text-4xl md:text-5xl font-bold">Как мы работаем</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="relative flex items-start space-x-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 border-2 border-accent flex items-center justify-center hover:rotate-[360deg] transition-transform duration-500">
                            <i data-lucide="calendar" class="w-8 h-8 text-accent"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-6xl font-bold text-gray-200 absolute -top-4 right-0 z-[-1]">01</div>
                        <h3 class="text-xl font-semibold mb-2">Выберите дату</h3>
                        <p class="text-muted-foreground">Забронируйте удобное время в нашем онлайн-календаре</p>
                    </div>
                </div>
                <div class="relative flex items-start space-x-4" data-aos="fade-up" data-aos-delay="250">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 border-2 border-accent flex items-center justify-center hover:rotate-[360deg] transition-transform duration-500">
                            <i data-lucide="camera" class="w-8 h-8 text-accent"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-6xl font-bold text-gray-200 absolute -top-4 right-0 z-[-1]">02</div>
                        <h3 class="text-xl font-semibold mb-2">Проведите съемку</h3>
                        <p class="text-muted-foreground">Профессиональный фотограф создаст уникальные снимки</p>
                    </div>
                </div>
                <div class="relative flex items-start space-x-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 border-2 border-accent flex items-center justify-center hover:rotate-[360deg] transition-transform duration-500">
                            <i data-lucide="image" class="w-8 h-8 text-accent"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-6xl font-bold text-gray-200 absolute -top-4 right-0 z-[-1]">03</div>
                        <h3 class="text-xl font-semibold mb-2">Получите фото</h3>
                        <p class="text-muted-foreground">Обработанные фотографии будут доступны в личном кабинете</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 px-4 lg:px-8 bg-black text-white">
        <div class="container mx-auto text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-6" data-aos="fade-up">Готовы создать идеальные кадры?</h2>
            <p class="text-xl mb-12 text-white/80 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                Забронируйте фотосессию прямо сейчас и получите скидку 10% на первое посещение
            </p>
            <div data-aos="fade-up" data-aos-delay="400">
                <a href="pages/booking.php" class="inline-block bg-accent text-white px-12 py-4 text-lg hover:bg-opacity-90 transition-colors">
                    Забронировать со скидкой
                </a>
            </div>
        </div>
    </section>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Инициализация иконок
        lucide.createIcons();

        // Инициализация анимаций
        AOS.init({
            once: true, // Анимация проигрывается один раз
            duration: 800,
            offset: 100
        });

        // Эффект зума для Hero при загрузке
        window.onload = () => {
            document.getElementById('hero-bg').style.transform = 'scale(1)';
        };
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