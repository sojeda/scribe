<?php

use Knuckles\Scribe\Config\AuthIn;
use Knuckles\Scribe\Config\ExternalTheme;
use Knuckles\Scribe\Extracting\Strategies;
use Knuckles\Scribe;

/**
 * For documentation, use your IDE's autocomplete features, or see https://scribe.knuckles.wtf/laravel/reference/config
 */

return Scribe\Config\Factory::make(
    extracting: Scribe\Config\Extracting::with(
        routes: Scribe\Config\Routes::match(
            prefixes: ['api/*'],
            domains: ['*'],
            alwaysInclude: [],
            alwaysExclude: [],
        ),
        defaultGroup: 'Endpoints',
        databaseConnectionsToTransact: [config('database.default')],
        fakerSeedForExamples: 1234,
        dataSourcesForExampleModels: ['factoryCreate', 'factoryMake', 'databaseFirst'],
        auth: Scribe\Config\Extracting::auth(
            enabled: false,
            default: false,
            in: AuthIn::BEARER,
            useValue: env('SCRIBE_AUTH_KEY'),
            placeholder: '{YOUR_AUTH_KEY}',
            extraInfo: <<<MARKDOWN
              You can retrieve your token by visiting your dashboard and clicking <b>Generate API token</b>.
            MARKDOWN
        ),
        strategies: Scribe\Config\Extracting::strategies(
            metadata: Scribe\Config\Defaults::metadataStrategies(),
            urlParameters: Scribe\Config\Defaults::urlParametersStrategies(),
            queryParameters: Scribe\Config\Defaults::queryParametersStrategies(),
            headers: Scribe\Config\Defaults::headersStrategies()
                ->override([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]),
            bodyParameters: Scribe\Config\Defaults::bodyParametersStrategies(),
            responses: Scribe\Config\Defaults::responsesStrategies()
                ->configure(Strategies\Responses\ResponseCalls::withSettings(
                    only: ['GET *'],
                    config: [
                        'app.env' => 'documentation',
                        // 'app.debug' => false,
                    ],
                    queryParams: [],
                    bodyParams: [],
                    fileParams: [],
                    cookies: [],
                )),
            responseFields: Scribe\Config\Defaults::responseFieldsStrategies(),
        )
    ),
    output: Scribe\Config\Output::with(
        type: Scribe\Config\Output::externalLaravelType(
            theme: ExternalTheme::Scalar,
            docsUrl: '/docs',
        ),
        title: 'Our Awesome API',
        description: '',
        baseUrls: [
            "production" => config("app.base_url"),
        ],
        exampleLanguages: ['bash', 'javascript'],
        logo: false,
        lastUpdated: 'Last updated: {date:F j, Y}',
        introText: <<<MARKDOWN
          This documentation aims to provide all the information you need to work with our API.

          <aside>As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
          You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).</aside>
        MARKDOWN,
        groupsOrder: [
            // 'This group will come first',
            // 'This group will come next' => [
            //     'POST /this-endpoint-will-come-first',
            //     'GET /this-endpoint-will-come-next',
            // ],
            // 'This group will come third' => [
            //     'This subgroup will come first' => [
            //         'GET /this-other-endpoint-will-come-first',
            //     ]
            // ]
        ],
        postman: Scribe\Config\Output::postman(
            enabled: true,
            overrides: [
                // 'info.version' => '2.0.0',
            ]
        ),
        openApi: Scribe\Config\Output::openApi(
            enabled: true,
            overrides: [
                // 'info.version' => '2.0.0',
            ],
            generators: [],
        ),
        tryItOut: Scribe\Config\Output::tryItOut(
            enabled: true,
        )
    )
);
