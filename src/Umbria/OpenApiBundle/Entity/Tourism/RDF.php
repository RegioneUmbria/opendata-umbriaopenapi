<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * RDF.
 *
 * @ORM\Table(name="tourism_rdf")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\RDFRepository")
 */
class RDF
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Proposal", mappedBy="rdf", cascade={"merge"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Proposal>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"rdf.*"})
     */
    private $proposte;

    /**
     * @ORM\OneToMany(targetEntity="Attractor", mappedBy="rdf", cascade={"merge"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Attractor>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"rdf.*"})
     */
    private $attrattori;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="rdf", cascade={"merge"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Event>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"rdf.*"})
     */
    private $eventi;

    /**
     * @ORM\OneToMany(targetEntity="TravelAgency", mappedBy="rdf", cascade={"merge"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\TravelAgency>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"rdf.*"})
     */
    private $agenzieViaggio;

    /**
     * @ORM\OneToMany(targetEntity="Consortium", mappedBy="rdf", cascade={"merge"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Consortium>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"rdf.*"})
     */
    private $consorzi;

    /**
     * @ORM\OneToMany(targetEntity="Profession", mappedBy="rdf", cascade={"merge"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Profession>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"rdf.*"})
     */
    private $professioni;

    /**
     * @ORM\OneToMany(targetEntity="Iat", mappedBy="rdf", cascade={"merge"})
     *
     * @JMS\Type("ArrayCollection<Umbria\OpenApiBundle\Entity\Tourism\Iat>")
     * @JMS\XmlList(entry="Description")
     *
     * @JMS\Groups({"rdf.*"})
     */
    private $iat;

    /**
     * Proposal constructor.
     */
    public function __construct()
    {
        $this->proposte = new ArrayCollection();
        $this->attrattori = new ArrayCollection();
        $this->eventi = new ArrayCollection();
        $this->agenzieViaggio = new ArrayCollection();
        $this->consorzi = new ArrayCollection();
        $this->professioni = new ArrayCollection();
        $this->iat = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add proposte.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Proposal $proposte
     *
     * @return RDF
     */
    public function addProposte(\Umbria\OpenApiBundle\Entity\Tourism\Proposal $proposte)
    {
        $this->proposte[] = $proposte;

        return $this;
    }

    /**
     * Remove proposte.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Proposal $proposte
     */
    public function removeProposte(\Umbria\OpenApiBundle\Entity\Tourism\Proposal $proposte)
    {
        $this->proposte->removeElement($proposte);
    }

    /**
     * Get proposte.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProposte()
    {
        return $this->proposte;
    }

    /**
     * Add attrattori.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Attractor $attrattori
     *
     * @return RDF
     */
    public function addAttrattori(\Umbria\OpenApiBundle\Entity\Tourism\Attractor $attrattori)
    {
        $this->attrattori[] = $attrattori;

        return $this;
    }

    /**
     * Remove attrattori.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Attractor $attrattori
     */
    public function removeAttrattori(\Umbria\OpenApiBundle\Entity\Tourism\Attractor $attrattori)
    {
        $this->attrattori->removeElement($attrattori);
    }

    /**
     * Get attrattori.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttrattori()
    {
        return $this->attrattori;
    }

    /**
     * Add eventi.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Event $eventi
     *
     * @return RDF
     */
    public function addEventi(\Umbria\OpenApiBundle\Entity\Tourism\Event $eventi)
    {
        $this->eventi[] = $eventi;

        return $this;
    }

    /**
     * Remove eventi.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Event $eventi
     */
    public function removeEventi(\Umbria\OpenApiBundle\Entity\Tourism\Event $eventi)
    {
        $this->eventi->removeElement($eventi);
    }

    /**
     * Get eventi.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEventi()
    {
        return $this->eventi;
    }

    /**
     * Add agenzieViaggio.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\TravelAgency $agenzieViaggio
     *
     * @return RDF
     */
    public function addAgenzieViaggio(\Umbria\OpenApiBundle\Entity\Tourism\TravelAgency $agenzieViaggio)
    {
        $this->agenzieViaggio[] = $agenzieViaggio;

        return $this;
    }

    /**
     * Remove agenzieViaggio.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\TravelAgency $agenzieViaggio
     */
    public function removeAgenzieViaggio(\Umbria\OpenApiBundle\Entity\Tourism\TravelAgency $agenzieViaggio)
    {
        $this->agenzieViaggio->removeElement($agenzieViaggio);
    }

    /**
     * Get agenzieViaggio.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAgenzieViaggio()
    {
        return $this->agenzieViaggio;
    }

    /**
     * Add consorzi.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Consortium $consorzi
     *
     * @return RDF
     */
    public function addConsorzi(\Umbria\OpenApiBundle\Entity\Tourism\Consortium $consorzi)
    {
        $this->consorzi[] = $consorzi;

        return $this;
    }

    /**
     * Remove consorzi.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Consortium $consorzi
     */
    public function removeConsorzi(\Umbria\OpenApiBundle\Entity\Tourism\Consortium $consorzi)
    {
        $this->consorzi->removeElement($consorzi);
    }

    /**
     * Get consorzi.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConsorzi()
    {
        return $this->consorzi;
    }

    /**
     * Add professioni.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Profession $professioni
     *
     * @return RDF
     */
    public function addProfessioni(\Umbria\OpenApiBundle\Entity\Tourism\Profession $professioni)
    {
        $this->professioni[] = $professioni;

        return $this;
    }

    /**
     * Remove professioni.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Profession $professioni
     */
    public function removeProfessioni(\Umbria\OpenApiBundle\Entity\Tourism\Profession $professioni)
    {
        $this->professioni->removeElement($professioni);
    }

    /**
     * Get professioni.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProfessioni()
    {
        return $this->professioni;
    }

    /**
     * Add iat.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Iat $iat
     *
     * @return RDF
     */
    public function addIat(\Umbria\OpenApiBundle\Entity\Tourism\Iat $iat)
    {
        $this->iat[] = $iat;

        return $this;
    }

    /**
     * Remove iat.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Iat $iat
     */
    public function removeIat(\Umbria\OpenApiBundle\Entity\Tourism\Iat $iat)
    {
        $this->iat->removeElement($iat);
    }

    /**
     * Get iat.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIat()
    {
        return $this->iat;
    }
}
