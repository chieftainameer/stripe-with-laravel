<?php

namespace App\Jobs\StripeWebhooks;

use App\Payment;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Models\WebhookCall;

class ChargeSucceededJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $webhookCall;
    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $charge = $this->webhookCall->payload['data']['object'];
        $user = User::where('stripe_id',$charge['customer'])->first();
        if ($user){
            Payment::create([
                'user_id' => $user->id,
                'stripe_id' =>$charge['id'],
                'subtotal' => $charge['amount'],
                'total' => $charge['amount'],
            ]);
        }
    }
}
