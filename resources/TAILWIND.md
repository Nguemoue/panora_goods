# TailwindCSS v4 Development Guide

Quick reference for using TailwindCSS v4 in this project.

## Overview

TailwindCSS is a utility-first CSS framework used to style all components in this application. Version 4 brings significant improvements and performance enhancements.

## When to Use This Guide

Activate the `tailwindcss-development` skill and use this guide when:

✅ Writing or fixing Tailwind utility classes in HTML templates  
✅ Building responsive grid layouts (card grids, product grids)  
✅ Creating flex/grid page structures (dashboards, sidebars, topbars)  
✅ Styling UI components (cards, tables, navbars, forms, inputs, badges)  
✅ Adding dark mode variants  
✅ Fixing spacing or typography issues  
✅ Working with TailwindCSS v3/v4  

## When NOT to Use

❌ Backend PHP logic  
❌ Database queries or API routes  
❌ JavaScript with no HTML/CSS component  
❌ CSS file audits  
❌ Build tool configuration  
❌ Vanilla CSS  

## Installation & Configuration

TailwindCSS is already configured in this project. Configuration lives in:
- `tailwind.config.js` - Tailwind configuration
- CSS imports in `resources/css/` - Global styles

## Core Concepts

### Utility Classes

Apply styles directly to HTML elements:

```html
<!-- Text styling -->
<p class="text-lg font-bold text-blue-600">Heading</p>

<!-- Spacing -->
<div class="p-4 m-2">Padded and margined content</div>

<!-- Colors -->
<button class="bg-blue-500 text-white hover:bg-blue-600">Click me</button>
```

### Responsive Design

Use breakpoint prefixes for responsive behavior:

```html
<!-- Mobile: 1 column, Tablet: 2 columns, Desktop: 3 columns -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <div>Card 1</div>
    <div>Card 2</div>
    <div>Card 3</div>
</div>
```

**Breakpoints:**
- `sm` - 640px
- `md` - 768px
- `lg` - 1024px
- `xl` - 1280px
- `2xl` - 1536px

### Dark Mode

Use `dark:` prefix for dark mode styles:

```html
<div class="bg-white dark:bg-gray-900 text-black dark:text-white">
    Content that adapts to light/dark mode
</div>
```

## Common Patterns

### Button Styles

```html
<!-- Primary button -->
<button class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
    Primary
</button>

<!-- Secondary button -->
<button class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
    Secondary
</button>

<!-- Danger button -->
<button class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
    Delete
</button>
```

### Card Component

```html
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Card Title</h3>
    <p class="text-gray-600">Card content goes here</p>
</div>
```

### Form Input

```html
<input 
    type="text" 
    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
    placeholder="Enter text"
>
```

### Flexbox Layout

```html
<!-- Centered flex container -->
<div class="flex items-center justify-center h-screen">
    <div class="text-center">
        <h1 class="text-3xl font-bold">Centered Content</h1>
    </div>
</div>

<!-- Horizontal layout with space between -->
<div class="flex justify-between items-center p-4">
    <div>Left content</div>
    <div>Right content</div>
</div>
```

### Grid Layout

```html
<!-- 3-column responsive grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="col-span-1">Item 1</div>
    <div class="col-span-1">Item 2</div>
    <div class="col-span-1">Item 3</div>
</div>

<!-- Asymmetric grid -->
<div class="grid grid-cols-3 gap-4">
    <div class="col-span-2">Main content (2 cols)</div>
    <div class="col-span-1">Sidebar (1 col)</div>
</div>
```

### Navigation Bar

```html
<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <span class="text-xl font-bold">Logo</span>
            </div>
            <div class="flex space-x-4">
                <a href="#" class="px-3 py-2 text-gray-700 hover:text-blue-600">Home</a>
                <a href="#" class="px-3 py-2 text-gray-700 hover:text-blue-600">About</a>
            </div>
        </div>
    </div>
</nav>
```

### Badge Component

```html
<!-- Success badge -->
<span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
    Active
</span>

<!-- Warning badge -->
<span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
    Pending
</span>

<!-- Error badge -->
<span class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
    Error
</span>
```

