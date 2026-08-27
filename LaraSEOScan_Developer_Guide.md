# LaraSEOScan — Complete Developer Guide & Knowledge Source

> **Last Updated:** 2026-08-27  
> **Project Repo:** `c:\xampp\htdocs\LaraSEOScan` (GitHub: `mayuroza3/LaraSEOScan`)  
> **Author:** Mayur Oza  
> **Status:** MVP / Functional — actively developed

---

## 1. Project Overview

**LaraSEOScan** is a self-hosted, open-source SEO auditing web application built on **Laravel 12**. It crawls websites, parses HTML, runs modular SEO rule checks, and stores results in a normalized database. Users can view paginated reports, export PDF/CSV, and track scan history.

### What It Does (Confirmed Working)
| Capability | Status |
|---|---|
| User Auth (Register/Login/Profile) | ✅ Implemented (Laravel Breeze) |
| Submit a URL for scanning | ✅ Working |
| Async scan via queue job | ✅ Working |
| Parallel crawl via Guzzle Pool | ✅ Working |
| robots.txt parsing & respect | ✅ Working |
| Sitemap.xml parsing | ✅ Working |
| SEO issue detection (rule engine) | ✅ Working |
| On-page meta checks | ✅ Working |
| H1 / heading checks | ✅ Working |
| Open Graph checks | ✅ Working |
| JSON-LD validation | ✅ Working |
| Duplicate content (shingle) | ✅ Working |
| Image optimization checks | ✅ Working |
| Keyword density analysis | ✅ Working |
| Broken link detection | ⚠️ Built, but DISABLED in config |
| SEO score (0–100) | ⚠️ Rough formula in view only, not persisted |
| Scan history dashboard | ✅ Working |
| PDF export | ✅ Working |
| CSV export | ✅ Working |
| Soft delete scans | ✅ Working |
| Daily scan limit (100/user) | ✅ (note: message says "5" but code checks 100) |
| Legal pages (Privacy/ToS/Cookie) | ✅ Views present |
| SSRF protection | ❌ Missing |
| Score stored in DB | ❌ Not implemented |
| Real-time scan progress (WebSocket/Polling) | ❌ Not implemented (page auto-refreshes every 2 min) |
| API endpoint | ❌ Not implemented |
| Admin panel | ❌ Not implemented |

---

## 2. Tech Stack

### Backend
| Layer | Technology |
|---|---|
| Framework | Laravel 12 (PHP 8.2+) |
| Auth scaffolding | Laravel Breeze |
| HTTP client / crawler | GuzzleHttp 7.x + Guzzle Pool (parallel) |
| HTML parsing | Symfony DomCrawler 7.x |
| DOM processing | PHP native `DOMDocument` + `DOMXPath` |
| Queue / Async jobs | Laravel Queue (database driver by default) |
| PDF export | `barryvdh/laravel-dompdf` ^3.1 |
| CSV/Excel export | `maatwebsite/excel` ^3.1 |
| ORM | Eloquent (Laravel) |
| Database | MySQL or PostgreSQL (configurable) |

### Frontend
| Layer | Technology |
|---|---|
| CSS framework | Bootstrap 5.3 (CDN) |
| Icons | Bootstrap Icons 1.11 |
| Charts | Apache ECharts 5.6 (CDN) |
| JavaScript | Vanilla JS + Alpine.js (via Vite) |
| Asset pipeline | Vite + laravel-vite-plugin |
| CSS pre-processing | TailwindCSS 3 (configured but app layout uses Bootstrap) |
| Fonts (dashboard) | Figtree (Google/Bunny) |
| Fonts (landing) | Inter (Google) |

> **Note:** There's a configuration mismatch: `package.json` has TailwindCSS, but actual UI uses Bootstrap 5 via CDN. TailwindCSS is mostly unused in current views.

---

## 3. Directory Structure

