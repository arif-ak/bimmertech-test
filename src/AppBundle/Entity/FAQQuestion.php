<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class FAQQuestion
 * @ORM\Table(name="app_buyers_guide_option")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FAQQuestionRepository")
 */
class FAQQuestion implements ResourceInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $code;

    /**
     * @var int|null
     */
    protected $position = 0;

    /**
     * @var boolean
     */
    protected $enabled;

    /**
     * @var string|null
     */
    protected $question;

    /**
     * @var string|null
     */
    protected $answer;

    /**
     * @var FaqHeader
     */
    protected $header;

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param null|string $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return null|string
     */
    public function getQuestion(): ?string
    {
        return $this->question;
    }

    /**
     * @param null|string $question
     */
    public function setQuestion(?string $question): void
    {
        $this->question = $question;
    }

    /**
     * @return null|string
     */
    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    /**
     * @param null|string $answer
     */
    public function setAnswer(?string $answer): void
    {
        $this->answer = $answer;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return FaqHeader
     */
    public function getHeader(): ?FaqHeader
    {
        return $this->header;
    }

    /**
     * @param FaqHeader $header
     */
    public function setHeader(FaqHeader $header): void
    {
        $this->header = $header;
    }
}