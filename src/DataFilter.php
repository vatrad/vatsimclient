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
use Aura\Filter\FilterFactory;
use Aura\Filter\SubjectFilter;
use InvalidArgumentException;
use Vatradar\Vatsimclient\Filter\FlightplanFilter;
use Vatradar\Vatsimclient\Filter\PilotFilter;

class DataFilter
{
    protected FilterFactory $filterFactory;
    /** @var array<string, string> */
    protected array $filterOpts = [
        'pilots' => PilotFilter::class,
        'flight_plan' => FlightplanFilter::class,
    ];
    /** @var array<string, SubjectFilter> */
    protected array $filters;

    /**
     * @param FilterFactory         $filterFactory
     * @param array<string, string> $filterOpts
     */
    public function __construct(FilterFactory $filterFactory, array $filterOpts = [])
    {
        $this->filterFactory = $filterFactory;
        $this->filterOpts = array_merge($this->filterOpts, $filterOpts);
        $this->loadFilters();
    }

    /**
     * @return void
     */
    protected function loadFilters(): void
    {
        foreach($this->filterOpts as $section => $filter)
        {
            $this->filters[$section] = $this->filterFactory->newSubjectFilter($filter);
        }
    }

    /**
     * @param array $data
     * @return array
     * @throws FilterFailed
     */
    public function run(array $data): array
    {
        $this->iterate($data);

        return $data;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function hasFilter(string $class): bool
    {
        if(array_key_exists($class, $this->filters)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $section
     * @return SubjectFilter
     */
    public function getFilter(string $section): SubjectFilter
    {
        if(!$this->hasFilter($section))
        {
            throw new InvalidArgumentException("Filter for type '".$section."' not defined");
        }

        return $this->filters[$section];
    }

    /**
     * @param array $subject
     * @return void
     * @throws FilterFailed
     */
    protected function iterate(array $subject): void
    {
        foreach($subject as $k => $v)
        {
            if(!$this->hasFilter($k)) {
                continue;
            }

            foreach($v as $idx => $data)
            {
                $this->filter($k, $data);
                $subject[$k][$idx] = $data;

                // flight plan is a special case
                // TODO figure out better way to handle this, probably?
                if(array_key_exists('flight_plan', $data) && $data['flight_plan'] !== null) {
                    $this->filter('flight_plan', $data['flight_plan']);
                    $subject[$k][$idx]['flight_plan'] = $data['flight_plan'];
                }
            }
        }
    }

    /**
     * @param string $filter
     * @param array $data
     * @return void
     * @throws FilterFailed
     */
    protected function filter(string $filter, array $data): void
    {
        if($this->hasFilter($filter))
        {
            $this->getFilter($filter)->assert($data);
        }
    }

}
