<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Description.
 *
 * @ORM\Table(name="tourism_description")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\DescriptionRepository")
 */
class Description
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Groups({"description.*"})
     */
    private $id;

    /**
     * @JMS\Type("string")
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"description.*"})
     */
    private $titolo;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"description.*"})
     */
    private $testo;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"description.*"})
     */
    private $corpo;

    /**
     * @ORM\ManyToOne(targetEntity="Proposal", inversedBy="descrizioni")
     * @ORM\JoinColumn(name="proposal_id", referencedColumnName="id")
     */
    private $proposta;

    /**
     * @ORM\ManyToOne(targetEntity="Attractor", inversedBy="descrizioni")
     * @ORM\JoinColumn(name="attractor_id", referencedColumnName="id")
     */
    private $attrattore;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="descrizioni")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    private $evento;

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
     * Set titolo.
     *
     * @param string $titolo
     *
     * @return Description
     */
    public function setTitolo($titolo)
    {
        $this->titolo = $titolo;

        return $this;
    }

    /**
     * Get titolo.
     *
     * @return string
     */
    public function getTitolo()
    {
        return $this->titolo;
    }

    /**
     * Set testo.
     *
     * @param string $testo
     *
     * @return Description
     */
    public function setTesto($testo)
    {
        $this->testo = $testo;

        return $this;
    }

    /**
     * Get testo.
     *
     * @return string
     */
    public function getTesto()
    {
        return $this->testo;
    }

    /**
     * Set corpo.
     *
     * @param string $corpo
     *
     * @return Description
     */
    public function setCorpo($corpo)
    {
        $this->corpo = $corpo;

        return $this;
    }

    /**
     * Get corpo.
     *
     * @return string
     */
    public function getCorpo()
    {
        return $this->corpo;
    }

    /**
     * Set proposta.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Proposal $proposta
     *
     * @return Description
     */
    public function setProposta(\Umbria\OpenApiBundle\Entity\Tourism\Proposal $proposta = null)
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
     * @return Description
     */
    public function setAttrattore(\Umbria\OpenApiBundle\Entity\Tourism\Attractor $attrattore = null)
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
     * @return Description
     */
    public function setEvento(\Umbria\OpenApiBundle\Entity\Tourism\Event $evento = null)
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
