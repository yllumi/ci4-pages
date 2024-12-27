# ci4-pages

**ci4-pages** adalah package untuk CodeIgniter 4 yang menyediakan mekanisme **routing berbasis page**. Package ini mempermudah Anda mengelola routing dengan pendekatan berbasis file dan struktur folder yang lebih fleksibel, sehingga cocok untuk proyek dengan kebutuhan routing dinamis. Bayangkan seperti Next.js atau Laravel Folio tapi dengan mempertahankan kekhasan dari gaya coding CodeIgniter 4. 

## Instalasi
Instal package ini melalui Composer dengan perintah berikut:

```bash
composer require vendor/ci4-pages
```

## Konfigurasi
1. Daftarkan class filter `PagesRouter.php` di **`app/Config/Filters.php`**

```php {4}
    # Daftarkan class alias
    public array $aliases = [
        ...
        'pagesRouter' => \Yllumi\Ci4Pages\Filters\PagesRouter::class,
    ];
    ...

    # Daftarkan alias di bagian $required
    public array $required = [
        'before' => [
            ...
            'pagesRouter',
        ],
        ...
    ];
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
│   │   ├── APIController.php
│   │   ├── index.php
│   │   ├── achievement/
│   │   │   ├── PageController.php
│   │   │   ├── APIController.php
│   │   │   ├── index.php
```

Setiap folder di dalam app/Pages/ akan menjadi rute halaman. Misalnya folder app/Pages/home akan dapat diakses di domain.com/home. Begitu juga folder app/Pages/profile/achievement/ akan dapat diakses di domain.com/profile/achievement/.

Ada satu file yang wajib ada di dalam folder halaman, yaitu PageController.php yang akan melayani permintaan halaman. Selain PageController, kamu juga dapat membuat file APIController.php untuk melayani permintaan API. Di luar itu kamu dapat membuat file .php lain untuk views dan sebagainya.

Mari kita lihat salah satu contoh kode di bawah ini

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
<p>Selamat berkarya dengan CodeIgniter!</p>
```

Pada contoh di atas, kita membuat dua buah file, yang pertama adalah PageController.php yaitu class controller dengan satu method `index()`. Kamu dapat membuat method apapun di dalam controller, tapi hanya method `index()` yang akan melayani rekues GET. 

Method `index()` boleh memiliki parameter yang nantinya akan menangkap uri segment setelah segment halaman. Misalnya pada contoh di atas kita dapat memanggil dengan domain.com/home/Toni dimana string 'Toni' akan diterima oleh parameter `$name`.

Pada contoh di atas method `index()` mengembalikan balikan fungsi `pageView()`. Fungsi ini sama seperti `view()` di CodeIgniter, tapi sudah disesuaikan agar dapat menerima path dari file view yang ada di bawah folder app/Pages/. `return pageView('home/index', $data);` berarti mengembalikan file view app/Pages/**home/index**.php.

Selain method `index()` kamu juga dapat membuat method `process()` untuk menerima rekues POST ke url halaman yang dimaksud.

Ini adalah route yang didaftarkan otomatis yang membuat semua ini menjadi mungkin:
```php
$routeCollection->get($uriPage . '(:any)', $controllerNamespace . '::index$1');
$routeCollection->post($uriPage, $controllerNamespace . '::process');
```

#### API Endpoint

Kamu juga dapat menerima rekues RESTful dengan membuat class APIController.php.

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

APIController dipanggil di endpoint halaman dengan prefix 'api/'. Pada contoh kode di atas endpoint untuk halaman profile adalah domain.com/api/profile.

Class APIController diturunkan dari ResourceController bawaan CodeIgniter apa adanya sehingga kamu dapat merujuk ke dokumentasi CodeIgniter 4 untuk penggunaannya. Sistem otomatis membuat resource route untuk setiap folder halaman yang dibuat.

```php
$routeCollection->resource('api/' . $uriPage, ['controller' => $controllerNamespace]);

# Route di atas equivalen dengan
$routes->get('api/' . $uriPage, $controllerNamespace . '::index');
$routes->get('api/' . $uriPage . '/new', $controllerNamespace . '::new');
$routes->post('api/' . $uriPage, $controllerNamespace . '::create');
$routes->get('api/' . $uriPage . '/(:segment)', $controllerNamespace . '::show/$1');
$routes->get('api/' . $uriPage . '/(:segment)/edit', $controllerNamespace . '::edit/$1');
$routes->put('api/' . $uriPage . '/(:segment)', $controllerNamespace . '::update/$1');
$routes->patch('api/' . $uriPage . '/(:segment)', $controllerNamespace . '::update/$1');
$routes->delete('api/' . $uriPage . '/(:segment)', $controllerNamespace . '::delete/$1'); 
```

Dengan resource controller di atas, method-method yang dapa kamu buat untuk melayani setiap resource endpoint adalah `index()`, `new()`, `create()`, `show()`, `edit()`, `update()`, `update()`, dan `delete()`.

## Kontribusi
Kami menerima kontribusi dari komunitas! Jika Anda memiliki ide atau menemukan bug, silakan kirimkan pull request atau buka issue di repository ini.

## Lisensi
Sama seperti repository CodeIgniter 4, package ini dilisensikan di bawah lisensi MIT. Lihat file [LICENSE](LICENSE) untuk informasi lebih lanjut.