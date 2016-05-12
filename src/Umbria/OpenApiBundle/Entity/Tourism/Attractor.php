<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Attractor.
 *
 * @ORM\Table(name="tourism_attractor")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\AttractorRepository")
 */
class Attractor
{
    const TYPE = 'tourism_attractor';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Groups({"attractor.*"})
     * @JMS\Exclude()
     */
    private $id;

    /**
     * @JMS\Type("string")
     */
    private $type;

    /**
     * @ORM\Column(type="string")
     *
     * @JMS\Type("string")
     *
     * @JMS\SerializedName("id")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $idElemento;

    /**
     * @ORM\Column(type="integer")
     *
     * @JMS\Type("integer")
     * @JMS\SerializedName("id")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $idContenuto;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\SerializedName("label")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $denominazione;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $urlRisorsa;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $descrizioneSintetica;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\SerializedName("subject")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $keywords;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $titoloTesto;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"attractor.*"})
     *
     */
    private $abstract;

    /**
     * @ORM\OneToMany(targetEntity="Description", mappedBy="attrattore", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Description>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $descrizioni;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $immagini;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $linkEsterniAssociati;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("etichetta_tempi_di_viaggio")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $etichettaTempiDiViaggio;

    /**
     * @ORM\OneToMany(targetEntity="TravelTime", mappedBy="attrattore", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\TravelTime>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $tempiDiViaggio;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $immagineSpallaDestra;

    /**
     * @ORM\OneToMany(targetEntity="Coordinate", mappedBy="attrattore", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Coordinate>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $coordinate;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\SerializedName("municipality")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $comune;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     ** @JMS\SerializedName("istat")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $codiceIstatComune;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="attrattore", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Category>")
     *
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $categorie;

    /**
     * @ORM\ManyToOne(targetEntity="RDF", inversedBy="attrattori")
     * @ORM\JoinColumn(name="rdf_id", referencedColumnName="id")
     */
    private $rdf;

    /********** Campi aggiuntivi **/
    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $dbpediaResource;

    /**
     * @ORM\Column(type="boolean")
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $dbpediaInfo = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $dbpediaAbstract;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $wikipediaLink;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @JMS\Groups({"attractor.*"})
     */
    private $locatedIn;



    /********** Constructor **/
    public function __construct()
    {
        $this->descrizioni = new ArrayCollection();
        $this->tempiDiViaggio = new ArrayCollection();
        $this->coordinate = new ArrayCollection();
        $this->categorie = new ArrayCollection();
    }

    /********** Getter e Setter **/
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
     * @return mixed
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * Set idElemento.
     *
     * @param string $idElemento
     *
     * @return Attractor
     */
    public function setIdElemento($idElemento)
    {
        $this->idElemento = $idElemento;

        return $this;
    }

    /**
     * Get idElemento.
     *
     * @return string
     */
    public function getIdElemento()
    {
        return $this->idElemento;
    }

    /**
     * Set idContenuto.
     *
     * @param int $idContenuto
     *
     * @return Attractor
     */
    public function setIdContenuto($idContenuto)
    {
        $this->idContenuto = $idContenuto;

        return $this;
    }

    /**
     * Get idContenuto.
     *
     * @return int
     */
    public function getIdContenuto()
    {
        return $this->idContenuto;
    }

    /**
     * Set denominazione.
     *
     * @param string $denominazione
     *
     * @return Attractor
     */
    public function setDenominazione($denominazione)
    {
        $this->denominazione = $denominazione;

        return $this;
    }

    /**
     * Get denominazione.
     *
     * @return string
     */
    public function getDenominazione()
    {
        return $this->denominazione;
    }

    /**
     * Set urlRisorsa.
     *
     * @param string $urlRisorsa
     *
     * @return Attractor
     */
    public function setUrlRisorsa($urlRisorsa)
    {
        $this->urlRisorsa = $urlRisorsa;

        return $this;
    }

    /**
     * Get urlRisorsa.
     *
     * @return string
     */
    public function getUrlRisorsa()
    {
        return $this->urlRisorsa;
    }

