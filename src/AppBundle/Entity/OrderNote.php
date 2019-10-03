<?php

namespace AppBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

class OrderNote implements ResourceInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $author;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var \Datetime
     */
    protected $createdAt;
    /**
     * @var Order
     */
    protected $order;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return \Datetime
     */
    public function getCreatedAt(): ?\Datetime
    {
        return $this->createdAt;
    }

    /**
     * @param \Datetime $createdAt
     */
    public function setCreatedAt(?\Datetime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Order
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(?Order $order): void
    {
        $this->order = $order;
    }
}
