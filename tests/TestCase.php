<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Stati\Site\Site;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var Site
     */
    protected $site;

    public function setUp()
    {
        $this->site = new Site([
            'permalink' => '/:year/',
            'includes_dir' => './',
            'layouts_dir' => __DIR__ . '/fixtures/post_test'
        ]);
    }
}