```
LaraSEOScan/
├── app/
│   ├── Exports/
│   │   └── SeoScanExport.php          # CSV export using maatwebsite/excel
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                  # Breeze auth controllers
│   │   │   ├── Controller.php         # Base controller
│   │   │   ├── ProfileController.php  # User profile update
│   │   │   └── SeoScanController.php  # MAIN controller: scan, results, history, export
│   │   └── Requests/
│   │       ├── Auth/                  # Breeze form requests
│   │       ├── ProfileUpdateRequest.php
│   │       └── StoreScanRequest.php   # URL validation for scan submission
│   ├── Jobs/
│   │   └── ProcessSeoScan.php         # Queue job that triggers SeoScannerService
│   ├── Models/
│   │   ├── Issue.php                  # Appears to be old/unused model
│   │   ├── SeoImage.php               # Images found on a page
│   │   ├── SeoIssue.php               # Single detected SEO problem
│   │   ├── SeoLink.php                # Links found on a page
│   │   ├── SeoPage.php                # A crawled page within a scan
│   │   ├── SeoScan.php                # Top-level scan entity
│   │   └── User.php                   # Extended with phone, company, role
│   ├── Providers/                     # Standard Laravel providers
│   ├── Rules/                         # Laravel validation rules (may be unused)
│   ├── Seo/
│   │   └── Rules/
│   │       ├── BrokenLinkRule.php     # Finds broken links (DISABLED)
│   │       ├── H1Rule.php             # H1 presence & heading hierarchy
│   │       ├── ImageOptimizationRule.php # Image format, size, lazy-load
│   │       ├── JsonLdValidatorRule.php   # JSON-LD / Schema.org validation
│   │       ├── KeywordDensityRule.php    # Keyword density & stop-word analysis
│   │       ├── MetaDescriptionRule.php   # Meta desc length checks
│   │       ├── MissingTitleRule.php      # Title tag checks
│   │       ├── OpenGraphRule.php         # OG tags + og:image size check
│   │       ├── Registry.php              # Reads config/seo.php, instantiates enabled rules
│   │       ├── SeoRule.php               # Interface all rules must implement
│   │       └── ShingleDuplicateRule.php  # Duplicate content detection
│   ├── Services/
│   │   ├── Seo/
│   │   │   ├── KeywordDensityService.php   # Extracts top 20 keywords
│   │   │   ├── RedirectAnalyzerService.php  # Analyzes redirect chains (unused?)
│   │   │   ├── RobotsTxtService.php         # Fetches, parses robots.txt
│   │   │   └── SitemapService.php           # Fetches, parses sitemap.xml
│   │   └── SeoScannerService.php            # THE BRAIN: crawling + parsing + rules
│   └── View/                               # View composers (if any)
├── config/
│   ├── seo.php         # CRITICAL: Enable/disable rules, weights, crawler settings
│   └── ...             # Standard Laravel configs
├── database/
│   └── migrations/     # 13 migrations covering full schema
├── resources/
│   ├── css/app.css     # Main app CSS (loaded via Vite)
│   ├── js/app.js       # Main app JS (Alpine.js bootstrap)
│   └── views/
│       ├── auth/            # Breeze auth views
│       ├── components/      # Blade components
│       │   ├── seo-issues-chart.blade.php   # ECharts donut chart
│       │   ├── seo-issues-table.blade.php   # Reusable issues table
│       │   ├── seo-score-card.blade.php     # Score display card
│       │   └── seo-summary-card.blade.php   # Summary stat cards
│       ├── exports/         # PDF template (scan-pdf.blade.php)
│       ├── layouts/
│       │   ├── app.blade.php        # Dashboard layout (Bootstrap)
│       │   ├── navigation.blade.php # Navbar
│       │   └── legal.blade.php      # Layout for legal pages
│       ├── legal/           # privacy, terms, cookies views
│       ├── profile/         # Profile edit view
│       ├── scan/
│       │   ├── index.blade.php    # "New Scan" form
│       │   ├── history.blade.php  # Dashboard / scan list
│       │   ├── results.blade.php  # Scan results report
│       │   └── status.blade.php   # Minimal status endpoint view
│       └── welcome.blade.php      # Public landing page
└── routes/
    ├── auth.php    # Breeze auth routes
    ├── console.php # Schedule (empty)
    └── web.php     # All application routes
```

