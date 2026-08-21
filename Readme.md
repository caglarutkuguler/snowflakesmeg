# Snow Effects - Christmas & Winter Atmosphere

A PrestaShop module that adds a snowfall effect to every page of your shop, in pure CSS. Choose a classic white snowfall or a festive Christmas mix, set the season dates once, and the snow starts and stops by itself.

Module folder name: `snowflakesmeg` · Version 2.0.0 · PrestaShop 1.7.0 and above

**Installable zip:** the archive GitHub generates on the releases page is a source snapshot, not an installable module — PrestaShop rejects it because the folder inside carries the version number. Download the ready-to-install zip from [megventure.com](https://megventure.com/en/free-modules/65-prestashop-snow-effect-christmas-snowfall-8691246247643.html).

## Highlights

- **Pure CSS** - no JavaScript, no jQuery, no external files or fonts. Nothing is loaded from third-party servers, so the effect is GDPR-friendly and adds no measurable weight to your pages.
- **Two themes** - classic snowflakes, or a Christmas mix that scatters bells, music notes, trees, snowmen and gifts between the flakes.
- **Season scheduling** - optional first and last day of snow. Set December 1 to January 6 once and forget about it; the module compares the dates on every visit.
- **Fully adjustable** - size, amount, fall speed (calm / normal / blizzard) and color of the flakes, plus a switch to hide the effect on phones.
- **Natural motion** - every flake gets a random position, size and timing on each page load, and the animation starts mid-fall, so the screen is never empty and the pattern never repeats.
- **Respectful by design** - the snow never intercepts clicks or text selection, and it hides itself automatically for visitors whose device requests reduced motion.

## Quick start

1. Install the module and open its configuration page (Modules > Module Manager > search for "Snow").
2. Check the **Current status and preview** panel - it tells you exactly what visitors see right now, and shows an animated preview of your saved settings.
3. Flip **Let it snow** on, pick a theme, and save. That is all.

## Settings

| Setting | What it does | Default |
| --- | --- | --- |
| Let it snow | Master switch for the whole effect. | Enabled |
| Theme | Classic snowfall, or Christmas mix with festive ornaments. | Classic |
| Snowfall intensity | Calm, Normal or Blizzard fall speed. | Normal |
| Snowflake size | Base size relative to your theme text, 0.5-5. Each flake varies slightly around it. | 1.5 |
| Number of snowflakes | 4-48 flakes on screen. | 12 |
| Snowflake color | Color of the snow glyphs (ornaments keep their own colors). A soft shadow keeps white flakes visible on light themes. | #ffffff |
| First day of snow | Optional date the snow appears. Empty = right away. | Empty |
| Last day of snow | Optional date after which the snow disappears. Empty = never. | Empty |
| Show on phones | Switch off to limit the snow to screens wider than 768 px. | Enabled |

All fields are validated when you save: sizes and amounts are checked against their allowed ranges (decimal commas are accepted), dates must be valid and in the right order, and colors must be real CSS colors. Invalid input is rejected with a clear message and nothing you typed is lost.

## Where the snow is rendered

The markup is injected through the standard `displayBeforeBodyClosingTag` hook, which every theme based on the default PrestaShop layouts includes. The stylesheet is registered through `actionFrontControllerSetMedia`, so it participates in PrestaShop's normal asset pipeline and is only loaded while the snow season is active.

If your theme is heavily customized and does not call that hook, either attach the module to another display hook from **Design > Positions**, or place it directly in a theme template with:

```smarty
{widget name='snowflakesmeg'}
```

The module renders at most once per page, no matter how many hooks or widget calls it is attached to.

## Upgrading from 1.x

Version 2.0.0 upgrades cleanly from any 1.x release. Your enabled/disabled state, chosen theme and snowflake size are migrated automatically; the new settings start at their defaults. Fixed since 1.x:

- **It snowed twice on PrestaShop 1.7.** 1.x registered both `displayHeader` and its legacy `header` alias, so each page got two overlapping snow layers. 2.0.0 registers each hook exactly once and the upgrade script removes the duplicates.
- **Christmas mode showed blank squares when a CDN was unreachable.** The festive icons came from a Font Awesome stylesheet on `netdna.bootstrapcdn.com` - a deprecated CDN, loaded on every page, on the storefront and in the back office. 2.0.0 uses native emoji and text glyphs; nothing external is loaded anymore.
- **Snowflakes could block clicks.** Falling flakes sat above buttons and links and swallowed taps, which is especially annoying on mobile. All snow layers now use `pointer-events: none`.
- **Sizes with a decimal comma were rejected.** Typing `1,5` failed validation with no explanation. Both `1.5` and `1,5` are accepted now, and the allowed range (0.5-5) is enforced instead of just "recommended".
- **Invalid HTML in the page head.** 1.x printed the snow markup inside `<head>`, leaving browsers to repair the document. The markup now lands where it belongs, just before `</body>`.
- **No way to end the season.** The snow kept falling until you remembered to switch it off. The new season dates handle that for you.

## Performance and accessibility notes

- The animation runs on `transform` only, so it stays on the browser's compositor thread - no layout work, no jank, even with 48 flakes.
- With the default settings the module adds one small stylesheet and roughly 1 KB of markup to the page. There are no database tables, no overrides and no core changes.
- Visitors with the `prefers-reduced-motion` system setting see no snow at all - this is intentional and required for motion-sensitive users.

## Uninstall

Uninstalling removes every module setting (including leftovers from 1.x installations). Since the module has no database tables, no overrides and touches no core files, uninstalling restores your shop exactly as it was.

## License

MIT License · © 2019-2026 MEG Venture & Consulting Ltd.
