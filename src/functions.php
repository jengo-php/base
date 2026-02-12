<?php

namespace Jengo\Base;

use Jengo\Base\Vite\Repositories\ViteRepository;
use mindplay\vite\Manifest;

function vite_tags()
{
    helper('Jengo\Base\Helpers\jengo');

    $vite_server_url = trim(env('VITE_DEV_SERVER'), "/") . "/";

    $vite = new Manifest(
        isDevelopment(),
        FCPATH . "dist/.vite/manifest.json",
        isDevelopment() ? $vite_server_url : base_url("dist/")
    );

    $entrypoints = (new ViteRepository())->getFullConfig()->entrypoints;

    $tags = $vite->createTags(...$entrypoints);

    return $tags->preload . PHP_EOL
        . $tags->css . PHP_EOL
        . $tags->js . PHP_EOL;
}