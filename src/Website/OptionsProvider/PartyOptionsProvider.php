<?php

namespace App\Website\OptionsProvider;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;
use Pimcore\Model\DataObject\Party;

class PartyOptionsProvider implements SelectOptionsProviderInterface
{
    public function getOptions(array $context, Data $fieldDefinition): array
    {
        $parties = new Party\Listing();
        $options = [];
        foreach($parties as $party) {
            $options[] = ["key" => $party->getParty(), "value" => $party->getId()];
        }
        
        return $options;
    }

    /**
     * @inheritDoc
     */
    public function hasStaticOptions(array $context, Data $fieldDefinition): bool
    {
        return $fieldDefinition->getDefaultValue();
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValue(array $context, Data $fieldDefinition): string | array | null
    {
        return true;
    }
}
