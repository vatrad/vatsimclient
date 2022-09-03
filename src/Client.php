<?php declare(strict_types=1);
/*
 * This file is part of a VATRadar package.
 *
 * Copyright (c) 2022 VATRadar <dev@vatradar.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vatradar\Vatsimclient;

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use JsonException;
use RuntimeException;
use Vatradar\Vatsimclient\Data\VatsimData;
use Vatradar\Vatsimclient\Exception\HttpException;
use Vatradar\Vatsimclient\Exception\MalformedJsonException;

class Client
{
    protected GuzzleClient $guzzle;
    /** @var string[] */
    protected array $options = [];
    /** @var string[]  */
    protected array $defaultOptions = [
        'version' => 'v3',
        'serializer' => 'json',
    ];

    /**
     * @param GuzzleClient $guzzle
     * @param array $options
     */
    public function __construct(GuzzleClient $guzzle, array $options = [])
    {
        $this->guzzle = $guzzle;
        $this->setOptions($options);
    }

    /**
     * @param string[] $opt
     * @return $this
     */
    public function setOptions(array $opt = []): self
    {
        $this->options = array_merge($this->defaultOptions, $opt);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function setOption(string $key, mixed $value): self
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getOption(string $key): mixed
    {
        return $this->hasOption($key) ? $this->options[$key]: null;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasOption(string $key): bool
    {
        if(array_key_exists($key, $this->options))
        { return true; }

        return false;
    }

    /**
     * @param string $uri
     * @param bool $raw
     * @return array|string
     * @throws HttpException
     * @throws MalformedJsonException
     */
    protected function guzzleRequest(string $uri, bool $raw = false): array|string
    {
        try {
            $jsonBody = (string) $this->guzzle->request('GET', $uri)->getBody();

            // ensure it is good json first
            $jsonArray = json_decode($jsonBody, true, 512, JSON_THROW_ON_ERROR);

            if($raw === true) {
                return $jsonBody;
            }

            return $jsonArray;

        } catch (GuzzleException $e) {
            throw new HttpException('Error getting VATSIM bootstrap: '.$e->getMessage());
        } catch (JsonException $e) {
            throw new MalformedJsonException('JSON from Vatsim bootstrap is Malformed: '.$e->getMessage());
        }
    }

    /**
     * @return $this
     * @throws HttpException
     * @throws MalformedJsonException
     */
    public function bootstrap(): self
    {
        if(!$this->hasOption('bootUri'))
        {
            throw new InvalidArgumentException("VATSIM Client requires the 'bootUri' option to be set to VATSIM's status.json location.");
        }

        $bootUri = $this->getOption('bootUri');
        $ver = $this->getOption('version');

        /** @var array $status */
        $status = $this->guzzleRequest($bootUri);

        /** @var ?string[] $returnedUris */
        $returnedUris = $status['data'][$ver];

        if(!is_array($returnedUris))
        {
            throw new RuntimeException('Invalid data received from VATSIM status.json');
        }

        try {
            $chooser = random_int(0, count($returnedUris) - 1);
        } catch (Exception $e) {
            throw new RuntimeException('No suitable randomizer exists on your platform (' .$e->getMessage(). ')');
        }

        $dataUri = $status['data'][$ver][$chooser];

        return $this->setOption('dataUri', $dataUri);
    }

    /**
     * @param bool $raw
     * @return VatsimData|string
     * @throws HttpException
     * @throws MalformedJsonException
     * @throws MappingError
     */
    public function retrieve(bool $raw = false): VatsimData|string
    {
        if(!$this->hasOption('dataUri'))
        {
            throw new RuntimeException('No Data URI defined, please bootstrap().');
        }

        $json = $this->guzzleRequest($this->getOption('dataUri'), true);

        if($raw === true) {
            return $json;
        }

        return (new MapperBuilder())
            ->supportDateFormats('Y-m-d\TH:i:s+')
            ->flexible()
            ->mapper()
            ->map(
              VatsimData::class,
              Source::json($json)->camelCaseKeys()
            );
    }

}
