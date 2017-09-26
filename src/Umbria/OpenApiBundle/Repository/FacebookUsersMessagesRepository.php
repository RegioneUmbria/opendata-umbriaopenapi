<?php

namespace Umbria\OpenApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FacebookUsersMessagesRepository extends EntityRepository
{

    public function findLastUserMessage($sender)
    {
        return $this->findOneBy(array('sender' => $sender), array('timeStamp' => 'DESC'));
    }
}