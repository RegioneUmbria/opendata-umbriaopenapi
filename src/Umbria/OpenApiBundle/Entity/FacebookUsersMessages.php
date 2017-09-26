<?php
/**
 * Created by PhpStorm.
 * User: DeveloperOspite
 * Date: 25/09/2017
 * Time: 12:03
 */

namespace Umbria\OpenApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * FacebookUsersMessages
 *
 * @ORM\Table(name="facebook_users_messages")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\FacebookUsersMessagesRepository")
 */
class FacebookUsersMessages
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $sender;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timeStamp;

    /**
     * @ORM\Column(type="json_array")
     */
    private $entry;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param mixed $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return mixed
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }

    /**
     * @param mixed $timeStamp
     */
    public function setTimeStamp($timeStamp)
    {
        $this->timeStamp = $timeStamp;
    }

    /**
     * @return mixed
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param mixed $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }

}