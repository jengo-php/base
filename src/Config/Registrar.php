<?php

declare(strict_types=1);

namespace Jengo\Base\Config;

class Registrar
{
    public static function Generators(): array
    {
        return [
            'views' => [
                'make:repo' => 'Jengo\Base\Commands\Generators\Views\repo.tpl.php',
                'make:page' => 'Jengo\Base\Commands\Generators\Views\page.tpl.php',
                'make:layout' => [
                    'main' => 'Jengo\Base\Commands\Generators\Views\Layouts\layout.tpl.php',
                    'base' => 'Jengo\Base\Commands\Generators\Views\Layouts\base.tpl.php'
                ],
            ]
        ];
    }
}
