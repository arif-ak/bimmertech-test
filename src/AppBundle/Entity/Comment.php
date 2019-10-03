<?php

namespace AppBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class Comment
 * @package AppBundle\Entity
 */
class Comment implements ResourceInterface
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var AdminUser
     */
    protected $user;

    /**
     * @var OrderItem
     */
    protected $subject;

    /**
     * @var $comment
     */
    protected $comment;

    /**
     * @var $date
     */
    protected $date;

    /**
     * @var Order
     */
    protected $order;

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->setDate(new \DateTime('now'));
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return AdminUser
     */
    public function getUser(): AdminUser
    {
        return $this->user;
    }

    /**
     * @param AdminUser $user
     */
    public function setUser(AdminUser $user): void
    {
        $this->user = $user;
    }

    /**
     * @return OrderItem
     */
    public function getSubject(): OrderItem
    {
        return $this->subject;
    }

    /**
     * @param OrderItem $subject
     */
    public function setSubject(OrderItem $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }
}
