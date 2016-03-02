<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Info.
 *
 * @ORM\Table(name="tourism_info")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\InfoRepository")
 */
class Info
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
     * @JMS\Groups({"info.*"})
     */
    private $titolo;

    /**
     * @ORM\Column(type="text")
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"info.*"})
     */
    private $testo;

    /**
     * @ORM\ManyToOne(targetEntity="Proposal", inversedBy="informazioni")
     * @ORM\JoinColumn(name="proposal_id", referencedColumnName="id")
     */
    private $proposta;
}
