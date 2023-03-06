<?php

declare(strict_types=1);

namespace VatRadar\VatsimClient;

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\Tree\Message\Messages;
use CuyZ\Valinor\MapperBuilder;
use VatRadar\VatsimClient\Exception\ObjectMappingException;

class Mapper
{
    private MapperBuilder $mapper;

    public function __construct(MapperBuilder $mapper, private readonly string $toClass)
    {
        $this->mapper = $this->mapperSetup($mapper);
    }

    private function mapperSetup(MapperBuilder $mapper): MapperBuilder
    {
        return $mapper->supportDateFormats('Y-m-d\TH:i:s+')
            ->enableFlexibleCasting();
    }

    public function makeIterable(string $json): iterable
    {
        return Source::json($json)->camelCaseKeys();
    }

    public function map(iterable $source): object
    {
        try {
            return $this->mapper->mapper()->map($this->toClass, $source);
        } catch (MappingError $e) {
            $messages = Messages::flattenFromNode($e->node());
            throw new ObjectMappingException($messages->errors());
        }
    }
}
