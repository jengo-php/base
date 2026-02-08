<?php

declare(strict_types=1);

namespace Jengo\Base\Events;

use BadMethodCallException;

/**
 * Abstract class to enforce the structure for all application events.
 * Concrete event classes must extend this class and override the static event() method
 * and the NAME constant.
 */
abstract class AbstractEvent implements EventNameInterface
{
    /**
     * The unique, application-wide name for this event (required by EventNameInterface).
     * * IMPORTANT: This value MUST be overridden in the concrete class.
     */
    public const NAME = 'abstract.event.name';

    /**
     * The method that is executed when this event is triggered.
     * * IMPORTANT: Concrete classes MUST implement this method with the static keyword.
     * This method accepts any number of arguments, which represent the data passed to the event.
     *
     * @param mixed ...$args The data payload associated with the event.
     */
    public static function event(mixed ...$args): void
    {
        // This default implementation ensures that if a class extends AbstractEvent 
        // but fails to implement the static event() method, an informative error is thrown.
        throw new BadMethodCallException(
            sprintf("The static 'event' method must be implemented in the concrete class %s.", static::class)
        );
    }
}