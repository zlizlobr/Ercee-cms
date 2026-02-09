# Funnel Engine - Průvodce pro vývojáře

Tento dokument popisuje, jak pracovat s funnel enginem v Ercee CMS.

## Co je Funnel?

Funnel (trychtýř) je automatizovaná sekvence kroků, která se spustí na základě určité události. Například:
- Uživatel vyplní formulář → automaticky se mu pošle uvítací email
- Po 24 hodinách → pošle se follow-up email
- Přidá se tag "lead_qualified"

## Architektura

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  Event          │────▶│  FunnelStarter   │────▶│  FunnelRun      │
│  (ContractCreated)    │  (Service)       │     │  (tracking)     │
└─────────────────┘     └──────────────────┘     └────────┬────────┘
                                                          │
                                                          ▼
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  StepExecutor   │◀────│  ExecuteFunnel   │◀────│  Queue Job      │
│  (delay/email/..)     │  StepJob         │     │  (async)        │
└─────────────────┘     └──────────────────┘     └─────────────────┘
```

## Klíčové soubory

### Modely

| Soubor | Popis |
|--------|-------|
| `app/Domain/Funnel/Funnel.php` | Hlavní model funnelu |
| `app/Domain/Funnel/FunnelStep.php` | Jednotlivé kroky funnelu |
| `app/Domain/Funnel/FunnelRun.php` | Běžící instance funnelu pro subscribera |
| `app/Domain/Funnel/FunnelRunStep.php` | Log provedení jednotlivého kroku |

### Services

| Soubor | Popis |
|--------|-------|
| `app/Domain/Funnel/Services/FunnelStarter.php` | Spouští funnely na základě triggerů |
| `app/Domain/Funnel/Jobs/ExecuteFunnelStepJob.php` | Queue job pro provedení kroku |

### Step Executors

| Soubor | Popis |
|--------|-------|
| `app/Domain/Funnel/StepExecutors/DelayExecutor.php` | Časové zpoždění |
| `app/Domain/Funnel/StepExecutors/EmailExecutor.php` | Odeslání emailu |
| `app/Domain/Funnel/StepExecutors/WebhookExecutor.php` | HTTP volání |
| `app/Domain/Funnel/StepExecutors/TagExecutor.php` | Přidání tagu subscriberovi |

## Databázové tabulky

### funnels
```sql
id              - primární klíč
name            - název funnelu
trigger_type    - typ triggeru (contract_created, order_paid, manual)
active          - je funnel aktivní?
created_at
updated_at
```

### funnel_steps
```sql
id              - primární klíč
funnel_id       - FK na funnels
type            - typ kroku (delay, email, webhook, tag)
config          - JSON konfigurace kroku
position        - pořadí kroku (0, 1, 2, ...)
created_at
updated_at
```

### funnel_runs
```sql
id              - primární klíč
funnel_id       - FK na funnels
subscriber_id   - FK na subscribers
status          - stav (running, completed, failed)
current_step    - aktuální krok
started_at      - kdy začal
completed_at    - kdy skončil
created_at
updated_at
```

### funnel_run_steps
```sql
id              - primární klíč
funnel_run_id   - FK na funnel_runs
funnel_step_id  - FK na funnel_steps
status          - stav (pending, success, failed)
executed_at     - kdy byl proveden
payload         - JSON výstup z executoru
error_message   - chybová hláška (pokud failed)
created_at
updated_at
```

## Jak vytvořit funnel v admin panelu

1. Jdi na `/admin/funnels`
2. Klikni na "Create"
3. Vyplň:
   - **Name**: Název funnelu (např. "Welcome Sequence")
   - **Trigger**: Kdy se má spustit
   - **Active**: Zapnout/vypnout
4. Přidej kroky pomocí "Add Step":
   - Vyber typ kroku
   - Nastav konfiguraci

### Příklad: Welcome Email Sequence

```
Funnel: Welcome Sequence
Trigger: Contract Created

Step 1: Tag
  - tag: "new_lead"

