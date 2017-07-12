<?php

namespace Umbria\TelegramBotBundle\UpdateReceiver;

use Shaygan\TelegramBotApiBundle\Type\Update;

interface UpdateReceiverInterface
{

    public function handleUpdate(Update $update);
}
