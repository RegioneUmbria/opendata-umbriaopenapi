<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Image.
 *
 * @ORM\Table(name="tourism_image")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\ImageRepository")
 */
class Image
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
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"image.*"})
     */
    private $homepage;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"image.*"})
     */
    private $testoAlternativo;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="immagini")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    private $evento;
}
