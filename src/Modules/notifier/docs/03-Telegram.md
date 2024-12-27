# Menggunakan Telegram Bot

Telegram Bot di MeinCMS menggunakan library Telegram Bot API dari https://telegram-bot-sdk.readme.io/docs.

Untuk mengirim notifikasi ke Telegram via Telegram bot, gunakan library `bot/telegrambot.php`.

```php
$this->load->library('bot/telegrambot');
$payload['message'] = 'Halo kak, ini gambarnya';
$payload['photo'] = 'https://www.codepolitan.com/themes/belajarcoding/assets/img/codepolitan_logo.png';

// Panggil method setAsync($priority = 9, $expire_after = null) untuk menyimpan ke antrian
// Jangan panggil ini kalo mau send langsung
$this->telegrambot->setAsync(5, 300);

// Send ke grup, nama grup terdaftar di config telegram.php
// groups: 'group_premium','group_notif','group_sales','public_channel'
$response = $this->telegrambot->sendMessageToGroup('group_premium', $payload);

// Send ke user
$user_id = $this->session->user_id;
$response = $this->telegrambot->sendMessageToUser($user_id, $payload);
```
Untuk data keperluan pengiriman, field yang wajib adalah 'message'. Adapun field 'photo' bersifat opsional hanya bila kamu perlu mengirimkan pesan dengan menyertakan gambar. Selain 'photo', Kamu juga bisa menambahkan opsi lain yang disediakan oleh [Telegram Bot API](https://core.telegram.org/bots/api) ke dalam payload seperti 'parse_mode', 'disable_web_page_preview' dsb.

## Parsing Data

Kita juga bisa mengirimkan pesan template beserta data replacementnya, dengan mengeset data 'message' sebagai array seperti ini:

```php
// Index 0 untuk template pesannya, dan index 1 untuk array data yang akan diparsing
// PERHATIAN: tag template harus sama jumlahnya dengan data yang disuplai!
$payload['message'] = [
	'Halo kak {name}, ini lokasi eventnya: {address}',
	[
		'name' => 'Toni',
		'address' => 'Padalarang'
	]
];
```