    /**
     * Set descrizioneSintetica.
     *
     * @param string $descrizioneSintetica
     *
     * @return Attractor
     */
    public function setDescrizioneSintetica($descrizioneSintetica)
    {
        $this->descrizioneSintetica = $descrizioneSintetica;

        return $this;
    }

    /**
     * Get descrizioneSintetica.
     *
     * @return string
     */
    public function getDescrizioneSintetica()
    {
        return $this->descrizioneSintetica;
    }

    /**
     * Set keywords.
     *
     * @param string $keywords
     *
     * @return Attractor
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords.
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set titoloTesto.
     *
     * @param string $titoloTesto
     *
     * @return Attractor
     */
    public function setTitoloTesto($titoloTesto)
    {
        $this->titoloTesto = $titoloTesto;

        return $this;
    }

    /**
     * Get titoloTesto.
     *
     * @return string
     */
    public function getTitoloTesto()
    {
        return $this->titoloTesto;
    }

    /**
     * Set abstract.
     *
     * @param string $abstract
     *
     * @return Attractor
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;

        return $this;
    }

    /**
     * Get abstract.
     *
     * @return string
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * Set immagini.
     *
     * @param string $immagini
     *
     * @return Attractor
     */
    public function setImmagini($immagini)
    {
        $this->immagini = $immagini;

        return $this;
    }

    /**
     * Get immagini.
     *
     * @return string
     */
    public function getImmagini()
    {
        return $this->immagini;
    }

    /**
     * Set linkEsterniAssociati.
     *
     * @param string $linkEsterniAssociati
     *
     * @return Attractor
     */
    public function setLinkEsterniAssociati($linkEsterniAssociati)
    {
        $this->linkEsterniAssociati = $linkEsterniAssociati;

        return $this;
    }

    /**
     * Get linkEsterniAssociati.
     *
     * @return string
     */
    public function getLinkEsterniAssociati()
    {
        return $this->linkEsterniAssociati;
    }

    /**
     * Set etichettaTempiDiViaggio.
     *
     * @param string $etichettaTempiDiViaggio
     *
     * @return Attractor
     */
    public function setEtichettaTempiDiViaggio($etichettaTempiDiViaggio)
    {
        $this->etichettaTempiDiViaggio = $etichettaTempiDiViaggio;

        return $this;
    }

    /**
     * Get etichettaTempiDiViaggio.
     *
     * @return string
     */
    public function getEtichettaTempiDiViaggio()
    {
        return $this->etichettaTempiDiViaggio;
    }

    /**
     * Set immagineSpallaDestra.
     *
     * @param string $immagineSpallaDestra
     *
     * @return Attractor
     */
    public function setImmagineSpallaDestra($immagineSpallaDestra)
    {
        $this->immagineSpallaDestra = $immagineSpallaDestra;

        return $this;
    }

    /**
     * Get immagineSpallaDestra.
     *
     * @return string
     */
    public function getImmagineSpallaDestra()
    {
        return $this->immagineSpallaDestra;
    }

    /**
     * Set comune.
     *
     * @param string $comune
     *
     * @return Attractor
     */
    public function setComune($comune)
    {
        $this->comune = $comune;

        return $this;
    }

    /**
     * Get comune.
     *
     * @return string
     */
    public function getComune()
    {
        return $this->comune;
    }

    /**
     * Set codiceIstatComune.
     *
     * @param string $codiceIstatComune
     *
     * @return Attractor
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
     * Set dbpediaResource.
     *
     * @param string $dbpediaResource
     *
     * @return Attractor
     */
    public function setDbpediaResource($dbpediaResource)
    {
        $this->dbpediaResource = $dbpediaResource;

        return $this;
    }

    /**
     * Get dbpediaResource.
     *
     * @return string
     */
    public function getDbpediaResource()
    {
        return $this->dbpediaResource;
    }

    /**
     * Set dbpediaInfo.
     *
     * @param bool $dbpediaInfo
     *
     * @return Attractor
     */
    public function setDbpediaInfo($dbpediaInfo)
    {
        $this->dbpediaInfo = $dbpediaInfo;

        return $this;
    }

    /**
     * Get dbpediaInfo.
     *
     * @return bool
     */
    public function getDbpediaInfo()
    {
        return $this->dbpediaInfo;
    }

