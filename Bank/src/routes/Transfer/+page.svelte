<script lang="ts">
    import Sidebar from '../components/sidebar.svelte';

    // State Variables
    let balance: number = 0.0;
    let userId: string | null = null;
    let recipientName: string = "";
    let accountNumber: string = "";
    let transferAmount: number = 0.0;
    let withdrawAmount: number = 0.0;
    let depositAmount: number = 0.0;
    let showModal: boolean = false;
    let isLoading: boolean = false;
    let transactions: Array<{ type: string, amount: string, date: string }> = [];

    console.log({ 'user-id': userId, withdrawAmount });


    // Fetch User Info
    const fetchUserInfo = async () => {
        userId = localStorage.getItem('user-id');
        if (!userId) {
            alert("User ID not found. Please log in again.");
            return;
        }

        try {
            const response = await fetch(`http://localhost/bankmanagement/bank/backend/modules/get-user.php?userid=${userId}`);
            if (!response.ok) throw new Error('Failed to fetch account information.');

            const data = await response.json();
            balance = parseFloat(data.balance);
            await fetchTransactionHistory();
        } catch (error) {
            handleError(error, "Unable to fetch user info.");
        }
    };

    // Fetch Transaction History
    const fetchTransactionHistory = async () => {
        if (!userId) {
            console.error("User ID is not set. Cannot fetch transaction history.");
            return;
        }

        try {
            console.log({ 'user-id': userId, transactions });  // Log userId for debugging
            const response = await fetch(`http://localhost/bankmanagement/bank/backend/modules/history.php?userid=${userId}`);
            if (!response.ok) throw new Error('Failed to fetch transaction history.');

            const data = await response.json();
            transactions = data.transactions || [];
            transactions.reverse();  // Reverse the array to show the most recent first
        } catch (error) {
            handleError(error, "Error fetching transaction history.");
        }
    };

    // Log Transaction
    const logTransaction = async (type: string, amount: number) => {
        if (!userId) return;

        try {
            const response = await fetch('http://localhost/bankmanagement/bank/backend/modules/add-transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    'user-id': userId,
                    type,
                    amount,
                }),
            });

            const data = await response.json();
            if (data.status !== 'success') {
                console.error('Failed to log transaction:', data.message);
            }
        } catch (error) {
            console.error('Error logging transaction:', error);
        }
    };

    // Handle Deposit
    const handleDeposit = async () => {
        if (depositAmount <= 0) return alert('Please enter a valid deposit amount.');

        try {
            isLoading = true;
            const response = await fetch('http://localhost/bankmanagement/bank/backend/modules/deposit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 'user-id': userId, depositAmount }),
            });

            const data = await response.json();
            if (data.status === 'success') {
                balance += depositAmount;
                alert('Deposit successful!');
                await logTransaction('deposit', depositAmount);
                depositAmount = 0;
                await fetchTransactionHistory();
            } else {
                alert(data.message || 'Deposit failed.');
            }
        } catch (error) {
            handleError(error, "Deposit operation failed.");
        } finally {
            isLoading = false;
        }
    };

    // Handle Withdrawal
    const handleWithdraw = async () => {
    console.log({ 'user-id': userId, withdrawAmount });

    if (withdrawAmount <= 0) {
        alert('Enter a valid withdrawal amount.');
        return;
    }
    if (withdrawAmount > balance) {
        alert('Insufficient balance.');
        return;
    }

    try {
        isLoading = true;

        // Calculate the new balance
        const newBalance = balance - withdrawAmount;

        const response = await fetch('http://localhost/bankmanagement/bank/backend/modules/withdraw.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 'user-id': userId, balance: newBalance }),
        });

        const data = await response.json();

        if (data.status === 'success') {
            balance = newBalance; 
            alert('Withdrawal successful!');
            await logTransaction('withdraw', withdrawAmount); 
            withdrawAmount = 0; 
            await fetchTransactionHistory(); 
        } else {
            alert(data.message || 'Withdrawal failed.');
        }
    } catch (error) {
        handleError(error, 'Withdrawal operation failed.');
    } finally {
        isLoading = false;
    }
};

    // Handle Transfer
    const handleTransfer = async () => {
        if (!accountNumber || transferAmount <= 0) return alert('Please fill in all fields.');
        if (transferAmount > balance) return alert('Insufficient balance.');

        try {
            isLoading = true;
            const response = await fetch('http://localhost/bankmanagement/bank/backend/modules/banktransfer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    sender_id: userId,
                    recipient_accountnum: accountNumber,
                    transfer_amount: transferAmount,
                }),
            });

            const data = await response.json();
            if (data.status === 'success') {
                balance = data.sender_new_balance;
                alert('Transfer successful!');
                await logTransaction('transfer', transferAmount);
                transferAmount = 0;
                accountNumber = "";
                await fetchTransactionHistory();
            } else {
                alert(data.message || 'Transfer failed.');
            }
        } catch (error) {
            handleError(error, "Transfer operation failed.");
        } finally {
            isLoading = false;
            closeModal();
        }
    };

    // Open/Close Modal
    const openModal = () => (showModal = true);
    const closeModal = () => {
        showModal = false;
        recipientName = "";
        accountNumber = "";
        transferAmount = 0;
    };

    // Error Handler
    const handleError = (error: unknown, defaultMessage: string) => {
        console.error(defaultMessage, error);
        alert(error instanceof Error ? error.message : defaultMessage);
    };

    // Initialize Data
    fetchUserInfo();
