<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 480px; margin: 40px auto; }
        .card { border: 1px solid #ccc; padding: 12px 16px; margin-top: 12px; border-radius: 6px; }
        .num { font-size: 24px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Bank Dashboard</h2>
    <p><a href="{{ route('transactions.index') }}">Transactions</a> |
       <a href="{{ route('transactions.create') }}">New Transaction</a></p>

    <div class="card">Total Accounts<div class="num" id="stat-accounts">–</div></div>
    <div class="card">Total Money in Bank<div class="num">₱<span id="stat-money">–</span></div></div>
    <div class="card">Transactions Today<div class="num" id="stat-transactions">–</div></div>
    <p><small>Updates every 30 seconds. Last updated: <span id="last-updated">never</span></small></p>

    <script>
        async function loadStats() {
            try {
                const res = await fetch('/api/dashboard/stats', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Request failed');
                const data = await res.json();

                document.getElementById('stat-accounts').textContent = data.total_accounts;
                document.getElementById('stat-money').textContent =
                    Number(data.total_money).toLocaleString(undefined, { minimumFractionDigits: 2 });
                document.getElementById('stat-transactions').textContent = data.transactions_today;
                document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();
            } catch (e) {
                console.error('Could not refresh stats', e);
            }
        }

        loadStats();                    // load immediately
        setInterval(loadStats, 30000);  // then every 30 seconds
    </script>
</body>
</html>