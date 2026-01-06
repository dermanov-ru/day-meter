<!-- SoftwareApplication Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "SoftwareApplication",
    "name": "DayMeter",
    "description": "Приватный сервис осознанного трекинга жизни. Отслеживайте состояние, здоровье и культурное потребление. Анализируйте данные с помощью ИИ.",
    "url": "https://daymeter.com",
    "applicationCategory": "HealthApplication",
    "operatingSystem": "Web",
    "offers": {
        "@@type": "Offer",
        "price": "0",
        "priceCurrency": "RUB"
    },
    "author": {
        "@@type": "Person",
        "name": "Mark Dermanov",
        "email": "mark@dermanov.ru"
    },
    "featureList": [
        "Отслеживание состояния и здоровья",
        "Фиксация привычек и поведения",
        "Журнал болезней с временной шкалой",
        "Отслеживание культурного потребления",
        "Экспорт данных для анализа",
        "PWA приложение с push-уведомлениями",
        "Биометрическая защита",
        "Полная приватность данных"
    ]
}
</script>

<!-- Organization Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "DayMeter",
    "url": "https://daymeter.com",
    "description": "Сервис для осознанного трекинга жизни и анализа данных",
    "contactPoint": {
        "@@type": "ContactPoint",
        "email": "mark@dermanov.ru",
        "contactType": "Customer Support"
    },
    "sameAs": [
        "https://github.com/dermanov-ru/day-meter"
    ]
}
</script>

<!-- FAQPage Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {
            "@@type": "Question",
            "name": "Что такое DayMeter?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "DayMeter — это приватный сервис для осознанного трекинга жизни. Позволяет отслеживать состояние, здоровье, культурное потребление и поведение. Данные полностью приватны и могут быть экспортированы для анализа."
            }
        },
        {
            "@type": "Question",
            "name": "Стоит ли платить за DayMeter?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Нет, DayMeter полностью бесплатный сервис. Никаких подписок, никаких скрытых платежей. Ваши данные полностью принадлежат вам."
            }
        },
        {
            "@type": "Question",
            "name": "Можно ли экспортировать данные?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Да, вы можете экспортировать свои данные в Markdown формате и использовать их для анализа с помощью ИИ или другого инструмента."
            }
        },
        {
            "@type": "Question",
            "name": "Насколько приватен DayMeter?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Все ваши данные хранятся приватно. Биометрические данные хранятся только на устройстве. Никакие сторонние сервисы не имеют доступа к вашей информации."
            }
        }
    ]
}
</script>
