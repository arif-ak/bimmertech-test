<?php

namespace AppBundle\Entity;

use Sylius\Component\Product\Model\ProductAttribute as BaseProductAttribute;

class ProductAttribute extends BaseProductAttribute
{

    /**
     * @var
     */
    protected $name;

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
        $this->setName($this->getTranslation('en_US')->getName());
    }
}