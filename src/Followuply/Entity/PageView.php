<?php

namespace Followuply\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Entity @Table(name="page_view")
 */
class PageView
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /** @Column(type="string") **/
    protected $url;

    /** @Column(type="datetime",name="dt_added") **/
    protected $dtAdded;

    /** @var @Column(type="string", length=500) */
    protected $email;

    /** @var @Column(type="string",name="visitor_uid") */
    protected $visitorUid;

    /** @var @Column(type="boolean") */
    protected $isProcessed;

    public function __construct()
    {
        $this->dtAdded = new \DateTime();
        $this->isProcessed = 0;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $visitorUid
     */
    public function setVisitorUid($visitorUid)
    {
        $this->visitorUid = $visitorUid;
    }

    /**
     * @return mixed
     */
    public function getVisitorUid()
    {
        return $this->visitorUid;
    }

    /**
     * @param mixed $isProcessed
     */
    public function setIsProcessed($isProcessed)
    {
        $this->isProcessed = $isProcessed;
    }

    /**
     * @return mixed
     */
    public function isProcessed()
    {
        return $this->isProcessed;
    }

    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
    }


} 