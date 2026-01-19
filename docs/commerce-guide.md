# Commerce & Checkout - Průvodce pro vývojáře

Tento dokument popisuje, jak pracovat s commerce modulem v Ercee CMS.

## Co je Commerce modul?

Lightweight e-commerce vrstva pro prodej 1-3 produktů bez plnohodnotného e-shop engine. Umožňuje:
- Prodej produktů online
- Zpracování plateb přes Stripe
- Sledování objednávek v admin panelu
- Napojení na marketing automation (funnely)

## Architektura

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  Frontend       │────▶│  POST /checkout  │────▶│  Order          │
│  (checkout form)│     │  (API)           │     │  (pending)      │
└─────────────────┘     └──────────────────┘     └────────┬────────┘
                                                          │
                                                          ▼
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  Stripe         │◀────│  StripeGateway   │◀────│  Payment        │
│  Checkout       │     │  (adapter)       │     │  (pending)      │
└────────┬────────┘     └──────────────────┘     └─────────────────┘
         │
         ▼
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  Webhook        │────▶│  WebhookController│───▶│  OrderPaid      │
│  (Stripe)       │     │  (verify + update)│    │  (event)        │
└─────────────────┘     └──────────────────┘     └────────┬────────┘
                                                          │
                                                          ▼
                                                 ┌─────────────────┐
                                                 │  Funnel         │
                                                 │  (automation)   │
                                                 └─────────────────┘
```

## Klíčové soubory

### Modely

| Soubor | Popis |
|--------|-------|
| `app/Domain/Commerce/Order.php` | Model objednávky |
| `app/Domain/Commerce/Payment.php` | Model platby |
| `app/Domain/Commerce/Product.php` | Model produktu |
| `app/Domain/Commerce/PaymentResult.php` | DTO pro výsledek platby |

### Contracts & Gateways

| Soubor | Popis |
|--------|-------|
| `app/Domain/Commerce/Contracts/PaymentGatewayInterface.php` | Interface pro payment gateways |
| `app/Domain/Commerce/Gateways/StripeGateway.php` | Stripe implementace |

### Controllers

| Soubor | Popis |
|--------|-------|
| `app/Http/Controllers/Api/CheckoutController.php` | API endpoint pro checkout |
| `app/Http/Controllers/Api/WebhookController.php` | Zpracování webhooků |

### Events

| Soubor | Popis |
|--------|-------|
| `app/Domain/Commerce/Events/OrderPaid.php` | Event při zaplacení objednávky |
| `app/Listeners/StartFunnelsOnOrderPaid.php` | Listener pro spuštění funnelů |

### Filament Resources

| Soubor | Popis |
|--------|-------|
| `app/Filament/Resources/OrderResource.php` | Admin UI pro objednávky |
| `app/Filament/Resources/PaymentResource.php` | Admin UI pro platby |
| `app/Filament/Resources/ProductResource.php` | Admin UI pro produkty |

## Databázové tabulky

### orders
```sql
id              - primární klíč
subscriber_id   - FK na subscribers (zákazník)
product_id      - FK na products (produkt)
email           - email zákazníka
price           - cena v haléřích (10000 = 100 Kč)
status          - stav (pending, paid, failed, cancelled)
created_at
updated_at
```

### payments
```sql
id              - primární klíč
order_id        - FK na orders
gateway         - platební brána (stripe, gopay, comgate)
transaction_id  - ID transakce v bráně
status          - stav (pending, paid, failed)
payload         - JSON data z brány
created_at
updated_at
```

### products
```sql
id              - primární klíč
name            - název produktu
price           - cena v haléřích
active          - je produkt aktivní?
created_at
updated_at
```

## Konfigurace Stripe

### 1. Vytvoř Stripe účet

Jdi na [stripe.com](https://stripe.com) a vytvoř účet.

### 2. Nastav environment proměnné

V `.env` přidej:

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_CURRENCY=czk
STRIPE_SUCCESS_URL=https://tvojestranka.cz/checkout/success
STRIPE_CANCEL_URL=https://tvojestranka.cz/checkout/cancel
```

