<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * Class BlogReview
 * @package AppBundle\Entity
 */
class BlogReview implements ResourceInterface
{
    public const STATUS_NEW = 'new';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $rating;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var Customer
     */
    protected $author;

    /**
     * @var string
     */
    protected $status = ReviewInterface::STATUS_NEW;

    /**
     * @var BlogPost
     */
    protected $reviewSubject;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var ArrayCollection
     */
    protected $reviewLike;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->reviewLike = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * {@inheritdoc}
     */
    public function setRating(?int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * {@inheritdoc}
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * {@inheritdoc}
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthor(): ?Customer
    {
        return $this->author;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor(?Customer $author): void
    {
        $this->author = $author;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function getReviewSubject(): ?BlogPost
    {
        return $this->reviewSubject;
    }

    /**
     * {@inheritdoc}
     */
    public function setReviewSubject(?BlogPost $reviewSubject): void
    {
        $this->reviewSubject = $reviewSubject;
    }

    /**
     * @return mixed
     */
    public function getReviewLike()
    {
        return $this->reviewLike;
    }

    /**
     * @param mixed $reviewLike
     */
    public function setReviewLike($reviewLike): void
    {
        $this->reviewLike = $reviewLike;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
