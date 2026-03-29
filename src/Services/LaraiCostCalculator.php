<?php

namespace Gometap\LaraiTracker\Services;

use Gometap\LaraiTracker\Models\LaraiModelPrice;

class LaraiCostCalculator
{
    /**
     * Map of models to costs per 1,000,000 tokens in USD.
     * Format: [input_cost, output_cost]
     * Prices as of Feb 2024 (approximate).
     */
    /**
     * Calculate the cost of an AI call.
     */
    public function calculate(string $provider, string $model, int $promptTokens, int $completionTokens): float
    {
        $model = strtolower($model);
        $provider = strtolower($provider);

        $pricing = $this->getPricing($provider, $model);

        $inputCost = ($promptTokens / 1000000) * $pricing['input'];
        $outputCost = ($completionTokens / 1000000) * $pricing['output'];

        return round($inputCost + $outputCost, 8);
    }

    protected function getPricing(string $provider, string $model): array
    {
        $pricing = $this->getDatabasePricing($provider, $model)
            ?? $this->getDefaultPricing($provider, $model);

        if ($pricing !== null) {
            return $pricing;
        }

        // Unknown model/provider: default to 0 to avoid overbilling.
        return ['input' => 0.00, 'output' => 0.00];
    }

    protected function getDatabasePricing(string $provider, string $model): ?array
    {
        try {
            $variants = $this->modelVariants($model);

            $dbPrice = LaraiModelPrice::query()
                ->whereRaw('LOWER(provider) = ?', [$provider])
                ->where(function ($query) use ($variants) {
                    foreach ($variants as $variant) {
                        $query->orWhereRaw('LOWER(model) = ?', [$variant]);
                    }
                })
                ->orderByRaw('LENGTH(model) DESC')
                ->first();

            if ($dbPrice !== null) {
                return [
                    'input' => (float) $dbPrice->input_price_per_1m,
                    'output' => (float) $dbPrice->output_price_per_1m,
                ];
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }

    protected function getDefaultPricing(string $provider, string $model): ?array
    {
        $variants = $this->modelVariants($model);

        // Fallback to defaults
        $defaults = [
            'openai' => [
                'gpt-4o' => ['input' => 5.00, 'output' => 15.00],
                'gpt-4o-mini' => ['input' => 0.15, 'output' => 0.60],
                'gpt-4.1' => ['input' => 2.00, 'output' => 8.00],
                'gpt-4.1-mini' => ['input' => 0.40, 'output' => 1.60],
                'gpt-4.1-nano' => ['input' => 0.10, 'output' => 0.40],
                'o1' => ['input' => 15.00, 'output' => 60.00],
                'o1-mini' => ['input' => 1.10, 'output' => 4.40],
                'o3-mini' => ['input' => 1.10, 'output' => 4.40],
                'gpt-4-turbo' => ['input' => 10.00, 'output' => 30.00],
                'gpt-4' => ['input' => 30.00, 'output' => 60.00],
                'gpt-3.5-turbo' => ['input' => 0.50, 'output' => 1.50],
            ],
            'google' => [
                'gemini-1.5-pro' => ['input' => 3.50, 'output' => 10.50],
                'gemini-1.5-flash' => ['input' => 0.075, 'output' => 0.30],
                'gemini-pro' => ['input' => 0.50, 'output' => 1.50],
            ],
        ];

        if (!isset($defaults[$provider])) {
            return null;
        }

        foreach ($variants as $variant) {
            if (isset($defaults[$provider][$variant])) {
                return $defaults[$provider][$variant];
            }
        }

        return null;
    }

    /**
     * Normalize model aliases (dated snapshots, latest aliases, etc.)
     * to improve pricing lookups.
     *
     * @return array<int, string>
     */
    protected function modelVariants(string $model): array
    {
        $variants = [$model];

        $normalized = preg_replace('/-\d{4}-\d{2}-\d{2}$/', '', $model) ?? $model;
        $variants[] = $normalized;
        $variants[] = preg_replace('/-(latest|preview)$/', '', $normalized) ?? $normalized;

        return array_values(array_unique(array_filter($variants)));
    }
}
