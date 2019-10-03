<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class DropDown
 *
 * @package AppBundle\Entity
 */
class DropDown implements ResourceInterface
{
    const CODDING_PRODUCT = 'coding';
    const PHYSICAL_PRODUCT = 'physical';
    const PHYSICAL_PRODUCT_WITH_CODDING = 'physical_coding';
    const NONE_PRODUCT = null;

    const COMPLETE = 'complete';
    const NOT_CODED= 'not coded';

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var DropDownOption
     */
    protected $dropDownOptions;

    public function __construct()
    {
        $this->dropDownOptions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
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

    /**
     * @return DropDownOption
     */
    public function getDropDownOptions()
    {
        return $this->dropDownOptions;
    }

    /**
     * @param DropDownOption $dropDownOption
     */
    public function addDropDownOption(DropDownOption $dropDownOption): void
    {
        if (true === $this->dropDownOptions->contains($dropDownOption)) {
            return;
        }
        $this->dropDownOptions->add($dropDownOption);
        $dropDownOption->setDropDown($this);
    }

    /**
     * @param DropDownOption $dropDownOption
     */
    public function removeDropDownOption(DropDownOption $dropDownOption): void
    {

        if (false === $this->dropDownOptions->contains($dropDownOption)) {
            return;
        }
        $this->dropDownOptions->removeElement($dropDownOption);
    }


    /**
     * @param ProductProperty $property
     */
    public function addProperty(ProductProperty $property)
    {
        if (true === $this->properties->contains($property)) {
            return;
        }
        $this->properties->add($property);
        $property->addProduct($this);
    }

    /**
     * @param ProductProperty $property
     */
    public function removeProperty(ProductProperty $property)
    {
        if (false === $this->properties->contains($property)) {
            return;
        }
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }
}