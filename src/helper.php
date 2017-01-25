<?php

if (! function_exists('trust')) {
    /**
     * @param $user
     *
     * @return \Znck\Trust\Trust
     */
    function trust($user = null)
    {
        $trust = app(\Znck\Trust\Trust::class);

        if (! is_null($user)) {
            $trust->setUser($user);
        }

        return $trust;
    }
}
