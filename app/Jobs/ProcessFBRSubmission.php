<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Services\FBRService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessFBRSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private Sale $sale
    ) {}

    public function handle(FBRService $fbrService): void
    {
        if (!$fbrService->isEnabled()) {
            return;
        }

        $saleWithRelations = $this->sale->load('customer', 'saleItems.product');
        $result = $fbrService->submitInvoice($saleWithRelations->toArray());

        if (!$result['success']) {
            Log::error('FBR submission failed', [
                'sale_id' => $this->sale->id,
                'error' => $result['message']
            ]);
            throw new \Exception('FBR submission failed: ' . $result['message']);
        }

        Log::info('FBR submission successful', ['sale_id' => $this->sale->id]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('FBR submission job failed permanently', [
            'sale_id' => $this->sale->id,
            'error' => $exception->getMessage()
        ]);
    }
}