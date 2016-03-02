<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Address.
 *
 * @ORM\Table(name="tourism_address")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\AddressRepository")
 */
class Address
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
     * @JMS\SerializedName("streetAddress")
     *
     * @JMS\Groups({"address.*"})
     */
    private $streetAddress;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("postalCode")
     *
     * @JMS\Groups({"address.*"})
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("addressLocality")
     *
     * @JMS\Groups({"address.*"})
     */
    private $addressLocality;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"address.*"})
     */
    private $codiceIstatComune;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("addressRegion")
     *
     * @JMS\Groups({"address.*"})
     */
    private $addressRegion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"address.*"})
     */
    private $coordinateGeografiche;

    /**
     * @ORM\ManyToOne(targetEntity="TravelAgency", inversedBy="address")
     * @ORM\JoinColumn(name="travel_agency_id", referencedColumnName="id")
     */
    private $agenziaViaggio;

    /**
     * @ORM\ManyToOne(targetEntity="Consortium", inversedBy="address")
     * @ORM\JoinColumn(name="consortium_id", referencedColumnName="id")
     */
    private $consorzio;

    /**
     * @ORM\ManyToOne(targetEntity="Profession", inversedBy="address")
     * @ORM\JoinColumn(name="profession_id", referencedColumnName="id")
     */
    private $professione;

    /********** Campi aggiuntivi **/
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"address.*"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"address.*"})
     */
    private $longitude;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set streetAddress.
     *
     * @param string $streetAddress
     *
     * @return Address
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    /**
     * Get streetAddress.
     *
     * @return string
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * Set postalCode.
     *
     * @param string $postalCode
     *
     * @return Address
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode.
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set addressLocality.
     *
     * @param string $addressLocality
     *
     * @return Address
     */
    public function setAddressLocality($addressLocality)
    {
        $this->addressLocality = $addressLocality;

        return $this;
    }

    /**
     * Get addressLocality.
     *
     * @return string
     */
    public function getAddressLocality()
    {
        return $this->addressLocality;
    }

    /**
     * Set codiceIstatComune.
     *
     * @param string $codiceIstatComune
     *
     * @return Address
     */
    public function setCodiceIstatComune($codiceIstatComune)
    {
        $this->codiceIstatComune = $codiceIstatComune;

        return $this;
    }

    /**
     * Get codiceIstatComune.
     *
     * @return string
     */
    public function getCodiceIstatComune()
    {
        return $this->codiceIstatComune;
    }

    /**
     * Set addressRegion.
     *
     * @param string $addressRegion
     *
     * @return Address
     */
    public function setAddressRegion($addressRegion)
    {
        $this->addressRegion = $addressRegion;

        return $this;
    }

    /**
     * Get addressRegion.
     *
     * @return string
     */
    public function getAddressRegion()
    {
        return $this->addressRegion;
    }

    /**
     * Set coordinateGeografiche.
     *
     * @param string $coordinateGeografiche
     *
     * @return Address
     */
    public function setCoordinateGeografiche($coordinateGeografiche)
    {
        $this->coordinateGeografiche = $coordinateGeografiche;

        return $this;
    }

    /**
     * Get coordinateGeografiche.
     *
     * @return string
     */
    public function getCoordinateGeografiche()
    {
        return $this->coordinateGeografiche;
    }

    /**
     * Set agenziaViaggio.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\TravelAgency $agenziaViaggio
     *
     * @return Address
     */
    public function setAgenziaViaggio(\Umbria\OpenApiBundle\Entity\Tourism\TravelAgency $agenziaViaggio = null)
    {
        $this->agenziaViaggio = $agenziaViaggio;

        return $this;
    }

    /**
     * Get agenziaViaggio.
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\TravelAgency
     */
    public function getAgenziaViaggio()
    {
        return $this->agenziaViaggio;
    }

    /**
     * Set consorzio.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Consortium $consorzio
     *
     * @return Address
     */
    public function setConsorzio(\Umbria\OpenApiBundle\Entity\Tourism\Consortium $consorzio = null)
    {
        $this->consorzio = $consorzio;

        return $this;
    }

    /**
     * Get consorzio.
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\Consortium
     */
    public function getConsorzio()
    {
        return $this->consorzio;
    }

    /**
     * Set professione.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Profession $professione
     *
     * @return Address
     */
    public function setProfessione(\Umbria\OpenApiBundle\Entity\Tourism\Profession $professione = null)
    {
        $this->professione = $professione;

        return $this;
    }

    /**
     * Get professione.
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\Profession
     */
    public function getProfessione()
    {
        return $this->professione;
    }

    /**
     * Set latitude.
     *
     * @param string $latitude
     *
     * @return Address
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude.
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude.
     *
     * @param string $longitude
     *
     * @return Address
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude.
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}
