
## Contracts

### What Are Contracts?

**Contracts** are PHP interfaces that define a standard set of methods for a service, ensuring consistency across different implementations.

- **Purpose**: Define “what” a service does without specifying “how,” enabling interchangeable implementations.
- **Location**: Typically in `app/Contracts`.
- **Examples**: Laravel’s core contracts like `Illuminate\Contracts\Auth\Guard` (authentication) or `Illuminate\Contracts\Cache\Repository` (caching).

### Why Use Contracts?

- **Decoupling**: Code depends on interfaces, not implementations, allowing easy swapping (e.g., Redis vs. Memcached).
- **Testability**: Mock contracts in unit tests to simulate behavior.
- **Flexibility**: Support multiple implementations of the same functionality.
- **Consistency**: Ensure consistent APIs across drivers.

### Example: Payment Gateway Contract

This example shows a `PaymentGateway` contract with two implementations: `StripePaymentService` and `PayPalPaymentService`.

#### Code: `app/Contracts/PaymentGateway.php`

```php
<?php

namespace App\Contracts;

interface PaymentGateway
{
    public function process(float $amount): bool;
    public function refund(float $amount): bool;
}
```

#### Code: `app/Services/StripePaymentService.php`

```php
<?php

namespace App\Services;

use App\Contracts\PaymentGateway;

class StripePaymentService implements PaymentGateway
{
    public function process(float $amount): bool
    {
        \Log::info("Processing payment of $amount via Stripe");
        return true;
    }

    public function refund(float $amount): bool
    {
        \Log::info("Refunding $amount via Stripe");
        return true;
    }
}
```

#### Code: `app/Services/PayPalPaymentService.php`

```php
<?php

namespace App\Services;

use App\Contracts\PaymentGateway;

class PayPalPaymentService implements PaymentGateway
{
    public function process(float $amount): bool
    {
        \Log::info("Processing payment of $amount via PayPal");
        return true;
    }

    public function refund(float $amount): bool
    {
        \Log::info("Refunding $amount via PayPal");
        return true;
    }
}
```

#### Binding in Service Provider

```php
<?php

namespace App\Providers;

use App\Contracts\PaymentGateway;
use App\Services\StripePaymentService;
use App\Services\PayPalPaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PaymentGateway::class, function ($app) {
            $gateway = config('payment.gateway', 'stripe');
            return $gateway === 'stripe' ? new StripePaymentService() : new PayPalPaymentService();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/payment.php' => config_path('payment.php'),
        ], 'payment-config');
    }
}
```

#### Config: `config/payment.php`

```php
<?php
return [
    'gateway' => env('PAYMENT_GATEWAY', 'stripe'),
];
```

#### Usage in Controller

```php
<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGateway;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function process(Request $request)
    {
        $amount = $request->input('amount');
        if ($this->paymentGateway->process($amount)) {
            return response()->json(['message' => 'Payment processed successfully']);
        }
        return response()->json(['message' => 'Payment failed'], 400);
    }
}
```

### Use Cases for Contracts

- **Multiple Drivers**: Support different implementations (e.g., caching with Redis or Memcached).
- **Testing**: Mock contracts for unit tests to avoid real API calls.
- **Extensibility**: Allow third-party packages or future implementations to conform to the same interface.
