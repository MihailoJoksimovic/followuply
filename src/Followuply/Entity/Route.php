<?php

namespace Followuply\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @Entity @Table(name="route")
 */
class Route
{
    const ROUTE_TYPE_BEGINS_WITH = 1;
    const ROUTE_TYPE_EQUALS      = 2;
    const ROUTE_TYPE_REGEX       = 3;

    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /** @Column(type="string",name="uri_pattern") **/
    protected $uriPattern;

    /** @Column(type="smallint",name="pattern_type") */
    protected $patternType;

    /** @Column(type="smallint") */
    protected $position;

    /** @Column(type="datetime",name="dt_added") **/
    protected $dtAdded;

    /**  @ManyToOne(targetEntity="Scenario", inversedBy="routes") */
    protected $scenario;

    public function __construct()
    {
        $this->dtAdded = new \DateTime();
    }

    /**
     * @param mixed $dtAdded
     */
    public function setDtAdded($dtAdded)
    {
        $this->dtAdded = $dtAdded;
    }

    /**
     * @return mixed
     */
    public function getDtAdded()
    {
        return $this->dtAdded;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $uriPattern
     */
    public function setUriPattern($uriPattern)
    {
        $this->uriPattern = $uriPattern;
    }

    /**
     * @return mixed
     */
    public function getUriPattern()
    {
        return $this->uriPattern;
    }

    /**
     * @param mixed $patternType
     */
    public function setPatternType($patternType)
    {
        $this->patternType = $patternType;
    }

    /**
     * @return mixed
     */
    public function getPatternType()
    {
        return $this->patternType;
    }

    /**
     * @param mixed $scenario
     */
    public function setScenario(Scenario $scenario)
    {
        $this->scenario = $scenario;
    }

    /**
     * @return Scenario
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('patternType', new Assert\Range(array('min' => 1, 'max' => 3)));
        $metadata->addPropertyConstraint('uriPattern', new Assert\NotNull());
        $metadata->addPropertyConstraint('position', new Assert\NotNull());
    }

}

