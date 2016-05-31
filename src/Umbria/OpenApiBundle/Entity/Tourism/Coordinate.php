<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Coordinate.
 *
 * @ORM\Table(name="tourism_coordinate")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\CoordinateRepository")
 */
class Coordinate
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
     * @ORM\Column(type="float", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"coordinate.*"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="float", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"coordinate.*"})
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"coordinate.*"})
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"coordinate.*"})
     */
    private $postalCode;

    /********** Campi aggiuntivi **/
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Groups({"coordinate.*"})
     */
    private $googleLatitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Groups({"coordinate.*"})
     */
    private $googleLongitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Groups({"coordinate.*"})
     */
    private $dbpediaLatitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Groups({"coordinate.*"})
     */
    private $dbpediaLongitude;

    /********** Association **/
    /**
     * @ORM\ManyToOne(targetEntity="Proposal", inversedBy="coordinate")
     * @ORM\JoinColumn(name="proposal_id", referencedColumnName="id")
     */
    private $proposta;

    /**
     * @ORM\ManyToOne(targetEntity="Attractor", inversedBy="coordinate")
     * @ORM\JoinColumn(name="attractor_id", referencedColumnName="id")
     */
    private $attrattore;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="coordinate")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    private $evento;

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
     * Set latitude.
     *
     * @param string $latitude
     *
     * @return Coordinate
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
     * @return Coordinate
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
     * Set address.
     *
     * @param string $address
     *
     * @return Coordinate
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set postalCode.
     *
     * @param string $postalCode
     *
     * @return Coordinate
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
     * Set googleLatitude.
     *
     * @param string $googleLatitude
     *
     * @return Coordinate
     */
    public function setGoogleLatitude($googleLatitude)
    {
        $this->googleLatitude = $googleLatitude;

        return $this;
    }

    /**
     * Get googleLatitude.
     *
     * @return string
     */
    public function getGoogleLatitude()
    {
        return $this->googleLatitude;
    }

    /**
     * Set googleLongitude.
     *
     * @param string $googleLongitude
     *
     * @return Coordinate
     */
    public function setGoogleLongitude($googleLongitude)
    {
        $this->googleLongitude = $googleLongitude;

        return $this;
    }

    /**
     * Get googleLongitude.
     *
     * @return string
     */
    public function getGoogleLongitude()
    {
        return $this->googleLongitude;
    }

    /**
     * Set dbpediaLatitude.
     *
     * @param string $dbpediaLatitude
     *
     * @return Coordinate
     */
    public function setDbpediaLatitude($dbpediaLatitude)
    {
        $this->dbpediaLatitude = $dbpediaLatitude;

        return $this;
    }

    /**
     * Get dbpediaLatitude.
     *
     * @return string
     */
    public function getDbpediaLatitude()
    {
        return $this->dbpediaLatitude;
    }

    /**
     * Set dbpediaLongitude.
     *
     * @param string $dbpediaLongitude
     *
     * @return Coordinate
     */
    public function setDbpediaLongitude($dbpediaLongitude)
    {
        $this->dbpediaLongitude = $dbpediaLongitude;

        return $this;
    }

    /**
     * Get dbpediaLongitude.
     *
     * @return string
     */
    public function getDbpediaLongitude()
    {
        return $this->dbpediaLongitude;
    }

    /**
     * Set proposta.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Proposal $proposta
     *
     * @return Coordinate
     */
    public function setProposta(Proposal $proposta = null)
    {
        $this->proposta = $proposta;

        return $this;
    }

    /**
     * Get proposta.
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\Proposal
     */
    public function getProposta()
    {
        return $this->proposta;
    }

    /**
     * Set attrattore.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Attractor $attrattore
     *
     * @return Coordinate
     */
    public function setAttrattore(Attractor $attrattore = null)
    {
        $this->attrattore = $attrattore;

        return $this;
    }

    /**
     * Get attrattore.
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\Attractor
     */
    public function getAttrattore()
    {
        return $this->attrattore;
    }

    /**
     * Set evento.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Event $evento
     *
     * @return Coordinate
     */
    public function setEvento(Event $evento = null)
    {
        $this->evento = $evento;

        return $this;
    }

    /**
     * Get evento.
     *
     * @return \Umbria\OpenApiBundle\Entity\Tourism\Event
     */
    public function getEvento()
    {
        return $this->evento;
    }
}
