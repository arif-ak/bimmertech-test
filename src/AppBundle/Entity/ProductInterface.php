<?php

namespace AppBundle\Entity;

/**
 * Interface ProductInterface
 * @package AppBundle\Entity
 */
interface ProductInterface
{
    const COMPATIBILITY_YES = 'Yes';
    const COMPATIBILITY_NO = 'No';
    const COMPATIBILITY_NOT_SURE = 'Not sure';

    const TYPE_PHYSICAL = null;
    const TYPE_CODING = 'codding';
    const TYPE_USB_CODING = 'usb_coding';
}
