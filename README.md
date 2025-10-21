# AwContentFeed - PrestaShop Module

Display YouTube videos and WordPress articles on your PrestaShop store.

## Features

- **YouTube videos**: Automatically fetches title and thumbnail via oEmbed API (no API key required)
- **WordPress articles**: Extracts metadata from Open Graph tags (title, description, thumbnail)
- **Front-office display**: Modern card-based grid layout
- **Metadata refresh**: One-click button to update metadata without editing
- **Active/inactive status**: Toggle visibility per item
- **Drag-free positioning**: Items ordered by position field

## Structure

```
awcontentfeed/
├── awcontentfeed.php           # Main module file
├── composer.json               # PSR-4 autoload configuration
├── config/
│   ├── routes.yml             # Symfony routes
│   └── services.yml           # Dependency injection
├── sql/
│   ├── install.php            # Database table creation
│   └── uninstall.php          # Database table cleanup
├── src/
│   ├── Controller/
│   │   └── AwContentFeedController.php
│   ├── Form/
│   │   ├── ContentFeedItemFormType.php
│   │   ├── ContentFeedItemFormDataProvider.php
│   │   └── (old General* files removed)
│   ├── Repository/
│   │   └── ContentFeedItemRepository.php
│   └── Service/
│       └── MetadataFetcherService.php
└── views/
    ├── css/
    │   └── awcontentfeed.css  # Front-office styles
    ├── js/admin/
    │   └── configuration.js   # Back-office interactions
    └── templates/
        ├── admin/
        │   └── configuration.html.twig
        └── hook/
            └── contentfeed.tpl
```

## Installation

1. Copy the `awcontentfeed` folder to `/modules/`
2. Run `composer dump-autoload` in the module directory
3. Install the module from PrestaShop back-office
4. The module automatically creates the `ps_awcontentfeed_item` table

## Database

The module uses a **dedicated table** (`ps_awcontentfeed_item`) instead of `ps_configuration`:

| Field | Type | Description |
|-------|------|-------------|
| `id_awcontentfeed_item` | INT | Primary key |
| `type` | ENUM | `youtube` or `wordpress` |
| `url` | VARCHAR(500) | Content URL |
| `title` | VARCHAR(255) | Fetched title |
| `description` | TEXT | Fetched description |
| `thumbnail` | VARCHAR(500) | Fetched thumbnail URL |
| `position` | INT | Display order |
| `active` | TINYINT | Visibility status |
| `date_add` / `date_upd` | DATETIME | Timestamps |

## Configuration

Back-office: **Modules → Content Feed**

### Add Content
1. Select type (YouTube or WordPress)
2. Enter URL
3. Toggle active status
4. Save → Metadata is **automatically fetched**

### Actions
- **Edit**: Modify content and re-fetch metadata
- **Refresh** 🔄: Re-fetch metadata without editing (useful when source content is updated)
- **Toggle**: Enable/disable display
- **Delete**: Remove item

## Front-office Display

**Default hook**: `displayHome`

The module implements the **WidgetInterface** and can be used in any hook via:
```php
{hook h='displayHome' mod='awcontentfeed'}
```

### Display Features
- Responsive grid: 1 col (mobile) → 2 cols (tablet) → 3 cols (desktop)
- Bootstrap 5 cards with hover effects
- Clickable cards (entire card is a link)
- Type badges (YouTube red, WordPress blue)
- Lazy-loaded images

## Theme Compatibility

**Built for**: Hummingbird theme (Bootstrap 5)

For other themes:
- Edit `views/css/awcontentfeed.css` for custom styling
- Edit `views/templates/hook/contentfeed.tpl` for markup changes

## Metadata Fetching

### YouTube
- **API**: oEmbed (no key required)
- **Data**: Title, thumbnail (maxresdefault)
- **Description**: Not available via oEmbed (set to `null`)

### WordPress
- **Method**: HTML parsing + Open Graph tags
- **Data**: `og:title`, `og:description`, `og:image`
- **Fallback**: Standard meta tags if OG not found

### Refresh Button
Use when source content changes:
- Video title updated on YouTube
- Article thumbnail changed on WordPress
- One-click re-fetch without re-entering URL

## Technical Details

- **PrestaShop**: 8.x compatible (min 1.7.0.0)
- **PHP**: ≥ 7.1
- **Symfony**: Forms, routes, services
- **Translation**: New system (domain-based)
- **JavaScript**: Vanilla (no jQuery), ES6 syntax
- **CSS**: Custom + Bootstrap 5 classes

## License

Academic Free License 3.0 (AFL-3.0)

## Author

Axelweb - contact@axelweb.fr
