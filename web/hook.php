<?php
// Load composer
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require __DIR__ . "/../vendor/autoload.php";

$API_KEY = '401516015:AAGhqAbK_Ni0NZvqwa10b-2aoqiJq630lpw';
$BOT_NAME = 'lcitbot';
try {
    // Handle telegram webhook request
    $telegram = new Shaygan\TelegramBotApiBundle\TelegramBotApi($API_KEY, $BOT_NAME);
    $telegram->handle();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    // log telegram errors
    // echo $e;
}