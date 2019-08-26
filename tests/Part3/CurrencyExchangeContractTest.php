<?php

namespace Tests\Part3;

use App\Currency;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class CurrencyExchangeContractTest extends TestCase
{
    use DatabaseMigrations, MockeryPHPUnitIntegration;

    protected $contract;

    protected $spy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contract = 'App\Contracts\CurrencyExchangeContract';
        $this->spy = $this->spy($this->contract);
    }

    /**
     * @test
     * It verifies that currency exchange contract has a binding in the service container
     * - Assert Contract has a binding in the service container
     */
    public function it_tests_that_contract_is_defined_and_has_binding_in_the_service_container()
    {
        $this->assertTrue(
            app()->bound($this->contract)
        );
    }

    /** @test
     * It verifies that contract is NOT called on index method of controller
     * It verifies that index() method of controller doesn't fetch the currencies from external API
     * Beacuse that is the work of the contract method: bootstrapCurrencies()
     */
    public function it_tests_that_contract_is_not_called_on_index_method_of_controller()
    {
        $this->get('/api/v1/currencies');

        $this->spy
             ->shouldNotHaveReceived()
             ->listCurrencies();
    }

    /** @test
     * It verifies that contract method: bootstrapCurrencies() called on command
     * bootstrapCurrencies() will fetch currencies from external API and persist to DB
     */
    public function it_tests_that_contract_is_called_on_command_which_seeds_currencies()
    {
        $this->artisan('currencies:bootstrap')
            ->assertExitCode(0);

        $this->spy
            ->shouldHaveReceived()
            ->listCurrencies()
            ->once();
    }

    /**
     * @test
     * It verifies that contract is called on show($code) method of controller
     * It is called to fetch the exchange rate against USD, from external API
     */
    public function it_tests_that_contract_is_called_on_show_method_of_controller_to_fetch_exchange_rate()
    { 
        $currency = Currency::create([
            'code' => 'ILS',
            'symbol' => '₪'
        ]);

        $currency->translations()
            ->create([
                'locale' => 'en',
                'translation_text' => 'Israeli New Sheqels'
            ]);

        $this->get('/api/v1/currencies/' . $currency->code)
            ->assertOk()
            ->assertJson([
                'code' => $currency->code,
                'symbol' => $currency->symbol,
                'rate' => $currency->fresh()->rate
            ]);
        
        $this->spy
             ->shouldHaveReceived()
             ->rate($currency->code)
             ->once();
        
    }
}   
