# SCSS Design System

Authoring guide for the spacing and typography tokens, `@mixin` helpers, and functions in this theme's SCSS.

---

## Core idea

Write in **px** — the browser gets **rem**. The system converts for you so you can think visually without sacrificing accessibility.

---

## Spacing tokens — `$px-{N}`

A fixed scale of spacing values defined in `helpers/_settings.scss`. Use these instead of hard-coding pixel or rem values.

| Token     | px    | rem      |
| --------- | ----- | -------- |
| `$px-2`   | 2px   | 0.125rem |
| `$px-4`   | 4px   | 0.25rem  |
| `$px-8`   | 8px   | 0.5rem   |
| `$px-16`  | 16px  | 1rem     |
| `$px-24`  | 24px  | 1.5rem   |
| `$px-36`  | 36px  | 2.25rem  |
| `$px-48`  | 48px  | 3rem     |
| `$px-64`  | 64px  | 4rem     |
| `$px-128` | 128px | 8rem     |

**Usage:**

```scss
margin-top: $px-64;
padding: $px-24 $px-16;
gap: $px-8;
```

**Rounding rule:** When an existing value falls between two tokens, round to the nearest. Equidistant values round up (e.g. 20px → `$px-24`).

### When NOT to use spacing tokens

- **Border radius** — values like `border-radius: 36px` are design-intent, not spacing. Keep them as px literals.
- **Functional and aspect-ratio values** — `padding-bottom: 56.25%`, `height: 400px`, `height: 2.48rem` are deliberate constraints. Leave them alone.
- **Off-screen positioning** — `left: -9999rem`, `top: -9999rem` are technique-specific and must not change.
- **`em`-relative values** — `padding: 0.85em 1em` on buttons or `margin: 0 0.4em` on punctuation are intentionally proportional to font-size. Do not convert to px.
- **Values that must be exact** — if a design spec calls for a precise pixel value not on the scale, use `rem-calc()` instead (see below).

---

## `rem-calc(N)` — one-off px to rem

Defined in `helpers/_functions.scss`. Use this when you need a specific px value that is not on the spacing scale, or for any font size.

```scss
// Single value
font-size: rem-calc(13); // → 0.8125rem

// Shorthand list
padding: rem-calc(12 20); // → 0.75rem 1.25rem
```

Base is always 16px — `rem-calc(16)` → `1rem`.

### When NOT to use `rem-calc()`

- Don't use it for values already on the spacing scale — use the `$px-N` token instead.
- Don't use it for `em`-based properties (button padding, letter-spacing, line-height). Those units are intentional.
- Don't use it for percentages, viewport units, or bare numeric values (e.g. `line-height: 1.5`).

---

## `@include font-size(px)` — static font sizes

A `@mixin` shorthand in `helpers/_mixins.scss` that applies a `font-size` in rem using a px input.

```scss
@include font-size(16); // → font-size: 1rem
@include font-size(14); // → font-size: 0.875rem
```

This is equivalent to writing `font-size: rem-calc(N)` directly — use whichever reads more clearly in context.

---

## `@include fluid-font-size(desktop-px, mobile-px)` — responsive type

Outputs a `clamp()` that scales type linearly from `mobile-px` at 640px viewport width up to `desktop-px` at 1024px and above. Both values are authored in px.

```scss
// Scales from 28px on mobile to 56px on desktop
@include fluid-font-size(56, 28);

// Scales from 24px on mobile to 40px on desktop
@include fluid-font-size(40, 24);
```

**Compiled output example** — `fluid-font-size(56, 28)`:

```css
font-size: clamp(1.75rem, calc(7.2917vw - 18.6667px), 3.5rem);
```

### When NOT to use `fluid-font-size()`

- **Small UI text** — tags, labels, captions, dates (`rem-calc(12)`, `rem-calc(13)`) don't need to scale. The difference at mobile is negligible.
- **Body copy** — paragraph text is set globally and should remain stable.
- **When min and max are the same** — just use `font-size: rem-calc(N)` directly.

---

## `@include fluid-space(property, desktop-px, mobile-px?)` — responsive spacing

Outputs a `clamp()` that scales a spacing property between 640px and 1024px viewports. Author both values in px; rem conversion is automatic.

If `mobile-px` is omitted it defaults to 50% of `desktop-px` (minimum 8px). This matches the design principle that large desktop gaps should roughly halve on mobile.

```scss
// Auto mobile (50% default): 128px desktop → 64px mobile
@include fluid-space(margin-top, 128);

// Explicit mobile value
@include fluid-space(padding-top, 48, 24);
@include fluid-space(gap, 36, 16);
```

**Compiled output example** — `fluid-space(margin-top, 128)`:

```css
margin-top: clamp(4rem, calc(16.6667vw - 42.6667px), 8rem);
```

Shorthand properties (`padding`, `margin`) are not supported in a single call. Split into individual directions:

```scss
// Correct:
@include fluid-space(padding-top, 48, 24);
@include fluid-space(padding-bottom, 48, 24);
@include fluid-space(padding-left, 40, 20);
@include fluid-space(padding-right, 40, 20);

// Won't work as expected — @include outputs one property at a time:
@include fluid-space(padding, 48, 24); // ✗
```

### When NOT to use `fluid-space()`

- **Small gaps and padding** — `$px-4`, `$px-8`, `$px-16` don't need fluid scaling. Halving 8px on mobile is not meaningful.
- **Inner component padding** that doesn't feel cramped on mobile can stay as a fixed token.
- **`em`-based spacing** — never replace `em` values with this mixin.
- **`border-radius`** — not a spacing property.
- **When mobile and desktop should be the same** — use a static token instead.

---

## Viewport scale reference

All fluid helpers interpolate between these two breakpoints:

| Viewport | Breakpoint name           |
| -------- | ------------------------- |
| 640px    | `medium` (mobile floor)   |
| 1024px   | `large` (desktop ceiling) |

Below 640px the `clamp()` minimum value locks in. Above 1024px the maximum locks in.

---

## Quick reference

| Situation                                      | What to use                                         |
| ---------------------------------------------- | --------------------------------------------------- |
| Standard margin / padding / gap on the scale   | `$px-{N}`                                           |
| Non-scale px value or font size                | `rem-calc(N)`                                       |
| Static font size from a px value               | `font-size: rem-calc(N)` or `@include font-size(N)` |
| Responsive heading or display type             | `@include fluid-font-size(desktop, mobile)`         |
| Large spacing that should shrink on mobile     | `@include fluid-space(property, desktop, mobile?)`  |
| `em`-relative, percentage, or functional value | Leave as-is                                         |

---

## Reference links

- [Sass project architecture](http://www.sitepoint.com/architecture-sass-project/)
- [How to structure a Sass project](http://thesassway.com/beginner/how-to-structure-a-sass-project)
- [CSS-Tricks Sass style guide](http://css-tricks.com/sass-style-guide/)
- [Avoid nested selectors for modular CSS](http://thesassway.com/intermediate/avoid-nested-selectors-for-more-modular-css)
- [BEM syntax guide](http://csswizardry.com/2013/01/mindbemding-getting-your-head-round-bem-syntax/)
