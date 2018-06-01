<?php declare(strict_types=1);

namespace Shopware\Core\Framework\ORM\Dbal\FieldAccessorBuilder;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\ORM\Field\Field;
use Shopware\Core\Framework\ORM\Field\JsonObjectField;
use Shopware\Core\Framework\ORM\Write\FieldAware\StorageAware;

class JsonObjectFieldAccessorBuilder implements FieldAccessorBuilderInterface
{
    public function buildAccessor(string $root, Field $field, Context $context, string $accessor): ?string
    {
        /** @var StorageAware $field */
        if (!$field instanceof JsonObjectField) {
            return null;
        }

        $accessor = str_replace($field->getPropertyName() . '.', '', $accessor);

        return sprintf(
            'JSON_UNQUOTE(JSON_EXTRACT(`%s`.`%s`, "$.%s"))',
            $root,
            $field->getPropertyName(),
            $accessor
        );
    }
}
