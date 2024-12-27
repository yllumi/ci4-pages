<?php defined('BASEPATH') OR exit('No direct script access allowed');

use App\core\Frontend_Controller;
use App\modules\notifier\libraries\Sender;

class Test extends Frontend_Controller {

	public function index()
	{
		echo "Testing sender";

		// Telegram
		// $sender = new Sender('Telegram');
		// $res = $sender->sendText('233934050', 'Testing kirim pesan lagi bro, nuhunnn');
		// dd($res);

		// Wasender
		$sender = new Sender('WASender');
		// $sender->addToQueue();
		$res = $sender->sendText('628986818780', 'Testing kirim pesan pakai WASender tanggal '.date('d F Y H:i'));
		dd($res);

		// Woowa
		// $sender = new Sender('Woowa');
		// $res = $sender->sendText('628986818780', 'Test output');
		// dd($res);

		// Zenziva WhatsApp, Rp 250
		// $sender = new Sender('ZenzivaWA');
		// $res = $sender->sendText('628986818780', 'Testing kirim pesan ke WA dari Zenziva bro, nuhunnn');
		// dd($res);

		// Zenziva Voice Message, mahal, Rp 1200 per message
		// $sender = new Sender('ZenzivaVoice');
		// $res = $sender->sendText('08986818780', 'Halo Palupi, ada salam dari Santi. ');
		// dd($res);
	}
}