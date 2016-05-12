<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Homepage.
 *
 * @ORM\Table(name="tourism_homepage")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\HomepageRepository")
 */
class Homepage
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
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"homepage.*"})
     *
     * @JMS\SerializedName("url")
     */
    private $indirizzoInternet;

    /**
     * @ORM\ManyToOne(targetEntity="TravelAgency", inversedBy="homepage")
     * @ORM\JoinColumn(name="travel_agency_id", referencedColumnName="id")
     */
    private $agenziaViaggio;

    /**
     * @ORM\ManyToOne(targetEntity="Consortium", inversedBy="homepage")
     * @ORM\JoinColumn(name="consortium_id", referencedColumnName="id")
     */
    private $consorzio;

    /**
     * @ORM\ManyToOne(targetEntity="Profession", inversedBy="homepage")
     * @ORM\JoinColumn(name="professione_id", referencedColumnName="id")
     */
    private $professione;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set indirizzoInternet
     *
     * @param string $indirizzoInternet
     *
     * @return Homepage
     */
    public function setIndirizzoInternet($indirizzoInternet)
    {
        $this->indirizzoInternet = $indirizzoInternet;

        return $this;
    }

    /**
     * Get indirizzoInternet
     *
     * @return string
     */
    public function getIndirizzoInternet()
    {
        return $this->indirizzoInternet;
    }

    /**
     * Set agenziaViaggio
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\TravelAgency $agenziaViaggio
     *
     * @return Homepage
     */
    public function setAgenziaViaggio(\Umbria\OpenApiBundle\Entity\Tourism\TravelAgency $agenziaViaggio = null)
    {
        $this->agenziaViaggio = $agenziaViaggio;

        return $this;
    }

    /**
     * Get agenziaViaggio
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\TravelAgency
     */
    public function getAgenziaViaggio()
    {
        return $this->agenziaViaggio;
    }

    /**
     * Set consorzio
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Consortium $consorzio
     *
     * @return Homepage
     */
    public function setConsorzio(\Umbria\OpenApiBundle\Entity\Tourism\Consortium $consorzio = null)
    {
        $this->consorzio = $consorzio;

        return $this;
    }

    /**
     * Get consorzio
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\Consortium
     */
    public function getConsorzio()
    {
        return $this->consorzio;
    }

    /**
     * Set professione
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Profession $professione
     *
     * @return Homepage
     */
    public function setProfessione(\Umbria\OpenApiBundle\Entity\Tourism\Profession $professione = null)
    {
        $this->professione = $professione;

        return $this;
    }

    /**
     * Get professione
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\Profession
     */
    public function getProfessione()
    {
        return $this->professione;
    }
}
