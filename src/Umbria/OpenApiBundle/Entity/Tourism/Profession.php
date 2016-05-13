<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Profession.
 *
 * @ORM\Table(name="tourism_profession")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\ProfessionRepository")
 */
class Profession
{
    const TYPE = 'tourism_profession';

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
     * @ORM\Column(type="integer")
     *
     * @JMS\Type("integer")
     *
     * @JMS\Groups({"profession.*"})
     * @JMS\SerializedName("id")
     */
    private $idContenuto;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("firstName")
     *
     * @JMS\Groups({"profession.*"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("lastName")
     *
     * @JMS\Groups({"profession.*"})
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity="Address", mappedBy="professione", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Address>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"profession.*"})
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="Phone", mappedBy="professione", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Phone>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"profession.*"})
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="FaxNumber", mappedBy="professione", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\FaxNumber>")
     * @JMS\XmlList(entry="Description")
     * @JMS\SerializedName("fax_number")
     *
     * @JMS\Groups({"profession.*"})
     */
    private $faxNumber;

    /**
     * @ORM\OneToMany(targetEntity="Mbox", mappedBy="professione", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Mbox>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"profession.*"})
     */
    private $mbox;

    /**
     * @ORM\OneToMany(targetEntity="Homepage", mappedBy="professione", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Homepage>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"profession.*"})
     */
    private $homepage;

    /**
     * @ORM\OneToMany(targetEntity="Language", mappedBy="professione", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Language>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"profession.*"})
     */
    private $language;

    /**
     * @ORM\OneToMany(targetEntity="Specialization", mappedBy="professione", cascade={"merge", "remove"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Specialization>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"profession.*"})
     */
    private $specializzazione;

    /**
     * @ORM\ManyToOne(targetEntity="RDF", inversedBy="professioni")
     * @ORM\JoinColumn(name="rdf_id", referencedColumnName="id")
     */
    private $rdf;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->address = new ArrayCollection();
        $this->phone = new ArrayCollection();
        $this->faxNumber = new  ArrayCollection();
        $this->mbox = new  ArrayCollection();
        $this->homepage = new  ArrayCollection();
        $this->language = new  ArrayCollection();
        $this->specializzazione = new  ArrayCollection();
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
     * @return Profession
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
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return Profession
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return Profession
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Add address.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Address $address
     *
     * @return Profession
     */
    public function addAddress(Address $address)
    {
        $this->address[] = $address;

        return $this;
    }

    /**
     * Remove address.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Address $address
     */
    public function removeAddress(Address $address)
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
     * @return Profession
     */
    public function addPhone(Phone $phone)
    {
        $this->phone[] = $phone;

        return $this;
    }

    /**
     * Remove phone.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Phone $phone
     */
    public function removePhone(Phone $phone)
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
     * @return Profession
     */
    public function addFaxNumber(FaxNumber $faxNumber)
    {
        $this->faxNumber[] = $faxNumber;

        return $this;
    }

    /**
     * Remove faxNumber.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\FaxNumber $faxNumber
     */
    public function removeFaxNumber(FaxNumber $faxNumber)
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
     * Set rdf.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\RDF $rdf
     *
     * @return Profession
     */
    public function setRdf(RDF $rdf = null)
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
     * Add mbox.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Mbox $mbox
     *
     * @return Profession
     */
    public function addMbox(Mbox $mbox)
    {
        $this->mbox[] = $mbox;

        return $this;
    }

    /**
     * Remove mbox.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Mbox $mbox
     */
    public function removeMbox(Mbox $mbox)
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
     * @return Profession
     */
    public function addHomepage(Homepage $homepage)
    {
        $this->homepage[] = $homepage;

        return $this;
    }

    /**
     * Remove homepage.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Homepage $homepage
     */
    public function removeHomepage(Homepage $homepage)
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
     * Add language.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Language $language
     *
     * @return Profession
     */
    public function addLanguage(Language $language)
    {
        $this->language[] = $language;

        return $this;
    }

    /**
     * Remove language.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Language $language
     */
    public function removeLanguage(Language $language)
    {
        $this->language->removeElement($language);
    }

    /**
     * Get language.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Add specializzazione.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Specialization $specializzazione
     *
     * @return Profession
     */
    public function addSpecializzazione(Specialization $specializzazione)
    {
        $this->specializzazione[] = $specializzazione;

        return $this;
    }

    /**
     * Remove specializzazione.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Specialization $specializzazione
     */
    public function removeSpecializzazione(Specialization $specializzazione)
    {
        $this->specializzazione->removeElement($specializzazione);
    }

    /**
     * Get specializzazione.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSpecializzazione()
    {
        return $this->specializzazione;
    }
}
