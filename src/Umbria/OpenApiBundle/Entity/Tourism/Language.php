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
}
