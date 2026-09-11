<?php

namespace App\Console\Commands;

use App\Http\Controllers\TransactionController;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;

class PaymentStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:payment-status-updated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Payment status updated...!';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tnxs = Transaction::with('user')
            ->where('env', 'production')
            ->where('created_at', '<=', now()->subDay())
            ->where('status', 'pending')
            ->get();

        foreach ($tnxs as $tnx) {

            $callback_url = $tnx->callback_url;
            $callback_secret = User::where('id', $tnx->user_id)->value('callback_secret');

            $sendData = [
                'transaction_id' => $tnx->id,
                'order_id' => $tnx->mr_order_id,
                'reference_id' => $tnx->reference_id,
                'amount' => $tnx->amount,
                'refund_amount' => $tnx->refund_amount,
                'status' => 'failed',
                'payer_name' => $tnx->payer_name,
                'payer_email' => $tnx->payer_email,
                'payer_mobile' => $tnx->payer_mobile,
                'redirect_url' => $tnx->redirect_url,
                'callback_url' => $tnx->callback_url,
            ];

            $tnxController = new TransactionController();

            $tnxController->webhook($callback_url, $callback_secret, $sendData);

            $tnx->update(['status' => 'failed']);
        }

        $this->info($this->description);
    }
}
