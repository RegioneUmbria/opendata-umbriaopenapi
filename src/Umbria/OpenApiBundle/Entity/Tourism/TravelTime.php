<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * TravelTime.
 *
 * @ORM\Table(name="travel_time")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\TravelTimeRepository")
 */
class TravelTime
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Groups({"travel-time.*"})
     */
    private $id;

    /**
     * @JMS\Type("string")
     */
    private $type;

    /**
     * @JMS\Type("string")
     *
     * @JMS\Groups({"travel-time.*"})
     */
    private $provenienza;

    /**
     * @JMS\Type("string")
     *
     * @JMS\Groups({"travel-time.*"})
     */
    private $tempoDiPercorrenza;

    /**
     * @JMS\Type("string")
     *
     * @JMS\Groups({"travel-time.*"})
     */
    private $mezzoDiTrasporto;

    /**
     * @ORM\ManyToOne(targetEntity="Proposal", inversedBy="tempiDiViaggio")
     * @ORM\JoinColumn(name="proposal_id", referencedColumnName="id")
     */
    private $proposta;

    /**
     * @ORM\ManyToOne(targetEntity="Attractor", inversedBy="tempiDiViaggio")
     * @ORM\JoinColumn(name="attractor_id", referencedColumnName="id")
     */
    private $attrattore;
}
