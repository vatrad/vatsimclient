<?php declare(strict_types=1);
/*
 * This file is part of a VATRadar package.
 *
 * Copyright (c) 2022 VATRadar <dev@vatradar.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vatradar\Vatsimclient;

use Aura\Filter\Exception\FilterFailed;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use JsonException;
use RuntimeException;
use stdClass;
use Vatradar\Vatsimclient\Exception\HttpException;
use Vatradar\Vatsimclient\Exception\MalformedJsonException;

class Client
{
    protected GuzzleClient $guzzle;
    protected DataFilter $filter;
    /** @var string[] */
    protected array $options = [];
    /** @var string[]  */
    protected array $defaultOptions = [
        'version' => 'v3',
        'serializer' => 'json',
    ];
    protected array $rawData = [];
    protected array $filteredData = [];

    /**
     * @param GuzzleClient $guzzle
     * @param DataFilter $filter
     * @param array $options
     */
    public function __construct(GuzzleClient $guzzle, DataFilter $filter, array $options = [])
    {
        $this->guzzle = $guzzle;
        $this->filter = $filter;
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
     * @return array
     * @throws HttpException
     * @throws MalformedJsonException
     */
    protected function guzzleRequest(string $uri): array
    {
        try {
            return json_decode(
            (string)$this->guzzle->request('GET', $uri)->getBody(),
            true,
            512,
            JSON_THROW_ON_ERROR
            );
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

        /** @var stdClass $status */
        $status = $this->guzzleRequest($bootUri);

        /** @var ?string[] $returnedUris */
        $returnedUris = $status['data'][$ver];

        if(!is_array($returnedUris))
        {
            throw new RuntimeException("Invalid data received from VATSIM status.json");
        }

        try {
            $chooser = random_int(0, count($returnedUris) - 1);
        } catch (Exception $e) {
            throw new RuntimeException("No suitable randomizer exists on your platform (".$e->getMessage().")");
        }

        $dataUri = $status['data'][$ver][$chooser];

        return $this->setOption('dataUri', $dataUri);
    }

    /**
     * @param bool $filtered
     * @return array
     * @throws HttpException
     * @throws MalformedJsonException
     * @throws FilterFailed
     */
    public function retrieve(bool $filtered = true): array
    {
        if(!$this->hasOption('dataUri'))
        {
            throw new RuntimeException("No Data URI defined, please bootstrap().");
        }

        $vatsim = $this->guzzleRequest($this->getOption('dataUri'));

        // save raw data, then save filtered data
        $this->rawData = $vatsim;

        // filter and save filtered data
        $this->filter->run($vatsim);
        $this->filteredData = $vatsim;

        if($filtered === false) {
            return $this->rawData;
        }

        return $this->filteredData;
    }


}
