<?php
/**
 * Скрипт автоматического импорта задач из Jira в GitHub Issues и Milestones
 * Репозиторий: Vitali-Kol/news-php
 * 
 * Запуск:
 *   php import_tasks_to_github.php YOUR_GITHUB_TOKEN [open|closed]
 */

$repoOwner = 'Vitali-Kol';
$repoName  = 'news-php';

// Получаем токен из аргументов командной строки или запрашиваем ввод
$token = $argv[1] ?? '';
$initialState = ($argv[2] ?? 'closed') === 'open' ? 'open' : 'closed'; // по умолчанию закрываем выполненные задачи

if (empty($token)) {
    echo "========================================================" . PHP_EOL;
    echo "  Импорт задач из Jira в GitHub: $repoOwner/$repoName" . PHP_EOL;
    echo "========================================================" . PHP_EOL;
    echo "Введите ваш GitHub Personal Access Token (classic): ";
    $token = trim(fgets(STDIN));
    echo PHP_EOL;
}

if (empty($token)) {
    die("Ошибка: Токен GitHub не указан. Завершение работы." . PHP_EOL);
}

// Структура всех эпиков и подзадач из Jira
$epics = [
    [
        'title'       => 'Prepare job',
        'description' => 'NEW-1: Подготовка к разработке и планирование',
        'tasks'       => [
            ['key' => 'NEW-2',  'title' => 'Обсуждение с заказчиком'],
            ['key' => 'NEW-3',  'title' => 'Оформить цели проекта'],
            ['key' => 'NEW-4',  'title' => 'Необходимые ресурсы'],
            ['key' => 'NEW-5',  'title' => 'Подходящие инструменты'],
            ['key' => 'NEW-6',  'title' => 'Примерный вид сайта'],
            ['key' => 'NEW-20', 'title' => 'Создание репозитория, подключение к Jira'],
        ]
    ],
    [
        'title'       => 'Database',
        'description' => 'NEW-8: Проектирование и создание базы данных MySQL',
        'tasks'       => [
            ['key' => 'NEW-9',  'title' => 'Создание БД'],
            ['key' => 'NEW-10', 'title' => 'Создание таблиц'],
            ['key' => 'NEW-11', 'title' => 'Создание связей'],
            ['key' => 'NEW-12', 'title' => 'Наполнение тестовыми данными'],
        ]
    ],
    [
        'title'       => 'News list',
        'description' => 'NEW-13: Вывод списка новостей и детальный просмотр на сайте',
        'tasks'       => [
            ['key' => 'NEW-14', 'title' => 'Создание главной страницы'],
            ['key' => 'NEW-15', 'title' => 'Routing'],
            ['key' => 'NEW-16', 'title' => 'Получение данных из БД'],
            ['key' => 'NEW-17', 'title' => 'Controller'],
            ['key' => 'NEW-18', 'title' => 'Model'],
            ['key' => 'NEW-19', 'title' => 'View'],
            ['key' => 'NEW-32', 'title' => 'Page 404 если ошибочный адрес'],
        ]
    ],
    [
        'title'       => 'Add comments',
        'description' => 'NEW-21: Добавление и отображение комментариев к новостям',
        'tasks'       => [
            ['key' => 'NEW-22', 'title' => 'Доработка Controller'],
            ['key' => 'NEW-23', 'title' => 'Доработка View'],
            ['key' => 'NEW-24', 'title' => 'Доработка Model'],
            ['key' => 'NEW-25', 'title' => 'Доработка Routing'],
            ['key' => 'NEW-26', 'title' => 'Счетчик комментариев'],
        ]
    ],
    [
        'title'       => 'Login User',
        'description' => 'NEW-44: Аутентификация администратора и базовая структура панели',
        'tasks'       => [
            ['key' => 'NEW-45', 'title' => 'Структура папок Admin'],
            ['key' => 'NEW-46', 'title' => 'Layout'],
            ['key' => 'NEW-47', 'title' => 'Error 404'],
            ['key' => 'NEW-48', 'title' => 'Model admin'],
            ['key' => 'NEW-49', 'title' => 'Controller Admin'],
            ['key' => 'NEW-50', 'title' => 'View Admin'],
            ['key' => 'NEW-51', 'title' => 'startAdmin'],
            ['key' => 'NEW-52', 'title' => 'Routing Admin'],
            ['key' => 'NEW-53', 'title' => 'Index Admin'],
            ['key' => 'NEW-54', 'title' => 'Form Login'],
        ]
    ],
    [
        'title'       => 'Register new User',
        'description' => 'NEW-36: Регистрация новых пользователей с ролью user',
        'tasks'       => [
            ['key' => 'NEW-37', 'title' => 'Создание страницы регистрации'],
            ['key' => 'NEW-38', 'title' => 'Доработка Routing'],
            ['key' => 'NEW-39', 'title' => 'Доработка Model (валидация данных)'],
            ['key' => 'NEW-40', 'title' => 'Доработка Model (сохранение пользователя)'],
            ['key' => 'NEW-41', 'title' => 'Доработка Controller'],
            ['key' => 'NEW-42', 'title' => 'Страница успешной регистрации'],
            ['key' => 'NEW-43', 'title' => 'Создание шаблона Layout'],
        ]
    ],
    [
        'title'       => 'Admin access news',
        'description' => 'NEW-27: Список новостей и структура управления публикациями в админке',
        'tasks'       => [
            ['key' => 'NEW-28', 'title' => 'Доработка структуры админки'],
            ['key' => 'NEW-29', 'title' => 'Admin Routing'],
            ['key' => 'NEW-30', 'title' => 'Admin Model'],
            ['key' => 'NEW-31', 'title' => 'Admin View'],
            ['key' => 'NEW-35', 'title' => 'Доработка Layout'],
            ['key' => 'NEW-55', 'title' => 'newslist'],
        ]
    ],
    [
        'title'       => 'Admin Add news',
        'description' => 'NEW-56: Форма и сохранение добавления новости в админ-панели',
        'tasks'       => [
            ['key' => 'NEW-57', 'title' => 'Доработка Routing'],
            ['key' => 'NEW-58', 'title' => 'Доработка Model'],
            ['key' => 'NEW-59', 'title' => 'Доработка View'],
            ['key' => 'NEW-60', 'title' => 'Доработка Controller'],
            ['key' => 'NEW-61', 'title' => 'Доработка Index'],
            ['key' => 'NEW-62', 'title' => 'News Add Form'],
        ]
    ],
    [
        'title'       => 'Admin edit news',
        'description' => 'NEW-63: Редактирование новостей и просмотр деталей публикации',
        'tasks'       => [
            ['key' => 'NEW-64', 'title' => 'Доработка Routing'],
            ['key' => 'NEW-65', 'title' => 'Доработка Model'],
            ['key' => 'NEW-66', 'title' => 'Доработка View'],
            ['key' => 'NEW-67', 'title' => 'Доработка Controller'],
            ['key' => 'NEW-68', 'title' => 'edit form'],
        ]
    ],
    [
        'title'       => 'Admin delete news',
        'description' => 'NEW-69: Удаление новостей и подтверждение действия',
        'tasks'       => [
            ['key' => 'NEW-70', 'title' => 'Доработка Routing'],
            ['key' => 'NEW-71', 'title' => 'Доработка Controller'],
            ['key' => 'NEW-72', 'title' => 'Доработка Model'],
            ['key' => 'NEW-73', 'title' => 'Доработка news delete form'],
        ]
    ]
];

