<?php

namespace Umbria\OpenApiBundle\Entity\Tourism\GraphsEntitiesInnerObjects;

use Doctrine\ORM\Mapping as ORM;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Event;
use JMS\Serializer\Annotation as JMS;

/**
 * Created by PhpStorm.
 * User: Lorenzo Franco Ranucci
 * Date: 27/09/2016
 * Time: 12:41
 *
 * @ORM\Entity
 *
 * @ORM\Table(name="tourism_event_description")
 */
class EventDescription
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Exclude()
     **/
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;
    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255, nullable=true)
     */
    private $text;
    /**
     * @ORM\ManyToOne(targetEntity="\Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Event", inversedBy="descriptions")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="uri")
     */
    private $event;

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
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

}

