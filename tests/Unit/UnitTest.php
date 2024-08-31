<?php

namespace Tests\Unit;

use App\Events\ExchangeRatesGenerated;
use App\Http\Controllers\ExchangeRatesController;
use App\Http\Requests\ExchangeRatesRequest;
use App\Jobs\SaveExchangeRates;
use App\Mail\ExchangeRatesMail;
use App\Models\Rate;
use App\Repositories\ExchangeRatesRepository;
use App\Services\Api\ExchangeRatesApi;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class UnitTest extends TestCase
{
    private string $date;

    public function setUp(): void
    {
        parent::setUp();

        $this->date = Carbon::now()->format('Y-m-d');
    }

    public function test_api_call(): void
    {
        $client = Mockery::mock(Client::class);
        $data = [
            'base' => '1',
            'date' => '2',
            'rates' => '3'
        ];

        $api = new ExchangeRatesApi($client);

        $client
            ->shouldReceive('request')
            ->once()
            ->with('GET', $api->getUrl($this->date) ,['verify' => false])
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode($data)));

        $response = $api->getRate($this->date);

        $this->assertEquals($data, $response);
    }

    public function test_save_exchange_rates(): void
    {
        Bus::fake();

        $rate = Mockery::mock(Rate::class);
        $api = Mockery::mock(ExchangeRatesApi::class);

        $rate->shouldReceive('query')->andReturnSelf();
        $rate->shouldReceive('where')->with('date', $this->date)->andReturnSelf();
        $rate->shouldReceive('first')->with(['base', 'date', 'rates'])->andReturnNull();

        $api->shouldReceive('getRate')->andReturn(['data']);

        (new ExchangeRatesRepository($rate, $api))->getRates($this->date);

        Bus::assertDispatched(SaveExchangeRates::class);
    }

    public function test_email_send(): void
    {
        Mail::fake();

        $mockRepo = Mockery::mock(ExchangeRatesRepository::class);
        $request = Mockery::mock(ExchangeRatesRequest::class);

        $mockRepo->shouldReceive('getRates')->andReturn(['base' => 'EU', 'date' => $this->date, 'rates' => '']);
        $request->shouldReceive('validated')->andReturn($this->date);

        (new ExchangeRatesController($mockRepo))->show($request);

        Mail::assertSent(
            ExchangeRatesMail::class,
            function (ExchangeRatesMail $mail) {
                return count($mail->attachments()) > 0 && $mail->content()->view == 'emails.rates';
            }
        );
    }

    /**
     * @dataProvider eventCalls
     */
    public function test_event_calls(array $value, bool $called): void
    {
        Event::fake();

        $mockRepo = Mockery::mock(ExchangeRatesRepository::class);
        $request = Mockery::mock(ExchangeRatesRequest::class);

        $mockRepo->shouldReceive('getRates')->andReturn($value);
        $request->shouldReceive('validated')->andReturn('');

        (new ExchangeRatesController($mockRepo))->show($request);

        if ($called) {
            Event::assertDispatched(ExchangeRatesGenerated::class);
        } else {
            Event::assertNothingDispatched();
        }
    }

    static function eventCalls(): array
    {
        return [
            'event was called' => [
                'value' => ['test'],
                'called' => true
            ],
            'event was not called' => [
                'value' => [],
                'called' => false
            ]
        ];
    }
}
