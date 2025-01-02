# ci4-pages

**ci4-pages** adalah package untuk CodeIgniter 4 yang menyediakan mekanisme **routing berbasis page**. Package ini mempermudah Anda mengelola routing dengan pendekatan berbasis file dan struktur folder yang lebih fleksibel, sehingga cocok untuk proyek dengan kebutuhan routing dinamis. Bayangkan seperti Next.js atau Laravel Folio tapi dengan mempertahankan kekhasan dari gaya coding CodeIgniter 4.

## Instalasi
Instal package ini melalui Composer dengan perintah berikut:

```bash
composer require yllumi/ci4-pages
```

## Konfigurasi
Tambahkan method berikut ini di dalam class `Services` di dalam file **`app/Config/Services.php`**

```php
use CodeIgniter\HTTP\Request;
use CodeIgniter\Router\RouteCollectionInterface;
use Config\Services as AppServices;
use Yllumi\Ci4Pages\PageRouter;

// ...
public static function router(?RouteCollectionInterface $routes = null, ?Request $request = null, bool $getShared = true)
{
    if ($getShared) {
        return static::getSharedInstance('router', $routes, $request);
    }

    $routes ??= AppServices::get('routes');
    $request ??= AppServices::get('request');

    return new PageRouter($routes, $request);
}
```

lalu faftarkan pageview_helper di **`app/Controllers/BaseController.php`**

```php
protected $helpers = ['Yllumi\Ci4Pages\Helpers\pageview'];
```

## Cara Penggunaan

Buat folder `app/Pages/`. Folder ini akan menjadi tempat untuk menyimpan semua file controller page Anda.

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

Setiap folder di dalam app/Pages/ akan menjadi rute halaman. Misalnya folder app/Pages/home akan dapat diakses di domain.com/home. Begitu juga folder app/Pages/profile/achievement/ akan dapat diakses di domain.com/profile/achievement/.

Ada satu file yang wajib ada di dalam folder halaman, yaitu PageController.php yang akan melayani permintaan halaman. Selain itu kamu dapat membuat file .php lain untuk views dan sebagainya.

Mari kita lihat salah satu contoh kode di bawah ini

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

}
```

**`app/Pages/home/index.php`**
```php
<h1>Hello <?= $name ?>!</h1>
<p>Selamat berkarya dengan CodeIgniter!</p>
```

Pada contoh di atas, kita membuat dua buah file, yang pertama adalah PageController.php yaitu class controller dengan satu method `getIndex()`. Method ini akan melayani rekues dari mydomain.com/home atau mydomain.com/home/index.

Method `getIndex()` boleh memiliki parameter yang nantinya akan menangkap uri segment setelah segment halaman. Misalnya pada contoh di atas kita dapat memanggil dengan domain.com/home/Toni atau domain.com/home/index/Toni dimana string 'Toni' akan diterima oleh parameter `$name` dari method `getIndex()`.

Pada contoh di atas method `getIndex()` mengembalikan output dari fungsi `pageView()`. Fungsi ini sama seperti `view()` di CodeIgniter, tapi sudah disesuaikan agar dapat menerima path dari file view yang ada di bawah folder app/Pages/. `return pageView('home/index', $data);` berarti mengembalikan file view app/Pages/**home/index**.php.

Selain method `getIndex()` kamu juga dapat membuat method lainnya, contohnya seperti `getDetail()` atau `postInsert()`. Hanya method yang namanya diawali oleh http verb yang dapat menerima HTTP request. Mekanisme penamaan method pada controller ini sama seperti Auto Route (improved) yang disediakan oleh CodeIgniter 4.

#### API Endpoint

Kamu juga dapat mengembalikan response RESTful dengan menambahkan `ResponseTrait` pada class controller.

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

Selengkapnya tentang API Response Trait dapat dilihat di dokumentasi CodeIgniter ini: [API Responses Trait](https://codeigniter.com/user_guide/outgoing/api_responses.html)..

#### Kombinasi dengan Manual Route

Kamu tetap dapat menggunakan mekanisme Manual Route dan juga Auto Route (Improved) bawaan CodeIgniter 4, berbarengan dengan page based route ini. Urutan eksekusi routernya adalah [manual route] - [page based route] - [auto route].

## Kontribusi
Kami menerima kontribusi dari komunitas! Jika Anda memiliki ide atau menemukan bug, silakan kirimkan pull request atau buka issue di repository ini.

## Lisensi
Sama seperti repository CodeIgniter 4, package ini dilisensikan di bawah lisensi MIT. Lihat file [LICENSE](LICENSE) untuk informasi lebih lanjut.