# VATSIM Client

This module retrieves JSON data from VATSIM, does basic sanitization, and provides output in the form of a value object or raw JSON.

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

// Instantiate VATSIM Client
$client = new VatsimClient($guzzle, ['bootUri' => 'https://status.vatsim.net/status.json']);
$client->bootstrap();

// Retrieve data from VATSIM
// Returned in a VatsimData value object
$data = $client->retrieve();

// Retrieve data from VATSIM
// Returned as raw JSON
$data = $client->retrieve(true);
```
