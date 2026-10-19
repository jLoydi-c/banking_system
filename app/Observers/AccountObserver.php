<?php

namespace App\Observers;

use App\Exceptions\InsufficientFundsException;
use App\Models\Account;
use App\Models\Transaction;
use App\Notifications\LowBalanceNotification;

class AccountObserver
{
    /**
     * Runs BEFORE the transaction row is inserted.
     * Throwing here cancels the insert.
     */
    public function creating(Transaction $transaction): void
    {
        // lockForUpdate stops two simultaneous requests from reading the same balance
        $account = Account::lockForUpdate()->findOrFail($transaction->account_id);

        if ($transaction->type === 'deposit') {
            $newBalance = bcadd($account->balance, $transaction->amount, 2);
        } else { // withdrawal or transfer
            $newBalance = bcsub($account->balance, $transaction->amount, 2);

            if (bccomp($newBalance, '0', 2) < 0 ||
                bccomp($newBalance, $account->minimum_balance, 2) < 0) {
                throw new InsufficientFundsException(
                    'Insufficient funds: balance cannot fall below the minimum balance.'
                );
            }
        }

        $account->balance = $newBalance;

        // Balance recovered above the minimum, so re-arm the low-balance alert
        if (bccomp($newBalance, $account->minimum_balance, 2) > 0) {
            $account->low_balance_notified_at = null;
        }

        $account->save();
    }

    /**
     * Runs AFTER the transaction row is saved.
     * Sends the low-balance notification (only once per low period).
     */
    public function created(Transaction $transaction): void
    {
        if ($transaction->type === 'deposit') {
            return;
        }

        $account = Account::find($transaction->account_id);

        if (bccomp($account->balance, $account->minimum_balance, 2) <= 0
            && is_null($account->low_balance_notified_at)) {

            $account->user->notify(new LowBalanceNotification($account));
            $account->forceFill(['low_balance_notified_at' => now()])->save();
        }
    }
}