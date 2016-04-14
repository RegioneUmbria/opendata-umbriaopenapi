<?php

namespace Umbria\TelegramBotBundle\UpdateReceiver;

use Shaygan\TelegramBotApiBundle\TelegramBotApi;
use Shaygan\TelegramBotApiBundle\Type\Update;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

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
        $arrayOfArraysOfStrings = array(
            array("/hello", "/help", "/about")
        );
        $newKeyboard = new ReplyKeyboardMarkup($arrayOfArraysOfStrings);
        $message = json_decode(json_encode($update->message), true);

        /*$newArray = array('/hello' => "/hello", '/help' => "/help", '/about' => "/about");
        $newKeyboard = json_encode($newArray, true);*/

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
                    $text = "Ciao ". $message['from']['first_name'].". Oggi ti consiglio di visitare la Gola del Bottaccione";
                    break;
                case "/help":
                case "/start":
                    $text = "UmbriaTourismBot ti permette di ricevere informazioni turistiche. Invia la tua posizione per scoprire tutte le bellezze che la nostra regione ha in serbo per te\n\n";
                default :
                    $text .= "Lista comandi:\n";
                    $text .= "/about - Informazioni sul bot\n";
                    $text .= "/help - Visualizzazione comandi disponibili\n";
                    $text .= "/hello - Suggerimenti\n";
                    break;
            }


                $this->telegramBotApi->sendMessage($message['chat']['id'], $text, null, false, null, $newKeyboard);

        }

    }
}