    /**
     * Set dbpediaAbstract.
     *
     * @param string $dbpediaAbstract
     *
     * @return Attractor
     */
    public function setDbpediaAbstract($dbpediaAbstract)
    {
        $this->dbpediaAbstract = $dbpediaAbstract;

        return $this;
    }

    /**
     * Get dbpediaAbstract.
     *
     * @return string
     */
    public function getDbpediaAbstract()
    {
        return $this->dbpediaAbstract;
    }

    /**
     * Set wikipediaLink.
     *
     * @param string $wikipediaLink
     *
     * @return Attractor
     */
    public function setWikipediaLink($wikipediaLink)
    {
        $this->wikipediaLink = $wikipediaLink;

        return $this;
    }

    /**
     * Get wikipediaLink.
     *
     * @return string
     */
    public function getWikipediaLink()
    {
        return $this->wikipediaLink;
    }

    /**
     * @return string
     */
    public function getLocatedIn()
    {
        return $this->locatedIn;
    }

    /**
     * @param string $locatedIn
     */
    public function setLocatedIn($locatedIn)
    {
        $this->locatedIn = $locatedIn;
    }

    /**
     * Add descrizioni.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Description $descrizioni
     *
     * @return Attractor
     */
    public function addDescrizioni(\Umbria\OpenApiBundle\Entity\Tourism\Description $descrizioni)
    {
        $this->descrizioni[] = $descrizioni;

        return $this;
    }

    /**
     * Remove descrizioni.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Description $descrizioni
     */
    public function removeDescrizioni(\Umbria\OpenApiBundle\Entity\Tourism\Description $descrizioni)
    {
        $this->descrizioni->removeElement($descrizioni);
    }

    /**
     * Get descrizioni.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDescrizioni()
    {
        return $this->descrizioni;
    }

    /**
     * Add tempiDiViaggio.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\TravelTime $tempiDiViaggio
     *
     * @return Attractor
     */
    public function addTempiDiViaggio(\Umbria\OpenApiBundle\Entity\Tourism\TravelTime $tempiDiViaggio)
    {
        $this->tempiDiViaggio[] = $tempiDiViaggio;

        return $this;
    }

    /**
     * Remove tempiDiViaggio.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\TravelTime $tempiDiViaggio
     */
    public function removeTempiDiViaggio(\Umbria\OpenApiBundle\Entity\Tourism\TravelTime $tempiDiViaggio)
    {
        $this->tempiDiViaggio->removeElement($tempiDiViaggio);
    }

    /**
     * Get tempiDiViaggio.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTempiDiViaggio()
    {
        return $this->tempiDiViaggio;
    }

    /**
     * Add coordinate.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Coordinate $coordinate
     *
     * @return Attractor
     */
    public function addCoordinate(\Umbria\OpenApiBundle\Entity\Tourism\Coordinate $coordinate)
    {
        $this->coordinate[] = $coordinate;

        return $this;
    }

    /**
     * Remove coordinate.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Coordinate $coordinate
     */
    public function removeCoordinate(\Umbria\OpenApiBundle\Entity\Tourism\Coordinate $coordinate)
    {
        $this->coordinate->removeElement($coordinate);
    }

    /**
     * Get coordinate.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCoordinate()
    {
        return $this->coordinate;
    }

    /**
     * Add categorie.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Category $categorie
     *
     * @return Attractor
     */
    public function addCategorie(\Umbria\OpenApiBundle\Entity\Tourism\Category $categorie)
    {
        $this->categorie[] = $categorie;

        return $this;
    }

    /**
     * Remove categorie.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Category $categorie
     */
    public function removeCategorie(\Umbria\OpenApiBundle\Entity\Tourism\Category $categorie)
    {
        $this->categorie->removeElement($categorie);
    }

    /**
     * Get categorie.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set rdf.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\RDF $rdf
     *
     * @return Attractor
     */
    public function setRdf(\Umbria\OpenApiBundle\Entity\Tourism\RDF $rdf = null)
    {
        $this->rdf = $rdf;

        return $this;
    }

    /**
     * Get rdf.
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\RDF
     */
    public function getRdf()
    {
        return $this->rdf;
    }
}
