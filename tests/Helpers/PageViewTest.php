<?php

declare(strict_types=1);

/**
 * This file is part of yllumi/ci4-pages.
 *
 * (c) 2024 Toni Haryanto <toha.samba@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Yllumi\Ci4Pages\Tests\Helpers;

use PHPUnit\Framework\TestCase;

final class PageViewTest extends TestCase
{
    public function testPageView(): void
    {
        helper('Yllumi\Ci4Pages\Helpers\pageview');

        @mkdir(VENDORPATH . 'codeigniter4/framework/app/Pages');
        file_put_contents(VENDORPATH . 'codeigniter4/framework/app/Pages/home.php', '<?php echo "Hello, $name!"; ?>');

        $output = pageView('home', ['name' => 'John Doe']);

        $this->assertStringContainsString('Hello, John Doe!', $output);

        @rmdir(VENDORPATH . 'codeigniter4/framework/app/Pages');
    }
}
