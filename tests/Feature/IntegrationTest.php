<?php

namespace Tests\Feature;

use App\Interfaces\ExchangeRatesRepositoryInterface;
use App\Repositories\ExchangeRatesRepository;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    public function test_the_application_returns_a_successful_response(): void
    {
        Event::fake();
        Mail::fake();
        Bus::fake();

        $mock = \Mockery::mock(ExchangeRatesRepositoryInterface::class);
        $this->app->instance(ExchangeRatesRepository::class, $mock);

        $mock->shouldReceive('getRates')->andReturn([]);

        $response = $this->json('get', '/api/rate', ['date' => Carbon::now()->format('Y-m-d')]);

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * @dataProvider endPointRoutes
     */
    public function test_the_application_returns_a_unsuccessful_response(
        string $url,
        string $method,
        array $params,
        int $code
    ): void {
        $response = $this->json($method, $url, $params);

        $response->assertStatus($code);
    }

    static function endPointRoutes(): array
    {
        return [
            'get rates without parameter' => [
                'url' => '/api/rate',
                'method' => 'get',
                'params' => [],
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'get rates with empty parameter' => [
                'url' => '/api/rate',
                'method' => 'get',
                'params' => ['date' => ''],
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'get rates with parameter in incorrect format' => [
                'url' => '/api/rate',
                'method' => 'get',
                'params' => ['date' => Carbon::now()->format('d-m-Y')],
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'get rates with parameter in future' => [
                'url' => '/api/rate',
                'method' => 'get',
                'params' => ['date' => Carbon::now()->addDays(1)->format('Y-m-d')],
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY
            ],
        ];
    }
}
