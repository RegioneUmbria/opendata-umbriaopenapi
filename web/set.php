<?php
// Load composer
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require __DIR__ . "/../vendor/autoload.php";

$API_KEY = '401516015:AAGhqAbK_Ni0NZvqwa10b-2aoqiJq630lpw';
$BOT_NAME = 'lcitbot';
$hook_url = 'https://luke.it/hook.php';
$certificate_path = "/../lcitbotpp.pem";

try {
    // Set webhook
    $telegram = new Shaygan\TelegramBotApiBundle\TelegramBotApi($API_KEY, $BOT_NAME);
    $result = $telegram->setWebhook($hook_url, ['certificate' => $certificate_path]);
    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
}