Step 2: Email
  - subject: "Vítejte!"
  - body: "Děkujeme za registraci..."

Step 3: Delay
  - seconds: 86400 (24 hodin)

Step 4: Email
  - subject: "Jak se vám daří?"
  - body: "Máte nějaké otázky?..."
```

## Jak programově spustit funnel

### Automaticky při eventu

Funnely se spouštějí automaticky přes listener:

```php
// app/Listeners/StartFunnelsOnContractCreated.php
class StartFunnelsOnContractCreated
{
    public function handle(ContractCreated $event): void
    {
        $this->funnelStarter->startByTrigger(
            Funnel::TRIGGER_CONTRACT_CREATED,
            $event->subscriber
        );
    }
}
```

### Manuálně v kódu

```php
use App\Domain\Funnel\Funnel;
use App\Domain\Funnel\Services\FunnelStarter;
use Modules\Forms\Domain\Subscriber\Subscriber;

$starter = app(FunnelStarter::class);
$subscriber = Subscriber::find(1);

// Spustit všechny funnely s daným triggerem
$runs = $starter->startByTrigger(Funnel::TRIGGER_MANUAL, $subscriber);

// Nebo spustit konkrétní funnel
$funnel = Funnel::find(1);
$run = $starter->startManually($funnel, $subscriber);
```

## Jak přidat nový typ kroku

1. Vytvoř nový executor v `app/Domain/Funnel/StepExecutors/`:

```php
<?php

namespace App\Domain\Funnel\StepExecutors;

use App\Domain\Funnel\FunnelRun;
use App\Domain\Funnel\FunnelStep;
use Modules\Forms\Domain\Subscriber\Subscriber;

class SmsExecutor implements StepExecutorInterface
{
    public function execute(FunnelStep $step, FunnelRun $run, Subscriber $subscriber): array
    {
        $phone = $step->config['phone'] ?? $subscriber->phone;
        $message = $step->config['message'] ?? '';

        // Implementace odeslání SMS
        // $smsService->send($phone, $message);

        return [
            'payload' => [
                'phone' => $phone,
                'message' => $message,
            ],
        ];
    }
}
```

2. Zaregistruj v `StepExecutorFactory.php`:

```php
protected array $executors = [
    FunnelStep::TYPE_DELAY => DelayExecutor::class,
    FunnelStep::TYPE_EMAIL => EmailExecutor::class,
    FunnelStep::TYPE_WEBHOOK => WebhookExecutor::class,
    FunnelStep::TYPE_TAG => TagExecutor::class,
    'sms' => SmsExecutor::class,  // Nový typ
];
```

3. Přidej konstantu do `FunnelStep.php`:

```php
public const TYPE_SMS = 'sms';

public static function getTypes(): array
{
    return [
        self::TYPE_DELAY => 'Delay',
        self::TYPE_EMAIL => 'Email',
        self::TYPE_WEBHOOK => 'Webhook',
        self::TYPE_TAG => 'Tag',
        self::TYPE_SMS => 'SMS',  // Nový typ
    ];
}
```

4. Přidej UI v `FunnelResource.php` do repeateru:

```php
// SMS config
Forms\Components\Group::make([
    Forms\Components\TextInput::make('config.phone')
        ->label('Phone (optional)')
        ->helperText('Leave empty to use subscriber phone'),
    Forms\Components\Textarea::make('config.message')
        ->label('Message')
        ->required(fn (Get $get): bool => $get('type') === FunnelStep::TYPE_SMS),
])
    ->visible(fn (Get $get): bool => $get('type') === FunnelStep::TYPE_SMS)
    ->columnSpan(2),
