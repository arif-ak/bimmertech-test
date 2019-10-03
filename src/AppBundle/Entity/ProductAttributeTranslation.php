<?php
/**
 * Created by PhpStorm.
 * User: m153
 * Date: 2/23/19
 * Time: 10:18 PM
 */

namespace AppBundle\Entity;

use Sylius\Component\Product\Model\ProductAttributeTranslation as BaseAttributeTranslation;


class ProductAttributeTranslation extends BaseAttributeTranslation
{
    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
        if ($this->translatable) {
            $this->translatable->setName($name);
        }
    }
}