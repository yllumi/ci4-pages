
# ci4-pages

![tests build](https://github.com/yllumi/ci4-pages/workflows/tests%20build/badge.svg)

**ci4-pages** is a package for CodeIgniter 4 that provides a **page-based routing mechanism**. This package simplifies routing management with a file-based and folder-structured approach, making it suitable for projects requiring dynamic routing. Think of it as similar to Next.js or Laravel Folio but maintaining the coding style unique to CodeIgniter 4.

## Installation
Install this package via Composer with the following command:

```bash
composer require yllumi/ci4-pages
```

## Configuration

Register the pageview_helper in **`app/Controllers/BaseController.php`**

```php
protected $helpers = ['Yllumi\Ci4Pages\Helpers\pageview'];
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
│   │   ├── index.php
│   │   ├── achievement/
│   │   │   ├── PageController.php
│   │   │   ├── index.php
```

Each folder inside `app/Pages/` will represent a page route. For example, the folder `app/Pages/home` will be accessible at `domain.com/home`. Similarly, the folder `app/Pages/profile/achievement/` will be accessible at `domain.com/profile/achievement/`.

There is one mandatory file that must exist in the pages folder, namely PageController.php, which will handle page requests. Besides that, you can create other .php files for views and so on.

Let's look at an example code below:

**`app/Pages/home/PageController.php`**
```php
<?php

namespace App\Pages\home;

use App\Controllers\BaseController;

class PageController extends BaseController
{
    public function getIndex($name = null): string
    {
        $data['name'] = $name ?? 'World';

        return pageView('home/index', $data);
    }

    public function getDetail($id = null)
    {
        $data['name'] = 'Toni Haryanto';
        $data['id'] = $id;

        return pageView('home/detail', $data);
    }

}
```

**`app/Pages/home/index.php`**
```php
<h1>Hello <?= $name ?>!</h1>
<p>Selamat berkarya dengan CodeIgniter!</p>
```

In the example above, we created two files. The first is `PageController.php`, a controller class with one method `getIndex()`. This method handles requests to mydomain.com/home or mydomain.com/home/index.

The `getIndex()` method can have parameters that capture the URI segment after the page segment. For instance, in the example above, you can call domain.com/home/Toni or domain.com/home/index/Toni, where the string 'Toni' will be received by the `$name` parameter of the `getIndex()` method.

In the example above, the `getIndex()` method returns the output of the `pageView()` function. This function is similar to `view()` in CodeIgniter but is adjusted to accept the path of the view file located under the app/Pages/ folder. `return pageView('home/index', $data);` means it returns the view file app/Pages/**home/index**.php.

In addition to the `getIndex()` method, you can also create other methods i.e. `getDetail()` or `postInsert()`. Only methods whose names start with an HTTP verb can handle HTTP requests. The method naming mechanism in this controller is the same as the Auto Route (improved) provided by CodeIgniter 4. Method `getDetail()` for example, can be accessed from mydomain.com/home/detail/[id].

#### API Endpoint

You can also return RESTful responses by adding the ResponseTrait to the controller class.

**`app/Pages/home/PageController.php`**
```php
<?php

namespace App\Pages\profile;

use App\Controllers\BaseController;

class PageController extends BaseController
{
    use \CodeIgniter\API\ResponseTrait;

    public function getIndex()
    {
        $data['name'] = 'Toni Haryanto';
        $data['city'] = 'Bandung';

        return $this->respond($data);
    }

    public function getDetail($id = null)
    {
        $data['name'] = 'Toni Haryanto';
        $data['city'] = 'Bandung';
        $data['id'] = $id;

        return $this->respond($data);
    }
}
```

For more information about the API Response Trait, refer to the CodeIgniter documentation here: [API Responses Trait](https://codeigniter.com/user_guide/outgoing/api_responses.html).

#### Combination with Manual Routes

You can still use the Manual Route mechanism alongside the Auto Route (Improved) provided by CodeIgniter 4 in conjunction with this page-based routing. The execution order of the routers is [manual route] - [page-based route] - [auto route].

#### Page Template Generator

You can run this `spark` command to create a new page folder and files: 

```
php spark page:create pagename
php spark page:create pagename/subpage
```

This command will create a new page folder with its sample controller and view file.

## Contribution
We welcome community contributions! If you have ideas or find bugs, feel free to submit a pull request or open an issue in this repository.

## License
Similar to the CodeIgniter 4 repository, this package is licensed under the MIT license. See the [LICENSE](LICENSE) file for more details.