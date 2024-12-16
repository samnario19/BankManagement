<script>
    import Sidebar from '../components/sidebarteller.svelte';

    // Updated transaction data with sender and recipient fields
    let recentTransactions = [
        { date: "2024-12-01", sender: "Sender A", recipient: "Recipient A", amount: 500 },
        { date: "2024-12-02", sender: "Sender B", recipient: "Recipient B", amount: 300 }
    ];
</script>

<main class="flex">
    <Sidebar />
    <div class="ml-64 flex-grow p-6">
        <div class="min-h-screen bg-blue-50 p-8 space-y-8">
            <!-- Recent Transactions -->
            <div class="p-6 bg-white shadow rounded-lg">
                <h2 class="text-lg font-medium text-gray-600 mb-4">Recent Transactions</h2>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="border-b p-2">Date</th>
                            <th class="border-b p-2">Sender</th>
                            <th class="border-b p-2">Recipient</th>
                            <th class="border-b p-2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each recentTransactions as transaction}
                            <tr>
                                <td class="border-b p-2">{transaction.date}</td>
                                <td class="border-b p-2">{transaction.sender}</td>
                                <td class="border-b p-2">{transaction.recipient}</td>
                                <td class="border-b p-2">${transaction.amount.toFixed(2)}</td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