---

## 4. Database Schema (All Tables)

### `users`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| name | varchar | |
| email | varchar | unique |
| email_verified_at | timestamp | nullable |
| password | varchar | hashed |
| phone | varchar | nullable, added in migration |
| company | varchar | nullable |
| role | varchar | nullable |
| remember_token | varchar | |
| created_at / updated_at | timestamp | |

### `seo_scans`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| uuid | varchar | unique, auto-generated on create |
| user_id | bigint FK | → users.id |
| url | varchar | The root URL scanned |
| status | varchar | QUEUED → COMPLETED / FAILED |
| has_robots_txt | boolean | checked before queuing |
| has_sitemap_xml | boolean | checked before queuing |
| deleted_at | timestamp | SoftDeletes |
| created_at / updated_at | timestamp | |

### `seo_pages`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| seo_scan_id | bigint FK | → seo_scans.id |
| url | varchar | |
| title | varchar | nullable |
| description | text | nullable |
| canonical | varchar | nullable |
| robots | varchar | nullable (meta robots tag) |
| headings | json | array of {tag, text} |
| status_code | int | nullable |
| word_count | int | nullable |
| shingle_signature | text | nullable (comma-sep MD5 hashes) |
| structured_data | json | nullable (JSON-LD objects) |
| fetched_at | timestamp | nullable |
| keyword_density | json | nullable ({total_words, keywords: {word: {count, density}}}) |
| image_total_size | int | nullable (bytes) |
| image_unoptimized_count | int | nullable |
| deleted_at | timestamp | SoftDeletes |
| created_at / updated_at | timestamp | |

### `seo_links`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| seo_page_id | bigint FK | → seo_pages.id |
| href | varchar | absolute URL |
| status_code | int | nullable (null = not yet checked) |
| is_internal | boolean | |
| redirect_chain | json | nullable (array of URLs traversed) |
| deleted_at | timestamp | SoftDeletes |
| created_at / updated_at | timestamp | |

### `seo_images`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| seo_page_id | bigint FK | → seo_pages.id |
| src | varchar | |
| alt | varchar | nullable |
| created_at / updated_at | timestamp | |

### `seo_issues`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| seo_page_id | bigint FK | → seo_pages.id |
| rule_key | varchar | e.g. `meta.title`, `content.h1_count` |
| severity | varchar | `critical`, `error`, `warning`, `info` |
| message | text | Human-readable description |
| selector | varchar | nullable, CSS selector for element |
| context | json | nullable, extra data (lengths, URLs, etc.) |
| deleted_at | timestamp | SoftDeletes |
| created_at / updated_at | timestamp | |

### Standard Laravel tables
- `jobs`, `job_batches`, `failed_jobs` — Queue driver tables
- `cache`, `cache_locks` — Cache tables
- `password_reset_tokens`, `sessions` — Auth helpers

---

## 5. Routes Map

```
GET  /                          → welcome (landing page) [public]
GET  /scan                      → SeoScanController@create [auth]
POST /scan                      → SeoScanController@scan [auth]
GET  /results/{uuid}            → SeoScanController@results [auth]
GET  /scan/history              → SeoScanController@history [auth]
DELETE /scan/{uuid}             → SeoScanController@destroy [auth]
GET  /scan/{uuid}/status        → SeoScanController@status [auth]
GET  /scan/{uuid}/export/pdf    → SeoScanController@exportPdf [auth]
GET  /scan/{uuid}/export/csv    → SeoScanController@exportCsv [auth]
GET  /profile                   → ProfileController@edit [auth]
PATCH /profile                  → ProfileController@update [auth]
DELETE /profile                 → ProfileController@destroy [auth]
GET  /legal                     → legal.index [public]
GET  /privacy-policy            → legal.privacy [public]
GET  /terms-of-service          → legal.terms [public]
GET  /cookie-policy             → legal.cookies [public]
     + Standard Breeze auth routes (login, register, etc.)
```

