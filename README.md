# Terrific Fuse: Laravel Server Provisioner & Deployer

Terrific Fuse is a work-in-progress (WIP) project for provisioning servers and deploying Laravel applications. The goal is to provide automated server setup, secure credential management, and zero-downtime deployment workflows for Laravel projects.

- **Laravel 12.x**
- **Automated server provisioning** for Laravel applications
- **Zero-downtime deployment** to provisioned servers
- **Stripe billing** via [Laravel Cashier](https://laravel.com/docs/billing) (one plan, subscription required for dashboard access)
- **Stripe Checkout** for new subscriptions
- **Billing portal** for subscribers
- **Flux UI Pro** components (paid license required)
- **Honeybadger** for error tracking



## Requirements

- PHP >= 8.2
- Composer
- Node.js & npm
- [Flux UI Pro license](https://www.fluxui.com/pricing) (required for UI components)
- [Stripe account](https://dashboard.stripe.com/register)
- [Honeybadger account](https://www.honeybadger.io/)
- [Hetzner account](https://www.hetzner.com/cloud) (for server provisioning)

## Getting Started

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-username/terrific-fuse.git
   cd terrific-fuse
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Copy and configure environment:**
   ```bash
   cp .env.example .env
   # Set your database, Stripe, Flux UI Pro, Honeybadger, and Hetzner credentials in .env
   ```

4. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

5. **Run migrations:**
   ```bash
   php artisan migrate
   ```

6. **Start the development server:**
   ```bash
   php artisan serve
   ```

## Stripe Billing (Laravel Cashier)

- Uses [Laravel Cashier](https://laravel.com/docs/billing) for Stripe subscription management.
- Only one plan is configured by default (easy to extend).
- Users must have an active subscription to access the dashboard.
- Stripe Checkout is used for new subscriptions.
- Subscribers can access the Stripe billing portal to manage their subscription.

**Configuration:**
- Set your `STRIPE_KEY` and `STRIPE_SECRET` in `.env`.
- Update the plan ID in your billing configuration as needed.

## Flux UI Pro

- All UI components use [Flux UI Pro](https://www.fluxui.com/).
- **A paid Flux UI Pro license is required.**
- Set your Flux UI Pro credentials in `.env` as per the [Flux UI Pro documentation](https://www.fluxui.com/docs/pro/introduction).

## Honeybadger Error Tracking

- Integrated with [Honeybadger](https://www.honeybadger.io/) for error monitoring.
- Set your `HONEYBADGER_API_KEY` in `.env`.
- See the [Honeybadger Laravel docs](https://docs.honeybadger.io/lib/php/integration/laravel.html) for advanced configuration.

## Status

**This project is a work in progress.**

Terrific Fuse aims to be an opinionated platform for provisioning servers and deploying Laravel applications. Many features are still under development.

Feel free to fork and adapt to your needs, or follow along as Terrific Fuse evolves into a robust server provisioning and deployment platform for Laravel apps.

## License

This project is open source, but you must purchase your own Flux UI Pro, Honeybadger, and Hetzner licenses/accounts to use those services.

---

**Happy hacking!**

---

> _Built by Oliver as a Laravel server provisioner and deployer. PRs and suggestions welcome._
