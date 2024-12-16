<script lang="ts">
    import { onMount } from 'svelte';
    import Sidebar from '../components/sidebarteller.svelte';

    let tellerId: string | null = null;
    let userId: string | null = null; // Initially null, will be populated from account data
    let userName = ""; // Teller name, can be dynamic based on login
    let accountNum = ""; // Account number input
    let accountInfo = {
        accountNo: "",
        accountHolder: "",
        email: "",
        balance: 0,
    };
    let depositAmount = 0;  // For deposit
    let withdrawAmount = 0; // For withdraw
    let isLoading: boolean = false; // Track loading state

    // Simulating the teller ID from the session or wherever it's stored
    const tellerIdFromSession = '123'; // Replace with the actual logic to fetch logged-in teller's ID

    // Fetch the logged-in teller's info when the page loads
    const getTellerInfo = async (tellerId: string) => {
        try {
            const response = await fetch(`http://localhost/bankmanagement/bank/backend/modules/get-teller.php?tellerid=${tellerId}`);
            if (!response.ok) throw new Error("Failed to fetch teller data.");

            const data = await response.json();
            console.log("Teller Data:", data);

            if (data.error) {
                alert(data.error);
            } else {
                userName = data.name;
                tellerId = data.tellerID;
            }
        } catch (error) {
            console.error("Error fetching teller info:", error);
            alert("Unable to fetch teller info. Please try again later.");
        }
    };

    // Fetch account info and set userId on page load
    const getAccountInfo = async (accountNum: string) => {
        if (!accountNum.trim()) {
            alert("Please enter a valid account number.");
            return;
        }

        try {
            const response = await fetch(
                `http://localhost/bankmanagement/bank/backend/modules/get-accountNum.php?accountNum=${accountNum}`
            );
            if (!response.ok) throw new Error("Failed to fetch account information.");

            const data = await response.json();
            console.log("Account Data:", data);

            if (data.error) {
                alert(data.error);
            } else {
                // Populate accountInfo and userId
                accountInfo = {
                    accountNo: data.accountNum || "",
                    accountHolder: data.name || "",
                    email: data.email || "",
                    balance: parseFloat(data.balance) || 0,  // Ensure balance is a number
                };
                userId = data.userID; // Assign userID from account data
            }
        } catch (error) {
            console.error("Error fetching account info:", error);
            alert("Unable to fetch account info. Please try again later.");
        }
    };

    // Function to handle deposit
    const handleDeposit = async () => {
        if (depositAmount <= 0) {
            return alert("Please enter a valid deposit amount.");
        }

        if (!userId) {
            console.log("Invalid user ID:", userId);
            return alert("User ID is not available.");
        }

        try {
            isLoading = true;
            const response = await fetch('http://localhost/bankmanagement/bank/backend/modules/deposit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 'user-id': userId, depositAmount }),
            });

            const data = await response.json();
            if (data.status === 'success') {
                accountInfo.balance += depositAmount;
                alert(`Successfully deposited ₱${depositAmount.toFixed(2)}.`);
                depositAmount = 0; // Reset input field
            } else {
                alert(data.message || "Deposit failed.");
            }
        } catch (error) {
            console.error("Error during deposit:", error);
            alert("Deposit operation failed.");
        } finally {
            isLoading = false;
        }
    };

    // Function to handle withdraw
    const handleWithdraw = async () => {
        if (withdrawAmount <= 0) {
            return alert("Please enter a valid amount to withdraw.");
        }

        if (withdrawAmount > accountInfo.balance) {
            return alert("Insufficient balance for this withdrawal.");
        }

        if (!userId) {
            return alert("User ID is not available.");
        }

        try {
            isLoading = true;
            const response = await fetch('http://localhost/bankmanagement/bank/backend/modules/withdraw.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 'user-id': userId, withdrawAmount }),
            });

            const data = await response.json();
            if (data.status === 'success') {
                accountInfo.balance -= withdrawAmount;
                alert(`Successfully withdrew ₱${withdrawAmount.toFixed(2)}.`);
                withdrawAmount = 0; // Reset input field
            } else {
                alert(data.message || "Withdrawal failed.");
            }
        } catch (error) {
            console.error("Error during withdraw:", error);
            alert("Withdrawal operation failed.");
        } finally {
            isLoading = false;
        }
    };

    onMount(() => {
        // Fetch teller info on page load
        getTellerInfo(tellerIdFromSession);

        // Example accountNum to fetch data (can be dynamic)
        const accountNum = '123456789'; // Replace with dynamic input
        getAccountInfo(accountNum);
    });
