<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Phone.
 *
 * @ORM\Table(name="tourism_phone")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\PhoneRepository")
 */
class Phone
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
     * @JMS\SerializedName("num_telefono")
     *
     * @JMS\Groups({"phone.*"})
     */
    private $numTelefono;

    /**
     * @ORM\ManyToOne(targetEntity="TravelAgency", inversedBy="phone")
     * @ORM\JoinColumn(name="travel_agency_id", referencedColumnName="id")
     */
    private $agenziaViaggio;

    /**
     * @ORM\ManyToOne(targetEntity="Consortium", inversedBy="phone")
     * @ORM\JoinColumn(name="consortium_id", referencedColumnName="id")
     */
    private $consorzio;

    /**
     * @ORM\ManyToOne(targetEntity="Profession", inversedBy="phone")
     * @ORM\JoinColumn(name="profession_id", referencedColumnName="id")
     */
    private $professione;

    /**
     * @ORM\ManyToOne(targetEntity="Iat", inversedBy="phone")
     * @ORM\JoinColumn(name="iat_id", referencedColumnName="id")
     */
    private $iat;
}
