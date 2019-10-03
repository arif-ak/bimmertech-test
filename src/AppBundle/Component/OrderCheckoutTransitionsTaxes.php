<?php

namespace AppBundle\Component;

/**
 * Class OrderCheckoutTransitionsTaxes
 * @package AppBundle\Component
 */
final  class OrderCheckoutTransitionsTaxes
{
    public const TRANSITION_SKIP_TAXES = 'taxes_skip';
    public const TRANSITION_SELECT_TAXES = 'taxes_select';

    /**
     * OrderCheckoutTransitionsTaxes constructor.
     */
    private function __construct()
    {
    }
}