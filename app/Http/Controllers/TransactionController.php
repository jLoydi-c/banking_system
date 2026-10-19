<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientFundsException;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('account')->latest()->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $accounts = Account::all();

        return view('transactions.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:accounts,id'],
            'type'       => ['required', 'in:deposit,withdrawal'],
            'amount'     => ['required', 'numeric', 'min:1', 'decimal:0,2'],
            'notes'      => ['nullable', 'string'],
        ]);

        // Custom validation: sufficient funds check (after-validation hook)
        $validator->after(function ($validator) use ($request) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if ($request->type === 'withdrawal') {
                $account   = Account::find($request->account_id);
                $remaining = bcsub($account->balance, (string) $request->amount, 2);

                if (bccomp($remaining, $account->minimum_balance, 2) < 0) {
                    $validator->errors()->add(
                        'amount',
                        'Insufficient funds. You must keep at least ₱' .
                        number_format($account->minimum_balance, 2) . ' in the account.'
                    );
                }
            }
        });

        $data = $validator->validate();

        try {
            DB::transaction(function () use ($data) {
                Transaction::create([
                    'account_id'       => $data['account_id'],
                    'type'             => $data['type'],
                    'amount'           => $data['amount'],
                    'reference_number' => 'TXN-' . strtoupper(uniqid()),
                    'notes'            => $data['notes'] ?? null,
                ]);
            });
        } catch (InsufficientFundsException $e) {
            // Safety net if the balance changed after validation passed
            return back()->withInput()->withErrors(['amount' => $e->getMessage()]);
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction completed.');
    }
}