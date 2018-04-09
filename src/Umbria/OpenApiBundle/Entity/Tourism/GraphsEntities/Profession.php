<?php

namespace Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities;

use Doctrine\ORM\Mapping as ORM;
use Umbria\OpenApiBundle\Entity\Address;
use JMS\Serializer\Annotation as JMS;

/**
 * Profession
 *
 * @author Lorenzo Franco Ranucci
 *
 * @ORM\Table(name="tourism_profession")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\ProfessionRepository")
 */
class Profession
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
     * @ORM\Column(name="firstName", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255, nullable=true)
     */
    private $lastName;

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
     * @var string
     *
     * @ORM\Column(name="resourceOriginUrl", type="string", length=255, nullable=true)
     */
    private $resourceOriginUrl;

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
     * @var array
     *
     * @ORM\Column(name="spokenLanguage", type="array", nullable=true)
     */
    private $spokenLanguage;

    /**
     * @var string
     *
     * @ORM\Column(name="specialization", type="array", nullable=true)
     */
    private $specialization;


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
     * Set uri
     *
     * @param string $uri
     *
     * @return Profession
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Profession
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Profession
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }


    /**
     * Set email
     *
     * @param array $email
     *
     * @return Profession
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
     * @return Profession
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
     * Set resourceOriginUrl
     *
     * @param string $resourceOriginUrl
     *
     * @return Profession
     */
    public function setResourceOriginUrl($resourceOriginUrl)
    {
        $this->resourceOriginUrl = $resourceOriginUrl;

        return $this;
    }

    /**
     * Get resourceOriginUrl
     *
     * @return string
     */
    public function getResourceOriginUrl()
    {
        return $this->resourceOriginUrl;
    }

    /**
     * Set fax
     *
     * @param array $fax
     *
     * @return Profession
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
     * @return Profession
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
     * Set spokenLanguage
     *
     * @param array $spokenLanguage
     *
     * @return Profession
     */
    public function setSpokenLanguage($spokenLanguage)
    {
        $this->spokenLanguage = $spokenLanguage;

        return $this;
    }

    /**
     * Get spokenLanguage
     *
     * @return array
     */
    public function getSpokenLanguage()
    {
        return $this->spokenLanguage;
    }

    /**
     * Set specialization
     *
     * @param array $specialization
     *
     * @return Profession
     */
    public function setSpecialization($specialization)
    {
        $this->specialization = $specialization;

        return $this;
    }

    /**
     * Get specialization
     *
     * @return array
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }


    /**
     * Set address
     *
     * @param Address $address
     *
     * @return Consortium
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return Address
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