---

## 6. Core Data Flow (Scan Lifecycle)

```
1. User visits /scan → renders scan/index.blade.php (URL input form)

2. User submits URL → POST /scan → SeoScanController@scan
   a. Validates URL (StoreScanRequest: required|url|max:2048)
   b. Checks daily scan limit (100 per user per day)
   c. Checks robots.txt + sitemap.xml existence (blocking HTTP calls)
   d. Creates SeoScan record (status=QUEUED, uuid auto-generated)
   e. Dispatches ProcessSeoScan job to queue
   f. Redirects to /scan/history

3. Queue worker processes ProcessSeoScan job:
   a. Calls SeoScannerService@scan($scan)
   b. Calls RobotsTxtService@fetch() to parse robots.txt rules
   c. Calls SitemapService@fetch() to get all sitemap URLs
   d. Calls crawlBatch([$rootUrl], $scan, depth=0)

4. crawlBatch() parallel crawler:
   a. Filters URLs already visited / blocked by robots.txt
   b. Marks URLs as visited
   c. Creates Guzzle Pool (concurrency=5) for GET requests
   d. For each successful response:
      - Parses HTML with Symfony DomCrawler
      - Extracts headings (H1-H6)
      - Runs KeywordDensityService on raw HTML
      - Creates SeoPage record in DB
      - Extracts links → creates SeoLink records (status_code=null)
      - Extracts images → creates SeoImage records
      - Calls runRules($page, $html)
      - Collects internal links for nextBatch
   e. Recursively calls crawlBatch(nextBatch, scan, depth+1)
   f. Stops at maxDepth=5 or maxPages=200

5. runRules($page, $html):
   a. Creates DOMDocument from HTML (with UTF-8 fix hack)
   b. Creates DOMXPath for querying
   c. Gets enabled rules from Registry::all() → reads config/seo.php
   d. Calls $rule->check($page, $dom, $xpath) on each enabled rule
   e. Creates SeoIssue records for each returned issue

6. Post-crawl sitemap orphan check:
   - Gets all crawled URLs from DB
   - Compares with sitemap URLs
   - Creates sitemap.missing_page issues for uncrawled sitemap URLs (max 50)

7. scan->status set to COMPLETED

8. User views /results/{uuid}:
   a. Loads SeoScan by UUID (user-owned only)
   b. Calculates score in Blade template: 100 - (issues/pages * 5)
   c. Loads paginated issues (10/page, ordered by severity)
   d. Loads paginated pages with eager-loaded issues/links/images
   e. Shows score card, issues chart, summary stats, accordion per page
```

---

## 7. SEO Rules — Full Reference

All rules are in `app/Seo/Rules/` and implement the `SeoRule` interface.

### Interface Contract
```php
interface SeoRule {
    public function key(): string;       // e.g. 'meta.title'
    public function title(): string;     // e.g. 'Title tag presence and length'
    public function category(): string;  // e.g. 'meta'
    public function check(SeoPage $page, DOMDocument $dom, DOMXPath $xpath): array;
    // Returns array of issues: ['severity', 'message', 'selector', 'context']
}
```

### Rules Status Table

| Class | Key | Category | Status | What It Checks |
|---|---|---|---|---|
| `MissingTitleRule` | `meta.title` | meta | ✅ Enabled | Title missing; too short (<30); too long (>60) |
| `MetaDescriptionRule` | `meta.description` | meta | ✅ Enabled | Desc missing; too short (<50); too long (>160). Also saves desc to SeoPage |
| `H1Rule` | `content.h1_count` | content | ✅ Enabled | No H1; multiple H1s; H3 without H2 (hierarchy) |
| `ShingleDuplicateRule` | `content.shingle_duplicates` | content | ✅ Enabled | 5-word shingle comparison; 75% threshold; compares against ALL other pages in DB |
| `OpenGraphRule` | `og.basic_presence` | og | ✅ Enabled | Missing og:title/og:description/og:image; fetches og:image to check dimensions; duplicate OG tags |
| `JsonLdValidatorRule` | `structured.jsonld` | structured | ✅ Enabled | Invalid JSON; missing @type; unsupported type; Article/Product/BreadcrumbList field checks |
| `ImageOptimizationRule` | `image.optimization` | images | ✅ Enabled | Missing lazy-load attr; non-WebP/AVIF format; >200KB; updates SeoPage.image_total_size |
| `KeywordDensityRule` | `keyword.density` | content | ✅ Enabled | Reads from SeoPage.keyword_density (pre-computed); flags over/under density |
| `BrokenLinkRule` | `links.broken` | links | ⛔ DISABLED | HEAD-requests all links; flags 4xx/5xx; detects redirect chains |

