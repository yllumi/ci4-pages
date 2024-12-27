<?php

class SettingSeeder extends App\libraries\Seeder
{
	public $table = 'mein_options';

    public function run()
    {
        $this->db->truncate($this->table);

        $this->db->query(
        "INSERT INTO `mein_options` (`id`, `option_group`, `option_name`, `option_value`) VALUES
        (1087,  'site', 'site_title',   'HeroicBit'),
        (1088,  'site', 'site_desc',    'Page based and headless CMS'),
        (1089,  'site', 'site_logo',    ''),
        (1090,  'site', 'site_logo_small',  ''),
        (1091,  'site', 'login_cover',  ''),
        (1092,  'site', 'phone',    '087813277822'),
        (1093,  'site', 'address',  ''),
        (1094,  'site', 'currency', 'Rp'),
        (1095,  'site', 'enable_registration',  'on'),
        (1096,  'emailer',  'use_mailcatcher',  'yes'),
        (1097,  'emailer',  'smtp_host',    'in-v3.mailjet.com'),
        (1098,  'emailer',  'smtp_port',    '587'),
        (1099,  'emailer',  'smtp_username',    '8443024b25c98692c6e2647372c7be5f'),
        (1100,  'emailer',  'smtp_password',    '16ff075e5918803087bd3eb1d1f21b02'),
        (1101,  'emailer',  'email_from',   'contact@heroicbit.id'),
        (1102,  'emailer',  'sender_name',  'HeroicBit'),
        (1103,  'theme',    'homepage', 'home'),
        (1104,  'theme',    'admin_logo_bg',    '3A4651'),
        (1105,  'theme',    'navbar_color', 'FFFFFF'),
        (1106,  'theme',    'navbar_text_color',    '333333'),
        (1107,  'theme',    'link_color',   'E84A94'),
        (1108,  'theme',    'btn_primary',  '007BFF'),
        (1109,  'theme',    'btn_secondary',    '6C757D'),
        (1110,  'theme',    'btn_success',  '28A745'),
        (1111,  'theme',    'btn_info', '138496'),
        (1112,  'theme',    'btn_warning',  'E0A800'),
        (1113,  'theme',    'btn_danger',   'DC3545'),
        (1114,  'theme',    'admin_color',  'blue'),
        (1115,  'theme',    'facebook_pixel_code',  ''),
        (1116,  'theme',    'gtag_id',  ''),
        (1117,  'post', 'posttype_config',  'page:\r\n    label: Pages\r\n    entry: mein_post_page\r\nevent:\r\n    label: Events\r\n    entry: mein_post_event\r\n'),
        (1118,  'user', 'confirmation_type',    'link'),
        (1119,  'user', 'use_single_login', 'yes'),
        (1120,  'user', 'recaptcha_site_key',   ''),
        (1121,  'user', 'recaptcha_secret_key', ''),
        (1122,  'dashboard',    'maintenance_mode', 'off'),
        (1123,  'course',   'enable',   'off'),
        (1124,  'payment',  'before_order_expired', '30'),
        (1125,  'payment',  'order_expired',    '120'),
        (1126,  'payment',  'moota_webhook_secret_token',   ''),
        (1127,  'payment',  'transfer_bank_options',    ''),
        (1128,  'payment',  'transfer_destinations',    'BCA|1234567879|PT. HeroicBit'),
        (1129,  'payment',  'last_unique_number',   '158'),
        (1130,  'payment',  'static_origin_id', '115'),
        (1131,  'payment',  'flat_shipping_fee',    ''),
        (1132,  'payment',  'calculate_transaction_fee',    'no'),
        (1133,  'payment',  'calculate_tax',    'yes'),
        (1134,  'payment',  'active_payment_gateway',   'transfer'),
        (1135,  'payment',  'midtrans_secret_key',  ''),
        (1136,  'payment',  'xendit_secret_key',    ''),
        (1137,  'payment',  'xendit_callback_token',    ''),
        (1138,  'product',  'enable',   'off'),
        (1139,  'product',  'remind_expired',   '3'),
        (1140,  'membership',   'enable',   'off'),
        (1141,  'bot',  'enable_woowa', 'off'),
        (1142,  'bot',  'enable_woowa_async',   'off'),
        (1143,  'bot',  'woowa_license',    ''),
        (1144,  'bot',  'woowa_ip', ''),
        (1145,  'bot',  'woowa_key',    ''),
        (1146,  'bot',  'woowa_device_id',  ''),
        (1147,  'downloadable', 'enable',   'off'),
        (1148,  'sample',   'enable',   'off'),
        (1149,  'sample',   'title',    '');");
    }
}