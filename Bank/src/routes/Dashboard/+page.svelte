<script lang="ts">
    import Sidebar from '../components/sidebar.svelte';
    import { onMount } from 'svelte';

    let balance: number = 0.0;
    let userId: string | null = null;
    let accountNum: string = "";
    let transactions: Array<{ type: string, amount: string, date: string }> = [];
    let isLoading: boolean = true; // Set to true while loading
    let isTransactionsLoading: boolean = true; // Separate loading state for transactions

    // Fetch user data (balance, account number, and transaction history)
    const fetchAccountInfo = async () => {
        try {
            // Retrieve `userid` from local storage
            userId = localStorage.getItem('user-id');

            if (!userId) {
                throw new Error("User ID is not available.");
            }

            // Fetch user data (balance, accountNum) from the backend
            const response = await fetch(`http://localhost/bankmanagement/bank/backend/modules/get-user.php?userid=${userId}`);
            
            if (!response.ok) {
                throw new Error('Failed to fetch account information.');
            }

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            // Update the variables with the fetched data
            balance = parseFloat(data.balance); // Assuming balance is a number
            accountNum = data.accountNum;         // Assuming accountNum is a string
            transactions = data.transactions || []; // Directly assign transactions or fallback to empty array
        } catch (error) {
            console.error('Error fetching account info:', error);
        } finally {
            isLoading = false;
            isTransactionsLoading = false;
        }
    };

    // Fetch transaction history specifically
    const fetchTransactionHistory = async () => {
        if (!userId) {
            console.error("User ID is not set. Cannot fetch transaction history.");
            return;
        }

        try {
            const response = await fetch(`http://localhost/bankmanagement/bank/backend/modules/history.php?userid=${userId}`);
            if (!response.ok) throw new Error('Failed to fetch transaction history.');

            const data = await response.json();
            transactions = data.transactions || [];
            transactions.reverse();  // Reverse the array to show the most recent first
        } catch (error) {
            console.error('Error fetching transaction history:', error);
        } finally {
            isTransactionsLoading = false;
        }
    };

    // Fetch both account info and transaction history when the component is mounted
    onMount(() => {
        fetchAccountInfo();
        fetchTransactionHistory();
    });
</script>

<main class="flex">
    <Sidebar />
    <div class="ml-64 flex-grow p-6">
        <div class="min-h-screen bg-blue-50 p-6">
            <!-- Balance and Account Info -->
            <div class="grid grid-cols-2 gap-6">
                <!-- Balance Card -->
                <div class="p-6 bg-white shadow rounded-lg">
                    <h2 class="text-lg font-medium text-gray-600">Balance</h2>
                    {#if isLoading}
                        <p>Loading...</p>
                    {:else}
                        <p class="text-4xl font-bold text-gray-800 mt-2">
                            ₱ {balance.toFixed(2)}
                        </p>
                    {/if}
                </div>

                <!-- Account Information -->
                <div class="p-6 bg-blue-500 text-white rounded-lg">
                    <h2 class="text-lg font-semibold">Account Number</h2>
                    {#if isLoading}
                        <p>Loading...</p>
                    {:else}
                        <p class="text-xl font-mono mt-4">{accountNum}</p>
                    {/if}
                </div>
            </div>

            <!-- Transaction History -->
            <div class="mt-8 bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-600 mb-4">Transaction History</h2>
                {#if isTransactionsLoading}
                    <p>Loading transaction history...</p>
                {:else}
                    <ul>
                        {#each transactions as transaction}
                            <li class="flex justify-between items-center border-b py-2">
                                <div>
                                    <p class="font-semibold">{transaction.type}</p>
                                    <p class="text-gray-600 text-sm">
                                        {new Date(transaction.date).toLocaleString('en-PH', { timeZone: 'Asia/Manila' })}
                                    </p>
                                </div>
                                <p class="font-medium">₱{parseFloat(transaction.amount).toFixed(2)}</p>
                            </li>
                        {/each}
                    </ul>
                    {#if transactions.length === 0}
                        <p>No transactions available.</p>
                    {/if}
                {/if}
            </div>
        </div>
    </div>
</main>
