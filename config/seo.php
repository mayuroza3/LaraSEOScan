<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SEO Rules
    |--------------------------------------------------------------------------
    | List of rules enabled for scanning. Each rule is a class that implements
    | App\Seo\Rules\SeoRule. You can disable a rule by setting value to false.
    |--------------------------------------------------------------------------
    */
    'rules' => [

        // Security & Infrastructure
        \App\Seo\Rules\SsrfProtectionRule::class    => true,
        \App\Seo\Rules\HttpsAuditRule::class         => true,
        \App\Seo\Rules\RedirectChainRule::class      => true,
        \App\Seo\Rules\SecurityHeadersRule::class    => true,

        // Performance
        \App\Seo\Rules\TtfbRule::class              => true,
        \App\Seo\Rules\StatusCodeRule::class        => true,
        \App\Seo\Rules\HtmlSizeRule::class          => true,
        \App\Seo\Rules\JavascriptSizeRule::class    => true,
        \App\Seo\Rules\CssSizeRule::class           => true,
        \App\Seo\Rules\CompressionRule::class       => true,

        // Meta Tags & Head Inspection
        \App\Seo\Rules\MissingTitleRule::class       => true,
        \App\Seo\Rules\MetaDescriptionRule::class    => true,
        \App\Seo\Rules\NoindexNofollowRule::class    => true,
        \App\Seo\Rules\HtmlLangRule::class           => true,
        \App\Seo\Rules\ViewportRule::class           => true,
        \App\Seo\Rules\CharsetRule::class            => true,
        \App\Seo\Rules\FaviconRule::class             => true,
        \App\Seo\Rules\HreflangRule::class           => true,
        \App\Seo\Rules\InvalidHeadElementsRule::class => true,
        \App\Seo\Rules\CanonicalValidationRule::class => true,

        // Headings & Content
        \App\Seo\Rules\H1Rule::class                 => true,
        \App\Seo\Rules\HeadingHierarchyRule::class   => true,
        \App\Seo\Rules\ShingleDuplicateRule::class   => true,
        \App\Seo\Rules\ContentLengthRule::class      => true,
        \App\Seo\Rules\LongSentencesRule::class      => true,
        \App\Seo\Rules\TransitionWordsRule::class    => true,
        \App\Seo\Rules\KeywordInTitleRule::class     => true,
        \App\Seo\Rules\KeywordInIntroRule::class     => true,

        // Open Graph / Twitter
        \App\Seo\Rules\OpenGraphRule::class          => true,
        \App\Seo\Rules\OpenGraphCompleteRule::class  => true,
        \App\Seo\Rules\TwitterCardRule::class        => true,

        // AI & Search Crawler Intelligence
        \App\Seo\Rules\AiCrawlersRule::class         => true,
        \App\Seo\Rules\LlmsTxtRule::class            => true,

        // Structured Data
        \App\Seo\Rules\JsonLdValidatorRule::class    => true,

        // Links
        \App\Seo\Rules\BrokenLinkRule::class         => false,

        // Image Optimization & Formats
        \App\Seo\Rules\ImageOptimizationRule::class  => true,
        \App\Seo\Rules\ImageDimensionsRule::class    => true,
        \App\Seo\Rules\ImageLazyLoadingRule::class   => true,
        \App\Seo\Rules\BrokenImageRule::class        => true,
        \App\Seo\Rules\ModernImageFormatRule::class  => true,
        \App\Seo\Rules\KeywordDensityRule::class     => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Crawler Settings
    |--------------------------------------------------------------------------
    */
    'crawler' => [
        'max_redirects' => 5,
        'image_max_size_kb' => 200,
        'keyword_density_min' => 0.5,
        'keyword_density_max' => 3,
        'check_external_links' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rule Categories Weights
    |--------------------------------------------------------------------------
    */
    'weights' => [
        'security'   => 25,
        'meta'       => 25,
        'content'    => 20,
        'performance' => 15,
        'og'         => 10,
        'structured' => 5,
        'links'      => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Severity Levels
    |--------------------------------------------------------------------------
    */
    'severity' => [
        'critical' => 'red',
        'error'    => 'red',
        'high'     => 'orange',
        'warning'  => 'orange',
        'medium'   => 'yellow',
        'info'     => 'blue',
    ],

    /*
    |--------------------------------------------------------------------------
    | Shingle Duplicate Rule Settings
    |--------------------------------------------------------------------------
    */
    'shingles' => [
        'size'      => 5,
        'threshold' => 0.75,
    ],
];
