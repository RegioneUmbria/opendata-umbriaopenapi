<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Iat.
 *
 * @ORM\Table(name="tourism_iat")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\IatRepository")
 */
class Iat
{
    const TYPE = 'tourism_iat';

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
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"iat.*"})
     * @JMS\SerializedName("label")
     */
    private $denominazione;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"iat.*"})
     */
    private $listaComuni;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("streetAddress")
     *
     * @JMS\Groups({"iat.*"})
     */
    private $streetAddress;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("postalCode")
     *
     * @JMS\Groups({"iat.*"})
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("addressLocality")
     *
     * @JMS\Groups({"iat.*"})
     */
    private $addressLocality;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"iat.*"})
     * @JMS\SerializedName("istat")
     */
    private $codiceIstatComune;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("addressRegion")
     *
     * @JMS\Groups({"iat.*"})
     */
    private $addressRegion;

    /**
     * @ORM\OneToMany(targetEntity="Phone", mappedBy="iat", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Phone>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"iat.*"})
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="FaxNumber", mappedBy="iat", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\FaxNumber>")
     * @JMS\XmlList(entry="Description")
     * @JMS\SerializedName("faxNumber")
     *
     * @JMS\Groups({"iat.*"})
     */
    private $faxNumber;

    /**
     * @ORM\OneToMany(targetEntity="Mbox", mappedBy="iat", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Mbox>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"iat.*"})
     */
    private $mbox;

    /**
     * @ORM\ManyToOne(targetEntity="RDF", inversedBy="iat")
     * @ORM\JoinColumn(name="rdf_id", referencedColumnName="id")
     */
    private $rdf;

    /********** Campi aggiuntivi **/
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"iat.*"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"iat.*"})
     */
    private $longitude;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->phone = new \Doctrine\Common\Collections\ArrayCollection();
        $this->faxNumber = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mbox = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set denominazione.
     *
     * @param string $denominazione
     *
     * @return Iat
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
     * Set listaComuni.
     *
     * @param string $listaComuni
     *
     * @return Iat
     */
    public function setListaComuni($listaComuni)
    {
        $this->listaComuni = $listaComuni;

        return $this;
    }

    /**
     * Get listaComuni.
     *
     * @return string
     */
    public function getListaComuni()
    {
        return $this->listaComuni;
    }

    /**
     * Set streetAddress.
     *
     * @param string $streetAddress
     *
     * @return Iat
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    /**
     * Get streetAddress.
     *
     * @return string
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * Set postalCode.
     *
     * @param string $postalCode
     *
     * @return Iat
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode.
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set addressLocality.
     *
     * @param string $addressLocality
     *
     * @return Iat
     */
    public function setAddressLocality($addressLocality)
    {
        $this->addressLocality = $addressLocality;

        return $this;
    }

    /**
     * Get addressLocality.
     *
     * @return string
     */
    public function getAddressLocality()
    {
        return $this->addressLocality;
    }

    /**
     * Set codiceIstatComune.
     *
     * @param string $codiceIstatComune
     *
     * @return Iat
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
     * Set addressRegion.
     *
     * @param string $addressRegion
     *
     * @return Iat
     */
    public function setAddressRegion($addressRegion)
    {
        $this->addressRegion = $addressRegion;

        return $this;
    }

    /**
     * Get addressRegion.
     *
     * @return string
     */
    public function getAddressRegion()
    {
        return $this->addressRegion;
    }

    /**
     * Set latitude.
     *
     * @param string $latitude
     *
     * @return Iat
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude.
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude.
     *
     * @param string $longitude
     *
     * @return Iat
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude.
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Add phone.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Phone $phone
     *
     * @return Iat
     */
    public function addPhone(\Umbria\OpenApiBundle\Entity\Tourism\Phone $phone)
    {
        $this->phone[] = $phone;

        return $this;
    }

    /**
     * Remove phone.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Phone $phone
     */
    public function removePhone(\Umbria\OpenApiBundle\Entity\Tourism\Phone $phone)
    {
        $this->phone->removeElement($phone);
    }

    /**
     * Get phone.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Add faxNumber.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\FaxNumber $faxNumber
     *
     * @return Iat
     */
    public function addFaxNumber(\Umbria\OpenApiBundle\Entity\Tourism\FaxNumber $faxNumber)
    {
        $this->faxNumber[] = $faxNumber;

        return $this;
    }

    /**
     * Remove faxNumber.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\FaxNumber $faxNumber
     */
    public function removeFaxNumber(\Umbria\OpenApiBundle\Entity\Tourism\FaxNumber $faxNumber)
    {
        $this->faxNumber->removeElement($faxNumber);
    }

    /**
     * Get faxNumber.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFaxNumber()
    {
        return $this->faxNumber;
    }

    /**
     * Add mbox.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Mbox $mbox
     *
     * @return Iat
     */
    public function addMbox(\Umbria\OpenApiBundle\Entity\Tourism\Mbox $mbox)
    {
        $this->mbox[] = $mbox;

        return $this;
    }

    /**
     * Remove mbox.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Mbox $mbox
     */
    public function removeMbox(\Umbria\OpenApiBundle\Entity\Tourism\Mbox $mbox)
    {
        $this->mbox->removeElement($mbox);
    }

    /**
     * Get mbox.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMbox()
    {
        return $this->mbox;
    }

    /**
     * Set rdf.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\RDF $rdf
     *
     * @return Iat
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
