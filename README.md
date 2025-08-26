# Product Return Refund

Manages product return requests, refunds, and tracks customer return history efficiently.

## Features
- View and manage product returns, and refunds
- Searchable, paginated return refund listing

## Requirements

- PHP >=8.2
- Laravel Framework >= 12.x

## Installation

### 1. Add Git Repository to `composer.json`

```json
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/pavanraj92/admin-product-return-refunds.git"
        }
]
```

### 2. Require the package via Composer
    ```bash
    composer require admin/product_return_refunds:@dev
    ```

### 3. Publish assets
    ```bash
    php artisan return_refunds:publish --force
    ```
---


## Usage

**Handle Returns/Refunds**: View and update return/refund requests.

## Admin Panel Routes

| Method | Endpoint                                 | Description                              |
| ------ | ---------------------------------------- | ---------------------------------------- |
| GET    | /return_refunds                          | List all return/refund requests          |
| GET    | /return_refunds/{return_refund}          | Show return/refund details               |
| POST   | /return_refunds/updateStatus             | Update return/refund status              |

---

## Protecting Admin Routes

Protect your routes using the provided middleware:

```php
Route::middleware(['web','admin.auth'])->group(function () {
    // products routes here
});
```

## License

This package is open-sourced software licensed under the MIT license.