### 3. Nastav webhook v Stripe Dashboard

1. Jdi do Stripe Dashboard → Developers → Webhooks
2. Klikni "Add endpoint"
3. URL: `https://tvojestranka.cz/api/webhooks/stripe`
4. Vyber eventy:
   - `checkout.session.completed`
   - `checkout.session.expired`
5. Zkopíruj "Signing secret" do `STRIPE_WEBHOOK_SECRET`

### Pro lokální vývoj použij Stripe CLI

```bash
# Instalace
brew install stripe/stripe-cli/stripe

# Přihlášení
stripe login

# Forwarding webhooků na localhost
stripe listen --forward-to localhost:8000/api/webhooks/stripe

# Zkopíruj webhook signing secret do .env
```

## API Endpoint

### POST /api/v1/checkout

Iniciuje checkout proces.

**Request:**
```json
{
  "product_id": 1,
  "email": "zakaznik@example.com"
}
```

**Response (201):**
```json
{
  "message": "Checkout initiated",
  "data": {
    "order_id": 1,
    "redirect_url": "https://checkout.stripe.com/..."
  }
}
```

**Errors:**
- `422` - Validation failed (chybí product_id nebo email)
- `404` - Product not found or inactive

**Rate limiting:** 10 requestů/minuta per IP

### Příklad použití na frontendu

```javascript
async function checkout(productId, email) {
  const response = await fetch('/api/v1/checkout', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      product_id: productId,
      email: email,
    }),
  });

  const data = await response.json();

  if (response.ok) {
    // Přesměruj na Stripe Checkout
    window.location.href = data.data.redirect_url;
  } else {
    // Zobraz chybu
    console.error(data.errors);
  }
}
```

## Checkout Flow krok za krokem

### 1. Zákazník odešle checkout formulář

```
POST /api/v1/checkout
{
  "product_id": 1,
  "email": "zakaznik@example.com"
}
```

### 2. Backend vytvoří Order a Payment

```php
// CheckoutController.php
$subscriber = $this->subscriberService->findOrCreateByEmail($email);

$order = Order::create([
    'subscriber_id' => $subscriber->id,
    'product_id' => $product->id,
    'email' => $email,
    'price' => $product->price,
    'status' => Order::STATUS_PENDING,
]);

$redirectUrl = $this->paymentGateway->createPayment($order);
```

### 3. StripeGateway vytvoří Checkout Session

```php
// StripeGateway.php
$session = Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [...],
    'mode' => 'payment',
    'success_url' => '...',
    'cancel_url' => '...',
    'customer_email' => $order->email,
    'metadata' => ['order_id' => $order->id],
]);

// Vytvoří Payment záznam
$order->payments()->create([
    'gateway' => 'stripe',
    'transaction_id' => $session->id,
    'status' => 'pending',
]);

return $session->url;
```

### 4. Zákazník zaplatí na Stripe

Zákazník je přesměrován na Stripe Checkout a zadá platební údaje.

### 5. Stripe pošle webhook

```
POST /api/webhooks/stripe
{
  "type": "checkout.session.completed",
  "data": {
    "object": {
      "id": "cs_...",
      "payment_status": "paid"
    }
  }
}
```

### 6. WebhookController zpracuje webhook

```php
// WebhookController.php

// 1. Ověř podpis
$result = $this->paymentGateway->handleWebhook($request);

// 2. Najdi Payment podle transaction_id
$payment = Payment::where('transaction_id', $result->transactionId)->first();

// 3. Aktualizuj Payment a Order
$payment->update(['status' => 'paid']);
$payment->order->markAsPaid();

// 4. Dispatch event
OrderPaid::dispatch($order, $subscriber);
```

### 7. Event spustí funnel

```php
// StartFunnelsOnOrderPaid.php
$this->funnelStarter->startByTrigger(
    Funnel::TRIGGER_ORDER_PAID,
    $event->subscriber
);
```

## Stavy objednávky

| Status | Popis |
|--------|-------|
| `pending` | Čeká na platbu |
| `paid` | Zaplaceno |
| `failed` | Platba selhala |
| `cancelled` | Zrušeno |

