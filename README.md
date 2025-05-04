# Fuse - Laravel Server Provisioner & Deployer

**Fuse** is a powerful Laravel-based tool for provisioning and deploying servers with ease. Designed for developers who want to automate their server setup and deployment processes.

## Current Status

⚠️ Important Note: Fuse is currently under heavy development. We don't recommend using it in production yet.

## Quick Start

### Requirements
- PHP 8.3+
- Composer
- Node.js 16+
- MySQL/PostgreSQL
- Redis (recommended)

### Installation

1. Clone the repository:
```bash
git clone https://github.com/terrific-mx/fuse.git
cd fuse
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Run migrations:
```bash
php artisan migrate
```

5. Start the development server:
```bash
npm composer run dev
php artisan serve
```