</script>




<main class="flex">
    <Sidebar />
    <div class="ml-64 flex-grow p-6">
        <div class="min-h-screen bg-blue-50 p-6">
            <div class="grid grid-cols-3 gap-6">
                <!-- Bank Transfer -->
                <div class="p-6 bg-white shadow rounded-lg">
                    <h2 class="text-lg font-medium text-gray-600 mb-4">Bank Transfer</h2>
                    <input 
                        type="text" 
                        placeholder="Account Number" 
                        class="w-full p-2 rounded border focus:outline-none" 
                        bind:value={accountNumber} 
                    />
                    <input 
                        type="number" 
                        placeholder="Amount" 
                        class="w-full p-2 mt-4 rounded border focus:outline-none" 
                        bind:value={transferAmount} 
                    />
                    <button 
                        class="bg-blue-500 text-white mt-4 px-4 py-2 rounded hover:bg-blue-600" 
                        on:click={handleTransfer} 
                        disabled={isLoading}>
                        {#if isLoading}Processing...{:else}Transfer{/if}
                    </button>
                </div>
                

<!-- Withdraw -->
<div class="p-6 bg-white shadow rounded-lg">
    <h2 class="text-lg font-medium text-gray-600 mb-4">Withdraw</h2>
    <input
        type="number"
        placeholder="Enter Amount"
        class="w-full p-2 rounded border focus:outline-none"
        bind:value={withdrawAmount}
    />
    <button
        class="bg-blue-500 text-white mt-4 px-4 py-2 rounded hover:bg-blue-600"
        on:click={handleWithdraw}
        disabled={isLoading}
    >
        {#if isLoading}Processing...{:else}Withdraw{/if}
    </button>
</div>

                
               <!-- Deposit Section -->
<div class="p-6 bg-white shadow rounded-lg">
    <h2 class="text-lg font-medium text-gray-600 mb-4">Deposit</h2>
    <input
        placeholder="Enter Amount"
        class="w-full p-2 rounded border focus:outline-none"
        bind:value={depositAmount}
    />
    <button
        class="bg-green-500 text-white mt-4 px-4 py-2 rounded hover:bg-green-600"
        on:click={handleDeposit}
        disabled={isLoading}
    >
        {#if isLoading}Processing...{:else}Deposit{/if}
    </button>
</div>

            </div>

            <!-- Transaction History -->
<!-- Transaction History -->
<div class="mt-8 bg-white shadow rounded-lg p-6">
    <h2 class="text-lg font-medium text-gray-600 mb-4">Transaction History</h2>
    <ul>
        {#each transactions as transaction}
            <li class="flex justify-between items-center border-b py-2">
                <div>
                    <p class="font-semibold">{transaction.type}</p>
                    <p class="text-gray-600 text-sm">{new Date(transaction.date).toLocaleString()}</p>
                </div>
                <p class="font-medium">₱{parseFloat(transaction.amount).toFixed(2)}</p>
            </li>
        {/each}
    </ul>
</div>


            

            <!-- Transfer Confirmation Modal -->
            {#if showModal}
                <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
                        <h2 class="text-lg font-semibold text-gray-700">Confirm Transfer</h2>
                        <p class="mt-4"><strong>Recipient:</strong> {recipientName}</p>
                        <p><strong>Account Number:</strong> {accountNumber}</p>
                        <p><strong>Amount:</strong> ₱{transferAmount.toFixed(2)}</p>
                        <div class="flex justify-between mt-6">
                            <button class="bg-red-500 text-white px-4 py-2 rounded" on:click={closeModal}>Cancel</button>
                            <button class="bg-blue-500 text-white px-4 py-2 rounded" on:click={handleTransfer}>Confirm</button>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</main>
