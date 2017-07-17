<?php

namespace Proxies\__CG__\Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class TravelAgency extends \Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\TravelAgency implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'uri', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'name', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'email', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'telephone', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'resourceOriginUrl', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'fax', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'types', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'language', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'address', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'lat', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'lng', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'lastUpdateAt'];
        }

        return ['__isInitialized__', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'uri', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'name', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'email', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'telephone', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'resourceOriginUrl', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'fax', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'types', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'language', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'address', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'lat', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'lng', '' . "\0" . 'Umbria\\OpenApiBundle\\Entity\\Tourism\\GraphsEntities\\TravelAgency' . "\0" . 'lastUpdateAt'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (TravelAgency $proxy) {
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
    public function setUri($uri)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUri', [$uri]);

        return parent::setUri($uri);
    }

    /**
     * {@inheritDoc}
     */
    public function getUri()
    {
        if ($this->__isInitialized__ === false) {
            return  parent::getUri();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUri', []);

        return parent::getUri();
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', [$name]);

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', []);

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function setEmail($email)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEmail', [$email]);

        return parent::setEmail($email);
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEmail', []);

        return parent::getEmail();
    }

    /**
     * {@inheritDoc}
     */
    public function setTelephone($telephone)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTelephone', [$telephone]);

        return parent::setTelephone($telephone);
    }

    /**
     * {@inheritDoc}
     */
    public function getTelephone()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTelephone', []);

        return parent::getTelephone();
    }

    /**
     * {@inheritDoc}
     */
    public function setResourceOriginUrl($resourceOriginUrl)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setResourceOriginUrl', [$resourceOriginUrl]);

        return parent::setResourceOriginUrl($resourceOriginUrl);
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceOriginUrl()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getResourceOriginUrl', []);

        return parent::getResourceOriginUrl();
    }

    /**
     * {@inheritDoc}
     */
    public function setFax($fax)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFax', [$fax]);

        return parent::setFax($fax);
    }

    /**
     * {@inheritDoc}
     */
    public function getFax()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFax', []);

        return parent::getFax();
    }

    /**
     * {@inheritDoc}
     */
    public function setTypes($types)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTypes', [$types]);

        return parent::setTypes($types);
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
    public function setLanguage($language)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLanguage', [$language]);

        return parent::setLanguage($language);
    }

    /**
     * {@inheritDoc}
     */
    public function getLanguage()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLanguage', []);

        return parent::getLanguage();
    }

    /**
     * {@inheritDoc}
     */
    public function setAddress($address)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAddress', [$address]);

        return parent::setAddress($address);
    }

    /**
     * {@inheritDoc}
     */
    public function getAddress()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAddress', []);

        return parent::getAddress();
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
    public function setLastUpdateAt($lastUpdateAt)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLastUpdateAt', [$lastUpdateAt]);

        return parent::setLastUpdateAt($lastUpdateAt);
    }

    /**
     * {@inheritDoc}
     */
    public function getLastUpdateAt()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLastUpdateAt', []);

        return parent::getLastUpdateAt();
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

}
