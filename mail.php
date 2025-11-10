<?php
// 1) Глушим вывод ошибок, чтобы скрипт не ломал ожидаемый ответ
ini_set('display_errors', 0);
error_reporting(0);

// 2) Явно отдадим текст
header('Content-Type: text/plain; charset=utf-8');

$name    = $_POST["name"] ?? '';
$email   = $_POST["email"] ?? '';
$subject = $_POST["subject"] ?? '';
$message = $_POST["message"] ?? '';

$EmailTo = "masha3sha@gmail.com";
$Title   = "New Message Received";

// 3) Инициализируем тело письма
$Fields  = "";
$Fields .= "Name: "    . $name    . "\n";
$Fields .= "Email: "   . $email   . "\n";
$Fields .= "Subject: " . $subject . "\n";
$Fields .= "Message:\n" . $message . "\n";

// 4) Почтовые заголовки: From с доменом сайта, Reply-To на пользователя
$fromDomain = $_SERVER['SERVER_NAME'] ?? 'localhost';
$headers  = "From: no-reply@{$fromDomain}\r\n";
if ($email !== '') {
  $headers .= "Reply-To: {$email}\r\n";
}
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";

// 5) Отправляем
$success = @mail($EmailTo, $Title, $Fields, $headers);

// 6) Отвечаем строго тем, что ждёт фронтенд
if ($success) {
  // на всякий случай чистим буфер, если где-то что-то писалось
  if (function_exists('ob_get_length') && ob_get_length()) { ob_clean(); }
  echo "success";
  exit;
} else {
  // Для быстрой диагностики — лог в файл (можно удалить позже)
  @file_put_contents(__DIR__ . '/mail.log',
    date('c') . " mail()=false; from={$fromDomain}; email={$email}\n", FILE_APPEND);
  http_response_code(500);
  echo "error";
  exit;
}
