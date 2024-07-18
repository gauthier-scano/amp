<?php declare(strict_types=1);

namespace Amp;

use Revolt\EventLoop;

/**
 * This object invokes the given callback within a new coroutine every $interval seconds until either the
 * {@see self::disable()} method is called or the object is destroyed.
 */
final class Interval
{
    private readonly string $watcher;

    /**
     * @param float $interval Invoke the function every $interval seconds.
     * @param \Closure():void $closure Use {@see weakClosure()} to avoid a circular reference if storing this object
     *      as a property of another object.
     * @param bool $reference If false, unreference the underlying watcher.
     */
    public function __construct(float $interval, \Closure $closure, bool $reference = true)
    {
        $this->watcher = EventLoop::repeat($interval, $closure);

        if (!$reference) {
            EventLoop::unreference($this->watcher);
        }
    }

    public function __destruct()
    {
        EventLoop::cancel($this->watcher);
    }

    /**
     * @return bool True if the internal watcher is referenced.
     */
    public function isReferenced(): bool
    {
        return EventLoop::isReferenced($this->watcher);
    }

    /**
     * References the internal watcher in the event loop, keeping the loop running while the repeat loop is enabled.
     *
     * @return $this
     */
    public function reference(): self
    {
        EventLoop::reference($this->watcher);

        return $this;
    }

    /**
     * Unreferences the internal watcher in the event loop, allowing the loop to stop while the repeat loop is enabled.
     *
     * @return $this
     */
    public function unreference(): self
    {
        EventLoop::unreference($this->watcher);

        return $this;
    }

    /**
     * @return bool True if the repeating timer is enabled.
     */
    public function isEnabled(): bool
    {
        return EventLoop::isEnabled($this->watcher);
    }

    /**
     * Restart the repeating timer if previously stopped with {@see self::disable()}.
     *
     * @return $this
     */
    public function enable(): self
    {
        EventLoop::enable($this->watcher);

        return $this;
    }

    /**
     * Stop the repeating timer. Restart it with {@see self::enable()}.
     *
     * @return $this
     */
    public function disable(): self
    {
        EventLoop::disable($this->watcher);

        return $this;
    }
}
