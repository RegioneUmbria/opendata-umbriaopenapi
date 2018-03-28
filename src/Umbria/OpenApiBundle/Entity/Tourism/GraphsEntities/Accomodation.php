<?php

namespace Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities;

use Doctrine\ORM\Mapping as ORM;
use Umbria\OpenApiBundle\Entity\Address;
use Umbria\OpenApiBundle\Entity\Type;
use Umbria\OpenApiBundle\Entity\Category;
use JMS\Serializer\Annotation as JMS;

/**
 * Accomodation entity
 *
 * @author Lorenzo Franco Ranucci
 *
 * @ORM\Table(name="tourism_struttura_ricettiva")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\AccomodationRepository")
 */
class Accomodation
{

    /**
     * @var string
     *
     * @ORM\Column(name="uri", type="string", length=255)
     * @ORM\Id
     *
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
     * @ORM\JoinTable(name="StrutturaRicettivaType",
     *      joinColumns={@ORM\JoinColumn(name="struttura_ricettiva_uri", referencedColumnName="uri")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="type_uri", referencedColumnName="uri")}
     *      )
     */
    private $types;


    /**
     * @var string
     *
     * @ORM\Column(name="typology", type="string", length=255, nullable=true)
     */
    private $typology;

    /**
     * @var Category[]
     * @ORM\ManyToMany(targetEntity="\Umbria\OpenApiBundle\Entity\Category", orphanRemoval=true, cascade={"persist", "merge"})
     * @ORM\JoinTable(name="StrutturaRicettivaCategory",
     *      joinColumns={@ORM\JoinColumn(name="struttura_ricettiva_uri", referencedColumnName="uri")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_uri", referencedColumnName="uri")}
     *      )
     */
    private $categories;

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
     * @ORM\Column(name="units", type="integer", nullable=true)
     */
    private $units;

    /**
     * @var array
     *
     * @ORM\Column(name="beds", type="integer", nullable=true)
     */
    private $beds;

    /**
     * @var array
     *
     * @ORM\Column(name="toilets", type="integer", nullable=true)
     */
    private $toilets;

    /**
     * @var Address
     * @ORM\OneToOne(targetEntity="\Umbria\OpenApiBundle\Entity\Address", cascade={"persist", "merge", "remove"} )
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    private $address;

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
     * @return Accomodation
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
     * @return Accomodation
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
     * Set types
     *
     * @param Type[] $types
     *
     * @return Accomodation
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
     * @return string
     */
    public function getTypology()
    {
        return $this->typology;
    }

    /**
     * @param string $typology
     */
    public function setTypology($typology)
    {
        $this->typology = $typology;
    }

    /**
     * Set categories
     *
     * @param Category[] $categories
     *
     * @return Accomodation
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get categories
     *
     * @return Category[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Accomodation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
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
     * @return Accomodation
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
     * @return Accomodation
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
     * @return Accomodation
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
     * @return array
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @param array $units
     */
    public function setUnits($units)
    {
        $this->units = $units;
    }

    /**
     * @return array
     */
    public function getBeds()
    {
        return $this->beds;
    }

    /**
     * @param array $beds
     */
    public function setBeds($beds)
    {
        $this->beds = $beds;
    }

    /**
     * @return array
     */
    public function getToilets()
    {
        return $this->toilets;
    }

    /**
     * @param array $toilets
     */
    public function setToilets($toilets)
    {
        $this->toilets = $toilets;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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