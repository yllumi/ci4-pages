<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Heroic\Libraries;

use CodeIgniter\Autoloader\FileLocatorInterface;
use CodeIgniter\Debug\Toolbar\Collectors\Views;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\View\Exceptions\ViewException;
use CodeIgniter\View\View;
use CodeIgniter\View\ViewDecoratorTrait;
use Config\Toolbar;
use Config\View as ViewConfig;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Class View
 *
 * @see \CodeIgniter\View\ViewTest
 */
class PageRenderer extends View
{
    /**
     * Builds the output based upon a file name and any
     * data that has already been set.
     *
     * Valid $options:
     *  - cache      Number of seconds to cache for
     *  - cache_name Name to use for cache
     *
     * @param string                    $view     File name of the view source
     * @param array<string, mixed>|null $options  Reserved for 3rd-party uses since
     *                                            it might be needed to pass additional info
     *                                            to other template engines.
     * @param bool|null                 $saveData If true, saves data for subsequent calls,
     *                                            if false, cleans the data after displaying,
     *                                            if null, uses the config setting.
     */
    public function render(string $view, ?array $options = null, ?bool $saveData = null): string
    {
        $this->renderVars['start'] = microtime(true);

        // Store the results here so even if
        // multiple views are called in a view, it won't
        // clean it unless we mean it to.
        $saveData ??= $this->saveData;

        $fileExt = pathinfo($view, PATHINFO_EXTENSION);
        // allow Views as .html, .tpl, etc (from CI3)
        $this->renderVars['view'] = ($fileExt === '') ? $view . '.php' : $view;

        $this->renderVars['options'] = $options ?? [];

        // Was it cached?
        if (isset($this->renderVars['options']['cache'])) {
            $cacheName = $this->renderVars['options']['cache_name']
                ?? str_replace('.php', '', $this->renderVars['view']);
            $cacheName = str_replace(['\\', '/'], '', $cacheName);

            $this->renderVars['cacheName'] = $cacheName;

            if ($output = cache($this->renderVars['cacheName'])) {
                $this->logPerformance(
                    $this->renderVars['start'],
                    microtime(true),
                    $this->renderVars['view']
                );

                return $output;
            }
        }

        $this->renderVars['file'] = $this->viewPath . $this->renderVars['view'];

        if (! is_file($this->renderVars['file'])) {
            $this->renderVars['file'] = $this->loader->locateFile(
                $this->renderVars['view'],
                'Views',
                ($fileExt === '') ? 'php' : $fileExt
            );
        }

        // locateFile() will return false if the file cannot be found.
        if ($this->renderVars['file'] === false) {
            throw ViewException::forInvalidFile($this->renderVars['view']);
        }

        // Make our view data available to the view.
        $this->prepareTemplateData($saveData);

        // Save current vars
        $renderVars = $this->renderVars;

        $output = (function (): string {
            extract($this->tempData);
            ob_start();
            include $this->renderVars['file'];

            return ob_get_clean() ?: '';
        })();

        // Get back current vars
        $this->renderVars = $renderVars;

        // When using layouts, the data has already been stored
        // in $this->sections, and no other valid output
        // is allowed in $output so we'll overwrite it.
        if ($this->layout !== null && $this->sectionStack === []) {
            $layoutView   = $this->layout;
            $this->layout = null;
            // Save current vars
            $renderVars = $this->renderVars;
            $output     = $this->render($layoutView, $options, $saveData);
            // Get back current vars
            $this->renderVars = $renderVars;
        }

        $output = $this->decorateOutput($output);

        $this->logPerformance(
            $this->renderVars['start'],
            microtime(true),
            $this->renderVars['view']
        );

        // Check if DebugToolbar is enabled.
        $filters              = service('filters');
        $requiredAfterFilters = $filters->getRequiredFilters('after')[0];
        if (in_array('toolbar', $requiredAfterFilters, true)) {
            $debugBarEnabled = true;
        } else {
            $afterFilters    = $filters->getFiltersClass()['after'];
            $debugBarEnabled = in_array(DebugToolbar::class, $afterFilters, true);
        }

        if (
            $this->debug && $debugBarEnabled
            && (! isset($options['debug']) || $options['debug'] === true)
        ) {
            $toolbarCollectors = config(Toolbar::class)->collectors;

            if (in_array(Views::class, $toolbarCollectors, true)) {
                // Clean up our path names to make them a little cleaner
                $this->renderVars['file'] = clean_path($this->renderVars['file']);
                $this->renderVars['file'] = ++$this->viewsCount . ' ' . $this->renderVars['file'];

                $output = '<!-- DEBUG-VIEW START ' . $this->renderVars['file'] . ' -->' . PHP_EOL
                    . $output . PHP_EOL
                    . '<!-- DEBUG-VIEW ENDED ' . $this->renderVars['file'] . ' -->' . PHP_EOL;
            }
        }

        // Should we cache?
        if (isset($this->renderVars['options']['cache'])) {
            cache()->save(
                $this->renderVars['cacheName'],
                $output,
                (int) $this->renderVars['options']['cache']
            );
        }

        $this->tempData = null;

        return $output;
    }

}
