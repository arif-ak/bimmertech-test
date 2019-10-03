<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class FAQQuestion
 * @ORM\Table(name="app_buyers_guide_option")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BuyersGuideOptionRepository")
 */
class BuyersGuideOption implements ResourceInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }
}
