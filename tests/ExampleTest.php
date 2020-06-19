<?php

namespace Madnest\MadstoreGopay\Tests;

use Orchestra\Testbench\TestCase;
use Madnest\MadstoreGopay\MadstoreGopayServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [MadstoreGopayServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
