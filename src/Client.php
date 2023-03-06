<?php

declare(strict_types=1);
/*
 * This file is part of a VATRadar package.
 *
 * Copyright (c) 2022 VATRadar <dev@vatradar.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VatRadar\VatsimClient;

use JsonException;
use VatRadar\VatsimClient\Exception\HttpException;
use VatRadar\VatsimClient\Exception\MalformedJsonException;

class Client
{
    public function __construct(
        private readonly DataFetcher $fetcher,
        private readonly IterableSanitizer $sanitizer,
        private readonly Mapper $mapper
    ) {
    }

    /**
     * @throws HttpException
     * @throws JsonException|Exception\MalformedJsonException
     */
    private function fetch(): string
    {
        return $this->fetcher->fetch();
    }

    private function sanitize(iterable $data): iterable
    {
        return $this->sanitizer->clean($data);
    }

    private function makeIterable(string $data): iterable
    {
        return $this->mapper->makeIterable($data);
    }

    private function map(iterable $data): object
    {
        return $this->mapper->map($data);
    }

    /**
     * @throws HttpException
     * @throws JsonException
     * @throws MalformedJsonException
     */
    public function retrieve(): object
    {
        $json = $this->fetch();
        $iterable = $this->makeIterable($json);
        $sanitized = $this->sanitize($iterable);
        return $this->map($sanitized);
    }
}