### config/seo.php Crawler Settings
```php
'crawler' => [
    'max_redirects' => 5,
    'image_max_size_kb' => 200,
    'keyword_density_min' => 0.5,
    'keyword_density_max' => 3,
    'check_external_links' => false,
],
'weights' => [
    'meta' => 30, 'content' => 25, 'og' => 15, 'structured' => 30, 'links' => 10
],
```

---

## 8. Services — Full Reference

### `SeoScannerService` (app/Services/SeoScannerService.php)
The main brain. Injected with sub-services via constructor.

**Properties:**
- `$visited[]` — URL → true map to prevent re-crawling
- `$maxDepth = 5` — Max crawl depth from root
- `$maxPages = 200` — Max pages per scan

**Methods:**
- `scan(SeoScan $scan)` — Entry point. Calls crawlBatch then does sitemap orphan check
- `crawlBatch(array $urls, SeoScan $scan, int $depth)` — Parallel Guzzle Pool crawl
- `crawlAndScan()` — OLD method (commented out), was synchronous recursive crawl
- `runRules(SeoPage $page, string $html)` — Runs all enabled rules
- `resolveUrl($relative, $base)` — Converts relative to absolute URL
- `isInternal($url, $base)` — Checks if URL belongs to same host
- `checkLinks(array $links)` — Parallel link HEAD-check (used by BrokenLinkRule)
- `fetchRobotsTxt()` / `fetchSitemap()` — Helper fetchers (legacy)

### `RobotsTxtService` (app/Services/Seo/RobotsTxtService.php)
- `fetch(string $url)` — Fetches and parses robots.txt for the domain
- `isAllowed(string $url)` — Returns bool based on longest-match rule algorithm
- Handles `User-agent: *` and `User-agent: LaraSEOScanBot`

### `SitemapService` (app/Services/Seo/SitemapService.php)
- `fetch(string $url)` — Fetches sitemap.xml (handles sitemap index recursively)
- `getUrls()` — Returns all found URLs
- Handles gzip-encoded sitemaps

### `KeywordDensityService` (app/Services/Seo/KeywordDensityService.php)
- `analyze(string $html)` — Strips scripts/styles/tags, lowercases, removes stop words
- Returns `{total_words, keywords: {word: {count, density}}}` (top 20)

### `RedirectAnalyzerService` (app/Services/Seo/RedirectAnalyzerService.php)
- `analyze(string $url)` — Analyzes redirect chain for a URL
- Returns `{redirect_count, redirects[], final_status, final_url}`
- **NOTE: This service exists but appears to be unintegrated into the main flow**

---

## 9. Models — Relationships

```
User
  └── hasMany SeoScan (via seo_scans.user_id)
        └── hasMany SeoPage (via seo_pages.seo_scan_id)
              ├── hasMany SeoLink (via seo_links.seo_page_id)
              ├── hasMany SeoImage (via seo_images.seo_page_id)
              └── hasMany SeoIssue (via seo_issues.seo_page_id)
```

**SeoScan:**
- Has `SoftDeletes` trait
- Auto-generates UUID on creating via `booted()` observer
- `scopeTodayByUser($query, $userId)` — helper scope

**SeoPage:**
- Has `SoftDeletes` trait  
- Casts: `headings` → array, `structured_data` → array, `keyword_density` → array, `fetched_at` → datetime
- Has BOTH `scan()` and `seoscan()` relationships (duplicate, pick one)

