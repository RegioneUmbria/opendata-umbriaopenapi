<?php
/**
 * Created by PhpStorm.
 * User: lukechan170125
 * Date: 13/7/2017
 * Time: 上午10:34
 */
    // get the telegram api as a service
    $api = $this->container->get('shaygan.telegram_bot_api');

    // test the API by calling getMe method
    $user = $api->getMe();

?>