## Stavy platby

| Status | Popis |
|--------|-------|
| `pending` | Čeká na zpracování |
| `paid` | Úspěšně zaplaceno |
| `failed` | Platba selhala |

## Práce v Admin panelu

### Produkty

1. Jdi na `/admin/products`
2. Vytvoř produkt s názvem a cenou
3. Cena je v **haléřích** (10000 = 100 Kč)
4. Aktivuj produkt přepínačem "Active"

### Objednávky

1. Jdi na `/admin/orders`
2. Vidíš seznam všech objednávek
3. Filtruj podle statusu nebo produktu
4. Klikni na objednávku pro detail

### Platby

1. Jdi na `/admin/payments`
2. Vidíš seznam všech plateb
3. Klikni na platbu pro detail včetně JSON payloadu

## Jak přidat novou payment gateway

### 1. Vytvoř nový adapter

```php
<?php

namespace App\Domain\Commerce\Gateways;

use App\Domain\Commerce\Contracts\PaymentGatewayInterface;
use App\Domain\Commerce\Order;
use App\Domain\Commerce\Payment;
use App\Domain\Commerce\PaymentResult;
use Illuminate\Http\Request;

class GopayGateway implements PaymentGatewayInterface
{
    public function createPayment(Order $order): string
    {
        // 1. Vytvoř platbu v GoPay API
        // 2. Ulož Payment záznam
        // 3. Vrať redirect URL

        $gopayPayment = $this->gopayClient->createPayment([...]);

        $order->payments()->create([
            'gateway' => $this->getGatewayName(),
            'transaction_id' => $gopayPayment->id,
            'status' => Payment::STATUS_PENDING,
        ]);

        return $gopayPayment->gw_url;
    }

    public function handleWebhook(Request $request): PaymentResult
    {
        // Zpracuj GoPay notification
        $payment = $this->gopayClient->getPayment($request->id);

        if ($payment->state === 'PAID') {
            return PaymentResult::success($payment->id, [...]);
        }

        return PaymentResult::failed($payment->id, [...]);
    }

    public function getGatewayName(): string
    {
        return Payment::GATEWAY_GOPAY;
    }
}
```

### 2. Přidej konfiguraci

```php
// config/services.php
'gopay' => [
    'go_id' => env('GOPAY_GO_ID'),
    'client_id' => env('GOPAY_CLIENT_ID'),
    'client_secret' => env('GOPAY_CLIENT_SECRET'),
    'is_production' => env('GOPAY_PRODUCTION', false),
],
```

### 3. Zaregistruj v AppServiceProvider

```php
// Pro runtime switching můžeš použít contextual binding
$this->app->when(CheckoutController::class)
    ->needs(PaymentGatewayInterface::class)
    ->give(function () {
        $gateway = request()->input('gateway', 'stripe');
        return match ($gateway) {
            'gopay' => new GopayGateway(),
            default => new StripeGateway(),
        };
    });
```

### 4. Přidej webhook route

```php
// routes/api.php
Route::prefix('webhooks')->group(function () {
    Route::post('/stripe', [WebhookController::class, 'stripe']);
    Route::post('/gopay', [WebhookController::class, 'gopay']);
});
```

## Napojení na Funnely

### Automatické spuštění funnelu po platbě

1. Vytvoř funnel s triggerem `order_paid`
2. Přidej kroky (email, tag, webhook, ...)
3. Aktivuj funnel

Příklad funnelu "Thank You Sequence":

```
Funnel: Thank You Sequence
Trigger: Order Paid

Step 1: Tag
  - tag: "customer"

Step 2: Email
  - subject: "Děkujeme za nákup!"
  - body: "Vaše objednávka byla přijata..."

Step 3: Delay
  - seconds: 604800 (7 dní)

Step 4: Email
  - subject: "Jak jste spokojeni?"
  - body: "Rádi bychom věděli..."
```

### Programové spuštění

```php
use App\Domain\Commerce\Events\OrderPaid;

// Po úspěšné platbě se event dispatchuje automaticky
// Ale můžeš ho dispatchnout i manuálně:
OrderPaid::dispatch($order, $order->subscriber);
```