**SeoLink:**
- Has `SoftDeletes` trait
- Casts: `redirect_chain` → array, `is_internal` → boolean

**SeoIssue:**
- Has `SoftDeletes` trait
- Cast: `context` → array

---

## 10. Frontend / Views

### Layout Hierarchy
```
layouts/app.blade.php          (Dashboard layout)
  ├── Bootstrap 5.3 (CDN)
  ├── Bootstrap Icons (CDN)
  ├── ECharts 5.6 (CDN)
  ├── public/js/seo-dashboard.js (ECharts setup)
  └── @vite(['resources/css/app.css', 'resources/js/app.js'])
      └── layouts/navigation.blade.php (Navbar)

layouts/guest.blade.php        (Auth forms layout)
layouts/legal.blade.php        (Legal pages layout)
welcome.blade.php              (Public landing — standalone, no layout)
```

### Blade Components (resources/views/components/)
| Component | Props | Purpose |
|---|---|---|
| `seo-score-card` | `:score` | Circular score display |
| `seo-issues-chart` | `:critical :error :warning :info` | ECharts donut chart |
| `seo-summary-card` | `title :value icon color` | Stat card |
| `seo-issues-table` | `:issues` | Paginated/collection issues table |

### Key Pages
| Page | File | Description |
|---|---|---|
| Landing | `welcome.blade.php` | Public page, SEO meta, features, how-it-works, CTA |
| New Scan | `scan/index.blade.php` | Simple URL input form |
| History | `scan/history.blade.php` | Dashboard with stats + scans table, auto-refreshes every 120s |
| Results | `scan/results.blade.php` | Full report: score, chart, issue table, page accordion |
| Status | `scan/status.blade.php` | Minimal status check view |

### Score Formula (In Blade, Not Persisted)
```php
$deduction = ($critical * 10) + ($error * 5) + ($warning * 2);
$issuesPerPage = $totalIssues / $totalPages;
$score = max(0, 100 - ($issuesPerPage * 5));
```
> ⚠️ This is calculated in the view, not stored in `seo_scans.score`. The `$deduction` variable is computed but never used.

---

## 11. Known Bugs & Issues

### Critical Issues
1. **SSRF Vulnerability** — `StoreScanRequest` only validates `url` format. A user could input `http://192.168.1.1` or `http://localhost` to scan internal infrastructure.
2. **Race Condition on Scan Limit** — `todayScanCount` check is not atomic. Multiple rapid requests can bypass the daily limit.
3. **Score Not Persisted** — Score is recalculated every page load. Not stored in DB. Cannot be used for historical trending.
4. **Inconsistent Daily Limit** — Error message says "5 scans/day" but the code checks `>= 100`.

### Architectural Issues
5. **Memory Leak Risk** — `crawlBatch()` is recursive within a single job. For large sites (500+ pages), `$visited` array and PHP objects can exhaust memory limit.
6. **No Robots.txt Fetch in Scanner** — `RobotsTxtService` is instantiated via DI but never called with `->fetch()` before `crawlBatch` starts. The robots check in `crawlBatch` would always return "allowed" for URLs since rules are empty.
7. **SitemapService Not Called** — `SitemapService` is injected but `->fetch()` is never called in `scan()` before `crawlBatch`. The `getUrls()` call returns empty array.
8. **Missing Log import** — `SeoScannerService` uses `Log::error()` without `use Illuminate\Support\Facades\Log;` import.
9. **Duplicate Model Relationship** — `SeoPage` has both `scan()` and `seoscan()` pointing to the same `SeoScan`.
10. **`Issue.php` Model** — An `Issue.php` model exists in `app/Models/` but appears unused (separate from `SeoIssue.php`).
11. **`RedirectAnalyzerService`** — Service exists but is never called from `SeoScannerService` or any controller.
12. **BrokenLinkRule references seoPage** — Line 93 does `$linksArray[0]->seoPage->url` but `SeoLink` has a `page()` relationship, not `seoPage()`. This would cause an error when the rule is enabled.

