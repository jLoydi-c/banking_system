<!DOCTYPE html>
<html>
<head>
    <title>New Transaction</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 480px; margin: 40px auto; }
        label { display: block; margin-top: 12px; }
        input, select, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        button { margin-top: 16px; padding: 10px 16px; }
        .error { color: #b00020; font-size: 14px; }
    </style>
</head>
<body>
    <h2>New Transaction</h2>
    <p><a href="{{ route('transactions.index') }}">Transactions</a> |
       <a href="{{ route('dashboard') }}">Dashboard</a></p>

    <form method="POST" action="{{ route('transactions.store') }}">
        @csrf

        <label>Account</label>
        <select name="account_id">
            @foreach ($accounts as $account)
                <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>
                    {{ $account->account_number }} (Balance: ₱{{ number_format($account->balance, 2) }})
                </option>
            @endforeach
        </select>
        @error('account_id') <div class="error">{{ $message }}</div> @enderror

        <label>Type</label>
        <select name="type">
            <option value="deposit" @selected(old('type') == 'deposit')>Deposit</option>
            <option value="withdrawal" @selected(old('type') == 'withdrawal')>Withdrawal</option>
        </select>
        @error('type') <div class="error">{{ $message }}</div> @enderror

        <label>Amount</label>
        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}">
        @error('amount') <div class="error">{{ $message }}</div> @enderror

        <label>Notes (optional)</label>
        <textarea name="notes" rows="3">{{ old('notes') }}</textarea>

        <button type="submit">Submit</button>
    </form>
</body>
</html>