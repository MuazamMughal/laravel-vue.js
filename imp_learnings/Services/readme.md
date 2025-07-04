
## Services

### What Are Services?

A **service** in Laravel is a PHP class that encapsulates specific business logic or functionality, adhering to the **Single Responsibility Principle (SRP)**. Services are a design pattern used to organize code, not a Laravel-specific feature.

- **Purpose**: Move complex business logic out of controllers and models to keep them focused on their primary roles.
- **Location**: Typically stored in the `app/Services` directory (though this is a convention, not mandatory).
- **Characteristics**:
  - Plain PHP classes that handle tasks like payment processing, file uploads, or email sending.
  - Injected into controllers or other classes via dependency injection.
  - Can depend on other services, repositories, or external APIs.

### Why Use Services?

Services improve code quality and maintainability by offering:

- **Separation of Concerns**: Controllers handle HTTP requests and responses; services manage business logic.
- **Reusability**: Services can be reused across controllers, jobs, or Artisan commands.
- **Testability**: Isolated logic is easier to unit test, free from HTTP or framework concerns.
- **Maintainability**: Centralizing logic simplifies updates and refactoring.

### Example: Payment Service

Below is an example of a `PaymentService` that processes payments for a user.

#### Code: `app/Services/PaymentService.php`

```php
<?php

namespace App\Services;

use App\Models\User;
use Exception;

class PaymentService
{
    public function processPayment(User $user, float $amount): bool
    {
        try {
            if ($amount <= 0) {
                throw new Exception('Invalid payment amount.');
            }

            // Simulate payment gateway interaction (e.g., Stripe API call)
            $user->balance -= $amount;
            $user->save();

            return true;
        } catch (Exception $e) {
            \Log::error('Payment failed: ' . $e->getMessage());
            return false;
        }
    }
}
```

#### Usage in a Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function process(Request $request, User $user)
    {
        $amount = $request->input('amount');
        if ($this->paymentService->processPayment($user, $amount)) {
            return response()->json(['message' => 'Payment processed successfully']);
        }
        return response()->json(['message' => 'Payment failed'], 400);
    }
}
```

### Use Cases for Services

- **Payment Processing**: Manage interactions with payment gateways like Stripe or PayPal.
- **File Uploads**: Handle file validation, storage (e.g., AWS S3), and metadata management.
- **Email Notifications**: Send emails via providers like Mailgun or AWS SES.
- **Data Processing**: Perform complex calculations or integrations with external APIs.

### When to Use Services

Use services when:
- Business logic is too complex for controllers or models.
- Functionality needs to be reused across multiple parts of the application.
- You want to isolate logic for unit testing.