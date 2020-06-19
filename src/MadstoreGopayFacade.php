<?php

namespace Madnest\MadstoreGopay;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Madnest\MadstoreGopay\Skeleton\SkeletonClass
 */
class MadstoreGopayFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'madstore-gopay';
    }
}
