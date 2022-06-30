# VATSIM Client

This module retrieves JSON data from VATSIM, does basic sanitization, and provides output in the form of an associative array.

## Quick Start

```php
<?php

use Aura\Filter\FilterFactory;
use GuzzleHttp\Client as GuzzleClient;
use Vatradar\Vatsimclient\Client as VatsimClient;
use Vatradar\Vatsimclient\DataFilter;

require '/path/to/vendor/autoload.php';

// set up Guzzle client
$guzzle = new GuzzleClient();

// Set up Filters
$filterFactory = new FilterFactory();
$filter = new DataFilter($filterFactory);

// Instantiate VATSIM Client
$client = new VatsimClient($guzzle, $filter, ['bootUri' => 'https://status.vatsim.net/status.json']);
$client->bootstrap();

// Retrieve data from VATSIM
// Returned in an associative array
$data = $client->retrieve();
```
