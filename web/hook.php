<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

$API_KEY = '411313340:AAHh7qgj9GNQX5bNpqo5P_WyDoFhWCPnDZs';
$BOT_NAME = ‘lcitbot’;

try {
    // Create Telegram API object
    $telegram = new shaygan\TelegramBotApiBzundle\Telegram($API_KEY, $BOT_NAME);

    // Handle telegram webhook request
    $telegram->handle();
} catch (shaygan $e) {
    echo $e;
}