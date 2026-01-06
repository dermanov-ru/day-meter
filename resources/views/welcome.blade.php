<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DayMeter — приватный сервис осознанного трекинга жизни. Отслеживайте состояние, здоровье и культурное потребление. Экспортируйте данные для анализа ИИ. Без геймификации.">
    <meta name="keywords" content="трекер жизни, дневник здоровья, отслеживание привычек, личный журнал, самоанализ, health tracking, life logging">
    <meta name="author" content="Mark Dermanov">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="DayMeter - Личный сервис осознанного трекинга жизни">
    <meta property="og:description" content="Отслеживайте состояние, здоровье и культурную жизнь. Анализируйте данные с помощью ИИ. Полностью приватно.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://day-meter.dermanov.ru">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="DayMeter - Осознанный трекинг жизни">
    <meta name="twitter:description" content="Приватный сервис для ежедневной фиксации жизни и анализа данных.">
    <link rel="canonical" href="https://day-meter.dermanov.ru">
    <title>DayMeter - Личный сервис осознанного трекинга жизни</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            color: #e2e8f0;
            min-height: 100vh;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .navbar {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(51, 65, 85, 0.3);
            padding: 1.5rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .btn-secondary {
            background: transparent;
            color: #e2e8f0;
            border: 1.5px solid rgba(51, 65, 85, 0.5);
        }

        .btn-secondary:hover {
            background: rgba(51, 65, 85, 0.2);
            border-color: rgba(51, 65, 85, 0.8);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }

        .hero {
            max-width: 1200px;
            margin: 0 auto;
            padding: 6rem 2rem 4rem;
            text-align: center;
            animation: fadeInDown 1s ease-out;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 50%, #60a5fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -1px;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: #94a3b8;
            margin-bottom: 1.5rem;
            animation: fadeInUp 1.2s ease-out 0.2s both;
        }

        .hero-tagline {
            font-size: 1.1rem;
            color: #64748b;
            margin-bottom: 3rem;
            animation: fadeInUp 1.2s ease-out 0.4s both;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 2rem;
            animation: fadeInUp 1.2s ease-out 0.6s both;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section {
            padding: 6rem 2rem;
            border-top: 1px solid rgba(51, 65, 85, 0.3);
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #f1f5f9;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: #94a3b8;
            margin-bottom: 3rem;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .card {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(51, 65, 85, 0.3);
            border-radius: 0.75rem;
            padding: 2rem;
            transition: all 0.4s ease;
            animation: fadeInUp 0.8s ease-out both;
        }

        .card:nth-child(1) { animation-delay: 0s; }
        .card:nth-child(2) { animation-delay: 0.1s; }
        .card:nth-child(3) { animation-delay: 0.2s; }
        .card:nth-child(4) { animation-delay: 0.3s; }
        .card:nth-child(5) { animation-delay: 0.4s; }
        .card:nth-child(6) { animation-delay: 0.5s; }

        .card:hover {
            background: rgba(30, 41, 59, 0.8);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.15);
            transform: translateY(-5px);
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #f1f5f9;
        }

        .card-text {
            color: #cbd5e1;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .features-list {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(51, 65, 85, 0.3);
            border-radius: 0.75rem;
            padding: 2rem;
            margin-bottom: 2rem;
            animation: slideInLeft 0.8s ease-out;
        }

        .feature-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .feature-item:last-child {
            margin-bottom: 0;
        }

        .feature-bullet {
            color: #3b82f6;
            font-weight: 700;
            min-width: 24px;
            margin-top: 2px;
        }

        .feature-text {
            color: #cbd5e1;
            line-height: 1.6;
        }

        .feature-text strong {
            color: #f1f5f9;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            margin-bottom: 3rem;
        }

        .two-column.reverse {
            direction: rtl;
        }

        .two-column.reverse > * {
            direction: ltr;
        }

        .column-text {
            animation: slideInLeft 0.8s ease-out;
        }

        .column-visual {
            animation: slideInRight 0.8s ease-out;
        }

        .two-column.reverse .column-text {
            animation: slideInRight 0.8s ease-out;
        }

        .two-column.reverse .column-visual {
            animation: slideInLeft 0.8s ease-out;
        }

        .column-text h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #f1f5f9;
        }

        .column-text p {
            color: #cbd5e1;
            line-height: 1.8;
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }

        .visual-box {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(51, 65, 85, 0.3);
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
            color: #94a3b8;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
        }

        .highlight-box {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 0.75rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .highlight-box p {
            color: #cbd5e1;
            line-height: 1.8;
            font-size: 1.05rem;
        }

        .cta-section {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 1rem;
            margin-top: 6rem;
            animation: fadeInUp 0.8s ease-out;
        }

        .cta-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #f1f5f9;
        }

        .cta-text {
            font-size: 1.1rem;
            color: #cbd5e1;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .free-badge {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 1rem;
            padding: 3rem 2rem;
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
        }

        .badge-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: inline-block;
        }

        .badge-title {
            font-size: 2rem;
            font-weight: 700;
            color: #22c55e;
            margin-bottom: 1rem;
        }

        .badge-text {
            font-size: 1.05rem;
            color: #cbd5e1;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.8;
        }

        .contact-section {
            padding: 6rem 2rem;
            border-top: 1px solid rgba(51, 65, 85, 0.3);
        }

        .contact-box {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
            border: 2px solid rgba(59, 130, 246, 0.2);
            border-radius: 1rem;
            padding: 4rem 2rem;
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
        }

        .contact-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 1rem;
        }

        .contact-text {
            font-size: 1.1rem;
            color: #94a3b8;
            margin-bottom: 1.5rem;
        }

        .contact-email {
            display: inline-block;
            font-size: 1.5rem;
            font-weight: 700;
            color: #3b82f6;
            text-decoration: none;
            margin-bottom: 2rem;
            padding: 1rem 2rem;
            border: 2px solid rgba(59, 130, 246, 0.3);
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .contact-email:hover {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.6);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
        }

        .contact-note {
            font-size: 0.95rem;
            color: #64748b;
            font-style: italic;
        }

        .github-box {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
            border: 2px solid rgba(139, 92, 246, 0.2);
            border-radius: 1rem;
            padding: 3rem 2rem;
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
        }

        .github-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: inline-block;
        }

        .github-title {
            font-size: 2rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 1rem;
        }

        .github-text {
            font-size: 1.05rem;
            color: #cbd5e1;
            max-width: 600px;
            margin: 0 auto 2rem;
            line-height: 1.8;
        }

        .github-link {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #8b5cf6;
            text-decoration: none;
            padding: 1rem 2rem;
            border: 2px solid rgba(139, 92, 246, 0.3);
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .github-link:hover {
            background: rgba(139, 92, 246, 0.1);
            border-color: rgba(139, 92, 246, 0.6);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.2);
        }

        .github-link-icon {
            font-size: 1.3rem;
        }

        .footer {
            background: rgba(15, 23, 42, 0.5);
            border-top: 1px solid rgba(51, 65, 85, 0.3);
            padding: 3rem 2rem;
            text-align: center;
            color: #64748b;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .hero-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .two-column {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .two-column.reverse {
                direction: ltr;
            }

            .column-text h3 {
                font-size: 1.5rem;
            }

            .nav-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn {
                width: 100%;
                text-align: center;
            }

            .contact-box {
                padding: 2rem 1.5rem;
            }

            .contact-title {
                font-size: 1.8rem;
            }

            .contact-email {
                font-size: 1.1rem;
                padding: 0.75rem 1.5rem;
            }

            .github-box {
                padding: 2rem 1.5rem;
            }

            .github-title {
                font-size: 1.5rem;
            }

            .github-link {
                flex-direction: column;
                font-size: 1rem;
                padding: 0.75rem 1.5rem;
            }
        }
    </style>
    <x-schema-org />
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-content">
            <div class="logo">DayMeter</div>
            <div class="nav-buttons">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary">Вход</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">Регистрация</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero container">
        <h1 class="hero-title">DayMeter</h1>
        <p class="hero-subtitle">Личный сервис осознанного трекинга жизни</p>
        <p class="hero-tagline">
            Без подписки. Без смс. Но с регистрацией :)
        </p>
        <div class="hero-buttons">
            <a href="{{ route('register') }}" class="btn btn-primary">Начать прямо сейчас</a>
            <a href="#features" class="btn btn-secondary">Узнать больше ↓</a>
        </div>
    </section>

    <!-- Why DayMeter -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Зачем DayMeter</h2>
            <p class="section-subtitle">Приватный сервис для ежедневной фиксации жизни</p>

            <div class="cards-grid">
                <div class="card">
                    <div class="card-icon">🧠</div>
                    <div class="card-title">Состояния</div>
                    <div class="card-text">Отслеживайте свои внутренние состояния без суждений</div>
                </div>
                <div class="card">
                    <div class="card-icon">🎯</div>
                    <div class="card-title">Поведение</div>
                    <div class="card-text">Фиксируйте свои действия и привычки</div>
                </div>
                <div class="card">
                    <div class="card-icon">⚡</div>
                    <div class="card-title">Нагрузка</div>
                    <div class="card-text">Понимайте свой уровень энергии и утомления</div>
                </div>
                <div class="card">
                    <div class="card-icon">🌍</div>
                    <div class="card-title">Контекст</div>
                    <div class="card-text">Описывайте контекст и обстоятельства дня</div>
                </div>
                <div class="card">
                    <div class="card-icon">📊</div>
                    <div class="card-title">Чистые данные</div>
                    <div class="card-text">Получайте объективную информацию без интерпретаций</div>
                </div>
                <div class="card">
                    <div class="card-icon">🤖</div>
                    <div class="card-title">Для ИИ анализа</div>
                    <div class="card-text">Экспортируйте и отдавайте ИИ для осмысленных выводов</div>
                </div>
                <div class="card">
                    <div class="card-icon">📱</div>
                    <div class="card-title">Мобильное приложение</div>
                    <div class="card-text">PWA с push-уведомлениями для регулярного трекинга</div>
                </div>
            </div>

            <div class="highlight-box">
                <p>DayMeter не говорит, как жить правильно. Он даёт <strong>чистые данные</strong>, которые можно перечитать через месяц, проанализировать за год, или отдать ИИ и получить осмысленные выводы.</p>
            </div>
        </div>
    </section>

    <!-- What You Track -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Что вы фиксируете каждый день</h2>

            <div class="two-column">
                <div class="column-text">
                    <h3>🔹 Структурированные метрики</h3>
                    <p>Метрики — это стандартные блоки отслеживания. Вы выбираете, что именно важно:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Булевы</strong> (да / нет) — простые флаги</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Числовые шкалы</strong> (например, 1–10) — градации</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;">Примеры: <strong>настроение, раздражение, утомила работа, работал ночью, гулял с детьми, болел, был на массаже...</strong></p>
                    <p style="margin-top: 1.5rem;"><strong>Метрики полностью настраиваемые.</strong> Вы сами решаете, что отслеживать.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">📋</div>
                </div>
            </div>

            <div class="two-column reverse">
                <div class="column-text">
                    <h3>🔹 Комментарии и заметка дня</h3>
                    <p>К каждой метрике можно добавить комментарий, а в конце дня — общую заметку:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Короткое описание дня своими словами</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Пояснения к конкретным метрикам</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Никакой автокоррекции — текст хранится как есть</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Это даёт контекст, которого нет у цифр.</strong> Комбинация структурированных данных и человеческого описания — вот полная картина.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">💬</div>
                </div>
            </div>

            <div class="two-column" style="margin-top: 3rem;">
                <div class="column-text">
                    <h3>📌 Вывод дня — ключевая мысль</h3>
                    <p>Отделите главное от суеты. Каждый день — одна осознанная идея:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Одна важная мысль за день (до 500 символов)</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Осознание, вывод или вдохновляющая цитата</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Отделено от описания событий — фокус на смыслах</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Видно в хронике жизни как отдельный слой</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Через год вы увидите не просто дни, а эволюцию вашего мышления.</strong> Ваши выводы за год — это карта развития.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">🧠</div>
                </div>
            </div>

            <div class="two-column" style="margin-top: 3rem;">
                <div class="column-text">
                    <h3>📷 Фото дня — ваш визуальный архив</h3>
                    <p>Фотографии помогают вспомнить детали, которые слова не передают. DayMeter сохраняет фото как часть вашей истории:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Загружайте любое количество фотографий за день без лимитов</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Добавьте комментарий к каждому фото — почему это момент важен</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Все фото привязаны к конкретной дате и видны в хронике</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Удаляйте фото одно за другим — только нужные снимки сохраняются</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Фотография + контекст = полная память дня.</strong> Через год вы не просто прочитаете записи — вы перелистаете альбом вашей жизни с пояснениями к каждому кадру.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">📸</div>
                </div>
            </div>

            <div class="two-column reverse" style="margin-top: 3rem;">
                <div class="column-text">
                    <h3>📋 Фото-хроника — отдельный архив</h3>
                    <p>Чтобы не перегружать летопись фото, мы сохранили все значимые фото в отдельный раздел:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Отдельный раздел "Фото-хроника" для всех фотографий</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Полноразмерное отображение фото и комментариев</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Группировка фото по датам с новыми в начале</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Ленивая загрузка изображений — быстрая навигация</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Отдельные разделы — лучшая организация.</strong> Летопись за 30 дней загружается быстро, фото смотрите в отдельном потоке.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">📋</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Культурная жизнь -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Культурная жизнь как хроника</h2>
            <p class="section-subtitle">Фиксируйте свое культурное потребление — от книг до концертов</p>

            <div class="two-column">
                <div class="column-text">
                    <h3>📚 Два режима времени</h3>
                    <p>DayMeter различает два типа культурных событий — это важно:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Мгновенные события</strong> (фильм в кино, концерт, спектакль) — фиксируются с точной датой и временем</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Протяжённые процессы</strong> (книга, сериал) — от даты начала до завершения</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;">Это даёт правильное ощущение: концерт — событие, а прочитанная книга — процесс жизни.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">🎭</div>
                </div>
            </div>

            <div class="two-column reverse" style="margin-top: 3rem;">
                <div class="column-text">
                    <h3>🎬 Что можно отслеживать</h3>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Фильмы</strong> — в кинотеатре или онлайн</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Книги</strong> — бумажные, электронные или аудиокниги</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Сериалы</strong> — смотрите на стриминге или слушаете аудиокниги</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Театр и живая музыка</strong> — спектакли, концерты, оперы</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;">Каждая активность может иметь свой формат — от кинотеатра до стриминга.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">🎬📚🎵</div>
                </div>
            </div>

            <div class="two-column" style="margin-top: 3rem;">
                <div class="column-text">
                    <h3>💭 Впечатления и оценки</h3>
                    <p>Фиксируйте свою реакцию на культурное потребление:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Оценка от 0 до 10 (как вам понравилось)</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Свободные впечатления (чем вас зацепило, что не понравилось)</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Дата завершения для длительных активностей (когда закончили читать)</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Со временем вы увидите:</strong> какой жанр вам нравится, когда вы больше читаете или смотрите, как меняются ваши вкусы.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">⭐</div>
                </div>
            </div>

            <div class="highlight-box" style="margin-top: 3rem;">
                <p>Культурные активности ложатся в единую хронику жизни. Вы сможете читать свою историю: "В июне читал Достоевского, смотрел три фильма, ходил на концерт". Это не просто список книг — это <strong>жизнь, записанная в контексте.</strong></p>
            </div>
        </div>
    </section>

    <!-- Здоровье и болезни -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Здоровье как история болезни</h2>
            <p class="section-subtitle">Отслеживайте недомогания, лечение и восстановление в контексте жизни</p>

            <div class="two-column">
                <div class="column-text">
                    <h3>🏥 Журнал болезней</h3>
                    <p>Вместо просто отметок "был болен" ведите полный журнал каждого недомогания:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Категория болезни</strong> — ОРВИ, ЖКТ, ЛОР, аллергия, грипп, воспаление и другие</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Описание и названние</strong> — точное название заболевания или симптомы</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Дата начала</strong> — когда появились первые симптомы</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Дата завершения</strong> — когда выздоровели</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Это даёт полную картину:</strong> как часто вы болеете, какие сезоны опаснее, сколько времени занимает восстановление.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">📋</div>
                </div>
            </div>

            <div class="two-column reverse" style="margin-top: 3rem;">
                <div class="column-text">
                    <h3>📝 Временная шкала болезни</h3>
                    <p>К каждой болезни добавляйте заметки о ходе болезни с разными типами записей:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Симптомы</strong> — что беспокоит (кашель, насморк, боли)</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Лечение</strong> — какие лекарства или процедуры применяли</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Состояние</strong> — как себя чувствуете (с числовой оценкой если нужно)</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Мероприятия</strong> — посещение врача, анализы, осмотры</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Лекарства</strong> — какие препараты были назначены</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Свободные заметки</strong> — дополнительная информация и наблюдения</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Каждая запись привязана к дате</strong> — вы видите полную хронологию болезни день за днём.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">📅</div>
                </div>
            </div>

            <div class="two-column" style="margin-top: 3rem;">
                <div class="column-text">
                    <h3>📊 История здоровья</h3>
                    <p>Все болезни организованы в едином интерфейсе:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Текущие болезни — в процессе лечения</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Завершённые болезни — полная история с временной шкалой</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Фильтр по категориям — найти все ОРВИ за год</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Быстрый просмотр — сколько дней заняло лечение</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Со временем видна закономерность:</strong> какие болезни возвращаются, какие методы работают лучше, как сезоны влияют на здоровье.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">💪</div>
                </div>
            </div>

            <div class="highlight-box" style="margin-top: 3rem;">
                <p>Здоровье — это не просто отметка в чекбоксе "был болен". Это процесс, который стоит документировать. DayMeter позволяет вести <strong>полный журнал каждого эпизода болезни</strong> — от первых симптомов до полного восстановления. Это помогает врачам и самому лучше понять закономерности.</p>
            </div>
        </div>
    </section>

    <!-- Удобный ввод -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Удобный ввод данных</h2>

            <div class="two-column reverse">
                <div class="column-text">
                    <h3>Страница ввода (/entry)</h3>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Все метрики сгруппированы по категориям</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Булевы</strong> — чекбоксы</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Числовые</strong> — удобные слайдеры</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Комментарии к метрикам — по желанию</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Заметка дня — в конце формы</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Ввод занимает 30–60 секунд.</strong></p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">⚡</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Быстрый ввод и голосовой ввод -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Быстрое добавление заметок</h2>
            <p class="section-subtitle">Фиксируйте мысли в течение дня без необходимости редактирования</p>

            <div class="two-column">
                <div class="column-text">
                    <h3>⚡ Дельта-ввод (быстрое добавление)</h3>
                    <p>На странице ввода есть отдельное компактное поле для коротких заметок. Каждая записанная мысль автоматически попадает в основную заметку дня с временной отметкой:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Отдельное поле для быстрого ввода</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">При добавлении текст присоединяется к основной заметке</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Автоматическая временная отметка (ЧЧ:МИ)</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Поле очищается и остаётся в фокусе для новых записей</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Идеально для фиксации событий в течение дня</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Пример формата:</strong> <br />— 14:30<br />Обед с коллегами, обсудили проект</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">⏱️</div>
                </div>
            </div>

            <div class="two-column reverse" style="margin-top: 3rem;">
                <div class="column-text">
                    <h3>🎤 Голосовой ввод (Web Speech API)</h3>
                    <p>Рядом с полем быстрого добавления находится кнопка микрофона. Нажмите — и просто диктуйте:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Распознавание речи на русском языке</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Текст обновляется в реальном времени</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">После завершения диктовки текст остаётся в поле</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Можно отредактировать перед добавлением</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Особенно удобно на мобильных устройствах</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Работает только в браузерах с поддержкой Web Speech API.</strong> На старых браузерах кнопка микрофона просто не видна.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">🗣️</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Хроника -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Хроника жизни</h2>
            <p class="section-subtitle">Страница "Летопись" (/chronicle)</p>

            <div class="two-column">
                <div class="column-text">
                    <p>Ваши записи идут в хронологическом порядке — это читаемая история жизни, а не просто набор данных:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Записи в правильном порядке (новые сверху)</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Под каждым днём: заметка дня</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Метрики, сгруппированные по категориям</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Комментарии к метрикам прямо в контексте</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Это не аналитика. Это читаемая история жизни.</strong></p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">📖</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Экспорт -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Экспорт для ИИ и анализа</h2>

            <div class="two-column reverse">
                <div class="column-text">
                    <h3>Экспорт летописи в Markdown</h3>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Выбор периода: месяц, квартал, год</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Готовый текстовый формат</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Можно целиком скопировать и отправить в ИИ</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>ИИ сможет:</strong></p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">→</div>
                            <div class="feature-text">Делать summary периода</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">→</div>
                            <div class="feature-text">Находить корреляции между метриками</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">→</div>
                            <div class="feature-text">Выявлять триггеры и паттерны</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">→</div>
                            <div class="feature-text">Формулировать выводы словами</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>DayMeter не анализирует вас.</strong> Он даёт материал для анализа.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">📤</div>
                </div>
            </div>
        </div>
    </section>

    <!-- PWA и Push уведомления -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Мобильное приложение и напоминания</h2>
            <p class="section-subtitle">Используйте DayMeter как нативное приложение на смартфоне</p>

            <div class="two-column">
                <div class="column-text">
                    <h3>📱 Progressive Web App (PWA)</h3>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Установка как нативное приложение на мобильное устройство</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Собственная иконка в списке приложений</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Полноэкранный режим без адресной строки браузера</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Базовая работа офлайн (просмотр ранее загруженных данных)</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Просто откройте сайт в браузере</strong> — система предложит установить приложение автоматически.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">📲</div>
                </div>
            </div>

            <div class="two-column reverse" style="margin-top: 3rem;">
                <div class="column-text">
                    <h3>🔔 Push-уведомления</h3>
                    <p>Настройте персональные напоминания о заполнении данных за день:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Выбор удобного времени уведомления (с точностью до минуты)</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Уведомления работают даже когда приложение закрыто</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Поддержка нескольких устройств для одного пользователя</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Простое управление подписками через настройки профиля</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Автоматическая отправка через планировщик</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Регулярность — ключ к качественной аналитике.</strong> Напоминания помогают не забывать фиксировать данные каждый день.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">⏰</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Биометрическая блокировка -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Биометрическая защита приложения</h2>
            <p class="section-subtitle">Дополнительный уровень безопасности с использованием отпечатка пальца и Face ID</p>

            <div class="two-column">
                <div class="column-text">
                    <h3>🔐 App Lock с биометрией</h3>
                    <p>Ваши личные данные защищены дополнительным уровнем безопасности:</p>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Отпечаток пальца</strong> (Touch ID на iOS, биометрия на Android)</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text"><strong>Face ID</strong> на iPhone и подобные системы на Android</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Работает поверх существующей сессии (не требует повторную авторизацию на сервере)</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Настраивается после входа — полностью опционально</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Используется стандарт WebAuthn (FIDO2) для максимальной безопасности</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Как работает:</strong> При открытии приложения после возврата из фона вы видите экран блокировки. Одно прикосновение пальцем или сканирование лица — и приложение разблокировано.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">👆</div>
                </div>
            </div>

            <div class="two-column reverse" style="margin-top: 3rem;">
                <div class="column-text">
                    <h3>🛡️ Автоматическая блокировка</h3>
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Приложение блокируется автоматически при сворачивании</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Также блокируется при переключении на другое приложение</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Таймаут неактивности (30 минут) — приложение заблокируется само</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Обновление страницы не требует повторную биометрию</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-bullet">✓</div>
                            <div class="feature-text">Биометрические данные хранятся только на устройстве (не отправляются на сервер)</div>
                        </div>
                    </div>
                    <p style="margin-top: 1.5rem;"><strong>Безопасность вашей приватности:</strong> Сервер знает только о том, что биометрия включена. Сами отпечатки и лицо никогда не отправляются на сервер.</p>
                </div>
                <div class="column-visual">
                    <div class="visual-box">🔒</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Кому подойдет -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Кому подойдёт DayMeter</h2>

            <div class="cards-grid">
                <div class="card">
                    <div class="card-icon">😓</div>
                    <div class="card-title">Уставшие от трекеров</div>
                    <div class="card-text">Если вас раздражают бейджи, достижения и "геймификация" — DayMeter для вас.</div>
                </div>
                <div class="card">
                    <div class="card-icon">🧭</div>
                    <div class="card-title">Ищущие понимания</div>
                    <div class="card-text">Кто хочет понимать себя, а не оптимизироваться по чужим правилам.</div>
                </div>
                <div class="card">
                    <div class="card-icon">🤖</div>
                    <div class="card-title">Работающие с ИИ</div>
                    <div class="card-text">Кто хочет использовать ИИ осмысленно, а не ради магии.</div>
                </div>
                <div class="card">
                    <div class="card-icon">⚡</div>
                    <div class="card-title">Минималисты</div>
                    <div class="card-text">Кто ценит простые и честные инструменты без лишних фич.</div>
                </div>
                <div class="card">
                    <div class="card-icon">📚</div>
                    <div class="card-title">Любящие анализ</div>
                    <div class="card-text">Кто хочет вернуться к своим записям через год и понять, как менялась жизнь.</div>
                </div>
                <div class="card">
                    <div class="card-icon">🎯</div>
                    <div class="card-title">Практичные люди</div>
                    <div class="card-text">Кто нужны данные, а не советы, коучинг и мотивационные уведомления.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Free Badge -->
    <section class="section">
        <div class="container">
            <div class="free-badge">
                <div class="badge-icon">✨</div>
                <h2 class="badge-title">Сервис полностью бесплатный</h2>
                <p class="badge-text">DayMeter готов к использованию прямо сейчас. Никаких платежей, никаких ограничений. Ваши данные принадлежат только вам.</p>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="cta-section container" style="margin-bottom: 4rem;">
        <h2 class="cta-title">DayMeter в двух словах</h2>
        <p class="cta-text">
            Это ваша личная хроника жизни, структурированная настолько, чтобы её мог понять ИИ, и достаточно человеческая, чтобы её было приятно читать самому.
        </p>
        <div class="hero-buttons">
            <a href="{{ route('register') }}" class="btn btn-primary">Начать трекинг</a>
            @if (Route::has('login'))
                @guest
                    <a href="{{ route('login') }}" class="btn btn-secondary">Уже есть аккаунт?</a>
                @endguest
            @endif
        </div>
    </section>

    <!-- GitHub Section -->
    <section class="section">
        <div class="container">
            <div class="github-box">
                <div class="github-icon">👨‍💻</div>
                <h2 class="github-title">Код проекта открытый</h2>
                <p class="github-text">DayMeter — это открытый проект. Смотрите исходный код, улучшайте его, создавайте форки.</p>
                <a href="https://github.com/dermanov-ru/day-meter" target="_blank" rel="noopener noreferrer" class="github-link">
                    <span class="github-link-icon">📦</span>
                    github.com/dermanov-ru/day-meter
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section contact-section">
        <div class="container">
            <div class="contact-box">
                <h2 class="contact-title">Остались вопросы?</h2>
                <p class="contact-text">Напишите автору сервиса</p>
                <a href="mailto:mark@dermanov.ru" class="contact-email">mark@dermanov.ru</a>
                <p class="contact-note">Я прочитаю ваше письмо и отвечу в ближайшее время.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 DayMeter. Личный сервис осознанного трекинга жизни.</p>
    </footer>

    <x-yandex-metrika />
</body>
</html>
