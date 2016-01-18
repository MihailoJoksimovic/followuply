<?php

namespace Followuply\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Entity @Table(name="scenario")
 * @UniqueEntity("appUid")
 */
class Scenario
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /** @Column(type="string",name="app_uid", unique=true) **/
    protected $appUid;

    /** @Column(type="smallint") */
    protected $timeframe;

    /** @OneToMany(targetEntity="Route", mappedBy="scenario") */
    protected $routes;

    /** @Column(type="datetime",name="dt_added") **/
    protected $dtAdded;

    public function __construct()
    {
        $this->dtAdded = new \DateTime();
        $this->routes = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getDtAdded()
    {
        return $this->dtAdded;
    }

    /**
     * @param mixed $appUid
     */
    public function setAppUid($appUid)
    {
        $this->appUid = $appUid;
    }

    /**
     * @return mixed
     */
    public function getAppUid()
    {
        return $this->appUid;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $timeframe
     */
    public function setTimeframe($timeframe)
    {
        $this->timeframe = $timeframe;
    }

    /**
     * @return mixed
     */
    public function getTimeframe()
    {
        return $this->timeframe;
    }

    /**
     * @return Route[]|ArrayCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('appUid', new Assert\NotNull());
        $metadata->addPropertyConstraint('timeframe', new Assert\NotNull());
    }


} 