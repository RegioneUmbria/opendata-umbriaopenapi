<?php

namespace Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities;

use Doctrine\ORM\Mapping as ORM;
use Umbria\OpenApiBundle\Entity\Address;
use JMS\Serializer\Annotation as JMS;

/**
 * Iat
 *
 * @author Lorenzo Franco Ranucci
 *
 * @ORM\Table(name="tourism_iat")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\IatRepository")
 */
class Iat
{

    /**
     * @var string
     *
     * @ORM\Column(name="uri", type="string", length=255, unique=true)
     *
     * @ORM\Id
     */
    private $uri;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;


    /**
     * @var array
     *
     * @ORM\Column(name="email", type="array", nullable=true)
     */
    private $email;

    /**
     * @var array
     *
     * @ORM\Column(name="telephone", type="array", nullable=true)
     */
    private $telephone;

    /**
     * @var array
     *
     * @ORM\Column(name="fax", type="array", nullable=true)
     */
    private $fax;

    /**
     * @var array
     *
     * @ORM\Column(name="types", type="array", nullable=true)
     */
    private $types;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=255, nullable=true)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="municipalitiesList", type="string", length=2048, nullable=true)
     */
    private $municipalitiesList;

    /**
     * @var Address
     * @ORM\OneToOne(targetEntity="\Umbria\OpenApiBundle\Entity\Address", cascade={"persist", "merge", "remove"} )
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    private $address;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float", nullable=true)
     */
    private $lat;

    /**
     * @var float
     *
     * @ORM\Column(name="lng", type="float", nullable=true)
     */
    private $lng;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update_at", type="datetime")
     *
     * @JMS\Exclude()
     */
    private $lastUpdateAt;

    /**
     * @var \boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     * @JMS\Exclude()
     **/
    private $isDeleted;

    /**
     * @var \boolean
     *
     * @ORM\Column(name="is_in_error", type="boolean")
     * @JMS\Exclude()
     **/
    private $isInError;


    /**
     * Set uri
     *
     * @param string $uri
     *
     * @return Iat
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Iat
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set email
     *
     * @param array $email
     *
     * @return Iat
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return array
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telephone
     *
     * @param array $telephone
     *
     * @return Iat
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return array
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set fax
     *
     * @param array $fax
     *
     * @return Iat
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return array
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set types
     *
     * @param array $types
     *
     * @return Iat
     */
    public function setTypes($types)
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Get types
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return Iat
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set municipalitiesList
     *
     * @param string $municipalitiesList
     *
     * @return Iat
     */
    public function setMunicipalitiesList($municipalitiesList)
    {
        $this->municipalitiesList = $municipalitiesList;

        return $this;
    }

    /**
     * Get municipalitiesList
     *
     * @return string
     */
    public function getMunicipalitiesList()
    {
        return $this->municipalitiesList;
    }

    /**
     * Set address
     *
     * @param Address $address
     *
     * @return Iat
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \stdClass
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set lat
     *
     * @param float $lat
     *
     * @return Consortium
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param float $lng
     *
     * @return Consortium
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set lastUpdateAt
     *
     * @param \timestamp $lastUpdateAt
     *
     * @return Attractor
     */
    public function setLastUpdateAt($lastUpdateAt)
    {
        $this->lastUpdateAt = $lastUpdateAt;

        return $this;
    }

    /**
     * Get lastUpdateAt
     *
     * @return \DateTime
     */
    public function getLastUpdateAt()
    {
        return $this->lastUpdateAt;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @param boolean $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    public function getId()
    {
        $uriarray = explode("/", $this->uri);
        return $uriarray[count($uriarray) - 1];
    }

    /**
     * @return boolean
     */
    public function isInError()
    {
        return $this->isInError;
    }

    /**
     * @param boolean $isInError
     */
    public function setIsInError($isInError)
    {
        $this->isInError = $isInError;
    }
}
