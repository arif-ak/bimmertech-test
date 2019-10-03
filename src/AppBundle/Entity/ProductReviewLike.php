<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class ProductReviewLike
{
    /**
     * @var $id
     */
    protected $id;

    /**
     * @var string
     */
    protected $clientToken;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var ProductReview
     */
    protected $review;

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
     * @return string
     */
    public function getClientToken(): string
    {
        return $this->clientToken;
    }

    /**
     * @param string $clientToken
     */
    public function setClientToken(string $clientToken): void
    {
        $this->clientToken = $clientToken;
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     */
    public function setSessionId(string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return ProductReview
     */
    public function getReview(): ?ProductReview
    {
        return $this->review;
    }

    /**
     * @param ProductReview $review
     */
    public function setReview(ProductReview $review): void
    {
        $this->review = $review;
    }
}
