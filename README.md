<div align="center">

# Ramisa Whatsapp Chat

### A polished, lightweight, and RTL-ready floating contact widget for WordPress

Create a professional floating chat experience with working hours, multiple contact options, quick replies, live preview, visual customization, and shortcode support—without external APIs or build dependencies.

[![Version](https://img.shields.io/badge/version-1.0.0-6f42c1.svg)](#changelog)
[![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-21759b.svg?logo=wordpress&logoColor=white)](#requirements)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4.svg?logo=php&logoColor=white)](#requirements)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)](LICENSE)
[![RTL Ready](https://img.shields.io/badge/RTL-ready-success.svg)](#features)

[Features](#features) · [Installation](#installation) · [Shortcodes](#shortcodes) · [Privacy](#privacy-and-external-services) · [Contributing](#contributing)

</div>

---

## Overview

**Ramisa Online Chat** adds a customizable floating contact widget to WordPress. It gives site administrators a native settings panel for configuring the chat destination, support profile, visual appearance, working hours, additional contacts, quick replies, and inline shortcode buttons.

The plugin is self-contained and works after activation without license keys, trial restrictions, hidden upgrade gates, remote build dependencies, or server-side API calls.

## Features

### Chat experience

- Floating chat button and expandable contact card
- Public chat URL or click-to-chat number
- Custom support name, role, message, avatar, title, and subtitle
- Up to three quick-reply buttons
- Up to three separate support contacts
- Optional contact search
- List or grid contact layout
- Optional visitor-time display
- Optional notification badge
- Automatic card opening
- Open links in a new tab

### Availability controls

- Per-day working schedules
- Configurable start and end times
- Full timezone selection
- Custom online and offline messages
- Optional hiding outside working hours
- Independent desktop and mobile visibility

### Appearance

- Five color palettes: Emerald, Blue, Violet, Gold, and Dark
- Three button sizes: Small, Normal, and Large
- Three button shapes: Rounded, Circle, and Pill
- Three card styles: Neumorphic, Glass, and Compact
- Five animation modes: Soft, Pulse, Bounce, Float, and None
- Left or right screen positioning
- RTL-ready and responsive layout
- Live preview inside the WordPress dashboard

### WordPress integration

- Dedicated **Ramisa Chat** admin menu
- Native WordPress capability and nonce checks
- Sanitized settings and escaped frontend output
- Local CSS and JavaScript assets
- Dedicated custom database table
- Automatic cleanup during plugin deletion
- Translation-ready text domain
- Two shortcode aliases

## Screenshots

> Add your screenshots to `.github/assets/` and update the paths below.

<table>
  <tr>
    <td width="50%">
      <img src=".github/assets/frontend-widget.png" alt="Ramisa Online Chat frontend widget">
      <p align="center"><strong>Frontend chat widget</strong></p>
    </td>
    <td width="50%">
      <img src=".github/assets/settings-panel.png" alt="Ramisa Online Chat settings panel">
      <p align="center"><strong>Native settings panel</strong></p>
    </td>
  </tr>
  <tr>
    <td width="50%">
      <img src=".github/assets/contacts-and-replies.png" alt="Additional contacts and quick replies">
      <p align="center"><strong>Contacts and quick replies</strong></p>
    </td>
    <td width="50%">
      <img src=".github/assets/working-hours.png" alt="Working hours and availability">
      <p align="center"><strong>Working hours</strong></p>
    </td>
  </tr>
</table>

## Requirements

| Requirement | Minimum |
|---|---:|
| WordPress | 5.8 |
| PHP | 7.4 |
| Plugin version | 1.0.0 |
| Build process | Not required |

## Installation

### From the WordPress dashboard

1. Download the latest plugin ZIP.
2. Open **WordPress Dashboard → Plugins → Add New Plugin**.
3. Select **Upload Plugin**.
4. Upload the ZIP file and choose **Install Now**.
5. Activate **Ramisa Online Chat**.
6. Open **Ramisa Chat** in the dashboard.
7. Configure the destination, appearance, contacts, and availability.
8. Save the settings.

### Manual installation

Copy the plugin directory to:

```text
wp-content/plugins/ramisa-online-chat
```

Then activate it from **Plugins → Installed Plugins**.

### Git installation

```bash
cd wp-content/plugins
git clone https://github.com/YOUR_GITHUB_USERNAME/ramisa-online-chat.git
```

Activate the plugin from the WordPress dashboard.

## Quick start

After activation:

1. Open **Ramisa Chat**.
2. Enter a public chat URL or click-to-chat number.
3. Customize the support profile and widget text.
4. Select a theme, button shape, size, position, and card style.
5. Configure working hours and timezone when needed.
6. Add extra contacts or quick replies.
7. Review the live preview.
8. Save the settings.

The floating widget is rendered automatically on the frontend when enabled.

## Shortcodes

Ramisa Online Chat provides two equivalent shortcodes:

```text
[ramisa_online_chat]
[ramisa_online_chat_button]
```

### Basic example

```text
[ramisa_online_chat]
```

### Custom button text

```text
[ramisa_online_chat text="Contact support"]
```

### Custom destination

```text
[ramisa_online_chat url="https://example.com/contact"]
```

### Styled button

```text
[ramisa_online_chat text="Start a conversation" theme="blue" icon="support" size="large"]
```

### Shortcode attributes

| Attribute | Description | Accepted values |
|---|---|---|
| `url` | Overrides the configured destination | Any valid URL |
| `text` | Overrides the button label | Plain text |
| `theme` | Changes the button color palette | `green`, `blue`, `violet`, `gold`, `dark` |
| `icon` | Changes the displayed icon | `chat`, `support`, `send`, `phone`, `help` |
| `size` | Changes the inline button size | `small`, `normal`, `large` |

When an attribute is omitted, the shortcode uses the value saved in the plugin settings.

## Data storage

Plugin settings are stored in a dedicated table:

```text
{wordpress_prefix}ramisa_online_chat_settings
```

For a standard WordPress installation, the table will usually be:

```text
wp_ramisa_online_chat_settings
```

The table is:

- Created when the plugin is activated
- Preserved when the plugin is deactivated
- Removed by `uninstall.php` when the plugin is deleted

This keeps the plugin configuration separate from posts, post metadata, and regular WordPress option rows.

## Privacy and external services

Ramisa Online Chat does not send data to an external service from the WordPress server and does not call a remote messaging API.

When a visitor clicks a configured contact link, the browser opens the destination selected by the site administrator. That destination may belong to a third-party messaging or contact platform.

Site administrators remain responsible for:

- Choosing the destination URL
- Reviewing the destination service's terms
- Updating their privacy policy when required
- Obtaining any legally required user consent

The dashboard destination indicator validates the configured value locally and does not ping a remote server.

## Source structure

```text
ramisa-online-chat/
├── assets/
│   ├── css/
│   │   ├── ramisa-online-chat-admin.css
│   │   └── ramisa-online-chat.css
│   ├── image/
│   │   ├── agent1.webp
│   │   ├── agent2.webp
│   │   ├── agent3.webp
│   │   └── user.webp
│   └── js/
│       ├── ramisa-online-chat-admin.js
│       └── ramisa-online-chat.js
├── languages/
├── index.php
├── ramisa.php
├── readme.md
├── readme.txt
└── uninstall.php
```

## Development

The distributed plugin contains editable PHP, CSS, and JavaScript source files. No Node.js, Composer, bundler, or compilation step is required for the included functionality.

### Local setup

1. Install WordPress locally.
2. Clone or copy the repository into `wp-content/plugins/`.
3. Activate **Ramisa Online Chat**.
4. Make changes directly in the source files.
5. Test the admin panel, frontend widget, responsive behavior, RTL layout, and uninstall routine.

### Recommended checks

Before submitting a pull request, verify:

- WordPress coding conventions
- PHP 7.4 compatibility
- Input sanitization
- Output escaping
- Nonce and capability checks
- RTL and LTR layouts
- Desktop and mobile visibility
- Working-hours calculations
- Shortcode output
- Plugin activation, deactivation, and deletion

## Internationalization

The plugin uses the text domain:

```text
ramisa-online-chat
```

Translation files belong in:

```text
languages/
```

Example filenames:

```text
ramisa-online-chat-fa_IR.po
ramisa-online-chat-fa_IR.mo
```

Do not translate shortcode names, attribute names, database identifiers, file paths, or text-domain values.

## WordPress.org repository layout

For WordPress.org SVN, keep plugin files and visual assets separated:

```text
ramisa-online-chat/
├── assets/
├── tags/
│   └── 1.0.0/
└── trunk/
```

WordPress.org banners, icons, and screenshots belong in the root SVN `assets/` directory—not inside the plugin's own `trunk/assets/` directory.

## Compatibility

| Environment | Status |
|---|---|
| LTR websites | Supported |
| RTL websites | Supported |
| Desktop | Supported |
| Mobile | Supported |
| WordPress Multisite | Requires site-level activation testing |
| Classic Editor | Supported through shortcodes |
| Block Editor | Supported through the Shortcode block |

## Security

The plugin follows standard WordPress security patterns, including:

- `manage_options` capability checks for administration
- Nonce verification before saving settings
- Sanitization of submitted values
- Escaping of rendered attributes and content
- Direct-access protection in PHP files
- `noopener noreferrer` for new-tab links

To report a security issue, avoid opening a public GitHub issue. Contact the maintainer privately:

```text
https://shabnam.dev
```

## Contributing

Contributions, bug reports, and focused improvements are welcome.

1. Fork the repository.
2. Create a feature branch:

```bash
git checkout -b feature/clear-feature-name
```

3. Commit your changes:

```bash
git commit -m "Add: clear description of the change"
```

4. Push the branch:

```bash
git push origin feature/clear-feature-name
```

5. Open a pull request with:
   - A concise summary
   - Testing steps
   - Screenshots for visual changes
   - Compatibility notes when relevant

Please keep pull requests focused and avoid unrelated formatting changes.

## Changelog

### 1.0.0

- Initial WordPress.org release
- Added the floating chat widget
- Added the dedicated Ramisa dashboard
- Added custom database storage
- Added working hours and availability controls
- Added extra contacts and contact search
- Added quick replies
- Added live preview
- Added shortcode output
- Added RTL-ready responsive styling
- Added local destination validation
- Added uninstall cleanup

## Roadmap

Potential future improvements may include:

- Import and export settings
- More contact slots
- Per-page visibility rules
- Additional layout presets
- Accessibility refinements
- Automated test coverage
- Native block editor integration

Roadmap items are proposals and do not represent guaranteed release commitments.

## License

Ramisa Online Chat is licensed under the **GNU General Public License v2.0 or later**.

```text
Copyright (C) SHABNAM

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or any later version.
```

See the [GNU General Public License](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) for details.

## Author

Developed and maintained by **SHABNAM**.

- Website: [shabnam.dev](https://shabnam.dev)
- Plugin website: [shcd.ir](https://shcd.ir)

---

<div align="center">

Built with care for WordPress, responsive interfaces, and RTL websites.

**[Back to top](#ramisa-online-chat)**

</div>
