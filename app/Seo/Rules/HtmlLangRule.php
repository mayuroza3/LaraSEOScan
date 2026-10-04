<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class HtmlLangRule implements SeoRule
{
    public function key(): string { return 'meta.lang'; }
    public function title(): string { return 'HTML Language Attribute Audit'; }
    public function category(): string { return 'meta'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $htmlNodes = $xpath->query('//html');

        if (!$htmlNodes || $htmlNodes->length === 0) {
            return $issues;
        }

        $htmlTag = $htmlNodes->item(0);
        $lang = trim($htmlTag->getAttribute('lang'));

        if (empty($lang)) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'high',
                'message' => 'Missing "lang" attribute on <html> element. Specifying language helps screen readers and search engine localization.',
                'selector' => 'html',
                'context' => [],
            ];
        } elseif (!preg_match('/^[a-z]{2,3}(-[a-z0-9]{2,8})?$/i', $lang)) {
            $issues[] = [
                'rule' => $this->key() . '.format',
                'severity' => 'medium',
                'message' => "Invalid <html> lang attribute format '{$lang}'. Expected ISO standard format like 'en' or 'en-US'.",
                'selector' => 'html[lang]',
                'context' => ['lang' => $lang],
            ];
        }

        return $issues;
    }
}
