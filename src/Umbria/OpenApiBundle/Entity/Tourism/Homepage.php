<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Homepage.
 *
 * @ORM\Table(name="tourism_homepage")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\HomepageRepository")
 */
class Homepage
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
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"homepage.*"})
     */
    private $indirizzoInternet;

    /**
     * @ORM\ManyToOne(targetEntity="TravelAgency", inversedBy="homepage")
     * @ORM\JoinColumn(name="travel_agency_id", referencedColumnName="id")
     */
    private $agenziaViaggio;

    /**
     * @ORM\ManyToOne(targetEntity="Consortium", inversedBy="homepage")
     * @ORM\JoinColumn(name="consortium_id", referencedColumnName="id")
     */
    private $consorzio;

    /**
     * @ORM\ManyToOne(targetEntity="Profession", inversedBy="homepage")
     * @ORM\JoinColumn(name="professione_id", referencedColumnName="id")
     */
    private $professione;
}
