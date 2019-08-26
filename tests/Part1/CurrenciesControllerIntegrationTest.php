<?php

namespace Tests\Part1;

use App\Currency;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CurrenciesControllerIntegrationTest extends TestCase
{
    use DatabaseMigrations;

    protected $currency;

    protected $enLocaleTranslation;


    protected function setUp(): void
    {
        parent::setUp();

        /*
        Persist an instance of Currency to the testing DB (sqlite)
        this instance will be available for all methods through the test class
        */
        $this->currency = Currency::create([
            'symbol' => '$',
            'code' => 'USD',
            'rate' => 1
        ]);

        /**
         * Create a record in currency_translations table
         * locale = en - DEFAULT
         * this record will hold the default name for the persisted currency
         */
        $this->enLocaleTranslation = $this->currency
            ->translations()
            ->create([
                'locale' => 'en',
                'translation_text' => 'United States Dollars'
            ]);
    }

    /**
     * @test
     * Fetch all currencies from the databse
     * HTTP Request: GET /api/v1/currencies
     * Assert Status Code 200 - OK 
     * Assert Response to have collection type data:  Illuminate\Database\Eloquent\Collection 
     * Assert each instance in the collection to be of class:  App\Currency
     * Assert each instance have predefined attributes with values
     */
    public function it_fetches_all_currencies()
    {
        /**
         * Perofrm an HTTP GET Request
         * URI: /api/v1/currencies
         */
        $response = $this->get('/api/v1/currencies');

        $response->assertOk();

        $currencyAsAssocArray = $this->currency->toArray();

        $currencies = json_decode($response->getContent(), true)['data'];

        $this->assertCount(1, $currencies);
        $this->assertIsArray($currencies);
        $this->assertContains($currencyAsAssocArray, $currencies);
    }

    /**
     * @test
     * Fetch a specific currency from the DB provided its code
     * HTTP Request: GET /api/v1/currencies/{code}
     * Response Contains an instance of type App\Currency with the code provided 
     * Response Status Should be 200 (OK)
     */
    public function it_fetches_a_specific_currency_by_code()
    {
        //Perform an HTTP Get Request to /api/v1/currencies/{code}
        //With code = USD
        $code = $this->currency->code;
        $response = $this->get("/api/v1/currencies/{$code}");

        $response->assertOk()
            ->assertJson([
                'symbol' => $this->currency->symbol,
                'code' => $this->currency->code,
                'rate' => $this->currency->rate
            ]);

        /*
        The currency's name will be fetched from currency_translations table
        at least one record with locale=en will hold the default name for the Currency 
        */
        $this->assertEquals(
            $this->currency->translatedName,
            $this->enLocaleTranslation->translation_text
         );
    }

    /** @test
     * GET /api/vi/currencies/{code}
     * Nonexisting Currency code
     * Assert Response Status Code is 404 - Not founc 
     */
    public function it_fails_with_404_code_when_fetching_nonexisting_currency()
    {
        /* 
        * Try to fetch currency with Code that doesn't exist
        * Perform an HTTP Get Request to /api/v1/currencies/{code}
        */
        $response = $this->get('/api/v1/currencies/JOD');

        $response->assertNotFound();
    }
}
