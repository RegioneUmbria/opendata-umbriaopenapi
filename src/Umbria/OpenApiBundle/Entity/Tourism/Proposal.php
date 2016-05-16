<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Proposal.
 *
 * @ORM\Table(name="tourism_proposal")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\ProposalRepository")
 */
class Proposal
{
    const TYPE = 'tourism_proposal';

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
     * @JMS\Groups({"proposal.*"})
     *
     * @JMS\SerializedName("id")
     */
    private $idElemento;

    /**
     * @ORM\Column(type="integer")
     *
     * @JMS\Type("integer")
     *
     * @JMS\Groups({"proposal.*"})
     *
     * @JMS\SerializedName("id")
     */
    private $idContenuto;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"proposal.*"})
     *
     * @JMS\SerializedName("label")
     */
    private $nomeProposta;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $urlRisorsa;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\SerializedName("subject")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $keywords;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $descrizioneSintetica;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $titoloTesto;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"proposal.*"})
     *
     * @JMS\SerializedName("comment")
     */
    private $abstract;

    /**
     * @ORM\ManyToOne(targetEntity="RDF", inversedBy="proposte")
     * @ORM\JoinColumn(name="rdf_id", referencedColumnName="id")
     */
    private $rdf;

    /**
     * @ORM\OneToMany(targetEntity="Description", mappedBy="proposta", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Description>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $descrizioni;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $immagini;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="proposta", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Category>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $categorie;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $linkEsterniAssociati;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $luogoDa;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $luogoA;

    /**
     * @ORM\OneToMany(targetEntity="Info", mappedBy="proposta", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Info>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $informazioni;

    /**
     * @ORM\OneToMany(targetEntity="TravelTime", mappedBy="proposta", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\TravelTime>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $tempiDiViaggio;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $immagineSpallaDestra;

    /**
     * @ORM\OneToMany(targetEntity="Coordinate", mappedBy="proposta", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Coordinate>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $coordinate;

    /**
     * @ORM\OneToMany(targetEntity="Download", mappedBy="proposta", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Download>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"proposal.*"})
     */
    private $download;

    /**
     * Proposal constructor.
     */
    public function __construct()
    {
        $this->descrizioni = new ArrayCollection();
        $this->categorie = new ArrayCollection();
        $this->informazioni = new ArrayCollection();
        $this->tempiDiViaggio = new ArrayCollection();
        $this->download = new ArrayCollection();
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
     * Set idElemento.
     *
     * @param string $idElemento
     *
     * @return Proposal
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
     * @return Proposal
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
     * Set nomeProposta.
     *
     * @param string $nomeProposta
     *
     * @return Proposal
     */
    public function setNomeProposta($nomeProposta)
    {
        $this->nomeProposta = $nomeProposta;

        return $this;
    }

    /**
     * Get nomeProposta.
     *
     * @return string
     */
    public function getNomeProposta()
    {
        return $this->nomeProposta;
    }

    /**
     * Set urlRisorsa.
     *
     * @param string $urlRisorsa
     *
     * @return Proposal
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
     * Set keywords.
     *
     * @param string $keywords
     *
     * @return Proposal
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
     * Set descrizioneSintetica.
     *
     * @param string $descrizioneSintetica
     *
     * @return Proposal
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
     * Set titoloTesto.
     *
     * @param string $titoloTesto
     *
     * @return Proposal
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
     * @return Proposal
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
     * @return Proposal
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
     * @return Proposal
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
     * Set luogoDa.
     *
     * @param string $luogoDa
     *
     * @return Proposal
     */
    public function setLuogoDa($luogoDa)
    {
        $this->luogoDa = $luogoDa;

        return $this;
    }

    /**
     * Get luogoDa.
     *
     * @return string
     */
    public function getLuogoDa()
    {
        return $this->luogoDa;
    }

    /**
     * Set luogoA.
     *
     * @param string $luogoA
     *
     * @return Proposal
     */
    public function setLuogoA($luogoA)
    {
        $this->luogoA = $luogoA;

        return $this;
    }

    /**
     * Get luogoA.
     *
     * @return string
     */
    public function getLuogoA()
    {
        return $this->luogoA;
    }

    /**
     * Set immagineSpallaDestra.
     *
     * @param string $immagineSpallaDestra
     *
     * @return Proposal
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
     * Set rdf.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\RDF $rdf
     *
     * @return Proposal
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

    /**
     * Add descrizioni.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Description $descrizioni
     *
     * @return Proposal
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
     * @return Proposal
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
     * Add informazioni.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Info $informazioni
     *
     * @return Proposal
     */
    public function addInformazioni(\Umbria\OpenApiBundle\Entity\Tourism\Info $informazioni)
    {
        $this->informazioni[] = $informazioni;

        return $this;
    }

    /**
     * Remove informazioni.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Info $informazioni
     */
    public function removeInformazioni(\Umbria\OpenApiBundle\Entity\Tourism\Info $informazioni)
    {
        $this->informazioni->removeElement($informazioni);
    }

    /**
     * Get informazioni.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInformazioni()
    {
        return $this->informazioni;
    }

    /**
     * Add tempiDiViaggio.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\TravelTime $tempiDiViaggio
     *
     * @return Proposal
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
     * @return Proposal
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
     * Add download.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Download $download
     *
     * @return Proposal
     */
    public function addDownload(\Umbria\OpenApiBundle\Entity\Tourism\Download $download)
    {
        $this->download[] = $download;

        return $this;
    }

    /**
     * Remove download.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Download $download
     */
    public function removeDownload(\Umbria\OpenApiBundle\Entity\Tourism\Download $download)
    {
        $this->download->removeElement($download);
    }

    /**
     * Get download.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDownload()
    {
        return $this->download;
    }
}
