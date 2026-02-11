<?php

declare(strict_types=1);

namespace Tests\Unit\Commands;

use CodeIgniter\Config\Factories;
use Jengo\Base\Vite\Config\ViteConfig;
use Tests\Support\CommandTestCase;

final class ViteConfigCOmmandTest extends CommandTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $config = new ViteConfig();

        $config->entrypoints = [
            'app.css',
            'user/book.ts',
            'main.ts'
        ];

        Factories::injectMock('config', ViteConfig::class, $config);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        var_dump($this->output);
    }

    public function test(): void
    {
        command('vite:config');
    }
}
