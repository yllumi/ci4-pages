
# ci4-pages

**ci4-pages** is a package for CodeIgniter 4 that provides a **page-based routing mechanism**. This package simplifies routing management with a file-based and folder-structured approach, making it suitable for projects requiring dynamic routing. Think of it as similar to Next.js or Laravel Folio but maintaining the coding style unique to CodeIgniter 4.

## Installation
Install this package via Composer with the following command:

```bash
composer require yllumi/ci4-pages
```

## Configuration
1. Register the `PagesRouter.php` filter class in **`app/Config/Filters.php`**:

```php {4}
    # Register the class alias
    public array $aliases = [
        ...
        'pagesRouter' => \Yllumi\Ci4Pages\Filters\PagesRouter::class,
    ];
    ...

    # Register the alias in the $required section
    public array $required = [
        'before' => [
            ...
            'pagesRouter',
        ],
        ...
    ];
```

## Usage

Create a folder named `app/Pages/`. This folder will be used to store all your page controller files.

```plaintext
app/
├── Pages/
│   ├── home/
│   │   ├── PageController.php
│   │   ├── index.php
│   ├── profile/
│   │   ├── PageController.php
│   │   ├── APIController.php
│   │   ├── index.php
│   │   ├── achievement/
│   │   │   ├── PageController.php
│   │   │   ├── APIController.php
│   │   │   ├── index.php
```

Each folder inside `app/Pages/` will represent a page route. For example, the folder `app/Pages/home` will be accessible at `domain.com/home`. Similarly, the folder `app/Pages/profile/achievement/` will be accessible at `domain.com/profile/achievement/`.

Each page folder must include a `PageController.php` file to handle page requests. Additionally, you can create an `APIController.php` file for handling API requests. Other `.php` files can be used for views and other purposes.

Let’s take a look at an example:

**`app/Pages/home/PageController.php`**
```php
<?php

namespace App\Pages\home;

use Yllumi\Ci4Pages\Controllers\BasePageController;

class PageController extends BasePageController
{
    public function index($name = null): string
    {
        $data['name'] = $name ?? 'World';

        return pageView('home/index', $data);
    }
}
```

**`app/Pages/home/index.php`**
```php
<h1>Hello <?= $name ?>!</h1>
<p>Enjoy coding with CodeIgniter!</p>
```

In the example above, we created two files. The first is `PageController.php`, a controller class with a single `index()` method. You can create any method inside the controller, but only the `index()` method will handle GET requests.

The `index()` method can accept parameters that will capture URI segments following the page segment. For example, the URL `domain.com/home/Toni` will pass the string 'Toni' to the `$name` parameter.

The `index()` method in this example returns the `pageView()` function, similar to CodeIgniter's `view()` but adapted to work with view files under the `app/Pages/` folder. For instance, `return pageView('home/index', $data);` renders the view file `app/Pages/**home/index**.php`.

In addition to the `index()` method, you can also define a `process()` method to handle POST requests to the specified page URL.

Here’s the route registered automatically that makes this work:
```php
$routeCollection->get($uriPage . '(:any)', $controllerNamespace . '::index$1');
$routeCollection->post($uriPage . '(:any)', $controllerNamespace . '::process$1');
```

If you need other methods to handle GET and POST requests aside from those provided above, you can add new methods within the PageController class by using the get_ and post_ prefixes. For instance, the `get_methodname()` method can be called using the query string `?get=methodname`, and the `post_methodname()` method can be called using the query string `?post=methodname`.

Below is a complete example of a controller:
```php
<?php

namespace App\Pages\home;

use Yllumi\Ci4Pages\Controllers\BasePageController;

class PageController extends BasePageController
{

     // Can be accessed at the URL /home
    public function index($id = null): string
    {
       
    }

    // Can be accessed with a POST method at the URL /home
    public function process($id = null): string
    {
       
    }

    // Can be accessed at the URL /home?get=detail or /home/id?get=detail
    public function get_detail($id = null): string
    {
       
    }

    // Can be accessed with a POST method 
    // at the URL /home?post=update or /home/id?post=update
    public function post_update($id = null): string
    {
       
    }

}
```

#### API Endpoint

You can also handle RESTful requests by creating an `APIController.php` class.

**`app/Pages/profile/APIController.php`**
```php
<?php

namespace App\Pages\profile;

use CodeIgniter\RESTful\ResourceController;

class APIController extends ResourceController
{
    public function index()
    {
        $data['name'] = 'Toni Haryanto';
        $data['city'] = 'Bandung';

        return $this->respond($data);
    }
    
    public function show($id = null)
    {
        $data['name'] = 'Toni Haryanto';
        $data['city'] = 'Bandung';
        $data['id'] = $id;
        
        return $this->respond($data);
    }
}
```

The `APIController` can be accessed through the page endpoint with the prefix `api/`. For example, the API endpoint for the profile page is `domain.com/api/profile`.

The `APIController` class extends CodeIgniter's `ResourceController` directly, so you can refer to the CodeIgniter 4 documentation for its usage. The system automatically creates resource routes for every page folder.

```php
$routeCollection->resource('api/' . $uriPage, ['controller' => $controllerNamespace]);

# The above route is equivalent to
$routes->get('api/' . $uriPage, $controllerNamespace . '::index');
$routes->get('api/' . $uriPage . '/new', $controllerNamespace . '::new');
$routes->post('api/' . $uriPage, $controllerNamespace . '::create');
$routes->get('api/' . $uriPage . '/(:segment)', $controllerNamespace . '::show/$1');
$routes->get('api/' . $uriPage . '/(:segment)/edit', $controllerNamespace . '::edit/$1');
$routes->put('api/' . $uriPage . '/(:segment)', $controllerNamespace . '::update/$1');
$routes->patch('api/' . $uriPage . '/(:segment)', $controllerNamespace . '::update/$1');
$routes->delete('api/' . $uriPage . '/(:segment)', $controllerNamespace . '::delete/$1'); 
```

Using the resource controller above, the available methods to serve each resource endpoint are `index()`, `new()`, `create()`, `show()`, `edit()`, `update()`, `update()`, and `delete()`.

## Contribution
We welcome community contributions! If you have ideas or find bugs, feel free to submit a pull request or open an issue in this repository.

## License
Similar to the CodeIgniter 4 repository, this package is licensed under the MIT license. See the [LICENSE](LICENSE) file for more details.