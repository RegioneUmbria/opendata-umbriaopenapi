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
     * Set numTelefono
     *
     * @param string $numTelefono
     *
     * @return Phone
     */
    public function setNumTelefono($numTelefono)
    {
        $this->numTelefono = $numTelefono;

        return $this;
    }

    /**
     * Get numTelefono
     *
     * @return string
     */
    public function getNumTelefono()
    {
        return $this->numTelefono;
    }

    /**
     * Set agenziaViaggio
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\TravelAgency $agenziaViaggio
     *
     * @return Phone
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
     * @return Phone
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
     * @return Phone
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
     * @return Phone
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
