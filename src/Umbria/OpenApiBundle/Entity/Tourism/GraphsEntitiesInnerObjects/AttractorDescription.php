<?php

namespace Umbria\OpenApiBundle\Entity\Tourism\GraphsEntitiesInnerObjects;

use Doctrine\ORM\Mapping as ORM;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Attractor;
use JMS\Serializer\Annotation as JMS;

/**
 * Created by PhpStorm.
 * User: Lorenzo Franco Ranucci
 * Date: 27/09/2016
 * Time: 12:41
 *
 * @ORM\Entity
 *
 * @ORM\Table(name="tourism_attractor_description")
 */
class AttractorDescription
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
     * @ORM\Column(name="title", type="text", nullable=true)
     */
    private $title;
    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;
    /**
     * @ORM\ManyToOne(targetEntity="\Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Attractor", inversedBy="descriptions")
     * @ORM\JoinColumn(name="attractor_id", referencedColumnName="uri")
     */
    private $attractor;

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
     * @return Attractor
     */
    public function getAttractor()
    {
        return $this->attractor;
    }

    /**
     * @param mixed $attractor
     */
    public function setAttractor($attractor)
    {
        $this->attractor = $attractor;
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

