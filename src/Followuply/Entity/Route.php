<?php

namespace Followuply\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @Entity @Table(name="route")
 */
class Route
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /** @Column(type="string",name="page_a") **/
    protected $pageA;

    /** @Column(type="string",name="page_b") **/
    protected $pageB;

    /** @Column(type="smallint") **/
    protected $timeframe;

    /** @Column(type="string") **/
    protected $emailTemplate;

    /** @Column(type="datetime",name="dt_added") **/
    protected $dtAdded;

    public function __construct()
    {
        $this->dtAdded = new \DateTime();
        $this->emailTemplate = "This is default and totally useless email template";
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
     * @param mixed $pageA
     */
    public function setPageA($pageA)
    {
        $this->pageA = $pageA;
    }

    /**
     * @return mixed
     */
    public function getPageA()
    {
        return $this->pageA;
    }

    /**
     * @param mixed $pageB
     */
    public function setPageB($pageB)
    {
        $this->pageB = $pageB;
    }

    /**
     * @return mixed
     */
    public function getPageB()
    {
        return $this->pageB;
    }

    /**
     * @param mixed $emailTemplate
     */
    public function setEmailTemplate($emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * @return mixed
     */
    public function getEmailTemplate()
    {
        return $this->emailTemplate;
    }

    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('pageA', new Assert\NotBlank());
        $metadata->addPropertyConstraint('pageB', new Assert\NotBlank());
        $metadata->addPropertyConstraint('timeframe', new Assert\NotBlank());
    }


} 