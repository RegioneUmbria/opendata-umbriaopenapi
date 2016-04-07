<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Language.
 *
 * @ORM\Table(name="tourism_language")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\LanguageRepository")
 */
class Language
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
     * @JMS\Groups({"language.*"})
     */
    private $linguaParlata;

    /**
     * @ORM\ManyToOne(targetEntity="Profession", inversedBy="language")
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
     * Set linguaParlata
     *
     * @param string $linguaParlata
     *
     * @return Language
     */
    public function setLinguaParlata($linguaParlata)
    {
        $this->linguaParlata = $linguaParlata;

        return $this;
    }

    /**
     * Get linguaParlata
     *
     * @return string
     */
    public function getLinguaParlata()
    {
        return $this->linguaParlata;
    }

    /**
     * Set professione
     *
     * @param \Umbria\OpenApiBundle\Entity\Tourism\Profession $professione
     *
     * @return Language
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