</script>


<main class="flex">
    <Sidebar />
    <div class="ml-64 flex-grow p-6">
        <div class="min-h-screen bg-blue-50 p-8 space-y-8">
            <!-- Teller Information -->
            <div class="grid grid-cols-2 gap-8">
                <div class="p-6 bg-white shadow rounded-lg">
                    <h2 class="text-lg font-medium text-gray-600">Teller Name</h2>
                    <p class="text-4xl font-bold text-gray-800 mt-2">{userName}</p>
                </div>
                <div class="p-6 bg-blue-500 text-white rounded-lg">
                    <h2 class="text-lg font-semibold">Teller ID</h2>
                    <p class="text-xl font-mono mt-4">{tellerId}</p>
                </div>
            </div>

            <!-- Account Information -->
            <div class="p-6 bg-white shadow rounded-lg space-y-6">
                <h2 class="text-center text-lg font-medium text-gray-600">Account Information</h2>

                <!-- Account Number Input -->
                <div class="flex items-center bg-green-100 p-4 rounded-lg">
                    <input
                        type="text"
                        placeholder="Enter Account Number"
                        bind:value={accountNum}
                        class="w-full p-2 rounded border focus:outline-none mr-4"
                    />
                    <button
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                        on:click={() => getAccountInfo(accountNum)}>
                        Get Account Info
                    </button>
                </div>

                <!-- Account Details and Actions -->
                <div class="grid grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div>
                            <p class="block font-semibold text-gray-600">Account No.</p>
                            <input
                                type="text"
                                value={accountInfo.accountNo}
                                disabled
                                class="w-full p-2 rounded border bg-gray-100 focus:outline-none"
                            />
                        </div>
                        <div>
                            <p class="block font-semibold text-gray-600">Account Holder Name</p>
                            <input
                                type="text"
                                value={accountInfo.accountHolder}
                                disabled
                                class="w-full p-2 rounded border bg-gray-100 focus:outline-none"
                            />
                        </div>
                        <div>
                            <p class="block font-semibold text-gray-600">Account Holder Email</p>
                            <input
                                type="text"
                                value={accountInfo.email}
                                disabled
                                class="w-full p-2 rounded border bg-gray-100 focus:outline-none"
                            />
                        </div>
                        <div>
                            <p class="block font-semibold text-gray-600">Bank Balance</p>
                            <input
                                type="text"
                                value={`₱${accountInfo.balance.toFixed(2)}`}
                                disabled
                                class="w-full p-2 rounded border bg-gray-100 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="block font-semibold text-gray-600">Deposit Amount</p>
                            <input
                                placeholder="Enter Amount"
                                bind:value={depositAmount}
                                class="w-full p-2 rounded border focus:outline-none"
                            />
                        </div>
                        <button
                            class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
                            on:click={handleDeposit}>
                            Deposit
                        </button>
                        <div>
                            <p class="block font-semibold text-gray-600">Withdrawal Amount</p>
                            <input
                                placeholder="Enter Amount"
                                bind:value={withdrawAmount}
                                class="w-full p-2 rounded border focus:outline-none"
                            />
                        </div>
                        <button
                            class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                            on:click={handleWithdraw}>
                            Withdraw
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
