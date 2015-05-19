<?php

namespace Amp;

/**
 * A PrivatePromisor creates read-only Promise instances that can only be
 * resolved by holders of the PrivatePromisor instance. This creates an
 * additional layer of API protection beyond the PublicPromisor.
 */
trait PrivatePromisor {
    private $resolver;
    private $updater;
    private $promise;

    public function __construct() {
        $unresolved = new Unresolved;
        $resolver = function(\Exception $error = null, $result = null) {
            $this->resolve($error, $result); // bound to private Unresolved::resolve()
        };
        $updater = function($progress) {
            $this->update($progress); // bound to private Unresolved::update()
        };
        $this->resolver = $resolver->bindTo($unresolved, $unresolved);
        $this->updater = $updater->bindTo($unresolved, $unresolved);
        $this->promise = $unresolved;
    }

    /**
     * Promise future fulfillment of the returned placeholder value
     *
     * @return \Amp\Promise
     */
    public function promise() {
        return $this->promise;
    }

    /**
     * Update watchers of progress resolving the promised value
     *
     * @param mixed $progress
     * @return void
     */
    public function update($progress) {
        call_user_func($this->updater, $progress);
    }

    /**
     * Resolve the associated promise placeholder as a success
     *
     * @param mixed $result
     * @return void
     */
    public function succeed($result = null) {
        call_user_func($this->resolver, $error = null, $result);
    }

    /**
     * Resolve the associated promise placeholder as a failure
     *
     * @param \Exception $error
     * @return void
     */
    public function fail(\Exception $error) {
        call_user_func($this->resolver, $error, $result = null);
    }
}