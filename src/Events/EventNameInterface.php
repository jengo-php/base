<?php

declare(strict_types=1);

namespace Jengo\Base\Events;

/**
 * Interface to enforce the presence and type of the event NAME constant.
 */
interface EventNameInterface
{
    /**
     * @var string The unique, application-wide name for this event.
     */
    public const NAME = '';
}