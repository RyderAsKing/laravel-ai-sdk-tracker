<?php

use Gometap\LaraiTracker\Listeners\InterceptAiResponse;
use Gometap\LaraiTracker\Listeners\LogLaravelAiSdkCall;
use Gometap\LaraiTracker\Models\LaraiLog;

uses(\Gometap\LaraiTracker\Tests\TestCase::class);

test('it tracks laravel ai sdk invocation payload usage', function () {
    $listener = new LogLaravelAiSdkCall();

    $event = (object) [
        'invocationId' => 'invocation-1',
        'prompt' => (object) [
            'model' => 'gpt-4o-mini-2024-07-18',
            'provider' => 'openai',
        ],
        'response' => (object) [
            'usage' => (object) [
                'promptTokens' => 123,
                'completionTokens' => 45,
            ],
            'meta' => (object) [
                'provider' => 'openai',
                'model' => 'gpt-4o-mini-2024-07-18',
            ],
        ],
    ];

    $listener->handle($event);

    $log = LaraiLog::first();

    expect(LaraiLog::count())->toBe(1)
        ->and($log->provider)->toBe('openai')
        ->and($log->model)->toBe('gpt-4o-mini-2024-07-18')
        ->and($log->prompt_tokens)->toBe(123)
        ->and($log->completion_tokens)->toBe(45)
        ->and($log->total_tokens)->toBe(168);
});

test('it does not double log the same laravel ai sdk invocation id', function () {
    $listener = new LogLaravelAiSdkCall();

    $event = (object) [
        'invocationId' => 'invocation-duplicate',
        'prompt' => (object) [
            'model' => 'gpt-4o-mini',
            'provider' => 'openai',
        ],
        'response' => (object) [
            'usage' => (object) [
                'promptTokens' => 10,
                'completionTokens' => 5,
            ],
            'meta' => (object) [
                'provider' => 'openai',
                'model' => 'gpt-4o-mini',
            ],
        ],
    ];

    $listener->handle($event);
    $listener->handle($event);

    expect(LaraiLog::count())->toBe(1)
        ->and(LaraiLog::first()->total_tokens)->toBe(15);
});

test('it maps openai responses api usage input output tokens', function () {
    $listener = new InterceptAiResponse();
    $method = new ReflectionMethod($listener, 'logOpenAiFormat');
    $method->setAccessible(true);

    $method->invoke($listener, 'https://api.openai.com/v1/responses', [
        'model' => 'gpt-4o-mini-2024-07-18',
        'usage' => [
            'input_tokens' => 77,
            'output_tokens' => 33,
        ],
    ]);

    $log = LaraiLog::first();

    expect($log->prompt_tokens)->toBe(77)
        ->and($log->completion_tokens)->toBe(33)
        ->and($log->total_tokens)->toBe(110);
});

test('it still maps legacy prompt completion token usage', function () {
    $listener = new InterceptAiResponse();
    $method = new ReflectionMethod($listener, 'logOpenAiFormat');
    $method->setAccessible(true);

    $method->invoke($listener, 'https://api.openai.com/v1/chat/completions', [
        'model' => 'gpt-4o-mini-2024-07-18',
        'usage' => [
            'prompt_tokens' => 55,
            'completion_tokens' => 22,
        ],
    ]);

    $log = LaraiLog::first();

    expect($log->prompt_tokens)->toBe(55)
        ->and($log->completion_tokens)->toBe(22)
        ->and($log->total_tokens)->toBe(77);
});
