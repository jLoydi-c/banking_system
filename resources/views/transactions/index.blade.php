<!DOCTYPE html>
<html>
<head>
    <title>Transactions</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .success { background: #e6f4ea; padding: 10px; margin-top: 12px; }
    </style>
</head>
<body>
    <h2>Transactions</h2>
    <p><a href="{{ route('transactions.create') }}">New Transaction</a> |
       <a href="{{ route('dashboard') }}">Dashboard</a></p>

    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Reference</th><th>Account</th><th>Type</th>
                <th>Amount</th><th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $t)
                <tr>
                    <td>{{ $t->reference_number }}</td>
                    <td>{{ $t->account->account_number }}</td>
                    <td>{{ ucfirst($t->type) }}</td>
                    <td>₱{{ number_format($t->amount, 2) }}</td>
                    <td>{{ $t->created_at->format('M d, Y h:i A') }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No transactions yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $transactions->links() }}
</body>
</html>