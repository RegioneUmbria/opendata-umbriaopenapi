<?php
/**
 * Created by PhpStorm.
 * User: Lorenzo Franco Ranucci
 * Date: 29/09/2016
 * Time: 09:57
 */

namespace Umbria\OpenApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="Umbria\OpenApiBundle\Repository\AddressRepository")
 */
class Address
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Exclude()
     **/
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="street_address", type="string", length=255, nullable=true)
     */
    private $streetAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="address_locality", type="string", length=255, nullable=true)
     */
    private $addressLocality;

    /**
     * @var string
     *
     * @ORM\Column(name="address_region", type="string", length=255, nullable=true)
     */
    private $addressRegion;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code", type="string", length=20, nullable=true)
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="istat_code", type="string", length=20, nullable=true)
     */
    private $istat;

    /**
     * @var array
     *
     * @ORM\Column(name="types", type="array", nullable=true)
     */
    private $types;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * @param string $streetAddress
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;
    }

    /**
     * @return string
     */
    public function getAddressLocality()
    {
        return $this->addressLocality;
    }

    /**
     * @param string $addressLocality
     */
    public function setAddressLocality($addressLocality)
    {
        $this->addressLocality = $addressLocality;
    }

    /**
     * @return string
     */
    public function getAddressRegion()
    {
        return $this->addressRegion;
    }

    /**
     * @param string $addressRegion
     */
    public function setAddressRegion($addressRegion)
    {
        $this->addressRegion = $addressRegion;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getIstat()
    {
        return $this->istat;
    }

    /**
     * @param string $istat
     */
    public function setIstat($istat)
    {
        $this->istat = $istat;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param array $types
     */
    public function setTypes($types)
    {
        $this->types = $types;
    }


}