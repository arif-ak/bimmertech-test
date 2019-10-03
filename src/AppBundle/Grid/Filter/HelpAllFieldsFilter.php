<?php

namespace AppBundle\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class HelpAllFieldsFilter implements FilterInterface
{
    public const NAME = 'string';
    public const TYPE_CONTAINS = 'contains';
    public const TYPE_NOT_CONTAINS = 'not_contains';



    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        $expressionBuilder = $dataSource->getExpressionBuilder();

        if (is_array($data) && !isset($data['type'])) {
            $data['type'] = $options['type'] ?? self::TYPE_CONTAINS;
        }

        if (!is_array($data)) {
            $data = ['type' => self::TYPE_CONTAINS, 'value' => $data];
        }

        $fields = array_key_exists('fields', $options) ? $options['fields'] : [$name];

        $type = $data['type'];
        $value = array_key_exists('value', $data) ? $data['value'] : null;



        if (1 === count($fields)) {
            $dataSource->restrict($this->getExpression($expressionBuilder, $type, current($fields), $value));

            return;
        }

        $expressions = [];
        foreach ($fields as $field) {
            $expressions[] = $this->getExpression($expressionBuilder, $type, $field, $value);
        }

        if (self::TYPE_NOT_CONTAINS === $type) {
            $dataSource->restrict($expressionBuilder->andX(...$expressions));

            return;
        }

        $dataSource->restrict($expressionBuilder->orX(...$expressions));
    }

    /**
     * @param ExpressionBuilderInterface $expressionBuilder
     * @param string $type
     * @param string $field
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    private function getExpression(
        ExpressionBuilderInterface $expressionBuilder,
        string $type,
        string $field,
        $value
    ) {
        switch ($type) {
            case self::TYPE_CONTAINS:
                return $expressionBuilder->like($field, '%' . $value . '%');
            case self::TYPE_NOT_CONTAINS:
                return $expressionBuilder->notLike($field, '%' . $value . '%');
            default:
                throw new \InvalidArgumentException(sprintf('Could not get an expression for type "%s"!', $type));
        }
    }
}
