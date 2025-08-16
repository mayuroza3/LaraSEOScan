<?php
namespace App\Seo\Rules;

class Registry
{
    public static function all(): array
    {
        return [
            \App\Seo\Rules\MissingTitleRule::class,
            \App\Seo\Rules\MetaDescriptionRule::class,
            \App\Seo\Rules\H1Rule::class,
            \App\Seo\Rules\OpenGraphRule::class,
            \App\Seo\Rules\JsonLdValidatorRule::class,
            \App\Seo\Rules\ShingleDuplicateRule::class,
        ];
    }
}
