<?php

namespace Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities;

use Doctrine\ORM\Mapping as ORM;
use Umbria\OpenApiBundle\Entity\Category;
use Umbria\OpenApiBundle\Entity\ExternalResource;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntitiesInnerObjects\AttractorDescription;
use JMS\Serializer\Annotation as JMS;
use Umbria\OpenApiBundle\Entity\Type;

/**
 * Attractor entity
 *
 * @author Lorenzo Franco Ranucci
 *
 * @ORM\Table(name="tourism_attractor")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\AttractorRepository")
 */
class Attractor
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
     * @ORM\JoinTable(name="AttractorType",
     *      joinColumns={@ORM\JoinColumn(name="attractor_uri", referencedColumnName="uri")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="type_uri", referencedColumnName="uri")}
     *      )
     */
    private $types;

    /**
     * @var string
     *
     * @ORM\Column(name="provenance", type="string", length=255, nullable=true)
     */
    private $provenance;

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
     * @ORM\Column(name="istat", type="string", length=255, nullable=true)
     */
    private $istat;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     */
    private $subject;

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
     * @var array
     *
     * @ORM\Column(name="images", type="array", nullable=true)
     */
    private $images;

    /**
     * @var string
     *
     * @ORM\Column(name="textTitle", type="string", length=255, nullable=true)
     */
    private $textTitle;


    /**
     * @var string
     *
     * @ORM\Column(name="resourceOriginUrl", type="string", length=255, nullable=true)
     */
    private $resourceOriginUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="shortDescription", type="text" , nullable=true)
     */
    private $shortDescription;

    /**
     * @var ExternalResource[]
     * @ORM\ManyToMany(targetEntity="\Umbria\OpenApiBundle\Entity\ExternalResource", orphanRemoval=true, cascade={"persist", "merge"})
     * @ORM\JoinTable(name="same_as",
     *      joinColumns={@ORM\JoinColumn(name="ru_resource_uri", referencedColumnName="uri")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="external_resource_uri", referencedColumnName="uri")}
     *      )
     */
    private $sameAs;

    /**
     * @var ExternalResource[]
     * @ORM\ManyToMany(targetEntity="\Umbria\OpenApiBundle\Entity\ExternalResource", orphanRemoval=true, cascade={"persist", "merge"})
     * @ORM\JoinTable(name="located_in",
     *      joinColumns={@ORM\JoinColumn(name="ru_resource_uri", referencedColumnName="uri")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="external_resource_uri", referencedColumnName="uri")}
     *      )
     */
    private $locatedIn;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=255, nullable=true)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;


    /**
     * @var AttractorDescription
     * @ORM\OneToMany(targetEntity="\Umbria\OpenApiBundle\Entity\Tourism\GraphsEntitiesInnerObjects\AttractorDescription", mappedBy="attractor", cascade={"persist", "merge", "remove"})
     */
    private $descriptions;


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
     * @var Category[]
     * @ORM\ManyToMany(targetEntity="\Umbria\OpenApiBundle\Entity\Category", orphanRemoval=true, cascade={"persist", "merge"})
     * @ORM\JoinTable(name="AttractorCategory",
     *      joinColumns={@ORM\JoinColumn(name="attractor_uri", referencedColumnName="uri")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_uri", referencedColumnName="uri")}
     *      )
     */
    private $categories;


    /**
     * Set uri
     *
     * @param string $uri
     *
     * @return Attractor
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
     * @return Attractor
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
     * Set provenance
     *
     * @param string $provenance
     *
     * @return Attractor
     */
    public function setProvenance($provenance)
    {
        $this->provenance = $provenance;

        return $this;
    }

    /**
     * Get provenance
     *
     * @return string
     */
    public function getProvenance()
    {
        return $this->provenance;
    }

    /**
     * Set municipality
     *
     * @param string $municipality
     *
     * @return Attractor
     */
    public function setMunicipality($municipality)
    {
        $this->municipality = $municipality;

        return $this;
    }

    /**
     * Get municipality
     *
     * @return string
     */
    public function getMunicipality()
    {
        return $this->municipality;
    }

    /**
     * Set istat
     *
     * @param string $istat
     *
     * @return Attractor
     */
    public function setIstat($istat)
    {
        $this->istat = $istat;

        return $this;
    }

    /**
     * Get istat
     *
     * @return string
     */
    public function getIstat()
    {
        return $this->istat;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Attractor
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
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
     * Set images
     *
     * @param array $images
     *
     * @return Attractor
     */
    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * Get images
     *
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set textTitle
     *
     * @param string $textTitle
     *
     * @return Attractor
     */
    public function setTextTitle($textTitle)
    {
        $this->textTitle = $textTitle;

        return $this;
    }

    /**
     * Get textTitle
     *
     * @return string
     */
    public function getTextTitle()
    {
        return $this->textTitle;
    }


    /**
     * Set resourceOriginUrl
     *
     * @param string $resourceOriginUrl
     *
     * @return Attractor
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
     * Set shortDescription
     *
     * @param string $shortDescription
     *
     * @return Attractor
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set sameAs
     *
     * @param array $sameAs
     *
     * @return Attractor
     */
    public function setSameAs($sameAs)
    {
        $this->sameAs = $sameAs;

        return $this;
    }

    /**
     * Get sameAs
     *
     * @return array
     */
    public function getSameAs()
    {
        return $this->sameAs;
    }

    /**
     * @return \Umbria\OpenApiBundle\Entity\ExternalResource[]
     */
    public function getLocatedIn()
    {
        return $this->locatedIn;
    }

    /**
     * @param \Umbria\OpenApiBundle\Entity\ExternalResource[] $locatedIn
     */
    public function setLocatedIn($locatedIn)
    {
        $this->locatedIn = $locatedIn;
    }


    /**
     * Set language
     *
     * @param string $language
     *
     * @return Attractor
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
     * Set comment
     *
     * @param string $comment
     *
     * @return Attractor
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }


    /**
     * @return AttractorDescription
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }

    /**
     * @param array $descriptions
     */
    public function setDescriptions($descriptions)
    {
        $this->descriptions = $descriptions;
    }


    public function hasDbpediaInfo()
    {
        return ($this->sameAs != null && count($this->sameAs->getValues()) > 0);
    }

    public function hasDbpediaLocalization()
    {
        return ($this->locatedIn != null && count($this->locatedIn->getValues()) > 0);
    }

    public function getId()
    {
        $uriarray = explode("/", $this->uri);
        return $uriarray[count($uriarray) - 1];
    }

    /**
     * Set categories
     *
     * @param Category[] $categories
     *
     * @return Attractor
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


