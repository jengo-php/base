<?php

use CodeIgniter\Events\Events;
use Jengo\Base\Vite\Repositories\ViteRepository;

Events::on('post_controller_constructor', static function () {
    (new ViteRepository())->scan();
});