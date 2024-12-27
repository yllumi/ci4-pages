<?php

class NavigationSeeder extends App\libraries\Seeder
{
    public function run()
    {
        $this->db->query("INSERT INTO `mein_navigations` (`id`, `area_id`, `caption`, `url`, `url_type`, `target`, `status`, `icon`, `nav_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
            (1,	2,	'Updates',	'dashboard',	'uri',	'_self',	'publish',	'bi bi-house',	1,	'2022-06-24 04:46:24',	NULL,	NULL),
            (2,	2,	'Learn',	'courses',	'uri',	'_self',	'publish',	'bi bi-book',	2,	'2022-06-24 04:46:45',	NULL,	NULL),
            (3,	2,	'Products',	'catalog',	'uri',	'_self',	'publish',	'bi bi-box',	3,	'2022-06-24 04:47:11',	NULL,	NULL),
            (4,	2,	'Earning',	'earnings',	'uri',	'_self',	'publish',	'bi bi-wallet2',	4,	'2022-06-24 05:28:10',	NULL,	NULL),
            (5,	2,	'Account',	'profile',	'uri',	'_self',	'publish',	'bi bi-person',	5,	'2022-06-24 05:28:49',	NULL,	NULL),
            (6,	1,	'Courses',	'admin/course',	'uri',	'_self',	'publish',	'fa fa-book',	1,	'2022-06-24 05:31:47',	NULL,	NULL);");

        $this->db->query("INSERT INTO `mein_navigation_areas` (`id`, `area_name`, `area_slug`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
            (1,	'dashboard',	'dashboard',	'publish',	'2022-06-24 04:45:49',	NULL,	NULL),
            (2,	'frontend',	'frontend',	'publish',	'2022-06-24 04:45:59',	NULL,	NULL);");
    }
}