<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * FaxNumber.
 *
 * @ORM\Table(name="tourism_fax_number")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\FaxNumberRepository")
 */
class FaxNumber
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
     * @JMS\SerializedName("num_fax")
     *
     * @JMS\Groups({"fax-number.*"})
     */
    private $numFax;

    /**
     * @ORM\ManyToOne(targetEntity="TravelAgency", inversedBy="faxNumber")
     * @ORM\JoinColumn(name="travel_agency_id", referencedColumnName="id")
     */
    private $agenziaViaggio;

    /**
     * @ORM\ManyToOne(targetEntity="Consortium", inversedBy="faxNumber")
     * @ORM\JoinColumn(name="consortium_id", referencedColumnName="id")
     */
    private $consorzio;

    /**
     * @ORM\ManyToOne(targetEntity="Profession", inversedBy="faxNumber")
     * @ORM\JoinColumn(name="profession_id", referencedColumnName="id")
     */
    private $professione;

    /**
     * @ORM\ManyToOne(targetEntity="Iat", inversedBy="faxNumber")
     * @ORM\JoinColumn(name="iat_id", referencedColumnName="id")
     */
    private $iat;
}