### Modal/Dialog

```html
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
        <h2 class="text-xl font-bold mb-4">Modal Title</h2>
        <p class="text-gray-600 mb-6">Modal content here</p>
        <div class="flex gap-2">
            <button class="flex-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Confirm
            </button>
            <button class="flex-1 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">
                Cancel
            </button>
        </div>
    </div>
</div>
```

## Spacing Scale

```
0      0
1      0.25rem (4px)
2      0.5rem (8px)
3      0.75rem (12px)
4      1rem (16px)
6      1.5rem (24px)
8      2rem (32px)
12     3rem (48px)
16     4rem (64px)
```

## Color Palette

### Grays
- `gray-50` to `gray-950` (lightest to darkest)

### Blues
- `blue-50` to `blue-950`

### Reds
- `red-50` to `red-950`

### Greens
- `green-50` to `green-950`

### Yellows
- `yellow-50` to `yellow-950`

### Purples
- `purple-50` to `purple-950`

## Text Sizing

```
text-xs       0.75rem
text-sm       0.875rem
text-base     1rem
text-lg       1.125rem
text-xl       1.25rem
text-2xl      1.5rem
text-3xl      1.875rem
text-4xl      2.25rem
text-5xl      3rem
```

## Font Weights

```
font-thin       100
font-extralight 200
font-light      300
font-normal     400
font-medium     500
font-semibold   600
font-bold       700
font-extrabold  800
font-black      900
```

## Common Utilities

### Display & Visibility

```html
<div class="block">Block element</div>
<div class="inline">Inline element</div>
<div class="inline-block">Inline-block element</div>
<div class="hidden">Hidden on all screens</div>
<div class="sm:hidden">Hidden on mobile, visible on tablet+</div>
<div class="md:block">Invisible on mobile, visible on tablet+</div>
```

### Positioning

```html
<!-- Fixed positioning -->
<div class="fixed top-0 left-0 right-0 bg-white">Header</div>

<!-- Absolute positioning -->
<div class="relative">
    <div class="absolute top-2 right-2">Badge</div>
</div>

<!-- Sticky positioning -->
<div class="sticky top-0 bg-white">Sticky header</div>
```

### Overflow

```html
<div class="overflow-hidden">Hidden overflow</div>
<div class="overflow-y-auto max-h-96">Scrollable content</div>
<div class="overflow-x-auto">Horizontally scrollable</div>
```

### Border Radius

```html
<div class="rounded">Standard radius</div>
<div class="rounded-lg">Large radius</div>
<div class="rounded-full">Circular</div>
<div class="rounded-t-lg">Top radius only</div>
```

### Shadows

```html
<div class="shadow">Standard shadow</div>
<div class="shadow-lg">Large shadow</div>
<div class="shadow-xl">Extra large shadow</div>
<div class="shadow-2xl">2x large shadow</div>
```

### Transitions & Animations

```html
<button class="transition-colors duration-200 hover:bg-blue-600">Smooth transition</button>
<div class="animate-spin">Loading spinner</div>
<div class="animate-bounce">Bouncing element</div>
<div class="animate-pulse">Pulsing element</div>
```

## Advanced: Custom Classes

If you need to create reusable component classes, use Tailwind's `@layer` directive:

```css
@layer components {
    @apply px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors;
}
```

Then use in HTML:
```html
<button class="btn-primary">Click me</button>
```

## Building for Production

When changes aren't visible:

```bash
npm run build    # Production build
npm run dev      # Development with watch
composer run dev # Laravel dev mode
```

## Resources

- [TailwindCSS Documentation](https://tailwindcss.com/docs)
- [TailwindCSS Component Examples](https://tailwindcss.com/docs/installation)
- Use `search-docs` tool for version-specific guidance

## Performance Tips

1. **Use Tailwind utilities directly** - don't mix with custom CSS
2. **Avoid `@apply` unless creating components** - use utility classes
3. **Leverage dark mode** - add `dark:` prefixes for dark variants
4. **Use responsive prefixes** - design mobile-first with `sm:`, `md:`, etc.
5. **Minimize custom CSS** - Tailwind has utilities for almost everything
