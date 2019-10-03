<?php
/**
 * Created by PhpStorm.
 * User: m153
 * Date: 7/17/18
 * Time: 4:08 PM
 */

namespace AppBundle\Entity;


class Tag
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;


    protected $product;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}