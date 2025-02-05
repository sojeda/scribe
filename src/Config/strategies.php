<?php

namespace Knuckles\Scribe\Config;

use Illuminate\Support\Arr;

// Strategies can be:
// 1. (Original) A class name, e.g. Strategies\Responses\ResponseCalls::class
// 2. (New) A tuple containing the class name as item 1, and its config array as item 2
// 3. (New) A tuple containing "override" as item 1, and the values to override array as item 2
function overrideResults(array $strategies, array $valuesToOverride): array
{
    $overrideStrategy = ['override', $valuesToOverride];
    return addStrategies($strategies, [$overrideStrategy]);
}

function addStrategies(array $strategies, array $newStrategies = []): array
{
    return array_merge($strategies, $newStrategies);
}

function removeStrategies(array $strategies, array $strategyNamesToRemove): array
{
    $correspondingStrategies = Arr::where($strategies, function ($strategy) use ($strategyNamesToRemove) {
        $strategyName = is_string($strategy) ? $strategy : $strategy[0];
        return in_array($strategyName, $strategyNamesToRemove);
    });

    foreach ($correspondingStrategies as $key => $value) {
        unset($strategies[$key]);
    }

    return $strategies;
}

/**
 * Replaces the strategy entry in the list with a tuple containing [strategy_name, config_array].
 */
function withConfiguredStrategy(array $strategies, array $configurationTuple): array
{
    $strategyFound = false;
    $strategies = array_map(function ($strategy) use ($configurationTuple, &$strategyFound) {
        $strategyName = is_string($strategy) ? $strategy : $strategy[0];
        if ($strategyName == $configurationTuple[0]) {
            $strategyFound = true;
            return $configurationTuple;
        }

        return $strategy;
    }, $strategies);

    // If strategy wasn't in there, add it.
    if (!$strategyFound) {
        $strategies = addStrategies($strategies, [$configurationTuple]);
    }
    return $strategies;
}
