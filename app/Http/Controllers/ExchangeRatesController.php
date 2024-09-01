<?php

namespace App\Http\Controllers;

use App\Events\ExchangeRatesGenerated;
use App\Http\Requests\ExchangeRatesRequest;
use App\Interfaces\ExchangeRatesRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ExchangeRatesController extends Controller
{
    private ExchangeRatesRepositoryInterface $repository;

    public function __construct(ExchangeRatesRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function show(ExchangeRatesRequest $request): JsonResponse
    {
        $rates = $this->repository->getRates($request->validated('date'));

        if ($rates) {
            event(new ExchangeRatesGenerated($rates));
        }

        return response()->json(
            [
                'status' => 'success',
                'data' => $rates
            ],
            Response::HTTP_OK
        );
    }
}
