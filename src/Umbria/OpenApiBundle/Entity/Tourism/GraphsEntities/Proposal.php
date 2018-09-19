<?php

namespace Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntitiesInnerObjects\ProposalDescription;

/**
 * Proposal
 *
 * @author Lorenzo Franco Ranucci
 *
 * @ORM\Table(name="tourism_proposal")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\ProposalRepository")
 */
class Proposal
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
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     */
    private $subject;


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
     * @ORM\Column(name="resourceOriginUrl", type="string", length=1024, nullable=true)
     */
    private $resourceOriginUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="shortDescription", type="string", length=1024, nullable=true)
     */
    private $shortDescription;

    /**
     * @var array
     *
     * @ORM\Column(name="types", type="array", nullable=true)
     */
    private $types;

    /**
     * @var array
     *
     * @ORM\Column(name="categories", type="array", nullable=true)
     */
    private $categories;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=255, nullable=true)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="placeFrom", type="string", length=255, nullable=true)
     */
    private $placeFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="placeTo", type="string", length=255, nullable=true)
     */
    private $placeTo;

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
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=1024, nullable=true)
     */
    private $comment;

    /**
     * @var ProposalDescription
     * @ORM\OneToMany(targetEntity="\Umbria\OpenApiBundle\Entity\Tourism\GraphsEntitiesInnerObjects\ProposalDescription", mappedBy="proposal", cascade={"persist", "merge", "remove"})
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
     * Set uri
     *
     * @param string $uri
     *
     * @return Proposal
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
     * @return Proposal
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
     * Set subject
     *
     * @param string $subject
     *
     * @return Proposal
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
     * Set images
     *
     * @param array $images
     *
     * @return Proposal
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
     * @return Proposal
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
     * @return Proposal
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
     * @return Proposal
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
     * Set types
     *
     * @param array $types
     *
     * @return Proposal
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
     * Set categories
     *
     * @param array $categories
     *
     * @return Proposal
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get categories
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return Proposal
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
     * Set lat
     *
     * @param float $lat
     *
     * @return Proposal
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
     * @return Proposal
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
     * @return ProposalDescription
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
    public function getPlaceFrom()
    {
        return $this->placeFrom;
    }

    /**
     * @param string $placeFrom
     */
    public function setPlaceFrom($placeFrom)
    {
        $this->placeFrom = $placeFrom;
    }

    /**
     * @return string
     */
    public function getPlaceTo()
    {
        return $this->placeTo;
    }

    /**
     * @param string $placeTo
     */
    public function setPlaceTo($placeTo)
    {
        $this->placeTo = $placeTo;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
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

