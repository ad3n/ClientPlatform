# Client Platform #

### What

Client platform is simple framework that aim to simplify Frontend Developer that working with Web API

### Installation

Add `composer.json`

```json
{
    "require": {
        "ad3n/client-platform": "~0.1"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}

```

Create configuration file

```php
<?php

return array(
    'routes' => array(
        array(
            'path' => '/',
            'controller' => 'App:HomeController@index',
        ),
    ),
);
```

Create Front Controller aka `index.php`

```php
<?php

require __DIR__.'/../vendor/autoload.php';

use Ihsan\Client\Platform\Bootstrap;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();

$app = new Bootstrap(new Container());
$app->handle($request, require __DIR__.'/../app/config/config.php');

```

Create Controller

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