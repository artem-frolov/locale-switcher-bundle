# Locale Switcher Bundle for Symfony 2 / 3


## Installation

### 1. Install Composer package

You can install the bundle using [Composer](https://getcomposer.org),
update your project's `composer.json` file to include the following dependency:

```json
"require": {
    "artem-frolov/locale-switcher-bundle": "~1.0"
}
```

### 2. Register the bundle

Add the following line to `app/AppKernel.php`:

```
$bundles = array(
    // ...
    new ArtemFrolov\Bundle\LocaleSwitcherBundle\ArtemFrolovLocaleSwitcherBundle(),
);
```

### 3. Configure

Add the following parameters to your `config.yml`:

```
parameters:
    locale: en
    enabled_locales: ru|en
    custom_locale_routes:
        index:
            en: index_en
```

### Bundle

## Usage
