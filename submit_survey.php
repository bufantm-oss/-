<?php
// Встановлюємо часовий пояс для коректної дати (наприклад, Київський час)
date_default_timezone_set('Europe/Kiev'); 

// Отримуємо поточну дату і час для відображення та імені файлу
$submission_datetime = date('Y-m-d_H-i-s'); // для імені файлу
$display_datetime = date('d.m.Y о H:i:s');  // для виведення користувачу

// --- Отримання та очищення POST-даних ---
// Використовуємо htmlspecialchars для запобігання XSS-атакам (базова безпека)
$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : 'Не вказано';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : 'Не вказано';
$q1_frequency = isset($_POST['q1_frequency']) ? htmlspecialchars($_POST['q1_frequency']) : 'Не обрано';

// Обробка чекбоксів (q2_benefits - це масив, його треба перетворити на рядок)
$q2_benefits_arr = isset($_POST['q2_benefits']) ? $_POST['q2_benefits'] : [];
$q2_benefits = count($q2_benefits_arr) > 0 
    ? implode(', ', array_map('htmlspecialchars', $q2_benefits_arr)) 
    : 'Не обрано';

$q3_challenge = isset($_POST['q3_challenge']) ? htmlspecialchars($_POST['q3_challenge']) : 'Не вказано';

// --- Формування вмісту файлу ---
$file_data = "Дата заповнення: {$display_datetime}\n";
$file_data .= "Ім'я респондента: {$name}\n";
$file_data .= "Email респондента: {$email}\n";
$file_data .= "--------------------------------------------------\n";
$file_data .= "Питання 1 (Частота): {$q1_frequency}\n";
$file_data .= "Питання 2 (Переваги): {$q2_benefits}\n";
$file_data .= "Питання 3 (Виклик): {$q3_challenge}\n";
$file_data .= "--------------------------------------------------\n";


// --- Збереження у файл ---
$survey_dir = 'survey'; // Папка для збереження
// Формуємо повне ім'я файлу: [дата]_[час]_[ім'я].txt
$file_name = "{$submission_datetime}_{$name}.txt"; 
$file_path = "{$survey_dir}/{$file_name}";

// Перевіряємо, чи існує папка 'survey', якщо ні - створюємо
if (!is_dir($survey_dir)) {
    mkdir($survey_dir, 0777, true); 
}

// Записуємо дані у файл
if (file_put_contents($file_path, $file_data) !== false) {
    $message = "✅ Ваш відгук успішно збережено у файл: {$file_name}";
    $success = true;
} else {
    $message = "❌ Помилка при записі даних у файл. Перевірте права доступу.";
    $success = false;
}

?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Результат Опитування</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .success { color: green; border: 1px solid green; padding: 15px; background: #e6ffe6; }
        .error { color: red; border: 1px solid red; padding: 15px; background: #ffe6e6; }
    </style>
</head>
<body>
    <h1>Результат Відправлення Анкети</h1>
    
    <div class="<?= $success ? 'success' : 'error' ?>">
        <p><strong><?= $message ?></strong></p>
    </div>

    <h2>Час та дата заповнення:</h2>
    <p><strong><?= $display_datetime ?></strong></p>
    
    <p><a href="survey_form.html">Повернутися до форми</a></p>
</body>
</html>