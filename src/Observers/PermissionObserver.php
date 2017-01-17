<?php

namespace Znck\Trust\Observers;

class PermissionObserver
{
    public function created()
    {
        trust()->permissions(true);
    }

    public function updated()
    {
        trust()->permissions(true);
    }

    public function deleted()
    {
        trust()->permissions(false);
    }
}
