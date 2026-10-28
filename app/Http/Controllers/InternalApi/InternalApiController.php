<?php

namespace App\Http\Controllers\InternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class InternalApiController extends Controller
{
    protected function ok(string $message = 'OK', mixed $data = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    protected function fail(string $message = 'Error', mixed $errors = null, int $status = 422): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
            'data'    => null,
        ];

        if (!is_null($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    protected function paginated($paginator, string $message = 'OK'): JsonResponse
    {
        return $this->ok($message, [
            'items' => $paginator->items(),
            'meta'  => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
        ]);
    }
}
