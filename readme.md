# Client Platform #

### What

Client platform is simple framework that aim to simplify Frontend Developer that working with Web API

### Installation

Add `composer.json`

```json
{
    "require": {
        "ad3n/client-platform": "~1.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}

```

Create configuration file `config.yml`

```yaml
app:
    base_url: 'abc'
    routes:
        - { path: '/{a}/{b}', controller: 'App:HomeController@index', methods: ['GET'] }
    template:
        path: '/var/views'
        cache_dir: '/var/cache'
# Aktifkan jika ingin mencoba kerja event listenernya
#    event_listeners:
#        - { event: 'kernel.request', class: 'App\EventListener\FilterRequestListener', method: 'filter' }
```

Create application class `Application.php`

```php
<?php

namespace App;

use Ihsan\Client\Platform\Bootstrap;

class Application extends Bootstrap
{
    /**
     * @return string
     */
    protected function projectDir()
    {
        return __DIR__.'/..';
    }
}

```

Create Front Controller aka `index.php`

```php
<?php

require __DIR__.'/../vendor/autoload.php';

use App\Application;
use Symfony\Component\HttpFoundation\Request;

$configDir = __DIR__.'/../app/config';
$configFiles = ['loader.yml'];

$request = Request::createFromGlobals();

$app = new Application();
$app->boot($configDir, $configFiles);
$app->handle($request);

```

Create Controller `HomeController.php`

```php
<?php

namespace App\Controller;

use Ihsan\Client\Platform\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public function indexAction()
    {
        //$this->get('url', $args);
        //$this->post('url', $args);
        //$this->put('url', $args);
        //$this->delete('url', $args);
        //$this->client
        //Client is instance of \Ihsan\Client\Platform\Api\ClientInterface
        //$this->renderResponse('view_name', $viewArgs);
        
        return new Response('Hello World!.');
    }
}

```

For more information about controller, please read [`Ihsan\Client\Platform\Controller\AbstractController.php`](src/Controller/AbstractController.php)

### Configuration

For more information about configuration, please read [`Ihsan\Client\Platform\Configuration\Configuration.php#L78-L166`](src/Configuration/Configuration.php#L78-L166)