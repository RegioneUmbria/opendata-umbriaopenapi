<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Specialization.
 *
 * @ORM\Table(name="tourism_specialization")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\SpecializationRepository")
 */
class Specialization
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
     * @JMS\Groups({"specialization.*"})
     */
    private $spec;

    /**
     * @ORM\ManyToOne(targetEntity="Profession", inversedBy="specializzazione")
     * @ORM\JoinColumn(name="profession_id", referencedColumnName="id")
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
     * Set spec
     *
     * @param string $spec
     *
     * @return Specialization
     */
    public function setSpec($spec)
    {
        $this->spec = $spec;

        return $this;
    }

    /**
     * Get spec
     *
     * @return string
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * Set professione
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Profession $professione
     *
     * @return Specialization
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
