# Mengirim pesan ke WhatsApp

Mengirim pesan ke nomor WhatsApp sekarang sudah bisa, berkat dukungan dari Woowa. Untuk mengirim pesan, gunakan library `Woowa` yang ada di module `bot`.

```php
$this->load->library('bot/Woowa');

$data = [
	'message' => 'Bismillah, halo kak',
];
$result = $this->woowa->sendMessage('628986818780', $data);

print_code($result);
```

## Passing Data

Untuk mengirim pesan dengan template, gunakan bentuk array seperti ini. Index 0 dari 'message' untuk template pesan, dan index 1 untuk data yang akan dipassing. PERHATIAN: tag tamplate dan data yang dipassing harus sama jumlahnya.

```php
$this->load->library('bot/Woowa');

$data = [
    'message' => [
        'Bismillah, halo kak {name}',
        ['name' => 'Toni']
    ]
];
$result = $this->woowa->sendMessage('628986818780', $data);

print_code($result);
```


## Mengirim ke User

Untuk mengirim pesan langsung ke user, gunakan method `sendToUser`.

```php
$this->load->library('bot/Woowa');

$data = [
	'message' => [
        'Bismillah, halo kak {name}',
    	['name' => $user['name']]
    ]
];
$result = $this->woowa->sendToUser($user['id'], $data);

print_code($result);
```

## Menyimpan ke Antrian

Panggil method setAsync($priority = 9, $expire_after = null) sebelum mengirim pesan untuk menyimpan ke antrian. Jangan panggil ini kalo mau send langsung.

```php
$this->woowa->setAsync(5, 300);
$result = $this->woowa->sendMessage('628986818780', $data);
```

Parameter `$expire_after` digunakan untuk mengatur rentang waktu sebelum kadaluwarsa dari job. By default untuk Woowa kita set 10 menit. Artinya bila setelah durasi tersebut job masih gagal dieksekusi, dia akan diabaikan.

## Mengirim Gambar

Untuk mengirim gambar, cukup tambahkan index 'imgurl' pada data. Berlaku untuk sendToUser() maupun sendMessage().

```php
$data = [
	'photo' => 'https://www.codepolitan.com/sites/codepolitan/themes/belajarcoding/assets/img/codepolitan_logo.png',
	'message' => 'Gimana gambar yang ini?'
];
```

## Kemungkinan Response

Hasil dari pemanggilan kedua method di atas diantaranya:

```
// Bila pengiriman sukses
Array
(
    [status] => success
    [message] => Success
)

// Bila nomor WhatsApp tidak aktif
Array
(
    [status] => failed
    [message] => [20190919191909] Number not found
)

// Bila field phone di profil user masih kosong
Array
(
    [status] => failed
    [message] => User belum menginput nomor telepon.
)
// Bila layanan Woowa tidak aktif/expired
Array
(
    [status] => failed
    [message] => 
403 Forbidden
Forbidden
You don't have the permission to access the requested resource. It is either read-protected or not readable by the server.
)
```

## Mengecek Woowa Aktif

Adakalanya dalam sebuah project kita tidak ingin mengirim pesan WhatsApp otomatis ke user. Di file config mein/modules/bot/config/woowa.php kita bisa mengatur apakah akan menggunakan fitur woowa atau tidak. Kamu dapat mengatur langsung di dalam file config tsb atau melalui environment variable `ENABLE_WOOWA`. By default kita set false.

```php
$config["enable_woowa"] = $_ENV['ENABLE_WOOWA'] ?? false;
```