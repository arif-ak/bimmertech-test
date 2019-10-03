<?php

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\OrderItemInterface as BaseOrderItemInterface;

/**
 * Interface OrderItemInterface
 * @package AppBundle\Entity
 */
interface OrderItemInterface extends BaseOrderItemInterface
{
    const NEW = 'new';
    const COMPLETE = 'completed';
    const NOT_REQUIRED = 'not required';
    const NOT_SEND = 'not send';
    const NOT_ADDED = 'not added';
    const NOT_CODED = 'not coded';
    const PARTIALLY_ADDED = 'partially added';
    const PARTIALLY_CODED = 'partially coded';
    const NA = 'n/a';
    const DOWNLOAD = 'download';
    const IN_PROCESS = 'in process';
    const IN_VIA_EMAIL = 'Sent via email';

    const TYPE_ADDON = 'addon';
    const TYPE_INCLUDED_ADDON = 'includedAddon';
    const TYPE_ITEM = 'item';
    const TYPE_WARRANTY = 'warranty';
}
