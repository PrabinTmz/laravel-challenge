<?php

namespace Tests\Part1;

use Tests\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CurrencyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * Check for Currency Model Existence
     */
    public function it_checks_for_currency_model_existence()
    {
        $className = 'App\Currency';
        $currency = App::make($className);

        $this->assertNotNull($currency);
        $this->assertInstanceOf($className, $currency);
        $this->assertEquals($className, get_class($currency));
    }
}