### UI Issues
13. **No Real-time Progress** — Scan status is checked by hard-refreshing history page every 2 minutes. No WebSocket/polling on the results page.
14. **Hero Section Missing Image** — Landing page hero has a placeholder div instead of actual screenshot.
15. **Score Calculation Bug** — `$deduction` is calculated in the view but `$score` uses a different formula that ignores `$deduction`.

---

## 12. Config Reference (config/seo.php)

```php
return [
    'rules' => [
        \App\Seo\Rules\MissingTitleRule::class      => true,   // ENABLED
        \App\Seo\Rules\MetaDescriptionRule::class   => true,   // ENABLED
        \App\Seo\Rules\H1Rule::class                => true,   // ENABLED
        \App\Seo\Rules\ShingleDuplicateRule::class  => true,   // ENABLED
        \App\Seo\Rules\OpenGraphRule::class         => true,   // ENABLED
        \App\Seo\Rules\JsonLdValidatorRule::class   => true,   // ENABLED
        \App\Seo\Rules\BrokenLinkRule::class        => false,  // DISABLED
        \App\Seo\Rules\ImageOptimizationRule::class => true,   // ENABLED
        \App\Seo\Rules\KeywordDensityRule::class    => true,   // ENABLED
    ],
    'crawler' => [
        'max_redirects' => 5,
        'image_max_size_kb' => 200,
        'keyword_density_min' => 0.5,
        'keyword_density_max' => 3,
        'check_external_links' => false,
    ],
    'weights' => [
        'meta' => 30, 'content' => 25, 'og' => 15, 'structured' => 30, 'links' => 10,
    ],
    'severity' => ['error' => 'red', 'warning' => 'orange', 'info' => 'blue'],
    'shingles' => ['size' => 5, 'threshold' => 0.75],
];
```

---

## 13. How to Add a New SEO Rule

1. Create `app/Seo/Rules/MyNewRule.php`:
```php
<?php
namespace App\Seo\Rules;
use App\Models\SeoPage;

class MyNewRule implements SeoRule {
    public function key(): string { return 'category.rule_name'; }
    public function title(): string { return 'Human readable title'; }
    public function category(): string { return 'category'; }
    
    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array {
        $issues = [];
        // Your check logic here
        // Return issues array with: severity, message, selector (optional), context (optional)
        return $issues;
    }
}
```

2. Register in `config/seo.php`:
```php
'rules' => [
    // ... existing rules ...
    \App\Seo\Rules\MyNewRule::class => true,
],
```

That's it — the `Registry::all()` auto-discovers enabled rules.

---

## 14. Development Environment Setup

```bash
# 1. Clone
git clone https://github.com/mayuroza3/LaraSEOScan.git
cd LaraSEOScan

# 2. PHP dependencies
composer install

# 3. Node dependencies
npm install

# 4. Environment
cp .env.example .env
php artisan key:generate

# 5. Configure .env
DB_CONNECTION=mysql
DB_DATABASE=laraseocan
DB_USERNAME=root
DB_PASSWORD=

# 6. Database
php artisan migrate

# 7. Run everything (uses concurrently)
composer run dev
# This runs: php artisan serve + queue:listen + pail + npm run dev
```

**Or run individually:**
```bash
php artisan serve          # Web server on http://127.0.0.1:8000
php artisan queue:work     # Required for scans to actually process
npm run dev                # Vite hot reload
```

---

## 15. Dependency Summary

### Composer (PHP)
| Package | Version | Purpose |
|---|---|---|
| `laravel/framework` | ^12.0 | Core framework |
| `guzzlehttp/guzzle` | ^7.9 | HTTP client + Pool |
| `symfony/dom-crawler` | ^7.3 | HTML traversal |
| `barryvdh/laravel-dompdf` | ^3.1 | PDF generation |
| `maatwebsite/excel` | ^3.1 | CSV/Excel export |
| `laravel/breeze` | ^2.3 (dev) | Auth scaffolding |
| `laravel/tinker` | ^2.10.1 | REPL |

