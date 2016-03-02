<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Consortium.
 *
 * @ORM\Table(name="tourism_consortium")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\ConsortiumRepository")
 */
class Consortium
{
    const TYPE = 'tourism_consortium';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @JMS\Groups({"consortium.*"})
     */
    private $idElemento;

    /**
     * @ORM\Column(type="integer")
     *
     * @JMS\Type("integer")
     *
     * @JMS\Groups({"consortium.*"})
     */
    private $idContenuto;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"consortium.*"})
     */
    private $denominazione;

    /**
     * @ORM\OneToMany(targetEntity="Address", mappedBy="consorzio", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Address>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"consortium.*"})
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="Phone", mappedBy="consorzio", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Phone>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"consortium.*"})
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="FaxNumber", mappedBy="consorzio", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\FaxNumber>")
     * @JMS\XmlList(entry="Description")
     * @JMS\SerializedName("faxNumber")
     *
     * @JMS\Groups({"consortium.*"})
     */
    private $faxNumber;

    /**
     * @ORM\OneToMany(targetEntity="Mbox", mappedBy="consorzio", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Mbox>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"consortium.*"})
     */
    private $mbox;

    /**
     * @ORM\OneToMany(targetEntity="Homepage", mappedBy="consorzio", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Homepage>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"consortium.*"})
     */
    private $homepage;

    /**
     * @ORM\ManyToOne(targetEntity="RDF", inversedBy="consorzi")
     * @ORM\JoinColumn(name="rdf_id", referencedColumnName="id")
     */
    private $rdf;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->address = new \Doctrine\Common\Collections\ArrayCollection();
        $this->phone = new \Doctrine\Common\Collections\ArrayCollection();
        $this->faxNumber = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mbox = new \Doctrine\Common\Collections\ArrayCollection();
        $this->homepage = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Consortium
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
     * @return Consortium
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
     * @return Consortium
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
     * Add address.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Address $address
     *
     * @return Consortium
     */
    public function addAddress(\Umbria\OpenApiBundle\Entity\Tourism\Address $address)
    {
        $this->address[] = $address;

        return $this;
    }

    /**
     * Remove address.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Address $address
     */
    public function removeAddress(\Umbria\OpenApiBundle\Entity\Tourism\Address $address)
    {
        $this->address->removeElement($address);
    }

    /**
     * Get address.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Add phone.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Phone $phone
     *
     * @return Consortium
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
     * @return Consortium
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
     * @return Consortium
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
     * Add homepage.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Homepage $homepage
     *
     * @return Consortium
     */
    public function addHomepage(\Umbria\OpenApiBundle\Entity\Tourism\Homepage $homepage)
    {
        $this->homepage[] = $homepage;

        return $this;
    }

    /**
     * Remove homepage.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Homepage $homepage
     */
    public function removeHomepage(\Umbria\OpenApiBundle\Entity\Tourism\Homepage $homepage)
    {
        $this->homepage->removeElement($homepage);
    }

    /**
     * Get homepage.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Set rdf.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\RDF $rdf
     *
     * @return Consortium
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
