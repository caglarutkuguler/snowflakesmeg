# Changelog

All notable changes to **Snow Effects - Christmas and Winter Atmosphere** (`snowflakesmeg`).

## 2.0.2

### Fixed

- **Fatal error on newer PrestaShop cores.** `implements WidgetInterface` resolves against the global namespace, but newer cores ship the interface only as `PrestaShop\PrestaShop\Core\Module\WidgetInterface` with no global alias, so the module died with `ClassNotFoundError` on those shops. Whichever name the shop provides is now aliased to the global one before the class declaration.

## 2.0.1

### Fixed

- **Upgrade could disable the module on some shops.** The 2.0.0 upgrade
  step's success hinged directly on chained `registerHook()` calls
  returning true; a transient failure there (or a hook already registered
  from a partial prior attempt) marked the whole upgrade step failed and
  PrestaShop disabled the module. Hooks are now registered idempotently and
  the step always reports success.
