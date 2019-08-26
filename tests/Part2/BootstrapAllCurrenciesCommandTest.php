<?php

namespace Tests\Part2;

use App\Currency;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BootstrapAllCurrenciesCommandTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @test
     * It verifies that customr artisan command currencies:boostrap exists
     * Assert Command Exists
     */
    public function it_verifies_that_boostrap_all_currencies_command_exists()
    {
        /**
         * Assert Artisan Command Created;
         * use Artisan::all() to return array of defined artisan commands
         * Assert this array contains the new currencies:bootstrap command
         */
        $command = 'currencies:bootstrap';
        $this->assertTrue(
            array_has(Artisan::all(), $command)
        );
    }

    /**
     * @test
     * Run the command currencies:bootstrap
     * Verifies the ouput of running the command
     * Assert Running Command has common currencies returned from External API
     * Assuming Common Currencies are: USD , JPY (Yen), AUD(Australian Dollars), EUR (Euro)
     * GBP (Britain's Pounds) , ILS (Shekel), JOD (Jordanian Dinars)
     */
    public function it_verifies_that_running_command_has_common_currencies()
    {
        $this->artisan('currencies:bootstrap');

        $commonCurrenciesCodes = ['USD', 'JPY', 'AUD', 'EUR', 'GBP', 'ILS', 'JOD'];

        $existingCodes = Currency::whereIn('code', $commonCurrenciesCodes)
                                ->get();

        $this->assertEquals(
            count($commonCurrenciesCodes),
            $existingCodes->count()
        );
    }
}
