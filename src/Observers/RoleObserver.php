<?php namespace Znck\Trust\Observers;

class RoleObserver
{
    public function created() {
        trust()->roles(true);
    }

    public function updated() {
        trust()->roles(true);
    }

    public function deleted() {
        trust()->roles(true);
    }
}
