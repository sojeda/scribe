<?php

namespace Knuckles\Scribe\Config;

use Illuminate\Support\Arr;

class Extracting
{
    public function __construct(
        public Routes  $routes,
        public string  $defaultGroup = 'Endpoints',
        public array   $databaseConnectionsToTransact = [],
        public ?int    $fakerSeedForExamples = 1234,
        public array   $dataSourcesForExampleModels = ['factoryCreate', 'factoryMake', 'databaseFirst'],
        public array   $auth = [],
        public array   $strategies = [],
        public ?string $routeMatcher = null,
        public ?string $fractalSerializer = null,
    )
    {
    }

    public static function auth(
        bool    $enabled = true,
        bool    $default = false,
        AuthIn  $in = AuthIn::BEARER,
        string  $name = 'key',
        ?string $useValue = null,
        string  $placeholder = '{YOUR_AUTH_KEY}',
        string  $extraInfo = ''
    ): array
    {
        return array_merge(get_defined_vars(), ['in' => $in->value]);
    }

    public static function strategies(
        StrategyListWrapper $metadata,
        StrategyListWrapper $urlParameters,
        StrategyListWrapper $queryParameters,
        StrategyListWrapper $headers,
        StrategyListWrapper $bodyParameters,
        StrategyListWrapper $responses,
        StrategyListWrapper $responseFields,
    ): array
    {
        return array_map(fn($listWrapper) => $listWrapper->toArray(), get_defined_vars());
    }

    public static function with(
        Routes  $routes,
        string  $defaultGroup = 'Endpoints',
        array   $databaseConnectionsToTransact = [],
        ?int    $fakerSeedForExamples = 1234,
        array   $dataSourcesForExampleModels = ['factoryCreate', 'factoryMake', 'databaseFirst'],
        array   $auth = [],
        array   $strategies = [],
        ?string $routeMatcher = \Knuckles\Scribe\Matching\RouteMatcher::class,
        ?string $fractalSerializer = null,
    ): Extracting
    {
        return new self(...get_defined_vars());
    }

    public function serializeInto(array $config)
    {
        $core = [
            'examples' => [
                'faker_seed' => $this->fakerSeedForExamples,
                'models_source' => $this->dataSourcesForExampleModels,
            ],
            'routeMatcher' => $this->routeMatcher,
            'database_connections_to_transact' => $this->databaseConnectionsToTransact,
            'fractal' => [
                'serializer' => $this->fractalSerializer,
            ],
            'auth' => Serializer::translateKeys($this->auth),
            'strategies' => $this->strategies,
            'routes' => Serializer::generateRoutesConfig($this->routes),
            'groups.default' => $this->defaultGroup,
        ];

        foreach ($core as $key => $value) {
            data_set($config, $key, $value);
        }
        return $config;
    }
}

enum AuthIn: string
{
    case BEARER = 'bearer';
    case BASIC = 'basic';
    case HEADER = 'header';
    case QUERY = 'query';
    case BODY = 'body';
    case QUERY_OR_BODY = 'query_or_body';
}
