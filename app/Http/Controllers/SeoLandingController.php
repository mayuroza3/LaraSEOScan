<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SeoLandingController extends Controller
{
    private function getToolData(string $key): array
    {
        $tools = [
            'seo-checker' => [
                'title' => 'Free SEO Checker & Website Audit Tool | LaraSEOScan',
                'header' => 'Website SEO Checker',
                'meta_description' => 'Instantly scan and audit your website SEO performance. Get real-time scores, detect critical crawl issues, meta errors, and get actionable recommendations.',
                'description' => 'Get a comprehensive technical SEO audit of your website instantly. Find critical crawl errors, heading issues, meta data, sitemap checks, and OG tags.',
                'icon' => 'bi-search-heart',
                'focus' => 'Full Technical Audit',
                'keywords' => 'seo checker, website seo audit, free seo analysis, website auditor',
                'faqs' => [
                    ['q' => 'What does this Website SEO Checker do?', 'a' => 'Our tool audits your website against standard search engine ranking signals, looking for missing tags, broken layouts, and slow assets.'],
                    ['q' => 'How often should I run an SEO audit?', 'a' => 'We recommend checking your website weekly or after any major content and design changes.'],
                    ['q' => 'Is this analysis really free?', 'a' => 'Yes, our initial page scan is 100% free with no registration or credit cards required.']
                ],
                'h2_title' => 'Why Optimize Your Entire Site\'s Technical Health?',
                'h2_desc' => 'Search engine crawlers index your pages based on structured paths. A single broken link or missing robots directive can drop your entire domain from index listings.',
                'features' => [
                    'Crawler Path Check' => 'Simulates Googlebot navigation paths to check access guidelines.',
                    'Metadata Scorecard' => 'Verifies length and keywords optimization across crawled headers.',
                    'Core Performance Metrics' => 'Checks load times, cache settings, and asset optimizations.'
                ],
                'code_title' => 'Complete Technical SEO HTML Blueprint',
                'code_example' => "<head>\n  <title>Example Page Title</title>\n  <meta name=\"description\" content=\"A concise description under 160 characters.\">\n  <meta name=\"robots\" content=\"index, follow\">\n  <link rel=\"canonical\" href=\"https://example.com/page\">\n</head>",
                'checks' => [
                    ['title' => 'Headings Structure', 'desc' => 'Checks that H1, H2, H3 heading tags flow in a logical nesting hierarchy.'],
                    ['title' => 'Metadata Validation', 'desc' => 'Validates titles and descriptions for optimal lengths and uniqueness.'],
                    ['title' => 'Robots & Sitemaps', 'desc' => 'Scans for robots.txt files and XML sitemaps to verify indexing pathways.']
                ],
                'fixes' => [
                    ['problem' => 'Missing Title or Description', 'fix' => 'Add unique meta tags inside your HTML head element.'],
                    ['problem' => 'Slow Loading Assets', 'fix' => 'Compress script sizing, optimize heavy image assets, and configure cache.'],
                    ['problem' => 'Invalid Canonical Link', 'fix' => 'Ensure rel="canonical" points to the preferred URL variant.']
                ]
            ],
            'free-seo-checker' => [
                'title' => '100% Free SEO Checker - Audit Your Domain | LaraSEOScan',
                'header' => 'Free SEO Checker',
                'meta_description' => 'Analyze your website score, missing title tags, keyword densities, and image assets completely free without registration.',
                'description' => 'Analyze your website score, missing title tags, keyword densities, and image assets completely free.',
                'icon' => 'bi-gift-fill',
                'focus' => 'Free Domain SEO Check',
                'keywords' => 'free seo checker, free domain audit, online seo score tool',
                'faqs' => [
                    ['q' => 'Are there any hidden costs?', 'a' => 'None. You can run individual page audits completely free, and create an account to save your history.'],
                    ['q' => 'Will this scan affect my site\'s speed?', 'a' => 'No. Our scanner makes a limited number of asynchronous requests, ensuring zero impact on live traffic.']
                ],
                'h2_title' => 'Demonstrate Immediate Value with Instant Domain Analysis',
                'h2_desc' => 'Evaluate title tags, description limits, and heading structure in seconds. Receive a clear score out of 100 to guide your next optimization steps.',
                'features' => [
                    'Immediate Diagnostics' => 'Get audit feedback immediately after entering your URL.',
                    'Scorecard Breakdown' => 'Visual graphs segment errors, warnings, and notice indicators.',
                    'Actionable Roadmaps' => 'Clear checklists showing exactly what needs fixing.'
                ],
                'code_title' => 'Simple SEO Head Markup Structure',
                'code_example' => "<head>\n  <title>Free Domain Checker | LaraSEOScan</title>\n  <meta name=\"description\" content=\"Audit your website score completely free.\">\n</head>",
                'checks' => [
                    ['title' => 'SEO Score Rating', 'desc' => 'Calculates a score out of 100 based on core crawler rules.'],
                    ['title' => 'Heading Hierarchy', 'desc' => 'Checks if heading tags are structured cleanly.'],
                    ['title' => 'Asset Optimization', 'desc' => 'Verifies image formats and missing alternative tags.']
                ],
                'fixes' => [
                    ['problem' => 'Low Audit Score', 'fix' => 'Address red-marked critical errors before fixing warnings.'],
                    ['problem' => 'Missing Image Alt Text', 'fix' => 'Add descriptive alt tags to improve image search indexing.']
                ]
            ],
            'website-seo-checker' => [
                'title' => 'Complete Website SEO Checker & Site Auditor | LaraSEOScan',
                'header' => 'Website SEO Checker',
                'meta_description' => 'Run a full crawling check of your entire site hierarchy to discover performance issues, broken sitemaps, and indexing blockers.',
                'description' => 'Run a full crawling check of your entire site hierarchy to discover performance issues, sitemaps, and indexing blockers.',
                'icon' => 'bi-globe2',
                'focus' => 'Website Core Auditing',
                'keywords' => 'website seo checker, site auditor, link crawl tool',
                'faqs' => [
                    ['q' => 'Does this check subdomains?', 'a' => 'Yes, our advanced audit validates links and media assets mapping across primary domains and subdomains.'],
                    ['q' => 'Can I export my results?', 'a' => 'Yes! Registered users can download comprehensive PDF and CSV reports for their developers.']
                ],
                'h2_title' => 'Comprehensive Architecture Auditing',
                'h2_desc' => 'Large websites depend on clean internal links. Our auditor indexes path structures to find orphan URLs and redirect loops.',
                'features' => [
                    'Internal Link Audits' => 'Ensures PageRank flows correctly across all pages.',
                    'Asset Size Audits' => 'Scans heavy images and scripts that increase TTFB.',
                    'Crawl Error Logs' => 'Captures server response errors and invalid pathways.'
                ],
                'code_title' => 'Internal Link & Nav Sitemap Declaration',
                'code_example' => "<nav>\n  <a href=\"/about-us\">About</a>\n  <a href=\"/services\">Services</a>\n  <a href=\"/contact\" rel=\"nofollow\">Contact</a>\n</nav>",
                'checks' => [
                    ['title' => 'Link Response Codes', 'desc' => 'Ensures all internal anchors yield a 200 OK status.'],
                    ['title' => 'Orphan Page Scan', 'desc' => 'Checks if sitemap URLs are discoverable through standard linking structures.']
                ],
                'fixes' => [
                    ['problem' => 'Broken Links (404)', 'fix' => 'Remove dead anchor elements or update links to direct destinations.'],
                    ['problem' => 'Redirect Chains', 'fix' => 'Update redirects to point directly to their final addresses.']
                ]
            ],
            'meta-tag-checker' => [
                'title' => 'Meta Tag Checker - Verify SEO Meta Data | LaraSEOScan',
                'header' => 'Meta Tag Checker',
                'meta_description' => 'Quickly check your pages for meta title, description tags, and social media Open Graph headers to maximize click-through rates.',
                'description' => 'Quickly check your pages for meta title, description tags, and social media Open Graph headers.',
                'icon' => 'bi-tags-fill',
                'focus' => 'Meta Tag Presence',
                'keywords' => 'meta tag checker, meta tags validation, seo meta viewer',
                'faqs' => [
                    ['q' => 'Why are meta tags important?', 'a' => 'Meta tags introduce your pages to search crawlers and generate search result snippets that drive user clicks.'],
                    ['q' => 'Which meta tags are critical for SEO?', 'a' => 'The Title Tag, Meta Description, Meta Robots, and Canonical tags are absolute requirements.']
                ],
                'h2_title' => 'Ensure Clear Meta Communication with Search Engines',
                'h2_desc' => 'Missing metadata causes Google to auto-generate snippets, often yielding unappealing or incomplete descriptions in search results.',
                'features' => [
                    'Snippet Previews' => 'See what your page looks like on Google Search Results.',
                    'Validation Check' => 'Finds duplicate metadata and tags that are too long.',
                    'OG Social Card Support' => 'Ensures cards look beautiful when shared on social networks.'
                ],
                'code_title' => 'Complete Meta Tags HTML Template',
                'code_example' => "<head>\n  <title>SEO Target Title</title>\n  <meta name=\"description\" content=\"A concise description under 160 characters.\">\n  <meta name=\"robots\" content=\"index, follow\">\n  <meta property=\"og:title\" content=\"Social Card Title\">\n</head>",
                'checks' => [
                    ['title' => 'Title Tag Verification', 'desc' => 'Checks for title presence, concise length, and duplication.'],
                    ['title' => 'Description Verification', 'desc' => 'Validates that meta description character counts stay between 50 and 160.']
                ],
                'fixes' => [
                    ['problem' => 'Missing Meta Description', 'fix' => 'Add <meta name="description" content="..."> to the HTML head.'],
                    ['problem' => 'Missing Open Graph Tags', 'fix' => 'Ensure og:title, og:image and og:description properties are added.']
                ]
            ],
            'meta-description-checker' => [
                'title' => 'Meta Description Length & CTR Checker | LaraSEOScan',
                'header' => 'Meta Description Checker',
                'meta_description' => 'Test your meta description tag lengths. Keep descriptions between 50 and 160 characters for optimal search snippet display.',
                'description' => 'Test your meta description tag lengths. Ensure description tags are within 50 to 160 characters for optimal display.',
                'icon' => 'bi-file-earmark-text-fill',
                'focus' => 'Meta Description Optimization',
                'keywords' => 'meta description checker, meta description length tool, seo snippet checker',
                'faqs' => [
                    ['q' => 'What is the ideal meta description length?', 'a' => 'Google recommends keeping descriptions between 50 and 160 characters (or under 960 pixels on desktop).'],
                    ['q' => 'Does a meta description directly improve rankings?', 'a' => 'No, but it greatly improves Click-Through Rates (CTR), which is a positive search signal.']
                ],
                'h2_title' => 'Maximize Click-Through Rates with Optimized Snippets',
                'h2_desc' => 'Long descriptions get truncated with an ellipsis (...), hiding key calls-to-action. Short descriptions fail to provide enough context to searchers.',
                'features' => [
                    'Length Meter' => 'Instant warning if characters go above 160 or below 50.',
                    'Call-to-Action Checks' => 'Ensures descriptors contain actionable language.',
                    'Duplicate Scan' => 'Verifies that descriptions are unique across all scanned pages.'
                ],
                'code_title' => 'Meta Description Declaration Syntax',
                'code_example' => "<meta name=\"description\" content=\"Shop premium running shoes with free delivery and easy 30-day returns. Find your perfect fit online today!\" />",
                'checks' => [
                    ['title' => 'Character Count Check', 'desc' => 'Ensures description falls into the 50-160 characters sweet spot.'],
                    ['title' => 'Uniqueness Audit', 'desc' => 'Finds duplicate description fields across your site paths.']
                ],
                'fixes' => [
                    ['problem' => 'Description Too Long', 'fix' => 'Shorten description to focus on target search terms and clear CTA.'],
                    ['problem' => 'Description Too Short', 'fix' => 'Elaborate on page benefits to give searchers clear context.']
                ]
            ],
            'title-tag-checker' => [
                'title' => 'Title Tag Checker - Optimize Search Headings | LaraSEOScan',
                'header' => 'Title Tag Checker',
                'meta_description' => 'Validate title tag lengths and ensure your focus keywords are placed near the beginning of your page titles for better rankings.',
                'description' => 'Validate title tag lengths and ensure your focus keywords are placed near the beginning of your page titles.',
                'icon' => 'bi-type-h1',
                'focus' => 'SEO Title Validation',
                'keywords' => 'title tag checker, page title length tool, title tag optimizer',
                'faqs' => [
                    ['q' => 'How long should a title tag be?', 'a' => 'Keep your title tags between 30 and 60 characters to prevent truncation on search results.'],
                    ['q' => 'Where should I place keywords in title tags?', 'a' => 'Place your primary focus keyword near the beginning of the title for maximum weight.']
                ],
                'h2_title' => 'Mastering the Most Critical On-Page SEO Signal',
                'h2_desc' => 'The title tag is the primary link displayed in search results. An optimized title improves both search rankings and user clicks.',
                'features' => [
                    'Keyword Proximity analysis' => 'Checks if important phrases sit near the beginning of the title.',
                    'Brand Separation checks' => 'Ensures brand titles are cleanly separated from topics.',
                    'Truncation warnings' => 'Alerts you if titles are too long for mobile screens.'
                ],
                'code_title' => 'SEO Title Tag Structure Example',
                'code_example' => "<title>Running Shoes for Men & Women | BrandName</title>",
                'checks' => [
                    ['title' => 'Title Character Check', 'desc' => 'Checks if the title tag is within the optimal 30-60 character limit.'],
                    ['title' => 'Vague Title Detection', 'desc' => 'Flags generic titles like \"Home\" or \"New Page\" that dilute target weight.']
                ],
                'fixes' => [
                    ['problem' => 'Title Tag Too Long', 'fix' => 'Shorten target title to keep it concise and place brand name at the end.'],
                    ['problem' => 'Missing Title Tag', 'fix' => 'Create a <title> element inside the HTML <head>.']
                ]
            ],
            'h1-checker' => [
                'title' => 'H1 Header Tag Checker - Heading Hierarchy Auditor | LaraSEOScan',
                'header' => 'H1 Tag Checker',
                'meta_description' => 'Audits page headings to verify the presence of H1 tags and ensures heading tags follow a correct visual layout hierarchy.',
                'description' => 'Audits page headings to verify the presence of H1 tags and ensures heading tags follow a correct visual layout hierarchy.',
                'icon' => 'bi-hash',
                'focus' => 'Heading Tag Structure',
                'keywords' => 'h1 checker, heading hierarchy audit, h1 tag validator',
                'faqs' => [
                    ['q' => 'Can I have multiple H1 tags on a page?', 'a' => 'While HTML5 supports multiple H1s, SEO best practices recommend exactly one H1 tag per page representing the main topic.'],
                    ['q' => 'Why does heading order matter?', 'a' => 'Order helps crawlers understand the outline of your content. You should not jump from H1 to H3 directly.']
                ],
                'h2_title' => 'Structuring Pages for Search Engine Comprehension',
                'h2_desc' => 'Headings form the index outline of your page. A logical structure (H1 -> H2 -> H3) makes it easy for bots to parse your sections.',
                'features' => [
                    'Single H1 Validation' => 'Checks that every page has exactly one main H1 header.',
                    'Hierarchy Flow Checks' => 'Warns you if heading tags skip nesting levels.',
                    'Keyword Check' => 'Verifies focus terms are present in heading content.'
                ],
                'code_title' => 'HTML Heading Hierarchy Blueprint',
                'code_example' => "<h1>Main Topic of the Page</h1>\n<h2>Sub-section Header</h2>\n<h3>Detail Points</h3>",
                'checks' => [
                    ['title' => 'H1 Count Validation', 'desc' => 'Checks that every crawled page contains exactly one H1 element.'],
                    ['title' => 'Nesting Flow Check', 'desc' => 'Highlights places where heading tags skip levels (like jumping from H1 to H3).']
                ],
                'fixes' => [
                    ['problem' => 'Multiple H1 Tags', 'fix' => 'Keep only one H1 for main header, demote secondary H1s to H2 or H3 tags.'],
                    ['problem' => 'Missing H1 Element', 'fix' => 'Add exactly one <h1> containing page target topic.']
                ]
            ],
            'broken-link-checker' => [
                'title' => 'Broken Link Checker - Find Dead Internal & External Links | LaraSEOScan',
                'header' => 'Broken Link Checker',
                'meta_description' => 'Identifies broken hyperlinks on your site that yield 404 errors, protecting page link equity and user navigation.',
                'description' => 'Identifies broken hyperlinks on your site that yield 404 errors, protecting page link equity and navigation.',
                'icon' => 'bi-link-45deg',
                'focus' => 'Broken Hyperlinks (404s)',
                'keywords' => 'broken link checker, find 404 links, site link checker',
                'faqs' => [
                    ['q' => 'What is a broken link?', 'a' => 'A link pointing to a page that no longer exists, returning a 404 Not Found error.'],
                    ['q' => 'How do broken links affect my SEO?', 'a' => 'They stop crawl bots, waste your crawl budget, and create a bad experience that increases bounce rates.']
                ],
                'h2_title' => 'Protect Your Link Equity & Domain Authority',
                'h2_desc' => 'Search engines follow links to discover new content. A broken link acts as a dead end, diluting PageRank flow.',
                'features' => [
                    '404 Response Capturing' => 'Validates all outbound URLs to ensure they load properly.',
                    'Anchor Text Analysis' => 'Helps identify which links are broken and where they live.',
                    'Redirect Loops Detection' => 'Flags multiple nested hops that delay page loads.'
                ],
                'code_title' => 'HTML Hyperlink Declaration Syntax',
                'code_example' => "<a href=\"https://example.com/target-page\">\n  Descriptive Anchor Text\n</a>",
                'checks' => [
                    ['title' => 'Dead Links Audit', 'desc' => 'Inspects all anchor href parameters to catch 404 or 500 response codes.'],
                    ['title' => 'Loopback Check', 'desc' => 'Detects redirection structures that delay access loops.']
                ],
                'fixes' => [
                    ['problem' => 'Broken Outbound Links', 'fix' => 'Update target URL or remove the anchor tag if resource is permanently gone.'],
                    ['problem' => 'No-Follow Directives', 'fix' => 'Use rel="nofollow" for user-generated or sponsored link pathways.']
                ]
            ],
            'robots-txt-checker' => [
                'title' => 'Robots.txt Checker - Verify Crawler Allow Rules | LaraSEOScan',
                'header' => 'Robots.txt Checker',
                'meta_description' => 'Tests your domain\'s robots.txt file to confirm that critical search bots are not restricted from indexing important pages.',
                'description' => 'Tests your domain\'s robots.txt file to confirm that search bots are not restricted from indexing pages.',
                'icon' => 'bi-robot',
                'focus' => 'Robots.txt Crawl Directive Check',
                'keywords' => 'robots txt checker, robots txt validator, crawl accessibility checker',
                'faqs' => [
                    ['q' => 'What is a robots.txt file?', 'a' => 'A file at the root of your site instructing search engines on which paths they are allowed to crawl.'],
                    ['q' => 'Can robots.txt stop pages from indexing?', 'a' => 'Yes, if you disallow a path, Googlebot will not crawl it (though it might still index the URL if linked elsewhere).']
                ],
                'h2_title' => 'Control Your Crawl Budget and Access Path rules',
                'h2_desc' => 'Misconfigured disallow rules can block access to your entire CSS/JS asset directory, preventing search engines from rendering your site.',
                'features' => [
                    'Root Level Discovery' => 'Finds your robots.txt file automatically at standard locations.',
                    'Directive Parsing' => 'Extracts all User-Agent and Disallow rules for validation.',
                    'Sitemap Declarations Check' => 'Ensures the XML sitemap URL is listed in robots.txt.'
                ],
                'code_title' => 'Standard robots.txt Declaration Syntax',
                'code_example' => "User-agent: *\nDisallow: /admin/\nAllow: /admin/login\n\nSitemap: https://example.com/sitemap.xml",
                'checks' => [
                    ['title' => 'Robots File Existence', 'desc' => 'Verifies that robots.txt is active at your domain root.'],
                    ['title' => 'Sitemap Declaration check', 'desc' => 'Confirm sitemap path is explicitly listed inside rules.']
                ],
                'fixes' => [
                    ['problem' => 'Missing Robots File', 'fix' => 'Deploy a plain text robots.txt file to the public directory of your server.'],
                    ['problem' => 'Accidental Disallow All', 'fix' => 'Avoid Disallow: / which blocks all search engines from indexing your domain.']
                ]
            ],
            'sitemap-checker' => [
                'title' => 'XML Sitemap Checker - Validate URL Listings | LaraSEOScan',
                'header' => 'Sitemap.xml Checker',
                'meta_description' => 'Finds and tests your XML sitemap to guarantee all listed pages are reachable, clean, and indexed correctly by Google.',
                'description' => 'Finds and tests your XML sitemap to guarantee all listed pages are reachable, clean, and indexed correctly.',
                'icon' => 'bi-diagram-3-fill',
                'focus' => 'XML Sitemap Verification',
                'keywords' => 'sitemap checker, xml sitemap validator, sitemap url check',
                'faqs' => [
                    ['q' => 'What is an XML Sitemap?', 'a' => 'A map listing all your critical pages, telling search engines which URLs to crawl and prioritize.'],
                    ['q' => 'Should I list redirecting pages in sitemaps?', 'a' => 'No. Sitemaps should only contain pages that return a 200 OK status code.']
                ],
                'h2_title' => 'Optimize Crawler Discovery with Clean Maps',
                'h2_desc' => 'Providing broken or redirecting links in your XML sitemap wastes search engine resources, lowering your crawling efficiency.',
                'features' => [
                    'Sitemap Discovery' => 'Scans default directories and robots.txt declarations to find maps.',
                    'URL Status Verification' => 'Validates that listed links are active and not canonicalized.',
                    'Format Validation' => 'Checks structure compatibility with Google Search Console.'
                ],
                'code_title' => 'Standard XML Sitemap Structure',
                'code_example' => "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n  <url>\n    <loc>https://example.com/</loc>\n    <lastmod>2026-08-28</lastmod>\n  </url>\n</urlset>",
                'checks' => [
                    ['title' => 'Format Validation', 'desc' => 'Verifies sitemap structure matches sitemaps.org namespace.'],
                    ['title' => 'Reachable URLs', 'desc' => 'Validates that sitemap links return status code 200 OK.']
                ],
                'fixes' => [
                    ['problem' => 'Canonical Errors in Sitemap', 'fix' => 'Only include canonical URLs, removing redirects or 404 pages.'],
                    ['problem' => 'Missing XML Sitemap', 'fix' => 'Generate and submit a sitemap.xml to Google Search Console.']
                ]
            ],
            'schema-markup-checker' => [
                'title' => 'Schema Markup & JSON-LD Rich Snippet Checker | LaraSEOScan',
                'header' => 'Schema Markup Checker',
                'meta_description' => 'Extracts and validates JSON-LD structured data on your website to ensure search engines understand your content structure.',
                'description' => 'Extracts and validates JSON-LD structured data on your website to ensure search engines understand your products or articles.',
                'icon' => 'bi-code-slash',
                'focus' => 'Structured Data Validation',
                'keywords' => 'schema markup checker, json-ld validator, structured data audit',
                'faqs' => [
                    ['q' => 'What is schema markup?', 'a' => 'Code placed on your page helping search engines provide rich snippets (like product prices, review stars, and event dates).'],
                    ['q' => 'How do I check if my schema is valid?', 'a' => 'Enter your URL below to extract and validate JSON-LD schemas against standard schemas.']
                ],
                'h2_title' => 'Unlocking Rich Search Results & Higher CTRs',
                'h2_desc' => 'Structured data helps search engines understand the semantic meaning of your content, qualifying your pages for visual Rich Snippets.',
                'features' => [
                    'JSON-LD Extraction' => 'Extracts all script-based structured data on the page.',
                    'Validation Feedback' => 'Highlights missing parameters for common schemas.',
                    'Rich Snippet Eligibility Check' => 'Helps identify missing fields required for review stars.'
                ],
                'code_title' => 'Structured JSON-LD Schema Example',
                'code_example' => "<script type=\"application/ld+json\">\n{\n  \"@context\": \"https://schema.org\",\n  \"@type\": \"Organization\",\n  \"name\": \"BrandName\",\n  \"url\": \"https://example.com\"\n}\n</script>",
                'checks' => [
                    ['title' => 'JSON-LD Format Check', 'desc' => 'Parses schema scripts to ensure JSON structures contain no syntax errors.'],
                    ['title' => 'Type Declaration Check', 'desc' => 'Confirms standard schema types are properly declared.']
                ],
                'fixes' => [
                    ['problem' => 'Malformed JSON syntax', 'fix' => 'Escape quotation marks and ensure commas separate properties correctly.'],
                    ['problem' => 'Missing Required Fields', 'fix' => 'Review schema specifications to include required name or description values.']
                ]
            ],
            'open-graph-checker' => [
                'title' => 'Open Graph Meta Tag & Social Share Checker | LaraSEOScan',
                'header' => 'Open Graph Checker',
                'meta_description' => 'Validate og:title, og:image, and og:description properties to guarantee preview cards render beautifully on social platforms.',
                'description' => 'Validate og:title, og:image, and og:description properties to guarantee cards render beautifully on social platforms.',
                'icon' => 'bi-share-fill',
                'focus' => 'Social Share Optimization',
                'keywords' => 'open graph checker, og tag validator, social share preview',
                'faqs' => [
                    ['q' => 'What is Open Graph (OG)?', 'a' => 'A protocol used by social networks (Facebook, LinkedIn) to generate rich preview cards when links are shared.'],
                    ['q' => 'Why does og:image matter?', 'a' => 'An optimized image drives social clicks. Images should be 1200x630 pixels for modern platforms.']
                ],
                'h2_title' => 'Optimize Share Previews for Social Platforms',
                'h2_desc' => 'Ensure your shared content has descriptive titles, compelling summaries, and beautiful, high-resolution preview graphics.',
                'features' => [
                    'Open Graph Scraper' => 'Extracts og:title, og:type, og:image, and og:url fields.',
                    'Card Verification' => 'Ensures key assets load and are accessible to external scraper bots.',
                    'Fallback Tag Analysis' => 'Checks if Twitter Card meta tags are present.'
                ],
                'code_title' => 'Open Graph Meta Tags Example',
                'code_example' => "<meta property=\"og:title\" content=\"Social Page Title\" />\n<meta property=\"og:description\" content=\"Snippet shown when shared.\" />\n<meta property=\"og:image\" content=\"https://example.com/social-card.jpg\" />",
                'checks' => [
                    ['title' => 'OG Image Presence', 'desc' => 'Verifies social media cards have high-resolution preview images defined.'],
                    ['title' => 'Social Title check', 'desc' => 'Ensures shared card titles are descriptive and fit layout boundaries.']
                ],
                'fixes' => [
                    ['problem' => 'Missing og:image Tag', 'fix' => 'Add a meta tag pointing to a representative card banner.'],
                    ['problem' => 'Truncated Social Title', 'fix' => 'Keep Open Graph title values under 55 characters to prevent clipping.']
                ]
            ],
            'image-seo-checker' => [
                'title' => 'Image SEO & Optimization Performance Checker | LaraSEOScan',
                'header' => 'Image SEO Checker',
                'meta_description' => 'Analyze image sizing, alt tags, compression formats (WebP/AVIF), and lazy loading to maximize image search rankings.',
                'description' => 'Analyze image sizing, alt tags, compression formats, and lazy loading to maximize image search visibility.',
                'icon' => 'bi-image-fill',
                'focus' => 'Image SEO Optimization',
                'keywords' => 'image seo checker, alt tag validator, image optimization audit',
                'faqs' => [
                    ['q' => 'Why does image alt text matter?', 'a' => 'Alt text provides accessibility for visually impaired readers and describes image context to Google Search.'],
                    ['q' => 'How do images affect core web vitals?', 'a' => 'Large, uncompressed images slow down your page. Using lazy loading solves layout shift issues.']
                ],
                'h2_title' => 'Rank Higher in Image Search results',
                'h2_desc' => 'Optimize file names, alt attributes, and image dimensions to rank in Google Images and speed up page load times.',
                'features' => [
                    'Alt Tag Verification' => 'Identifies all images missing alternative descriptive text.',
                    'Lazy Loading Checks' => 'Checks for the loading="lazy" attribute on off-screen assets.',
                    'Modern Format Auditing' => 'Warns you if images are using heavy legacy formats (like PNG or JPG).'
                ],
                'code_title' => 'Optimized Image HTML Element Syntax',
                'code_example' => "<img src=\"/images/shoes.webp\" \n     alt=\"Premium running shoes for men\" \n     loading=\"lazy\" \n     width=\"600\" height=\"400\" />",
                'checks' => [
                    ['title' => 'Alt Tag validation', 'desc' => 'Scans images to flags missing or empty alt descriptors.'],
                    ['title' => 'Format Evaluation', 'desc' => 'Highlights legacy image formats that delay page loads.']
                ],
                'fixes' => [
                    ['problem' => 'Missing Image Alt Tag', 'fix' => 'Add clear descriptive alt text describing the image contents.'],
                    ['problem' => 'Heavy File Sizes', 'fix' => 'Compress images to modern formats like WebP or AVIF and set dimensions.']
                ]
            ],
        ];

        return $tools[$key] ?? $tools['seo-checker'];
    }

    public function show(string $tool)
    {
        $data = $this->getToolData($tool);
        return view('landing.seo-tool', compact('data', 'tool'));
    }

    public function hub()
    {
        $toolsList = [
            'seo-checker' => $this->getToolData('seo-checker'),
            'free-seo-checker' => $this->getToolData('free-seo-checker'),
            'website-seo-checker' => $this->getToolData('website-seo-checker'),
            'meta-tag-checker' => $this->getToolData('meta-tag-checker'),
            'meta-description-checker' => $this->getToolData('meta-description-checker'),
            'title-tag-checker' => $this->getToolData('title-tag-checker'),
            'h1-checker' => $this->getToolData('h1-checker'),
            'broken-link-checker' => $this->getToolData('broken-link-checker'),
            'robots-txt-checker' => $this->getToolData('robots-txt-checker'),
            'sitemap-checker' => $this->getToolData('sitemap-checker'),
            'schema-markup-checker' => $this->getToolData('schema-markup-checker'),
            'open-graph-checker' => $this->getToolData('open-graph-checker'),
            'image-seo-checker' => $this->getToolData('image-seo-checker'),
        ];
        return view('landing.hub', compact('toolsList'));
    }
}
