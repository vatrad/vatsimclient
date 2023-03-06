<?php

declare(strict_types=1);

namespace VatRadar\VatsimClient;

use Exception;
use GuzzleHttp\ClientInterface as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use JsonException;
use RangeException;
use RuntimeException;
use VatRadar\VatsimClient\Exception\HttpException;

use VatRadar\VatsimClient\Exception\MalformedJsonException;

use function json_decode;
use function property_exists;

class DataFetcher
{
    private array $servers = [];

    public function __construct(
        private readonly HttpClient $client,
        private readonly string $bootstrapUri,
        private readonly string $version = 'v3'
    ) {
    }

    /**
     * @throws HttpException
     */
    private function request(string $uri): string
    {
        try {
            $response = $this->client->request('GET', $uri);
        } catch (GuzzleException $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

        return (string) $response->getBody();
    }

    private function randomUrl(): string
    {
        try {
            $num = random_int(0, count($this->servers) - 1);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new RuntimeException('Bad Randomizer (' . $e->getMessage() . ')');
        }
        // @codeCoverageIgnoreEnd

        return $this->servers[$num];
    }

    /**
     * @throws JsonException
     */
    private function json(string $input): object
    {
        return json_decode($input, false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @noinspection PhpVariableVariableInspection
     */
    /**
     * @throws MalformedJsonException
     * @throws HttpException
     * @throws JsonException
     */
    private function bootstrap(): void
    {
        $vatsim = $this->json($this->request($this->bootstrapUri));
        $ver = $this->version;

        if (!property_exists($vatsim, 'data')) {
            throw new MalformedJsonException('Invalid JSON received from VATSIM');
        }

        if (!property_exists($vatsim->data, $ver)) {
            throw new InvalidArgumentException('Invalid VATSIM data version: ' . $ver);
        }

        $this->servers = $vatsim->data->$ver;

        if (count($this->servers) < 1) {
            throw new RangeException('VATSIM returned no data URLs');
        }
    }

    /**
     * @throws HttpException
     * @throws JsonException|MalformedJsonException
     */
    public function fetch(): string
    {
        if (count($this->servers) < 1) {
            $this->bootstrap();
        }

        return $this->request($this->randomUrl());
    }
}
