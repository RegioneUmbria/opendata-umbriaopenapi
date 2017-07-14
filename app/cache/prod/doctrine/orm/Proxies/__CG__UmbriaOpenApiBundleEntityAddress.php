<?php

namespace Proxies\__CG__\Umbria\OpenApiBundle\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Address extends \Umbria\OpenApiBundle\Entity\Address implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = [];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'id', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'streetAddress', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'addressLocality', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'addressRegion', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'postalCode', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'istat', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'lat', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'lng', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'types'];
        }

        return ['__isInitialized__', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'id', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'streetAddress', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'addressLocality', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'addressRegion', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'postalCode', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'istat', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'lat', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'lng', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Address' . "\0" . 'types'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Address $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getStreetAddress()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStreetAddress', []);

        return parent::getStreetAddress();
    }

    /**
     * {@inheritDoc}
     */
    public function setStreetAddress($streetAddress)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStreetAddress', [$streetAddress]);

        return parent::setStreetAddress($streetAddress);
    }

    /**
     * {@inheritDoc}
     */
    public function getAddressLocality()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAddressLocality', []);

        return parent::getAddressLocality();
    }

    /**
     * {@inheritDoc}
     */
    public function setAddressLocality($addressLocality)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAddressLocality', [$addressLocality]);

        return parent::setAddressLocality($addressLocality);
    }

    /**
     * {@inheritDoc}
     */
    public function getAddressRegion()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAddressRegion', []);

        return parent::getAddressRegion();
    }

    /**
     * {@inheritDoc}
     */
    public function setAddressRegion($addressRegion)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAddressRegion', [$addressRegion]);

        return parent::setAddressRegion($addressRegion);
    }

    /**
     * {@inheritDoc}
     */
    public function getPostalCode()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPostalCode', []);

        return parent::getPostalCode();
    }

    /**
     * {@inheritDoc}
     */
    public function setPostalCode($postalCode)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPostalCode', [$postalCode]);

        return parent::setPostalCode($postalCode);
    }

    /**
     * {@inheritDoc}
     */
    public function getIstat()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIstat', []);

        return parent::getIstat();
    }

    /**
     * {@inheritDoc}
     */
    public function setIstat($istat)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIstat', [$istat]);

        return parent::setIstat($istat);
    }

    /**
     * {@inheritDoc}
     */
    public function setLat($lat)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLat', [$lat]);

        return parent::setLat($lat);
    }

    /**
     * {@inheritDoc}
     */
    public function getLat()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLat', []);

        return parent::getLat();
    }

    /**
     * {@inheritDoc}
     */
    public function setLng($lng)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLng', [$lng]);

        return parent::setLng($lng);
    }

    /**
     * {@inheritDoc}
     */
    public function getLng()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLng', []);

        return parent::getLng();
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTypes', []);

        return parent::getTypes();
    }

    /**
     * {@inheritDoc}
     */
    public function setTypes($types)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTypes', [$types]);

        return parent::setTypes($types);
    }

}