## Testování

### Testovací karty Stripe

| Číslo karty | Výsledek |
|-------------|----------|
| `4242 4242 4242 4242` | Úspěšná platba |
| `4000 0000 0000 0002` | Zamítnuto |
| `4000 0000 0000 9995` | Nedostatek prostředků |

Použij libovolné datum expirace v budoucnosti a libovolné CVC.

### Unit test pro checkout

```php
public function test_checkout_creates_order_and_redirects_to_stripe(): void
{
    $product = Product::factory()->create(['active' => true, 'price' => 10000]);

    $response = $this->postJson('/api/v1/checkout', [
        'product_id' => $product->id,
        'email' => 'test@example.com',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => ['order_id', 'redirect_url'],
        ]);

    $this->assertDatabaseHas('orders', [
        'product_id' => $product->id,
        'email' => 'test@example.com',
        'status' => 'pending',
    ]);
}
```

### Test webhook handleru

```php
public function test_stripe_webhook_marks_order_as_paid(): void
{
    $order = Order::factory()->create(['status' => 'pending']);
    $payment = Payment::factory()->create([
        'order_id' => $order->id,
        'transaction_id' => 'cs_test_123',
        'status' => 'pending',
    ]);

    // Mock Stripe webhook
    $payload = json_encode([
        'type' => 'checkout.session.completed',
        'data' => ['object' => ['id' => 'cs_test_123']],
    ]);

    $response = $this->postJson('/api/webhooks/stripe', [], [
        'Stripe-Signature' => $this->generateStripeSignature($payload),
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'paid',
    ]);
}
```

## Debugging

### Kontrola stavu objednávky

```bash
php artisan tinker

use App\Domain\Commerce\Order;

// Všechny pending objednávky
Order::pending()->with('payments')->get();

// Detail konkrétní objednávky
$order = Order::with(['payments', 'subscriber', 'product'])->find(1);
dump($order->toArray());
```

### Kontrola webhook logů

```bash
# Zobraz Laravel log
tail -f storage/logs/laravel.log | grep -i webhook
```

### Stripe Dashboard

V Stripe Dashboard → Developers → Events vidíš všechny webhooky a jejich status.

## Časté problémy

### Webhook vrací 400 (Invalid signature)

1. Zkontroluj `STRIPE_WEBHOOK_SECRET` v `.env`
2. Pro lokální vývoj použij Stripe CLI
3. Ujisti se, že používáš správný secret (live vs test)

### Order zůstává v pending stavu

1. Běží queue worker? (`php artisan queue:work`)
2. Přišel webhook? (zkontroluj Stripe Dashboard)
3. Je správně nastavená webhook URL?

### Checkout vrací 404 (Product not found)

1. Je produkt `active`?
2. Existuje produkt s daným ID?
3. Zkontroluj validaci v `CheckoutController`

### Stripe session expiruje

Stripe Checkout session expiruje po 24 hodinách. Pokud zákazník nedokončí platbu, objednávka zůstane v `pending` stavu.

Můžeš přidat scheduled command pro cleanup starých pending objednávek:

```php
// app/Console/Commands/CleanupPendingOrders.php
Order::pending()
    ->where('created_at', '<', now()->subDays(7))
    ->update(['status' => Order::STATUS_CANCELLED]);
```

## Bezpečnost

### Webhook verifikace

Vždy ověřuj Stripe webhook signature:

```php
try {
    $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
} catch (SignatureVerificationException $e) {
    return response('Invalid signature', 400);
}
```

### Rate limiting

Checkout endpoint je rate-limited na 10 requestů/minuta per IP.

### Idempotence

Webhook handler je idempotentní - pokud přijde stejný webhook vícekrát, neaktualizuje se order dvakrát.

## Další rozvoj

Možná vylepšení:
- Podpora více produktů v jedné objednávce
- Kupóny a slevy
- Subscription platby
- Invoice generování
- Refund handling
- Podpora dalších platebních bran (GoPay, Comgate)
