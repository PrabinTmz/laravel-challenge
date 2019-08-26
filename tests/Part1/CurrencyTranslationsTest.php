<?php

namespace Tests\Part1;

use App\Currency;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CurrencyTranslationsTest extends TestCase
{
    use DatabaseMigrations;

    protected $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currency = Currency::create([
            'symbol' => '$',
            'code' => 'USD',
            'rate' => 1
        ]);

        $this->currency
            ->translations()
            ->create([
                'locale' => 'en',
                'translation_text' => 'United States Dollars',
            ]);
    }
    /**
     * @test
     * It verifies that translations of Currency Model are of type:
     * Illuminate\Database\Eloquent\Collection
     */
    public function it_verifies_that_currency_has_translations_of_collection_type()
    {
        $this->assertInstanceOf(Collection::class, $this->currency->translations);
    }

    /**
     * @test 
     * It verifies that currency model by default has en Locale translation stored in DB
     */
    public function it_verifies_that_currency_model_has_en_locale_translation()
    {
        $this->assertTrue(
            $this->currency
                ->translations
                ->isNotEmpty()
        );

        $enLocaleTranslation = $this->currency
            ->translations()
            ->where('locale', 'en')
            ->first();

        $this->assertNotNull($enLocaleTranslation);
        
        $this->assertTrue(
            $this->currency
                ->translations
                ->contains($enLocaleTranslation)
        );
        
        /** Assert than currency's Name will be the translation_text of the locale=en DB record */
        $this->assertEquals(
            $enLocaleTranslation->translation_text,
            $this->currency->translatedName
        );
    }
}
