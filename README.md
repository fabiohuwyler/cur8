# Cur8 Wordpress Plugin

A beautiful WordPress plugin for curating and sharing status updates, images, quotes, and locations with a modern, intuitive interface.

![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue)
![PHP](https://img.shields.io/badge/PHP-7.2%2B-purple)
![License](https://img.shields.io/badge/License-GPL%20v2-green)

## ✨ Features

- **4 Update Types**: Status updates, images, quotes, and locations
- **Beautiful Web Interface**: Access `/cur8` for a gorgeous posting experience
- **Password Protection**: Optional password-protect your posting interface
- **Link Support**: Add links to status updates with custom text
- **5 Design Themes**: Minimalist, Modern, Bold, Shadow, and Gradient
- **Color Customization**: Choose from color picker, HEX input, or theme colors
- **FSE Integration**: Automatically detects WordPress Full Site Editing theme colors
- **REST API**: Full REST API support for programmatic access
- **Responsive Design**: Beautiful on desktop, tablet, and mobile
- **Shortcode & Widget**: Display updates anywhere on your site

## 📸 Screenshots

### Posting Interface
Modern, gradient-styled interface at `/cur8` for creating updates.
![alt text](https://github.com/fabiohuwyler/cur8/blob/main/screenshots/cur8_posting.png?raw=true)


### Admin Dashboard
Manage all your updates with filtering, stats, and quick actions.

### Frontend Display
Beautiful grid or list layouts with 5 customizable themes.

## 🚀 Installation

### From GitHub

1. Download the latest release
2. Upload to `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to **Settings → Cur8** to configure

### Manual Installation

```bash
cd wp-content/plugins
git clone https://github.com/fabiohuwyler/cur8.git
```

Then activate through WordPress admin.

## 🎯 Usage

### Creating Updates

Visit `yoursite.com/cur8` to access the posting interface. Choose from:

- **📝 Status**: Text updates with optional links
- **📷 Image**: Upload images with captions
- **💬 Quote**: Share quotes with author attribution
- **📍 Location**: Share locations with descriptions

### Displaying Updates

#### Shortcode
```php
[cur8_updates limit="10" type="" layout="grid"]
```

**Parameters:**
- `limit` - Number of updates (default: 10)
- `type` - Filter by type: status, image, quote, location (default: all)
- `layout` - Display style: grid or list (default: grid)

**Examples:**
```php
[cur8_updates limit="5" type="image" layout="grid"]
[cur8_updates limit="20" type="status" layout="list"]
```

#### Widget
Go to **Appearance → Widgets** and add the "Cur8 Updates" widget.

#### PHP Function
```php
<?php
if (function_exists('cur8_display_updates')) {
    cur8_display_updates(array(
        'limit' => 10,
        'type' => '',
        'layout' => 'grid'
    ));
}
?>
```

## ⚙️ Configuration

### Settings (Settings → Cur8)

**Primary Color**
- Color picker for visual selection
- HEX code input for precise colors
- Theme color palette (FSE integration)

**Design Theme**
- **Minimalist**: Clean border-only design
- **Modern**: Subtle, professional styling (default)
- **Bold**: Filled backgrounds with high contrast
- **Shadow**: Elevated cards with depth
- **Gradient**: Colorful gradient backgrounds

**Access Password**
- Set a password to protect `/cur8` access
- Leave empty to disable protection
- Session-based authentication

## 🔌 REST API

### Endpoints

```
GET    /wp-json/cur8/v1/updates
POST   /wp-json/cur8/v1/updates
GET    /wp-json/cur8/v1/updates/{id}
PUT    /wp-json/cur8/v1/updates/{id}
DELETE /wp-json/cur8/v1/updates/{id}
```

### Example Request

```bash
curl -X POST https://yoursite.com/wp-json/cur8/v1/updates \
  -H "Content-Type: application/json" \
  -d '{
    "update_type": "status",
    "content": "Hello from the API!",
    "link_url": "https://example.com",
    "link_text": "Learn More"
  }'
```

## 🎨 Customization

### Custom CSS

Add custom styles in your theme:

```css
.wptext-update-card {
    /* Your custom styles */
}
```

### Theme Integration

Cur8 automatically integrates with WordPress FSE theme colors. Your theme's color palette will appear in the settings for easy selection.

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- Modern browser with JavaScript enabled

## 🛠️ Development

### File Structure

```
cur8/
├── cur8.php                 # Main plugin file
├── includes/
│   ├── class-cur8-database.php
│   ├── class-cur8-api.php
│   ├── class-cur8-frontend.php
│   ├── class-cur8-admin.php
│   └── class-cur8-styles.php
├── templates/
│   ├── cur8-form.php
│   ├── cur8-password.php
│   ├── updates-display.php
│   └── admin-*.php
├── assets/
│   ├── css/
│   └── js/
└── README.md
```

### Database Schema

**Table**: `wp_cur8_updates`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| update_type | varchar(20) | status, image, quote, location |
| content | text | Main content |
| link_url | varchar(255) | Optional link URL |
| link_text | varchar(100) | Optional link text |
| image_url | varchar(255) | Image URL for image type |
| quote_text | text | Quote content |
| quote_author | varchar(100) | Quote author |
| location | varchar(255) | Location name |
| status | varchar(20) | published, draft |
| created_at | datetime | Creation timestamp |

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 Changelog

### Version 1.0.0
- Initial release
- 4 update types (status, image, quote, location)
- Beautiful web interface at `/cur8`
- Password protection
- Link support in status updates
- 5 design themes
- Color customization with FSE integration
- REST API
- Shortcode and widget support

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## 👤 Author

**Fabio Huwyler**
- Website: [fabiohuwyler.ch](https://fabiohuwyler.ch)
- GitHub: [@fabiohuwyler](https://github.com/fabiohuwyler)

## 🙏 Acknowledgments

- Built with WordPress best practices
- Inspired by modern microblogging platforms
- Designed for content creators and curators

## 📞 Support

For support, please:
- Open an issue on [GitHub](https://github.com/fabiohuwyler/cur8/issues)

---

Made with ❤️ by [Fabio Huwyler](https://fabiohuwyler.ch)
