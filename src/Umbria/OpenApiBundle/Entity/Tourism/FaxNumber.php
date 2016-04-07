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

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set numFax
     *
     * @param string $numFax
     *
     * @return FaxNumber
     */
    public function setNumFax($numFax)
    {
        $this->numFax = $numFax;

        return $this;
    }

    /**
     * Get numFax
     *
     * @return string
     */
    public function getNumFax()
    {
        return $this->numFax;
    }

    /**
     * Set agenziaViaggio
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\TravelAgency $agenziaViaggio
     *
     * @return FaxNumber
     */
    public function setAgenziaViaggio(\Umbria\OpenApiBundle\Entity\Tourism\TravelAgency $agenziaViaggio = null)
    {
        $this->agenziaViaggio = $agenziaViaggio;

        return $this;
    }

    /**
     * Get agenziaViaggio
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\TravelAgency
     */
    public function getAgenziaViaggio()
    {
        return $this->agenziaViaggio;
    }

    /**
     * Set consorzio
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Consortium $consorzio
     *
     * @return FaxNumber
     */
    public function setConsorzio(\Umbria\OpenApiBundle\Entity\Tourism\Consortium $consorzio = null)
    {
        $this->consorzio = $consorzio;

        return $this;
    }

    /**
     * Get consorzio
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\Consortium
     */
    public function getConsorzio()
    {
        return $this->consorzio;
    }

    /**
     * Set professione
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Profession $professione
     *
     * @return FaxNumber
     */
    public function setProfessione(\Umbria\OpenApiBundle\Entity\Tourism\Profession $professione = null)
    {
        $this->professione = $professione;

        return $this;
    }

    /**
     * Get professione
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\Profession
     */
    public function getProfessione()
    {
        return $this->professione;
    }

    /**
     * Set iat
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Iat $iat
     *
     * @return FaxNumber
     */
    public function setIat(\Umbria\OpenApiBundle\Entity\Tourism\Iat $iat = null)
    {
        $this->iat = $iat;

        return $this;
    }

    /**
     * Get iat
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\Iat
     */
    public function getIat()
    {
        return $this->iat;
    }
}
