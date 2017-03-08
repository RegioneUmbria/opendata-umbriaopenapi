<?php

namespace Umbria\OpenApiBundle\Entity\Tourism;

use Doctrine\ORM\Mapping as ORM;

/**
 * Setting.
 *
 * @ORM\Table(name="tourism_setting")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\Tourism\SettingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Setting
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
     */
    private $datasetName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_start_at", type="datetime", nullable=true)
     */
    private $updateStartAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;


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
     * Set datasetName.
     *
     * @param string $datasetName
     *
     * @return Setting
     */
    public function setDatasetName($datasetName)
    {
        $this->datasetName = $datasetName;

        return $this;
    }

    /**
     * Get datasetName.
     *
     * @return string
     */
    public function getDatasetName()
    {
        return $this->datasetName;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return Setting
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateStartAt()
    {
        return $this->updateStartAt;
    }

    /**
     * @param \DateTime $updateStartAt
     */
    public function setUpdateStartAt($updateStartAt)
    {
        $this->updateStartAt = $updateStartAt;
    }


}