/**
 * Функция выполнения запросов к GitHub REST API
 */
function githubApiRequest($url, $token, $method = 'GET', $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-GitHub-Importer');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: token ' . $token,
        'Accept: application/vnd.github.v3+json',
        'Content-Type: application/json'
    ]);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'PATCH') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['code' => $httpCode, 'data' => json_decode($response, true)];
}

// 1. Проверяем доступность репозитория
echo "Проверка доступа к репозиторию $repoOwner/$repoName..." . PHP_EOL;
$repoCheck = githubApiRequest("https://api.github.com/repos/$repoOwner/$repoName", $token);

if ($repoCheck['code'] !== 200) {
    echo "Ошибка авторизации или репозиторий не найден (HTTP {$repoCheck['code']})." . PHP_EOL;
    echo "Сообщение GitHub: " . ($repoCheck['data']['message'] ?? 'Неизвестно') . PHP_EOL;
    exit(1);
}

echo "Успешно! Начинаем создание Milestones (Эпиков) и Задач..." . PHP_EOL . PHP_EOL;

// Получаем существующие Milestones, чтобы не дублировать
$existingMilestonesRes = githubApiRequest("https://api.github.com/repos/$repoOwner/$repoName/milestones?state=all", $token);
$existingMilestones = [];
if ($existingMilestonesRes['code'] === 200 && is_array($existingMilestonesRes['data'])) {
    foreach ($existingMilestonesRes['data'] as $m) {
        $existingMilestones[$m['title']] = $m['number'];
    }
}

