<?php

namespace AppBundle\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

/**
 * Class HelpHeaderFilter
 * @package AppBundle\Grid\Filter
 */
class HelpHeaderFilter implements FilterInterface
{
    /**
     * @param DataSourceInterface $dataSource
     * @param string $name
     * @param mixed $data
     * @param array $options
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if ($data['header'] != '0') {
            $dataSource->restrict($dataSource->getExpressionBuilder()->equals('header', $data['header']));
        }
    }
}