```

## Delay step - speciální chování

Delay step nečeká synchronně, ale naplánuje další job s delay:

```php
// V ExecuteFunnelStepJob.php
if ($result['delay'] ?? false) {
    self::dispatch($this->funnelRunId, $this->stepPosition + 1)
        ->delay(now()->addSeconds($result['delay']));
}
```

Takže pro 24h delay se job odloží o 24 hodin.

## Webhook - placeholdery

V webhook body můžeš použít placeholdery:

| Placeholder | Hodnota |
|------------|---------|
| `{{subscriber_id}}` | ID subscribera |
| `{{subscriber_email}}` | Email subscribera |
| `{{funnel_run_id}}` | ID aktuálního runu |
| `{{funnel_id}}` | ID funnelu |

Příklad:
```json
{
  "user_email": "{{subscriber_email}}",
  "source": "funnel_{{funnel_id}}"
}
```

## Debugging

### Zobrazení failed runů

V admin panelu jdi na `/admin/funnel-runs` a klikni na tab "Failed".

### Ruční spuštění queue workeru

```bash
php artisan queue:work --verbose
```

### Zobrazení obsahu queue

```bash
php artisan queue:monitor
```

### Tinker test

```php
php artisan tinker

use App\Domain\Funnel\FunnelRun;

// Všechny failed runy
FunnelRun::failed()->with('runSteps')->get();

// Detail konkrétního runu
$run = FunnelRun::with(['runSteps.funnelStep', 'subscriber'])->find(1);
$run->runSteps->each(fn($s) => dump($s->status, $s->error_message));
```

## Idempotence

Job `ExecuteFunnelStepJob` je idempotentní - pokud se retry, nekrokuje dvakrát:

```php
// Kontrola, zda už krok nebyl úspěšně proveden
$existingRunStep = FunnelRunStep::where('funnel_run_id', $run->id)
    ->where('funnel_step_id', $step->id)
    ->where('status', FunnelRunStep::STATUS_SUCCESS)
    ->exists();

if ($existingRunStep) {
    $this->scheduleNextStep($run, $step);
    return;
}
```

## Testování

### Unit test pro executor

```php
public function test_tag_executor_adds_tag_to_subscriber(): void
{
    $subscriber = Subscriber::factory()->create();
    $step = FunnelStep::factory()->create([
        'type' => FunnelStep::TYPE_TAG,
        'config' => ['tag' => 'vip'],
    ]);
    $run = FunnelRun::factory()->create(['subscriber_id' => $subscriber->id]);

    $executor = new TagExecutor();
    $result = $executor->execute($step, $run, $subscriber);

    $this->assertTrue($subscriber->hasTag('vip'));
    $this->assertEquals('vip', $result['payload']['tag']);
}
```

### Feature test pro celý flow

```php
public function test_contract_created_triggers_funnel(): void
{
    $funnel = Funnel::factory()
        ->has(FunnelStep::factory()->state(['type' => 'tag', 'config' => ['tag' => 'new']]))
        ->create(['trigger_type' => 'contract_created', 'active' => true]);

    $subscriber = Subscriber::factory()->create();

    ContractCreated::dispatch(
        Contract::factory()->create(['subscriber_id' => $subscriber->id]),
        $subscriber
    );

    $this->assertDatabaseHas('funnel_runs', [
        'funnel_id' => $funnel->id,
        'subscriber_id' => $subscriber->id,
        'status' => 'running',
    ]);
}
```

## Časté problémy

### Funnel se nespouští

1. Je funnel `active`?
2. Má funnel alespoň jeden step?
3. Běží queue worker? (`php artisan queue:work`)
4. Je správný `trigger_type`?

### Email se neposílá

1. Je nakonfigurovaný MAIL_* v `.env`?
2. Běží Mailpit? (`brew services start mailpit`)
3. Zkontroluj Mailpit UI na `http://localhost:8025`

### Webhook vrací chybu

1. Zkontroluj URL (musí být validní)
2. Zkontroluj timeout (max 30s)
3. Podívej se na `error_message` v `funnel_run_steps`

## Další rozvoj

Možná vylepšení:
- Podmíněné kroky (if/else)
- A/B testing emailů
- Integrce s externími ESP (Mailchimp, SendGrid)
- Vizuální funnel builder (drag & drop)
- Statistiky a analytics
