<div align="center">

# Ramisa Online Chat

### Multilingual README

**[English](#english-version)** · **[فارسی](#persian-version)**

</div>

---

<a id="english-version"></a>

<div align="center">

<img src="https://ps.w.org/ramisa-online-chat/assets/icon-256x256.png?rev=3607150" alt="Ramisa Online Chat icon" width="128" height="128">

# Ramisa Online Chat

### A polished floating contact and chat widget for WordPress



[![Version](https://img.shields.io/badge/version-1.0.0-6f42c1.svg)](#changelog)
[![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-21759b.svg?logo=wordpress&logoColor=white)](#requirements)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4.svg?logo=php&logoColor=white)](#requirements)
[![RTL](https://img.shields.io/badge/RTL-ready-16a085.svg)](#features)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-2ea44f.svg)](#license)

[WordPress.org](https://wordpress.org/plugins/ramisa-online-chat/) ·
[Download](https://downloads.wordpress.org/plugin/ramisa-online-chat.latest-stable.zip) ·
[Support](https://wordpress.org/support/plugin/ramisa-online-chat/) ·
[Developer](https://shabnam.dev)

</div>

<img src="https://ps.w.org/ramisa-online-chat/assets/banner-772x250.png?rev=3607150" alt="Ramisa Online Chat banner" width="100%">

---

## Overview

**Ramisa Online Chat** adds a responsive floating contact widget to WordPress. Site administrators can configure a public chat destination, support profile, working hours, additional contacts, quick replies, visual styles, animations, and inline shortcode buttons from a dedicated native dashboard.

The WordPress.org package is fully functional after activation and does not require a license key, trial subscription, remote build service, or server-side messaging API.

## Features

### Contact experience

- Floating chat button and expandable contact card
- Public chat URL or click-to-chat number
- Custom profile name, role, message, avatar, title, and subtitle
- Up to three quick-reply buttons
- Additional support contacts with separate links and optional avatars
- List and grid contact layouts
- Optional contact search
- Optional notification badge
- Optional visitor-time display
- Optional automatic card opening

### Availability

- Per-day working schedules
- Separate start and end times for each day
- Configurable timezone
- Custom online and offline text
- Optional hiding outside working hours
- Independent desktop and mobile visibility

### Appearance

- Emerald, blue, violet, gold, and dark palettes
- Small, normal, and large button sizes
- Rounded, circular, and pill button shapes
- Neumorphic, glass, and compact card styles
- Soft, pulse, bounce, floating, and no-motion modes
- Left or right screen position
- Responsive and RTL-ready layout
- Live preview in the WordPress dashboard

### WordPress integration

- Dedicated **Ramisa Chat** admin menu
- Local CSS and JavaScript assets
- Dedicated custom database table
- Automatic database cleanup on plugin deletion
- Translation-ready strings
- Native capability, nonce, sanitization, and escaping controls
- Two equivalent shortcodes

## Screenshots

<table>
  <tr>
    <td width="50%">
      <img src="https://ps.w.org/ramisa-online-chat/assets/screenshot-1.png?rev=3607150" alt="Floating chat card on the frontend">
      <p align="center"><strong>Frontend floating chat card</strong></p>
    </td>
    <td width="50%">
      <img src="https://ps.w.org/ramisa-online-chat/assets/screenshot-2.png?rev=3607150" alt="Vertical neumorphic settings panel">
      <p align="center"><strong>Vertical settings panel</strong></p>
    </td>
  </tr>
  <tr>
    <td width="50%">
      <img src="https://ps.w.org/ramisa-online-chat/assets/screenshot-3.png?rev=3607150" alt="Destination status and developer card">
      <p align="center"><strong>Destination status and developer card</strong></p>
    </td>
    <td width="50%">
      <img src="https://ps.w.org/ramisa-online-chat/assets/screenshot-4.png?rev=3607150" alt="Additional contacts and quick replies">
      <p align="center"><strong>Additional contacts and quick replies</strong></p>
    </td>
  </tr>
</table>

> The images above are loaded from the official WordPress.org plugin asset CDN.

## Requirements

| Requirement | Minimum |
|---|---:|
| WordPress | 5.8 |
| PHP | 7.4 |
| Plugin version | 1.0.0 |
| Build step | Not required |

## Installation

### WordPress dashboard

1. Open **Plugins → Add New Plugin**.
2. Select **Upload Plugin**.
3. Upload the plugin ZIP file.
4. Install and activate **Ramisa Online Chat**.
5. Open **Ramisa Chat** in the WordPress dashboard.
6. Configure the destination, profile, appearance, and availability.
7. Save the settings.

### Manual installation

Copy the plugin directory to:

```text
wp-content/plugins/ramisa-online-chat
```

Then activate the plugin from **Plugins → Installed Plugins**.

## Quick start

1. Open **Ramisa Chat** after activation.
2. Enter a public chat URL or click-to-chat number.
3. Customize the support profile and widget text.
4. Choose the palette, shape, size, card style, and position.
5. Configure working hours when required.
6. Add extra contacts or quick replies.
7. Check the live preview.
8. Save the settings.

The floating widget is rendered automatically on the frontend when enabled.

## Shortcodes

Both shortcode names render the same inline contact button:

```text
[ramisa_online_chat]
[ramisa_online_chat_button]
```

### Examples

```text
[ramisa_online_chat text="Contact support"]
```

```text
[ramisa_online_chat url="https://example.com/contact"]
```

```text
[ramisa_online_chat text="Start a conversation" theme="blue" icon="support" size="large"]
```

### Attributes

| Attribute | Purpose | Accepted values |
|---|---|---|
| `url` | Overrides the saved destination | A valid URL |
| `text` | Overrides the button label | Plain text |
| `theme` | Changes the color palette | `green`, `blue`, `violet`, `gold`, `dark` |
| `icon` | Changes the icon | `chat`, `support`, `send`, `phone`, `help` |
| `size` | Changes the button size | `small`, `normal`, `large` |

Omitted attributes use the values saved in the plugin settings.

## Data storage

Settings are stored in a dedicated custom table:

```text
{wordpress_prefix}ramisa_online_chat_settings
```

On a standard WordPress installation, the table is usually:

```text
wp_ramisa_online_chat_settings
```

The table is created during activation, preserved during deactivation, and removed by `uninstall.php` when the plugin is deleted.

## Privacy and external services

Ramisa Online Chat does not send data to an external service from the WordPress server and does not call a remote messaging API.

When a visitor clicks a configured contact link, the browser opens the destination selected by the site administrator. That destination may belong to a third-party messaging or contact platform. Site administrators are responsible for reviewing the relevant terms, privacy requirements, and consent obligations.

The dashboard destination indicator validates the configured value locally and does not ping a remote server.

## Project structure

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
├── readme.txt
└── uninstall.php
```

## Development

The distributed package contains editable PHP, CSS, and JavaScript source files. The included functionality does not require Composer, Node.js, a bundler, or a compilation step.

Before submitting a change, verify:

- PHP 7.4 compatibility
- WordPress coding conventions
- Capability and nonce checks
- Input sanitization and output escaping
- RTL and LTR layouts
- Desktop and mobile visibility
- Working-hours calculations
- Shortcode output
- Activation, deactivation, and uninstall behavior

## Internationalization

The text domain is:

```text
ramisa-online-chat
```

Translation files belong in:

```text
languages/
```

Example Persian filenames:

```text
ramisa-online-chat-fa_IR.po
ramisa-online-chat-fa_IR.mo
```

Do not translate shortcode names, attribute names, database identifiers, file paths, or the text domain.

## Security

The plugin follows standard WordPress security patterns, including:

- `manage_options` capability checks
- Nonce verification before saving
- Sanitization of submitted values
- Escaping of frontend and admin output
- Direct-access protection in PHP files
- `noopener noreferrer` on new-tab links

Please report security issues privately through the developer website rather than publishing sensitive details in a public issue.

## Contributing

Focused bug reports and pull requests are welcome.

1. Fork the repository.
2. Create a focused branch.
3. Make and test the change.
4. Use a clear commit message.
5. Open a pull request with testing steps and screenshots for visual changes.

Please avoid unrelated formatting changes in functional pull requests.

## Changelog

### 1.0.0

- Initial WordPress.org release
- Added the floating chat widget
- Added the dedicated Ramisa dashboard
- Added custom database storage
- Added working hours and availability controls
- Added additional contacts and quick replies
- Added live preview and shortcode output
- Added responsive RTL-ready styling
- Added local destination validation
- Added uninstall cleanup

## License

Ramisa Online Chat is licensed under the **GNU General Public License v2.0 or later**.

## Author

Developed and maintained by **SHABNAM**.

- [Developer website](https://shabnam.dev)
- [Plugin website](https://shcd.ir)
- [WordPress.org profile](https://profiles.wordpress.org/shcd/)

---

<div align="center">

Made with care for WordPress and RTL websites.

**[Back to top](#ramisa-online-chat)**

</div>


---

<div align="center">

**[فارسی](#persian-version)** · **[Back to top](#ramisa-online-chat)**

</div>

---

<a id="persian-version"></a>

<p align="center">
  <img src="https://ps.w.org/ramisa-online-chat/assets/icon-256x256.png?rev=3607150" alt="آیکون چت آنلاین رامیسا" width="128" height="128">
</p>

<h1 align="center">چت آنلاین رامیسا</h1>

<p align="center"><strong>ابزارک حرفه‌ای، واکنش‌گرا و راست‌چین تماس و گفت‌وگو برای وردپرس</strong></p>



<p align="center">
  <a href="#تغییرات"><img src="https://img.shields.io/badge/نسخه-1.0.0-6f42c1.svg" alt="نسخه 1.0.0"></a>
  <a href="#پیشنیازها"><img src="https://img.shields.io/badge/WordPress-5.8%2B-21759b.svg?logo=wordpress&logoColor=white" alt="وردپرس 5.8 به بالا"></a>
  <a href="#پیشنیازها"><img src="https://img.shields.io/badge/PHP-7.4%2B-777bb4.svg?logo=php&logoColor=white" alt="PHP 7.4 به بالا"></a>
  <a href="#قابلیتها"><img src="https://img.shields.io/badge/RTL-آماده-16a085.svg" alt="آماده برای راست‌چین"></a>
  <a href="#مجوز"><img src="https://img.shields.io/badge/مجوز-GPL--2.0%2B-2ea44f.svg" alt="مجوز GPL 2.0 یا بالاتر"></a>
</p>

<p align="center">
  <a href="https://wordpress.org/plugins/ramisa-online-chat/">WordPress.org</a> ·
  <a href="https://downloads.wordpress.org/plugin/ramisa-online-chat.latest-stable.zip">دانلود افزونه</a> ·
  <a href="https://wordpress.org/support/plugin/ramisa-online-chat/">پشتیبانی</a> ·
  <a href="https://shabnam.dev">توسعه‌دهنده</a>
</p>

<img src="https://ps.w.org/ramisa-online-chat/assets/banner-772x250.png?rev=3607150" alt="بنر چت آنلاین رامیسا" width="100%">

---

## معرفی

**چت آنلاین رامیسا** یک ابزارک شناور تماس و گفت‌وگو به سایت وردپرسی اضافه می‌کند. مدیر سایت می‌تواند از طریق یک پنل اختصاصی، مقصد گفت‌وگو، مشخصات پشتیبان، ساعات کاری، راه‌های تماس بیشتر، پاسخ‌های سریع، ظاهر ابزارک، پویانمایی‌ها و دکمه‌های شورت‌کدی را مدیریت کند.

نسخه منتشرشده در WordPress.org پس از فعال‌سازی کاملاً قابل استفاده است و برای قابلیت‌های همراه افزونه به کلید مجوز، اشتراک آزمایشی، سرویس ساخت خارجی یا API پیام‌رسانی سمت سرور نیاز ندارد.

## قابلیت‌ها

### تجربه تماس و گفت‌وگو

- دکمه شناور و کارت بازشونده تماس
- نشانی عمومی گفت‌وگو یا شماره کلیک برای گفت‌وگو
- نام، عنوان، پیام، آواتار، عنوان کارت و زیرعنوان سفارشی
- حداکثر سه دکمه پاسخ سریع
- راه‌های تماس بیشتر با پیوند و آواتار جداگانه
- چیدمان فهرستی یا شبکه‌ای مخاطبان
- جست‌وجوی اختیاری میان مخاطبان
- نشان اعلان اختیاری
- نمایش اختیاری زمان بازدیدکننده
- بازشدن خودکار و اختیاری کارت

### دسترس‌پذیری و ساعات کاری

- برنامه کاری جداگانه برای هر روز
- ساعت شروع و پایان مستقل برای روزهای هفته
- انتخاب منطقه زمانی
- متن سفارشی وضعیت آنلاین و آفلاین
- پنهان‌سازی اختیاری خارج از ساعات کاری
- کنترل مستقل نمایش در دسکتاپ و موبایل

### ظاهر و رابط کاربری

- پالت‌های زمردی، آبی، بنفش، طلایی و تیره
- اندازه‌های کوچک، معمولی و بزرگ
- شکل‌های گردگوشه، دایره‌ای و کپسولی
- سبک‌های نئومورفیک، شیشه‌ای و فشرده
- پویانمایی‌های نرم، تپش، جهش، شناوری و بدون حرکت
- جای‌گیری در سمت چپ یا راست صفحه
- طراحی واکنش‌گرا و آماده برای RTL
- پیش‌نمایش زنده در پیشخوان وردپرس

### یکپارچگی با وردپرس

- منوی اختصاصی **چت رامیسا**
- فایل‌های محلی CSS و JavaScript
- جدول اختصاصی در پایگاه‌داده
- پاک‌سازی خودکار داده‌ها هنگام حذف افزونه
- آماده ترجمه
- کنترل سطح دسترسی، nonce، پاک‌سازی ورودی و ایمن‌سازی خروجی
- دو شورت‌کد هم‌ارز

## تصاویر افزونه

<table>
  <tr>
    <td width="50%">
      <img src="https://ps.w.org/ramisa-online-chat/assets/screenshot-1.png?rev=3607150" alt="کارت شناور گفت‌وگو در سایت">
      <p align="center"><strong>کارت شناور در بخش کاربری</strong></p>
    </td>
    <td width="50%">
      <img src="https://ps.w.org/ramisa-online-chat/assets/screenshot-2.png?rev=3607150" alt="پنل تنظیمات عمودی نئومورفیک">
      <p align="center"><strong>پنل تنظیمات عمودی</strong></p>
    </td>
  </tr>
  <tr>
    <td width="50%">
      <img src="https://ps.w.org/ramisa-online-chat/assets/screenshot-3.png?rev=3607150" alt="وضعیت مقصد و کارت توسعه‌دهنده">
      <p align="center"><strong>وضعیت مقصد و کارت توسعه‌دهنده</strong></p>
    </td>
    <td width="50%">
      <img src="https://ps.w.org/ramisa-online-chat/assets/screenshot-4.png?rev=3607150" alt="مخاطبان بیشتر و پاسخ‌های سریع">
      <p align="center"><strong>راه‌های تماس و پاسخ‌های سریع</strong></p>
    </td>
  </tr>
</table>

> تصاویر بالا مستقیماً از CDN رسمی دارایی‌های افزونه در WordPress.org بارگذاری می‌شوند.

## پیش‌نیازها

| مورد | حداقل نسخه |
|---|---:|
| وردپرس | 5.8 |
| PHP | 7.4 |
| نسخه افزونه | 1.0.0 |
| فرایند Build | نیاز ندارد |

## نصب

### نصب از پیشخوان وردپرس

1. به **افزونه‌ها ← افزودن افزونه تازه** بروید.
2. گزینه **بارگذاری افزونه** را انتخاب کنید.
3. فایل ZIP افزونه را بارگذاری کنید.
4. افزونه **چت آنلاین رامیسا** را نصب و فعال کنید.
5. از پیشخوان، منوی **چت رامیسا** را باز کنید.
6. مقصد تماس، مشخصات پشتیبان، ظاهر و ساعات کاری را تنظیم کنید.
7. تنظیمات را ذخیره کنید.

### نصب دستی

پوشه افزونه را در مسیر زیر کپی کنید:

```text
wp-content/plugins/ramisa-online-chat
```

سپس از بخش **افزونه‌ها ← افزونه‌های نصب‌شده** آن را فعال کنید.

## راه‌اندازی سریع

1. پس از فعال‌سازی، منوی **چت رامیسا** را باز کنید.
2. نشانی عمومی گفت‌وگو یا شماره تماس را وارد کنید.
3. مشخصات پشتیبان و متن‌های ابزارک را تنظیم کنید.
4. رنگ، شکل، اندازه، سبک کارت و جای‌گیری را انتخاب کنید.
5. در صورت نیاز ساعات کاری را تنظیم کنید.
6. راه‌های تماس بیشتر یا پاسخ‌های سریع را بیفزایید.
7. نتیجه را در پیش‌نمایش زنده بررسی کنید.
8. تنظیمات را ذخیره کنید.

در صورت فعال‌بودن ابزارک، دکمه شناور به‌صورت خودکار در بخش کاربری سایت نمایش داده می‌شود.

## شورت‌کدها

هر دو شورت‌کد زیر یک دکمه تماس درون‌خطی تولید می‌کنند:

```text
[ramisa_online_chat]
[ramisa_online_chat_button]
```

### نمونه‌ها

```text
[ramisa_online_chat text="ارتباط با پشتیبانی"]
```

```text
[ramisa_online_chat url="https://example.com/contact"]
```

```text
[ramisa_online_chat text="شروع گفت‌وگو" theme="blue" icon="support" size="large"]
```

### ویژگی‌های شورت‌کد

| ویژگی | کاربرد | مقدارهای مجاز |
|---|---|---|
| `url` | جایگزینی مقصد ذخیره‌شده | یک نشانی معتبر |
| `text` | جایگزینی متن دکمه | متن ساده |
| `theme` | تغییر پالت رنگ | `green`، `blue`، `violet`، `gold`، `dark` |
| `icon` | تغییر آیکون | `chat`، `support`، `send`، `phone`، `help` |
| `size` | تغییر اندازه دکمه | `small`، `normal`، `large` |

ویژگی‌هایی که وارد نشوند، مقدار ذخیره‌شده در تنظیمات افزونه را دریافت می‌کنند.

## ذخیره‌سازی داده‌ها

تنظیمات در یک جدول سفارشی اختصاصی ذخیره می‌شوند:

```text
{wordpress_prefix}ramisa_online_chat_settings
```

در نصب استاندارد وردپرس، نام جدول معمولاً به این صورت است:

```text
wp_ramisa_online_chat_settings
```

این جدول هنگام فعال‌سازی ایجاد می‌شود، با غیرفعال‌کردن افزونه باقی می‌ماند و هنگام حذف کامل افزونه توسط `uninstall.php` پاک می‌شود.

## حریم خصوصی و خدمات خارجی

چت آنلاین رامیسا از سمت سرور وردپرس داده‌ای به خدمات خارجی ارسال نمی‌کند و API پیام‌رسانی راه دوری را فراخوانی نمی‌کند.

پس از کلیک بازدیدکننده روی پیوند تماس، مرورگر مقصدی را باز می‌کند که مدیر سایت تنظیم کرده است. این مقصد ممکن است متعلق به یک سرویس پیام‌رسانی یا تماس شخص ثالث باشد. بررسی شرایط استفاده، الزامات حریم خصوصی و رضایت‌های قانونی بر عهده مدیر سایت است.

نشانگر وضعیت مقصد در پیشخوان، مقدار تنظیم‌شده را به‌صورت محلی بررسی می‌کند و به سرور راه دور پینگ نمی‌فرستد.

## ساختار پروژه

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
├── readme.txt
└── uninstall.php
```

## توسعه

بسته افزونه شامل فایل‌های قابل‌ویرایش PHP، CSS و JavaScript است. قابلیت‌های فعلی به Composer، Node.js، Bundler یا فرایند کامپایل نیاز ندارند.

پیش از ثبت تغییرات، این موارد را بررسی کنید:

- سازگاری با PHP 7.4
- رعایت استانداردهای کدنویسی وردپرس
- کنترل سطح دسترسی و nonce
- پاک‌سازی ورودی و ایمن‌سازی خروجی
- چیدمان‌های RTL و LTR
- نمایش در دسکتاپ و موبایل
- محاسبه صحیح ساعات کاری
- خروجی شورت‌کدها
- فعال‌سازی، غیرفعال‌سازی و حذف کامل افزونه

## ترجمه‌پذیری

دامنه متنی افزونه:

```text
ramisa-online-chat
```

فایل‌های ترجمه در این پوشه قرار می‌گیرند:

```text
languages/
```

نام پیشنهادی فایل‌های فارسی:

```text
ramisa-online-chat-fa_IR.po
ramisa-online-chat-fa_IR.mo
```

نام شورت‌کدها، ویژگی‌های شورت‌کد، شناسه‌های پایگاه‌داده، مسیر فایل‌ها و دامنه متنی نباید ترجمه شوند.

## امنیت

افزونه از الگوهای امنیتی متعارف وردپرس استفاده می‌کند، از جمله:

- بررسی سطح دسترسی `manage_options`
- اعتبارسنجی nonce پیش از ذخیره
- پاک‌سازی مقادیر ورودی
- ایمن‌سازی خروجی در مدیریت و بخش کاربری
- جلوگیری از دسترسی مستقیم به فایل‌های PHP
- استفاده از `noopener noreferrer` برای پیوندهای برگه جدید

مشکلات امنیتی را به‌صورت خصوصی از طریق وب‌سایت توسعه‌دهنده گزارش کنید و جزئیات حساس را در Issue عمومی منتشر نکنید.

## مشارکت در توسعه

گزارش باگ و Pull Requestهای متمرکز پذیرفته می‌شوند.

1. مخزن را Fork کنید.
2. یک شاخه مشخص برای تغییر بسازید.
3. تغییر را پیاده‌سازی و آزمایش کنید.
4. پیام Commit روشن بنویسید.
5. Pull Request را همراه با مراحل آزمایش و تصاویر تغییرات ظاهری ارسال کنید.

در Pull Requestهای کاربردی از تغییرات قالب‌بندی نامرتبط خودداری کنید.

## تغییرات

### نسخه 1.0.0

- نخستین انتشار در WordPress.org
- افزودن ابزارک شناور گفت‌وگو
- افزودن پیشخوان اختصاصی رامیسا
- افزودن ذخیره‌سازی در جدول سفارشی پایگاه‌داده
- افزودن ساعات کاری و کنترل دسترس‌پذیری
- افزودن راه‌های تماس بیشتر و پاسخ‌های سریع
- افزودن پیش‌نمایش زنده و شورت‌کدها
- افزودن طراحی واکنش‌گرا و آماده برای RTL
- افزودن اعتبارسنجی محلی مقصد
- افزودن پاک‌سازی داده‌ها هنگام حذف افزونه

## مجوز

چت آنلاین رامیسا تحت مجوز **GNU General Public License v2.0 یا نسخه‌های بعدی** منتشر می‌شود.

## توسعه‌دهنده

توسعه و نگهداری توسط **SHABNAM** انجام می‌شود.

- [وب‌سایت توسعه‌دهنده](https://shabnam.dev)
- [وب‌سایت افزونه](https://shcd.ir)
- [نمایه WordPress.org](https://profiles.wordpress.org/shcd/)

---

<p align="center">
  ساخته‌شده با توجه به استانداردهای وردپرس و نیاز وب‌سایت‌های راست‌چین
</p>

<p align="center"><strong><a href="#چت-آنلاین-رامیسا">بازگشت به ابتدای صفحه</a></strong></p>


---

<div align="center">

**[English](#english-version)** · **[بازگشت به ابتدای فایل](#ramisa-online-chat)**

</div>
