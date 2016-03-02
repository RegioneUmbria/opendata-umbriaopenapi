<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Category.
 *
 * @ORM\Table(name="tourism_category")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\CategoryRepository")
 */
class Category
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
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"category.*"})
     */
    private $cat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"category.*"})
     */
    private $categoriaAssociata;

    /**
     * @ORM\ManyToOne(targetEntity="Proposal", inversedBy="categorie")
     * @ORM\JoinColumn(name="proposal_id", referencedColumnName="id")
     */
    private $proposta;

    /**
     * @ORM\ManyToOne(targetEntity="Attractor", inversedBy="categorie")
     * @ORM\JoinColumn(name="attractor_id", referencedColumnName="id")
     */
    private $attrattore;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="categorie")
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
     * Set cat.
     *
     * @param string $cat
     *
     * @return Category
     */
    public function setCat($cat)
    {
        $this->cat = $cat;

        return $this;
    }

    /**
     * Get cat.
     *
     * @return string
     */
    public function getCat()
    {
        return $this->cat;
    }

    /**
     * Set categoriaAssociata.
     *
     * @param string $categoriaAssociata
     *
     * @return Category
     */
    public function setCategoriaAssociata($categoriaAssociata)
    {
        $this->categoriaAssociata = $categoriaAssociata;

        return $this;
    }

    /**
     * Get categoriaAssociata.
     *
     * @return string
     */
    public function getCategoriaAssociata()
    {
        return $this->categoriaAssociata;
    }

    /**
     * Set proposta.
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Proposal $proposta
     *
     * @return Category
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
     * @return Category
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
     * @return Category
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
