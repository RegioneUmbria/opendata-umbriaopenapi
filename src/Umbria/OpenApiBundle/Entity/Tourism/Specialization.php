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
}
