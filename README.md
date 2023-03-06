# VATSIM Client

This module retrieves JSON data from VATSIM, does basic sanitization, and provides output in the form of a value object or raw JSON.

## Quick Start

```php
<?php

require __DIR__.'/vendor/autoload.php';

use CuyZ\Valinor\MapperBuilder;
use GuzzleHttp\Client as HttpClient;
use Vatradar\Dataobjects\Vatsim\VatsimData;
use VatRadar\VatsimClient\DataFetcher;
use VatRadar\VatsimClient\IterableSanitizer;
use VatRadar\VatsimClient\Mapper;

// Set up Dependencies
$fetcher = new DataFetcher(new HttpClient(), 'https://status.vatsim.net/status.json');
$sanitizer = new IterableSanitizer();
$mapper = new Mapper(new MapperBuilder(), VatsimData::class);

$client = new \VatRadar\VatsimClient\Client($fetcher, $sanitizer, $mapper);

$vatsimDataObject = $client->retrieve();
```
