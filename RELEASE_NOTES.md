# Snow Effects - Christmas and Winter Atmosphere — Release notes

_Only customer-relevant changes are listed. Internal refactoring, tests and housekeeping are omitted._

## 2.1.1

### New
- The configuration page links to the free MEG Venture Updates module, which installs updates for this module in one click. The line is hidden once that module is installed

## 2.1.0

### New
- A single review-request line on the module's own configuration page. It appears at the earliest 21 days after installing, asks once for a short review on megventure.com, and disappears forever after a click, a "No thanks", or three unanswered views. It makes no outbound request of any kind and stores nothing beyond three prefixed configuration values, which uninstalling removes

## 2.0.3

### Fixed
- Uninstalling the module could delete another module's settings. As part of cleaning up after old 1.x releases, the uninstall step also removed the shop-wide configuration rows named `SNOWFLAKES` and `sizesnowflakes`. Those names carry no module prefix, so if any other module on the shop stored a setting under one of them, uninstalling this module silently wiped it. The 1.x-to-2.0.0 upgrade had the same cleanup after migrating the values. Both now leave the old rows alone — a stale leftover row is harmless, deleting another module's setting is not
