<?php

namespace AppBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class History
 *
 * @package AppBundle\Entity
 */
class History implements ResourceInterface
{
    /**
     * @var
     */
    private $id;

    /**
     * @var
     */
    private $user;

    /**
     * @var
     */
    private $message;

    /**
     * @var
     */
    private $createdAt;

    /**
     * @var
     */
    private $order;

    /**
     * History constructor.
     */
    public function __construct()
    {
        $this->createdAt= new \DateTime('now');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order): void
    {
        $this->order = $order;
    }

}