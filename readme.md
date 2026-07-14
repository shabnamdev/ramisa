# Ramisa Online Chat

Ramisa Online Chat adds a polished floating contact widget to WordPress with RTL-ready styling, a native neumorphic settings panel, additional contact buttons, working hours, quick replies, animations, live preview, and shortcode support.

## Features

- Floating online chat widget
- Dedicated Ramisa Chat dashboard menu
- Responsive RTL-ready design
- Native vertical neumorphic settings panel
- Dedicated custom database table for plugin settings
- Developer card with SHABNAM.DEV and the version read from the plugin header
- Local green/red destination status indicator
- Public chat URL field with optional click-to-chat number fallback
- Live admin preview
- Custom profile name, role, message, avatar, icon, badge, label, card title, and card subtitle
- Additional contact options with separate URLs, optional avatars, list/grid layout, and search
- Quick reply buttons
- Working hours with timezone and per-day start/end schedule
- Online/offline status text and optional hide-while-offline behavior
- Multiple color palettes, sizes, shapes, card styles, and animation modes
- Shortcodes: `[ramisa_online_chat]` and `[ramisa_online_chat_button]`

## WordPress.org compliance notes

This package is fully functional after activation. It does not include license checks, trial restrictions, hidden upgrade gates, obfuscated code, remote build dependencies, or build-only block assets. CSS and JavaScript assets are local and loaded through WordPress enqueue APIs. Plugin settings are stored in a dedicated custom database table and are removed by uninstall.php when the plugin is deleted from WordPress.

## External services

The plugin does not call an external service from the server side. It only opens the public chat destination configured by the site administrator after a visitor clicks a link or button. The dashboard status indicator validates the destination locally and does not ping a remote server.

## License

GPLv2 or later.

## Version 1.0.0

Initial WordPress.org release with a dedicated Ramisa dashboard, custom database storage, working hours, extra contact options, quick replies, local destination status, polished copy, and fully working frontend controls.
