<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Event.
 *
 * @ORM\Table(name="tourism_event")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\EventRepository")
 */
class Event
{
    const TYPE = 'tourism_event';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @JMS\Groups({"event.*"})
     *
     * @JMS\SerializedName("id")
     */
    private $idElemento;

    /**
     * @ORM\Column(type="integer")
     *
     * @JMS\Type("integer")
     *
     * @JMS\SerializedName("id")
     *
     * @JMS\Groups({"event.*"})
     */
    private $idContenuto;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"event.*"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"event.*"})
     */
    private $homepage;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"event.*"})
     */
    private $eventDescription;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"event.*"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"event.*"})
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"event.*"})
     */
    private $titoloTesto;

    /**
     * @ORM\OneToMany(targetEntity="Description", mappedBy="evento", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Description>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"event.*"})
     */
    private $descrizioni;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="evento", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Category>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"event.*"})
     */
    private $categorie;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="evento", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Image>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"event.*"})
     */
    private $immagini;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"event.*"})
     */
    private $linkEsterniAssociati;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"event.*"})
     */
    private $immagineSpallaDestra;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"event.*"})
     */
    private $immagineCopertina;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"event.*"})
     */
    private $testoAlternativoImmagineCopertina;

    /**
     * @ORM\OneToMany(targetEntity="Coordinate", mappedBy="evento", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Coordinate>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"event.*"})
     */
    private $coordinate;

    /**
     * @ORM\ManyToOne(targetEntity="RDF", inversedBy="eventi")
     * @ORM\JoinColumn(name="rdf_id", referencedColumnName="id")
     */
    private $rdf;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->descrizioni = new ArrayCollection();
        $this->categorie = new ArrayCollection();
        $this->immagini = new ArrayCollection();
        $this->coordinate = new ArrayCollection();
    }

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
     * Set idContenuto.
     *
     * @param int $idContenuto
     *
     * @return Event
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
     * Set title.
     *
     * @param string $title
     *
     * @return Event
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set homepage.
     *
     * @param string $homepage
     *
     * @return Event
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;

        return $this;
    }

    /**
     * Get homepage.
     *
     * @return string
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Set eventDescription.
     *
     * @param string $eventDescription
     *
     * @return Event
     */
    public function setEventDescription($eventDescription)
    {
        $this->eventDescription = $eventDescription;

        return $this;
    }

    /**
     * Get eventDescription.
     *
     * @return string
     */
    public function getEventDescription()
    {
        return $this->eventDescription;
    }

    /**
     * Set startDate.
     *
     * @param string $startDate
     *
     * @return Event
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate.
     *
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate.
     *
     * @param string $endDate
     *
     * @return Event
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate.
     *
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set titoloTesto.
     *
     * @param string $titoloTesto
     *
     * @return Event
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
     * Set linkEsterniAssociati.
     *
     * @param string $linkEsterniAssociati
     *
     * @return Event
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
     * Set immagineSpallaDestra.
     *
     * @param string $immagineSpallaDestra
     *
     * @return Event
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
     * Set immagineCopertina.
     *
     * @param string $immagineCopertina
     *
     * @return Event
     */
    public function setImmagineCopertina($immagineCopertina)
    {
        $this->immagineCopertina = $immagineCopertina;

        return $this;
    }

    /**
     * Get immagineCopertina.
     *
     * @return string
     */
    public function getImmagineCopertina()
    {
        return $this->immagineCopertina;
    }

    /**
     * Set testoAlternativoImmagineCopertina.
     *
     * @param string $testoAlternativoImmagineCopertina
     *
     * @return Event
     */
    public function setTestoAlternativoImmagineCopertina($testoAlternativoImmagineCopertina)
    {
        $this->testoAlternativoImmagineCopertina = $testoAlternativoImmagineCopertina;

        return $this;
    }

    /**
     * Get testoAlternativoImmagineCopertina.
     *
     * @return string
     */
    public function getTestoAlternativoImmagineCopertina()
    {
        return $this->testoAlternativoImmagineCopertina;
    }

    /**
     * Add descrizioni.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Description $descrizioni
     *
     * @return Event
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
     * Add categorie.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Category $categorie
     *
     * @return Event
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
     * Add immagini.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Image $immagini
     *
     * @return Event
     */
    public function addImmagini(\Umbria\OpenApiBundle\Entity\Tourism\Image $immagini)
    {
        $this->immagini[] = $immagini;

        return $this;
    }

    /**
     * Remove immagini.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Image $immagini
     */
    public function removeImmagini(\Umbria\OpenApiBundle\Entity\Tourism\Image $immagini)
    {
        $this->immagini->removeElement($immagini);
    }

    /**
     * Get immagini.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImmagini()
    {
        return $this->immagini;
    }

    /**
     * Add coordinate.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Coordinate $coordinate
     *
     * @return Event
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
     * Set rdf.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\RDF $rdf
     *
     * @return Event
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
