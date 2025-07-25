# 💰 WooCommerce Multi-Currency Display (BGN/EUR)

<div align="center">

[![Bulgarian](https://img.shields.io/badge/Language-Български-green?style=for-the-badge)](#bulgarian)
[![English](https://img.shields.io/badge/Language-English-blue?style=for-the-badge)](#english)

</div>

---

<div id="bulgarian">

## 🎯 Преглед

Този плъгин автоматично добавя показване на цени в евро до лева (BGN) в WooCommerce, използвайки фиксирания курс на БНБ: **1 EUR = 1.95583 BGN**.

**⚠️ Изискване по закон:** От 1 август 2025 г. всички онлайн магазини в България трябва да показват цените си както в лева, така и в евро.

> **Цени в лева и евро** - WordPress плъгин, който автоматично показва цените на продуктите както в лева (BGN), така и в евро (EUR), използвайки фиксирания курс на БНБ.

[![WordPress Plugin Version](https://img.shields.io/badge/version-1.4.0-blue.svg)](https://wordpress.org/plugins/woo-bgn-eur-prices/)
[![WordPress Tested](https://img.shields.io/badge/WordPress-6.8%2B-green.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-orange.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2%2B-red.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

---

## ✨ Функции

- 🔄 **Автоматично конвертиране на валута** - Конвертира BGN цени в EUR използвайки официалния курс на БНБ
- 🛒 **Пълна интеграция с WooCommerce** - Работи на всички ключови страници и имейли
- 🧱 **Поддръжка за WooCommerce Blocks** - Работи с модерните блокови шаблони
- 📱 **Responsive дизайн** - Изглежда отлично на всички устройства
- ⚡ **Лек и бърз** - Минимално влияние върху производителността
- 🎨 **Чист дизайн** - Елегантно форматиране на цените
- 📧 **Интеграция с имейли** - Показва курса в имейли с поръчки
- ⚙️ **Без конфигурация** - Работи веднага след инсталиране

---

## 🚀 Инсталация

### Метод 1: WordPress административен панел
1. Отидете в **Разширения** → **Добави ново** в WordPress администрацията
2. Кликнете **Качи плъгин**
3. Изберете ZIP файла на плъгина
4. Кликнете **Инсталирай сега** и след това **Активирай**

### Метод 2: Ръчна инсталация
1. Качете папката на плъгина в `/wp-content/plugins/`
2. Активирайте плъгина чрез менюто **Разширения** в WordPress
3. Готово! Плъгинът започва да работи веднага

---

## 📍 Къде работи

Плъгинът автоматично показва двойни валутни цени на:

- 🏪 **Страници с продукти** - Индивидуални показвания на продукти
- 🛒 **Количка** - Общи суми и отделни артикули
- 💳 **Страница за плащане** - Общи суми на поръчки и отделни артикули
- 📧 **Имейли с поръчки** - Уведомления за клиенти и администратори
- 🔍 **Мини количка** - Dropdown виджет за количка
- 📱 **Мобилни изгледи** - Responsive дизайн

---

## 💡 Пример за изход

**Преди:**
```
Цена: 19.56 лв.
```

**След:**
```
Цена: 19.56 лв. (10.00 €)
```

---

## 🔧 Технически детайли

- **Обменен курс:** Фиксиран на 1 EUR = 1.95583 BGN (курс на БНБ)
- **Съвместимост:** WordPress 5.6+, WooCommerce 3.0+
- **PHP версия:** 7.4 или по-висока
- **Лиценз:** GPL v2 или по-късен

---

## ❓ Често задавани въпроси

### В: Ще работи ли с други валути освен BGN?
**О:** Не, плъгинът е предназначен само за магазини с основна валута BGN.

### В: Мога ли да променя курса?
**О:** Не, курсът е фиксиран по закон. Ако имате нужда от динамичен курс – използвайте много-валутен плъгин.

### В: Влияе ли върху производителността?
**О:** Минимално влияние - плъгинът добавя само малко изчисления и логика за показване.

### В: Съвместим ли е с други плъгини?
**О:** Да, проектиран е да работи заедно с други WooCommerce плъгини без конфликти.

---

## 🆘 Поддръжка

Ако срещнете проблеми или имате въпроси:

1. Проверете [страницата на плъгина в WordPress.org](https://wordpress.org/plugins/woo-bgn-eur-prices/)
2. Прегледайте секцията с често задавани въпроси по-горе
3. Свържете се с разработчика чрез страницата с настройки на плъгина

---

## ☕ Подкрепете разработчика

Ако намерите този плъгин за полезен, обмислете да подкрепите разработчика:

<div align="center">

[![Buy me a coffee](https://img.shields.io/badge/Buy%20me%20a%20coffee-☕%20Подкрепи%20разработчика-FFDD00?style=for-the-badge&logo=buymeacoffee&logoColor=black)](https://coff.ee/rezored)

</div>

---

## 📝 История на промените

### Версия 1.4.3
- ✅ **Подобрена сигурност** - Всички изходи са правилно ескейпнати според WordPress стандартите
- ✅ **Подобрена интернационализация** - Всички текстове са преводими
- ✅ **Съответствие с WordPress стандартите** - Плъгинът е готов за WordPress.org директорията

### Версия 1.4.2 
- Отстранен бъг свързан със скоростта на зареждане на цените

### Версия 1.4.1
- Подобрена съвместимост с най-новите версии на WordPress
- Отстранен бъг свързан със скоростта на зареждане на цените
- Подобрен алгоритъм за откриване на цени

### Версия 1.4.0
- ✅ **Добавена поддръжка за WooCommerce Blocks** - Работи с модерните блокови шаблони
- Подобрена съвместимост с най-новите версии на WordPress
- Подобрен алгоритъм за откриване на цени
- Поправки на грешки и оптимизации на производителността

### Версия 1.3.8
- Подобрена съвместимост с най-новите версии на WordPress
- Подобрен алгоритъм за откриване на цени
- Поправки на грешки и оптимизации на производителността

### Версия 1.3.7
- Добавено пояснение за курса в количка и checkout
- Подготвена административна страница с настройки
- Подобрена интеграция с имейли

### Версия 1.3.0
- Пълно показване на цени на всички страници
- Интеграция с имейли
- Първоначално стабилно издание

---

## 📄 Лиценз

Този проект е лицензиран под GPL v2 или по-късен - вижте файла [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) за детайли.

---

## 🛠️ Разработка

За информация относно разработката и поддръжката на плъгина, вижте [DEVELOPMENT_GUIDELINES.md](DEVELOPMENT_GUIDELINES.md).

---

<div align="center">

**Създадено с ❤️ за българската WooCommerce общност**

*Помагаме на бизнесите да спазват новите изисквания за показване на валути*

</div>

</div>

---

<div id="english">


## 🎯 Overview

This plugin automatically adds Euro price display alongside Bulgarian Lev prices in WooCommerce, using the fixed Bulgarian National Bank exchange rate: **1 EUR = 1.95583 BGN**.

**⚠️ Legal Requirement:** Starting from August 1, 2025, all online stores in Bulgaria are required by law to display prices in both BGN and EUR.

> **Prices in BGN and EUR** - A WordPress plugin that automatically displays product prices in both Bulgarian Lev (BGN) and Euro (EUR) using the fixed Bulgarian National Bank exchange rate.

[![WordPress Plugin Version](https://img.shields.io/badge/version-1.4.0-blue.svg)](https://wordpress.org/plugins/woo-bgn-eur-prices/)
[![WordPress Tested](https://img.shields.io/badge/WordPress-6.8%2B-green.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-orange.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2%2B-red.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

---

## ✨ Features

- 🔄 **Automatic Currency Conversion** - Converts BGN prices to EUR using the official BNB rate
- 🛒 **Full WooCommerce Integration** - Works on all key pages and emails
- 🧱 **WooCommerce Blocks Support** - Works with modern block templates
- 📱 **Responsive Design** - Looks great on all devices
- ⚡ **Lightweight & Fast** - Minimal performance impact
- 🎨 **Clean Display** - Elegant price formatting
- 📧 **Email Integration** - Shows exchange rate in order emails
- ⚙️ **Zero Configuration** - Works out of the box

---

## 🚀 Installation

### Method 1: WordPress Admin Panel
1. Go to **Plugins** → **Add New** in your WordPress admin
2. Click **Upload Plugin**
3. Choose the plugin ZIP file
4. Click **Install Now** and then **Activate**

### Method 2: Manual Installation
1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. That's it! The plugin starts working immediately

---

## 📍 Where It Works

The plugin automatically displays dual currency prices on:

- 🏪 **Product Pages** - Individual product displays
- 🛒 **Shopping Cart** - Cart totals and individual items
- 💳 **Checkout Page** - Order totals and line items
- 📧 **Order Emails** - Customer and admin notifications
- 🔍 **Mini Cart** - Dropdown cart widget
- 📱 **Mobile Views** - Responsive design

---

## 💡 Example Output

**Before:**
```
Price: 19.56 лв.
```

**After:**
```
Price: 19.56 лв. (10.00 €)
```

---

## 🔧 Technical Details

- **Exchange Rate:** Fixed at 1 EUR = 1.95583 BGN (BNB rate)
- **Compatibility:** WordPress 5.6+, WooCommerce 3.0+
- **PHP Version:** 7.4 or higher
- **License:** GPL v2 or later

---

## ❓ Frequently Asked Questions

### Q: Will it work with currencies other than BGN?
**A:** No, this plugin is specifically designed for stores with BGN as the primary currency.

### Q: Can I change the exchange rate?
**A:** No, the rate is fixed by law. If you need dynamic rates, consider using a multi-currency plugin.

### Q: Does it affect performance?
**A:** Minimal impact - the plugin only adds a small calculation and display logic.

### Q: Is it compatible with other plugins?
**A:** Yes, it's designed to work alongside other WooCommerce plugins without conflicts.

---

## 🆘 Support

If you encounter any issues or have questions:

1. Check the [WordPress.org plugin page](https://wordpress.org/plugins/woo-bgn-eur-prices/)
2. Review the FAQ section above
3. Contact the developer through the plugin settings page

---

## ☕ Support the Developer

If you find this plugin helpful, consider supporting the developer:

<div align="center">

[![Buy me a coffee](https://img.shields.io/badge/Buy%20me%20a%20coffee-☕%20Support%20Developer-FFDD00?style=for-the-badge&logo=buymeacoffee&logoColor=black)](https://coff.ee/rezored)

</div>

---

## 📝 Changelog

### Version 1.4.3
- ✅ **Enhanced Security** - All output properly escaped according to WordPress standards
- ✅ **Improved Internationalization** - All text is translatable
- ✅ **WordPress Standards Compliance** - Plugin ready for WordPress.org directory

### Version 1.4.2 
- Fixed a buf whith the loading speed in the custom block for card

### Версия 1.4.1
- Enhanced compatibility with latest WordPress versions
- Fixed a buf whith the loading speed in the custom block for card
- Improved price detection algorithm

### Version 1.4.0
- ✅ **Added WooCommerce Blocks Support** - Works with modern block templates
- Enhanced compatibility with latest WordPress versions
- Improved price detection algorithm
- Bug fixes and performance optimizations

### Version 1.3.8
- Enhanced compatibility with latest WordPress versions
- Improved price detection algorithm
- Bug fixes and performance optimizations

### Version 1.3.7
- Added exchange rate explanation in cart and checkout
- Prepared admin settings page
- Enhanced email integration

### Version 1.3.0
- Full multi-page price display
- Email integration
- Initial stable release

---

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) file for details.

---

## 🛠️ Development

For information about plugin development and maintenance, see [DEVELOPMENT_GUIDELINES.md](DEVELOPMENT_GUIDELINES.md).

---

<div align="center">

**Made with ❤️ for the Bulgarian WooCommerce community**

*Helping businesses comply with the new currency display requirements*

</div>

</div>