$totalTasks = 0;
$createdTasks = 0;

foreach ($epics as $epic) {
    $epicTitle = $epic['title'];
    $milestoneNumber = null;

    // Проверяем / создаем Milestone
    if (isset($existingMilestones[$epicTitle])) {
        $milestoneNumber = $existingMilestones[$epicTitle];
        echo "Milestone [$epicTitle] уже существует (№$milestoneNumber)." . PHP_EOL;
    } else {
        echo "Создание Milestone: [$epicTitle]... ";
        $createMRes = githubApiRequest("https://api.github.com/repos/$repoOwner/$repoName/milestones", $token, 'POST', [
            'title'       => $epicTitle,
            'description' => $epic->description ?? $epic['description'],
            'state'       => 'open'
        ]);

        if ($createMRes['code'] === 201) {
            $milestoneNumber = $createMRes['data']['number'];
            $existingMilestones[$epicTitle] = $milestoneNumber;
            echo "OK (№$milestoneNumber)" . PHP_EOL;
        } else {
            echo "Ошибка создания Milestone: " . ($createMRes['data']['message'] ?? '') . PHP_EOL;
        }
    }

    // Создаем задачи внутри этого эпика
    foreach ($epic['tasks'] as $task) {
        $totalTasks++;
        $issueTitle = "[{$task['key']}] {$task['title']}";
        $issueBody = "**Эпик:** {$epicTitle}\n**Код Jira:** `{$task['key']}`\n\n- [x] Задача реализована в рамках проекта Сайт Новостей (PHP MVC).";

        $issuePayload = [
            'title' => $issueTitle,
            'body'  => $issueBody
        ];
        if ($milestoneNumber) {
            $issuePayload['milestone'] = $milestoneNumber;
        }

        echo "  - Создание задачи: $issueTitle... ";
        $createIssueRes = githubApiRequest("https://api.github.com/repos/$repoOwner/$repoName/issues", $token, 'POST', $issuePayload);

        if ($createIssueRes['code'] === 201) {
            $issueNumber = $createIssueRes['data']['number'];
            $createdTasks++;

            // Если задачи нужно закрыть (так как они уже выполнены)
            if ($initialState === 'closed') {
                githubApiRequest("https://api.github.com/repos/$repoOwner/$repoName/issues/$issueNumber", $token, 'PATCH', [
                    'state' => 'closed'
                ]);
                echo "OK (#$issueNumber, статус: Closed)" . PHP_EOL;
            } else {
                echo "OK (#$issueNumber, статус: Open)" . PHP_EOL;
            }
        } else {
            echo "Ошибка: " . ($createIssueRes['data']['message'] ?? '') . PHP_EOL;
        }

        // Небольшая задержка, чтобы не превысить лимиты API GitHub
        usleep(250000); // 0.25 сек
    }
    echo PHP_EOL;
}

echo "========================================================" . PHP_EOL;
echo "Импорт завершён! Всего создано задач: $createdTasks из $totalTasks" . PHP_EOL;
echo "Посмотреть задачи можно тут: https://github.com/$repoOwner/$repoName/issues" . PHP_EOL;
echo "Посмотреть Milestones тут:  https://github.com/$repoOwner/$repoName/milestones" . PHP_EOL;
echo "========================================================" . PHP_EOL;
