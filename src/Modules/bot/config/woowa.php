<?php

$config["enable_woowa"] 	= setting_item('bot.enable_woowa') == 'on' ? true : false;

// CP
$config["woowa_license"] 	= setting_item('bot.woowa_license') ? setting_item('bot.woowa_license') : "5d407c6f0673e";
$config["woowa_ip"] 		= setting_item('bot.woowa_ip') ? setting_item('bot.woowa_ip') : "http://116.203.191.58";
$config["woowa_key"] 		= setting_item('bot.woowa_key') ? setting_item('bot.woowa_key') : "b6b7a5de687bbdf9ecd9fa26a9745897f2109c4173d7881d";
$config["woowa_device_id"] 	= setting_item('bot.woowa_device_id') ? setting_item('bot.woowa_device_id') : "16143279733yl5tzfdxjb5wq8k898x8m";

$config['message_confirm_register'] = [
	'Konfirmasi pendaftaran {title} dengan mengklik tautan berikut {token}',
	'Silakan aktifkan akunmu di {title} dengan mengklik tautan ini {token}',
	'Satu langkah lagi, klik tautan berikut untuk mengaktifkan akun {title}: {token}',
	'Aktifkan akun {title} dengan membuka tautan ini {token}',
	'Terima kasih sudah mendaftar, aktifkan akun {title} dengan membuka tautan ini {token}',
	'Pendaftaran akun {title} berhasil. Silakan aktifkan akun dengan membuka tautan ini {token}',
];

$config['message_confirm_register_otp'] = [
	'Masukkan kode berikut untuk mengkonfirmasi pendaftaran {title}: {token}',
	'Silakan aktifkan akun {title} Anda dengan memasukkan kode berikut di halaman konfirmasi: {token}',
	'Satu langkah lagi, masukkan kode berikut untuk mengaktifkan akun {title}: {token}',
	'Aktifkan akun {title} dengan memasukkan kode berikut ini: {token}',
	'Terima kasih sudah mendaftar, aktifkan akun {title} dengan memasukkan kode ini: {token}',
	'Pendaftaran akun {title} berhasil. Silakan aktifkan akun dengan memasukkan kode berikut: {token}',
];

$config['message_reset_password'] = [
	'Anda telah mengirimkan rekues penggantian kata sandi, silakan klik tautan ini untuk melanjutkan \n\n{url}',
	'Silakan buka tautan berikut untuk mengatur ulang kata sandi \n\n{url}',
	'Klik tautan tautan berikut untuk mengganti kata sandi \n\n{url}',
	'Permintaan reset kata sandi diterima. Silakan Klik tautan tautan berikut \n\n{url}',
];

$config['confirm_pending'] = [
	"Hai kak *{name}*,\n\nMau konfirmasi, pesanan *{product}* sudah kami catat dengan kode pesanan *#{code}*.\n\nSelanjutnya silakan transfer sejumlah *Rp {price}* ke {account}, pastikan angka transfer sama persis untuk memudahkan verifikasi.\n\nPembayaran paling telat 2 jam dari sekarang kak, pada pukul {expired}. Lewat dari itu maka pesanan akan expired dan kakak perlu melakukan pemesanan ulang.\n\nJangan lupa save nomor ini ya! dengan nama kontak CS Codepolitan biar kamu mudah menghubungi kami.\n\nTerima kasih."
];

$config['confirm_pending_midtrans'] = [
	"Hai kak *{name}*,\n\nMau konfirmasi, pesanan *{product}* sudah kami catat dengan kode pesanan *#{code}*.\n\nSelanjutnya silakan cek inbox email. Kami sudah mengirimkan panduan pembayaran.\n\nPembayaran paling telat 2 jam dari sekarang kak, pada pukul {expired}. Lewat dari itu maka pesanan akan expired dan kakak perlu melakukan pemesanan ulang.\n\nJangan lupa save nomor ini ya! dengan nama kontak CS Codepolitan biar kamu mudah menghubungi kami.\n\nTerima kasih."
];

$config['confirm_settlement'] = [
	"Terima kasih Kak *{name}*, pembayaran sejumlah *Rp {price}* untuk pemesanan dengan kode *#{code}* sudah kami terima"
];

$config['confirm_process'] = [
	"Kak {name}, pesanan produk *{product}* dengan kode *#{code}* sudah kami aktifkan. Selamat belajar Kak!\n\nTerima kasih"
];

$config['confirm_followup'] = [
	"Halo Kak *{name}*,\n\nMau mengingatkan terkait pembayaran untuk pesanan dengan kode *#{code}*. Status pemesanan akan expired pada pukul {expired}. Nanti transfernya dengan nominal yang sama persis ya untuk memudahkan verifikasi.\n\nTerima kasih"
];

$config['confirm_expired'] = [
	"Kak *{name}*, gimana kabarnya? mau mengingatkan, sebentar lagi layanan membership kakak sudah mau expired nih, tepatnya pada tanggal *{expired}*. Sebaiknya diperpanjang lagi agar belajarnya lebih nyaman kak.\n\nKunjungi halaman ini untuk memperpanjang kak https://www.codepolitan.com/membership/renew"
];

$config['author_forum_notif'] = [
	"Hai kak *{author}*, ada yang nanya di forum Codepolitan nih.\n\nDari *{user}*, menanyakan *{subject}*\n\nKlik untuk menjawab {url}"
];

$config['forum_unanswered_notif'] = [
	"Hai kakak semuanya, ada *{jumlah}* pertanyaan belum dijawab di forum nih. Segera jawabin dong, kasihan.\n\nKlik untuk menjawab https://www.codepolitan.com/forum"
];
