<?php

namespace Umbria\TelegramBotBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        // get the telegram api as a service
        $api = $this->container->get('shaygan.telegram_bot_api');

        // test the API by calling getMe method
        $user = $api->getMe();

        print_r($user);
        die;
    }
}
