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

        // Meta
        \App\Seo\Rules\MissingTitleRule::class       => true,
        \App\Seo\Rules\MetaDescriptionRule::class    => true,

        // Headings & Content
        \App\Seo\Rules\H1Rule::class                 => true,
        \App\Seo\Rules\ShingleDuplicateRule::class   => true,

        // Open Graph / Twitter
        \App\Seo\Rules\OpenGraphRule::class          => true,

        // Structured Data
        // Structured Data
        \App\Seo\Rules\JsonLdValidatorRule::class    => true,
        // Links
        \App\Seo\Rules\BrokenLinkRule::class         => true,
        // ImageOptimization
        \App\Seo\Rules\ImageOptimizationRule::class  => true,
        // keyword Density
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
    | Each category can have a weight (importance) in score calculation.
    | Example: meta is more important than OG, etc.
    |--------------------------------------------------------------------------
    */
    'weights' => [
        'meta'       => 30,
        'content'    => 25,
        'og'         => 15,
        'structured' => 30,
        'links'      => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Severity Levels
    |--------------------------------------------------------------------------
    | Severity labels you can use in rules. These can be styled in the UI.
    |--------------------------------------------------------------------------
    */
    'severity' => [
        'error'   => 'red',
        'warning' => 'orange',
        'info'    => 'blue',
    ],

    /*
    |--------------------------------------------------------------------------
    | Shingle Duplicate Rule Settings
    |--------------------------------------------------------------------------
    | These control how duplicate content detection works.
    |--------------------------------------------------------------------------
    */
    'shingles' => [
        'size'      => 5,     // number of words per shingle
        'threshold' => 0.75,  // overlap ratio for duplicate detection
    ],
];
