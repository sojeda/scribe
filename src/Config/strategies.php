<?php

namespace Knuckles\Scribe\Config;

use Illuminate\Support\Arr;

// Strategies can be:
// 1. (Original) A class name, e.g. Strategies\Responses\ResponseCalls::class
// 2. (New) A tuple containing the class name as item 1, and its config array as item 2
// 3. (New) A tuple containing "merge" as item 1, and the values to override array as item 2

/**
 * Remove one or more strategies from a list of strategies.
 */
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
 * Configure a strategy and add or update it in a list of strategies.
 * This method generates a tuple containing [strategy_name, config_array],
 * and adds or replaces the strategy entry in the list.
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
        $strategies = array_merge($strategies, [$configurationTuple]);
    }
    return $strategies;
}
