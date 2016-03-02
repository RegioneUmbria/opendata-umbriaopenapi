<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Download.
 *
 * @ORM\Table(name="tourism_download")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\DownloadRepository")
 */
class Download
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
     * @JMS\Type("string")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Type("string")
     *
     * @JMS\Groups({"download.*"})
     */
    private $icona;

    /**
     * @ORM\ManyToOne(targetEntity="Proposal", inversedBy="download")
     * @ORM\JoinColumn(name="proposal_id", referencedColumnName="id")
     */
    private $proposta;
}
