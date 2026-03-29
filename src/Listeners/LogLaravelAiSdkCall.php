<?php

namespace Gometap\LaraiTracker\Listeners;

use Gometap\LaraiTracker\Events\AiCallRecorded;
use Illuminate\Support\Facades\Auth;

class LogLaravelAiSdkCall
{
    /**
     * Keep track of invocation IDs handled in the current process
     * to avoid double logging when multiple listeners catch the same event.
     *
     * @var array<string, true>
     */
    protected static array $handledInvocationIds = [];

    /**
     * Handle a Laravel AI SDK event payload.
     */
    public function handle(object $event): void
    {
        $invocationId = (string) data_get($event, 'invocationId', '');

        if ($invocationId !== '' && isset(static::$handledInvocationIds[$invocationId])) {
            return;
        }

        $promptTokens = (int) data_get($event, 'response.usage.promptTokens', 0);
        $completionTokens = (int) data_get($event, 'response.usage.completionTokens', 0);

        AiCallRecorded::dispatch(
            Auth::id(),
            $this->resolveProvider($event),
            $this->resolveModel($event),
            $promptTokens,
            $completionTokens
        );

        if ($invocationId !== '') {
            static::$handledInvocationIds[$invocationId] = true;
        }
    }

    /**
     * Resolve provider from response metadata or prompt fallback.
     */
    protected function resolveProvider(object $event): string
    {
        $provider = data_get($event, 'response.meta.provider');

        if (is_string($provider) && $provider !== '') {
            return strtolower($provider);
        }

        $promptProvider = data_get($event, 'prompt.provider');

        if (is_object($promptProvider)) {
            if (method_exists($promptProvider, 'driver')) {
                return strtolower((string) $promptProvider->driver());
            }

            return strtolower((string) $promptProvider);
        }

        if (is_string($promptProvider) && $promptProvider !== '') {
            return strtolower($promptProvider);
        }

        return 'unknown';
    }

    /**
     * Resolve model from response metadata or prompt fallback.
     */
    protected function resolveModel(object $event): string
    {
        $model = data_get($event, 'response.meta.model');

        if (is_string($model) && $model !== '') {
            return $model;
        }

        $promptModel = data_get($event, 'prompt.model');

        if (is_string($promptModel) && $promptModel !== '') {
            return $promptModel;
        }

        return 'unknown';
    }
}