### NPM (Node)
| Package | Version | Purpose |
|---|---|---|
| `vite` | ^6.2.4 | Asset bundler |
| `laravel-vite-plugin` | ^1.2.0 | Laravel Vite integration |
| `alpinejs` | ^3.4.2 | Reactive JS for Blade |
| `tailwindcss` | ^3.1.0 | CSS framework (configured but mostly unused) |
| `axios` | ^1.8.2 | HTTP client for JS |
| `concurrently` | ^9.0.1 | Run multiple npm scripts |

---

## 16. Priority Issues to Fix (Recommended Next Development Steps)

### Immediate / Critical Fixes
1. **Fix SSRF** — Add `ValidPublicUrl` rule to `StoreScanRequest`
2. **Fix robots.txt** — Call `$this->robotsService->fetch($scan->url)` in `scan()` before `crawlBatch()`
3. **Fix sitemap fetch** — Call `$this->sitemapService->fetch($scan->url)` in `scan()` before crawling
4. **Add `use Log`** — Add `use Illuminate\Support\Facades\Log;` to `SeoScannerService`
5. **Fix scan limit message** — Change "5" in error message to "100" or change code limit to 5
6. **Fix score** — Persist score in `seo_scans` table and calculate properly

### Feature Additions
7. **Enable BrokenLinkRule** — Fix the `->seoPage` bug in BrokenLinkRule then enable
8. **Real-time scan status** — Replace 120s auto-refresh with AJAX polling of `/scan/{uuid}/status`
9. **Score history** — Store score in DB, show trend on dashboard
10. **SSRF Protection** — Block private IPs (10.x, 192.168.x, 127.x, 169.254.x, etc.)

---

## 17. Roadmap Summary (From roadmap.md)

### Phase 1: Stability & Security
- [ ] SSRF Protection Validator
- [ ] robots.txt parser call (actually fix the service to be called)
- [ ] Enable BrokenLinkRule by default
- [ ] Retry logic for timeouts

### Phase 2: SEO Feature Completion
- [ ] Scoring System (persist in DB, weighted by category)
- [ ] Image file size + format checks (partially done in ImageOptimizationRule)
- [ ] Keyword density — expand to support user-defined target keywords
- [ ] Sitemap validation (fix the service call issue first)

### Phase 3: UX & Visualization
- [ ] Dashboard graphs ("Issues over time")
- [ ] Issue grouping / filtering by severity
- [ ] Real-time progress bar (SSE or AJAX polling)

### Phase 4: SaaS Productization
- [ ] Billing/Credits system
- [ ] Team/Organization support
- [ ] REST API
- [ ] White-label PDF reports

---

## 18. Architecture Diagram

```
[Browser] → POST /scan → [SeoScanController]
                              ↓
                    [ProcessSeoScan Job] → Queue
                              ↓ (async)
                    [SeoScannerService]
                    ├── RobotsTxtService → HTTP GET /robots.txt
                    ├── SitemapService  → HTTP GET /sitemap.xml
                    └── crawlBatch()
                         ├── Guzzle Pool (5 concurrent)
                         │   ├── GET page1.html
                         │   ├── GET page2.html
                         │   └── ...
                         └── For each page:
                             ├── Symfony DomCrawler
                             ├── SeoPage::create()
                             ├── SeoLink::create() (for each link)
                             ├── SeoImage::create() (for each img)
                             └── runRules()
                                 ├── Registry::all() → config/seo.php
                                 ├── Rule1->check() → SeoIssue::create()
                                 ├── Rule2->check() → SeoIssue::create()
                                 └── ...
                              ↓ (recurse)
                          crawlBatch(nextBatch, depth+1)

[Browser] → GET /results/{uuid} → [SeoScanController@results]
                → loads scan + paginated pages/issues
                → renders Blade view with Bootstrap + ECharts
```

---

*This document was generated by comprehensive automated analysis of the LaraSEOScan codebase. Use as a primary reference for development, debugging, and AI-assisted development with ChatGPT or similar tools.*
