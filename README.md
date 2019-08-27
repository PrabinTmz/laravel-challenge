# Currencies Task - TDD
In this project, you'll be building a laravel 5.8 application following the Test-Driven Development (TDD) approach, to fetch currencies from an external API and perform various operations according to the set of tasks described below.

## Table of Contents

- [Getting Started](#getting-started)
	- [Prerequisites](#Prerequisites)
	- [Setup](#Setup)
- [Running Tests](#running-tests)
- [External APIs](#external-apis)
- [Tasks](#tasks)
	- [Part1: Model/Controller](#Part-1)
	- [Part2: Seed Command](#Part-2)
	- [Part3: Contract Pattern](#Part-3)
	- [Part4: Cache Access](#Part-4)
- [Authors](#Authors)


## Getting Started


### Prerequisites
* Laravel 5.8
* PHP 7
* MySQL Server.
* Any Preferred Workbench (Sequel Pro, MySQL Workbench) for SQL Databases.


### Setup
**Gitpod: Cloud/Web IDE**
1. Visit <https://gitpod.io/#https://github.com/Yamsafer/laravel-challenge>
2. Wait until startup tasks finishes.
3. *Voila :tada:, Happy Coding!*

**Local Development**:
1. Clone the Repository: `git clone https://github.com/Yamsafer/codeality-laravel.git`.
2. Run `composer install` to Install Dependencies.
3. Copy contents of  `.env.example` file to a new file `.env`, If you're on Mac OSX just run: `cp .env.example .env`
4. Generate an app encryption key: `php artisan key:generate`.
5. create an empty database for the project.
6. In the `.env` file, add database information by filling the `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD` options to match the credentials of the database you just created.
7. Migrate the database: `php artisan migrate`.


## Running Tests
* Run a single testclass or test using the `PHPUnit` command:
```bash
phpunit --filter <name-of-test-class>
phpunit --filter <name-of-test>
```


* Run a single testsuite using the `PHPUnit` command:
```bash
phpunit --testsuite <name-of-testsuite>
```


* Run all `PHPUnit` tests available in the `/tests` directory:
```bash
phpunit
```


## External APIs
You'll be using [The Free Currency Converter API](https://free.currencyconverterapi.com) throughout this project, specifically as following:
* List of all currencies: <https://free.currconv.com/api/v7/currencies?apiKey=773cea955c8c211a043b>
* Exchange Rate Url : <https://free.currencyconverterapi.com/api/v6/convert?apiKey=773cea955c8c211a043b&compact=y&q=USD_ILS>, where `q=USD_ILS` is the conversion query string from USD to ILS currency.

**NOTE**: Please try running the above external API(s) on `Postman` or in your browser beforehand, and see that you get valid responses. If you get an error message `API Limit Reached`, don't panic, no worries. Please go to <https://free.currencyconverterapi.com/> and GET your own **Free** API key and replace it in the URL(s), also have a quick look on the limits they define back in their website.


## Tasks
As mentioned earlier, this project targets TDD approach to be used, in a total of **4 Parts** as following:
### Part 1
In this part, you're asked to create a `Currency` eloquent model having the following attributes:
- name:   `string`
- symbol: `string`
- code:   `string`
- rate:   `float`

After that, you should add any neccery implementation to handle specifically two HTTP Requests:
- `GET /api/v1/currencies` - Returns Paginated JSON Response of Currencies in DB
- `GET /api/v1/currencies/{code}` - Returns a JSON Response having Currency Model with the specified `code` attribute.
 **NOTE**: a locale could be passed as query string `?locale=ar` to have currency `name` corresponding to specified locale.


**Example Response**

`GET /api/v1/currencies/USD?locale=ar`
 ```json
{
"id": 9,
"code": "USD",
"symbol": "$",
"rate": null,
"created_at": "2019-05-23 13:49:08",
"updated_at": "2019-05-23 13:49:08",
"name": "دولار امريكي"
}
```


Finally, you need add an other Model `Translation` which will have the following specifications:
- `Translation` model has the following attributes: 
    - `locale`: string,
    - `translation_text`: string
 
- Model will handle the way a `name` field is returned within the JSON response, depending on the Request's locale.
- Assign an appropriate Eloquent Relationship between the `Currency` and `Translation` models.
- Every `Currency` model persisted to database should have at least, the en-locale (English) recored in 'currency-translations' table.


`PHPUnit` test(s) for this part can be found under the directory `/tests/Part1`, tests whether Eloquent Model exists, in addition to a set of integrations tests for routes on Controller, with assertions on responses from HTTP Requests and validation for attributes. Run the tests using the **command**:
```bash
phpunit --testsuite Part1
```
---

### Part 2
In this part, you're asked to add any necessary implementation/logic for creating a new artisan command: `currencies:bootstrap` which will handle fetching the currencies from the external API and persist them to your local database.

**API Response - Partial Example**

`GET /https://free.currconv.com/api/v7/currencies?apiKey=773cea955c8c211a043b`
 ```json
{
"results":
{
"ALL": {
"currencyName": "Albanian Lek",
"currencySymbol": "Lek",
"id": "ALL"
},
"XCD": {
"currencyName": "East Caribbean Dollar",
"currencySymbol": "$",
"id": "XCD"
},
"EUR": {
"currencyName": "Euro",
"currencySymbol": "€",
"id": "EUR"
}
}
}
```

Next, you're required to extend the `show($code)` method's implementation on the Controller, to call the exchange rate API  with specified `$code` against USD, and assign it to `rate` attribute of retrieved `Currency` model from DB.

This would be the **JSON Response** for HTTP Request:

`GET /api/v1/currencies/EUR`

```json
{
"id": 3,
"code": "EUR",
"symbol": "€",
"rate": 0.89676,
"created_at": "2019-05-23 13:49:08",
"updated_at": "2019-05-23 13:49:08",
"name": "Euro"
}
```


`PHPUnit` test(s) for this part can be found under the directory `/tests/Part2`, tests written for this part verifies the **command** exists and that running the artisan command loads the common currencies. You are required to add any necessary implementation to make all tests pass. Run the tests using the **command**:
```bash
phpunit --testsuite Part2
```
---

### Part 3
Contract Pattern is one of the most important, highly used, design patters of Laravel framework. In this part, you'll be asked to apply this pattern. An Interface is already implemented to ease the communication, it can be found under `app/Contracts/CurrencyExchangeContract.php`

```php
<?php

namespace App\Contracts;

interface CurrencyExchangeContract
{
    /**
     * fetch list of currencies from external API 
     */
    public function listCurrencies() : array;

    /**
     * fetch exchange rate (against USD) for a currency using its code
     */
    public function rate(string $code) : float;
}
```
You are required to create a **Service** implementing the contract, with the source of data being again the [External APIs](#external-apis) and modify any logic in your code calling the APIs directly to inject the **Service** and use `listCurrencies()` and `rate($code)` methods instead.


`PHPUnit` test(s) for this part can be found under the directory `/tests/Part3`, run the tests using the **command**:
```bash
phpunit --testsuite Part3
```

---

### Part 4
In this part, you'll use Larave'ls built-in [Cache](https://laravel.com/docs/5.8/cache) to cache the exchange rate fetched from external API.

You are required to change the logic of the `rate($code)` method of the currency exchange **Service** implemented in [Part3](#Part-3), to check if the required exchange rate is already **Cached**. If not, you need to **cache** the fetched exchange rate of currency with `$code` against USD, for a period of **60 minutes**.


`PHPUnit` test(s) for this part can be found under the directory `/tests/Part4`, the test(s) verifies Cache is accessed on `show()` method of Controller and that it has correct vaule, which will be the exchange rate of the requested currency. You are required to add any necessary implementation to make all tests pass. Run the tests using the **command**:
```bash
phpunit --testsuite Part4
```

## Authors
Copyright 2019 © [Yamsafer](https://www.yamsafer.com)
