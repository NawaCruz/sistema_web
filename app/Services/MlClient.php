<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class MlClient {
    public function predict(array $items): array {
        $base  = rtrim(config('services.ml.base_url'), '/');
        $token = config('services.ml.token');

        $res = Http::timeout(10)
            ->withHeaders(['Authorization' => "Bearer {$token}"])
            ->post("{$base}/predict", $items);

        $res->throw();
        return $res->json(); // [{y_pred, prob_fiel}] o {y_pred, prob_fiel}
    }
}
