<?php

namespace Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Definition\Exception\Exception;
use Umbria\OpenApiBundle\Entity\ExternalResource;
use \EasyRdf_Sparql_Client as EasyRdf_Sparql_Client;

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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="types", type="array", nullable=true)
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
     * @var array
     *
     * @ORM\Column(name="externalLinks", type="array", nullable=true)
     */
    private $externalLinks;

    /**
     * @var string
     *
     * @ORM\Column(name="resourceOriginUrl", type="string", length=255, nullable=true)
     */
    private $resourceOriginUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="shortDescription", type="string", length=255, nullable=true)
     */
    private $shortDescription;

    /**
     * @var ExternalResource
     * @ORM\ManyToMany(targetEntity="\Umbria\OpenApiBundle\Entity\ExternalResource", orphanRemoval=true, cascade={"persist", "merge"})
     * @ORM\JoinTable(name="same_as",
     *      joinColumns={@ORM\JoinColumn(name="ru_resource_uri", referencedColumnName="uri")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="external_resource_uri", referencedColumnName="uri")}
     *      )
     */
    private $sameAs;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=255, nullable=true)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var array
     *
     * @ORM\Column(name="travelTime", type="array", nullable=true)
     */
    private $travelTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update_at", type="date")
     */
    private $lastUpdateAt;

    /**
     * Attractor public constructor.
     * @param \EasyRdf_Resource $resource
     * @param $lastUpdateAt \DateTime creation date
     * @param \EasyRdf_Resource null $sameAsResource
     * @return Attractor
     */
    public static function load($resource, $lastUpdateAt, $sameAsResource = null)
    {
        try {
            return new Attractor($resource, $lastUpdateAt, $sameAsResource);
        } catch (Exception $e) {
            return null;
        }
    }


    /**
     * Attractor constructor.
     * @param \EasyRdf_Resource $resource
     * @param $lastUpdateAt \DateTime creation date
     * @param \EasyRdf_Resource null $sameAsResource
     * @throws \Exception when uri is null
     */
    private function __construct($resource, $lastUpdateAt, $sameAsResource = null)
    {
        $uri = $resource->getUri();
        if ($uri != null) {
            $this->setUri($uri);
            $this->lastUpdateAt = $lastUpdateAt;
            $this->setName(($p = $resource->get("rdfs:label")) != null ? $p->getValue() : null);

            $typesarray = $resource->all("rdf:type");
            if ($typesarray != null) {
                $this->types = array();
                $cnt = 0;
                foreach ($typesarray as $type) {
                    $this->types[$cnt] = $type->toRdfPhp()['value'];
                    $cnt++;
                }
            }

            $this->setProvenance(($p = $resource->get("<http://purl.org/dc/elements/1.1/provenance>")) != null ? $p->getValue() : null);
            $this->setMunicipality(($p = $resource->get("<http://dbpedia.org/ontology/municipality>")) != null ? $p->getValue() : null);
            $this->setIstat(($p = $resource->get("<http://dbpedia.org/ontology/istat>")) != null ? $p->getValue() : null);
            $this->setSubject(($p = $resource->get("<http://purl.org/dc/elements/1.1/subject>")) != null ? $p->getValue() : null);
            $this->setLat(($p = $resource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#lat>")) != null ? (float)$p->getValue() : null);
            $this->setLng(($p = $resource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#long>")) != null ? (float)$p->getValue() : null);
            /*TODO images*/
            $this->setTextTitle(($p = $resource->get("<http://dati.umbria.it/tourism/ontology/titolo_testo>")) != null ? $p->getValue() : null);
            /*TODO link esterni associati*/
            $this->setResourceOriginUrl(($p = $resource->get("<http://dati.umbria.it/tourism/ontology/url_risorsa>")) != null ? $p->getValue() : null);
            $this->setShortDescription(($p = $resource->get("<http://dati.umbria.it/tourism/ontology/descrizione_sintetica>")) != null ? $p->getValue() : null);
            $this->setLanguage(($p = $resource->get("<http://purl.org/dc/elements/1.1/language>")) != null ? $p->getUri() : null);

            $descriptionResource = $resource->get("<http://dati.umbria.it/tourism/ontology/descrizione>");
            $descriptionText = $descriptionResource->get("<http://dati.umbria.it/tourism/ontology/testo>")->getValue();
            $this->setDescription($descriptionText);

            /*TODO travel time*/

            if ($sameAsResource != null) {
                $sameAsArray = $sameAsResource->all("<http://www.w3.org/2002/07/owl#sameAs>");
                if ($sameAsArray != null) {
                    $this->sameAs = array();
                    $cnt = 0;
                    foreach ($sameAsArray as $sameAs) {
                        $externalResource = new ExternalResource();
                        $externalResourceUri = $sameAs->toRdfPhp()['value'];
                        $externalResource->setUri($externalResourceUri);
                        $sparqlClient = new EasyRdf_Sparql_Client("http://dbpedia.org/sparql");

                        $queryLabel = "SELECT ?o WHERE {<" . $externalResourceUri . "> <http://www.w3.org/2000/01/rdf-schema#label> ?o. FILTER ( lang(?o) = \"it\" )}";
                        $sparqlResultLabel = $sparqlClient->query($queryLabel);
                        $sparqlResultLabel->rewind();
                        while ($sparqlResultLabel->valid()) {
                            $current = $sparqlResultLabel->current();
                            $externalResource->setName($current->o);
                            $sparqlResultLabel->next();
                        }

                        $queryAbstract = "SELECT ?o WHERE {<" . $externalResourceUri . "> <http://dbpedia.org/ontology/abstract> ?o. FILTER ( lang(?o) = \"it\" )}";
                        $sparqlResultAbstract = $sparqlClient->query($queryAbstract);
                        $sparqlResultAbstract->rewind();
                        while ($sparqlResultAbstract->valid()) {
                            $current = $sparqlResultAbstract->current();
                            $externalResource->setDescription($current->o);
                            $sparqlResultAbstract->next();
                        }

                        $this->sameAs[$cnt] = $externalResource;
                        $cnt++;
                    }
                }
            }
        } else {
            throw new \Exception();
        }
    }


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
     * @param array $types
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
     * @return array
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
     * Set externalLinks
     *
     * @param array $externalLinks
     *
     * @return Attractor
     */
    public function setExternalLinks($externalLinks)
    {
        $this->externalLinks = $externalLinks;

        return $this;
    }

    /**
     * Get externalLinks
     *
     * @return array
     */
    public function getExternalLinks()
    {
        return $this->externalLinks;
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
     * Set description
     *
     * @param string $description
     *
     * @return Attractor
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set travelTime
     *
     * @param array $travelTime
     *
     * @return Attractor
     */
    public function setTravelTime($travelTime)
    {
        $this->travelTime = $travelTime;

        return $this;
    }

    /**
     * Get travelTime
     *
     * @return array
     */
    public function getTravelTime()
    {
        return $this->travelTime;
    }

    /**
     * Set lastUpdateAt
     *
     * @param \DateTime $lastUpdateAt
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
}

