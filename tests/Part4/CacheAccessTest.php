<?php

namespace Tests\Part4;

use App\Currency;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CacheAccessTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * Assertions on Cache Access for show() method of CurrenciesController
     *   - Assertion that Cache is accessed.
     *   - Assertion that Cache has correct rate value.
     */
    public function it_verifies_that_cache_is_accessed_on_show_method_of_controller_and_has_correct_value()
    {
        /** 
         * Clear Cache to Isolate Test
         */
        cache()->flush();

        $currency = Currency::create([
            'code' => 'ILS',
            'symbol' => '₪'
        ]);

        $currency->translations()
            ->create([
                'locale' => 'en',
                'translation_text' => 'Israeli New Sheqels'
            ]);

        $code = $currency->code;
        $key = 'USD_' . $code;

        /**
         * Validation/Assertions on Response
         */
        $this->get('/api/v1/currencies/' . $code)
             ->assertOk()
             ->assertJson([
                 'code' => $currency->code,
                 'symbol' => $currency->symbol,
                 'rate' => cache()->get($key)
             ]);
        
        /** 
         * Assert Cache has been accessed 
         * Assert Cache has key: USD_<code>
        */
        $this->assertTrue(
            cache()->has($key)
        );
        
        /**
         * Assert Cache value is equal to rate of currency fetched
         */
        $this->assertEquals(
            $currency->fresh()->rate,
            cache()->get($key)
        );
    }
}