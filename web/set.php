<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

$API_KEY = '411313340:AAHh7qgj9GNQX5bNpqo5P_WyDoFhWCPnDZs';
$BOT_NAME = ‘lcitbot’;
$hook_url = 'https://luke.it/hook.php';
try {
    // Create Telegram API object
    $telegram = new shaygan\TelegramBotApiBundle\Telegram($API_KEY, $BOT_NAME);

    // Set webhook
    $result = $telegram->setWebhook($hook_url);
    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (shaygan $e) {
    echo $e;
}
