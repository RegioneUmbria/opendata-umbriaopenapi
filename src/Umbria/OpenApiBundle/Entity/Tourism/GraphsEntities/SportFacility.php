<?php

namespace Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities;

use Doctrine\ORM\Mapping as ORM;
use Umbria\OpenApiBundle\Entity\Address;
use JMS\Serializer\Annotation as JMS;
use Umbria\OpenApiBundle\Entity\Type;

/**
 * SportFacility
 *
 * @author Lorenzo Franco Ranucci
 *
 * @ORM\Table(name="tourism_impianto_sportivo")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\SportFacilityRepository")
 */
class SportFacility
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="uri", type="string", length=255, unique=true)
     */
    private $uri;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;
    /**
     * @var Type[]
     * @ORM\ManyToMany(targetEntity="\Umbria\OpenApiBundle\Entity\Type", orphanRemoval=true, cascade={"persist", "merge"})
     * @ORM\JoinTable(name="ImpiantoSportivoType",
     *      joinColumns={@ORM\JoinColumn(name="impianto_sportivo_uri", referencedColumnName="uri")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="type_uri", referencedColumnName="uri")}
     *      )
     */
    private $types;



    /**
     * @var array
     *
     * @ORM\Column(name="sport", type="array", nullable=true)
     */
    private $sport;

    /**
     * @var string
     *
     * @ORM\Column(name="municipality", type="string", length=255, nullable=true)
     *
     */
    private $municipality;

    /**
     * @var string
     *
     * @ORM\Column(name="publicTransport", type="string", length=255, nullable=true)
     */
    private $publicTransport;

    /**
     * @var integer
     *
     * @ORM\Column(name="parkings", type="integer", nullable=true)
     */
    private $parkings;

    /**
     * @var boolean
     *
     * @ORM\Column(name="disabledAccess", type="boolean", nullable=true)
     */
    private $disabledAccess;

    /**
     * @var integer
     *
     * @ORM\Column(name="employees", type="integer", nullable=true)
     */
    private $employees;

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
     * @return TravelAgency
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
     * @return TravelAgency
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
     * @return array
     */
    public function getSport()
    {
        return $this->sport;
    }

    /**
     * @param array $sport
     */
    public function setSport($sport)
    {
        $this->sport = $sport;
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
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getPublicTransport()
    {
        return $this->publicTransport;
    }

    /**
     * @param string $publicTransport
     */
    public function setPublicTransport($publicTransport)
    {
        $this->publicTransport = $publicTransport;
    }

    /**
     * @return int
     */
    public function getParkings()
    {
        return $this->parkings;
    }

    /**
     * @param int $parkings
     */
    public function setParkings($parkings)
    {
        $this->parkings = $parkings;
    }

    /**
     * @return boolean
     */
    public function isDisabledAccess()
    {
        return $this->disabledAccess;
    }

    /**
     * @param boolean $disabledAccess
     */
    public function setDisabledAccess($disabledAccess)
    {
        $this->disabledAccess = $disabledAccess;
    }

    /**
     * @return int
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    /**
     * @param int $employees
     */
    public function setEmployees($employees)
    {
        $this->employees = $employees;
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
     * @return string
     */
    public function getMunicipality()
    {
        return $this->municipality;
    }

    /**
     * @param string $municipality
     */
    public function setMunicipality($municipality)
    {
        $this->municipality = $municipality;
    }

    /**
     * Set types
     *
     * @param Type[] $types
     *
     * @return Attractor
     */
    public function setTypes($types)
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Get types
     *
     * @return Type[]
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set lat
     *
     * @param float $lat
     *
     * @return Attractor
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
     * @return Attractor
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