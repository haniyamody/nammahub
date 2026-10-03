# NammaHub — Bengaluru City Guide

NammaHub is a curated city guide for Bengaluru. It brings together places to eat, nightlife, local hotspots, and events in a single, visual experience.

## Explore

- **Food** — discover restaurants and local favourites.
- **Nightlife** — browse venues across Bengaluru.
- **Hotspots & events** — find places to visit and what is happening around the city.
- **Account flow** — local signup and login support for the PHP/MySQL version.

## Live demo

The public demo is served with GitHub Pages: **https://haniyamody.github.io/nammahub/**

> GitHub Pages hosts the static frontend. Login, signup, and database-powered features require a PHP server and MySQL, so they are not available in the Pages demo.

## Built with

- HTML, CSS, and vanilla JavaScript
- PHP API endpoints
- MySQL (for local/server-side data)

## Run locally

1. Copy `api/config.php.example` to `api/config.php`.
2. Set your local MySQL credentials and a strong admin key in `api/config.php`.
3. Create/import the `namma_blr` database used by the PHP API.
4. Serve the project through Apache/PHP (for example, XAMPP) and open it in your browser.

## Project structure

```text
├── index.html              # GitHub Pages entry point
├── landing page2.html      # Main NammaHub landing page
├── food3.html / food5.html # Food experiences
├── nightlife3.html         # Nightlife guide
├── hotspots2.html          # Hotspots and events
├── api/                    # PHP endpoints
└── config/                 # Local database connection code
```

## Security note

Local secrets belong in `api/config.php`, which is intentionally ignored by Git. Use `api/config.php.example` as the safe starting point for local configuration.
