<?php

namespace Umbria\TelegramBotBundle\UpdateReceiver;

use Shaygan\TelegramBotApiBundle\TelegramBotApi;
use Shaygan\TelegramBotApiBundle\Type\Update;

class UpdateReceiver implements UpdateReceiverInterface
{

    private $config;
    private $telegramBotApi;

    public function __construct(TelegramBotApi $telegramBotApi, $config)
    {
        $this->telegramBotApi = $telegramBotApi;
        $this->config = $config;
    }

    public function handleUpdate(Update $update)
    {
        $message = json_decode(json_encode($update->message), true);

        // Controllo se all'interno dell'Umbria
        if(isset($message['location'])) {
            if (($message['location']['latitude'] >= 45 AND $message['location']['latitude'] <= 45.7)
                AND ($message['location']['longitude'] >= 9 AND $message['location']['longitude'] <= 9.5)
            ) {
                $text = "Sei in provincia di Milano";
            } else {
                $text = "Non sei in provincia di Milano";
            }

            $this->telegramBotApi->sendMessage($message['chat']['id'], $text);
        }

        if(isset($message['text'])) {
            switch ($message['text']) {
                case "/about":
                    $text = "UmbriaTourismBot ti permette di ricevere informazioni turistiche. Invia la tua posizione per scoprire tutte le bellezze che la nostra regione ha in serbo per te";
                    break;
                case "/hello":
                    $text = "Ciao ". $message['from']['first_name']." Oggi ti consiglio di visitare la Gola del Bottaccione";
                    break;
                case "/help":
                default :
                    $text = "Command List:\n";
                    $text .= "/about - About this bot\n";
                    $text .= "/help - show this help message\n";
                    $text .= "/hello - show hello message\n";
                    break;
            }

            $this->telegramBotApi->sendMessage($message['chat']['id'], $text);
        }

    }
}
