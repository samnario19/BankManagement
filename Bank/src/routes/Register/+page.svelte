<script lang="ts">
    import Navbar from '../components/navbar.svelte';

    let activeTab = "user";
    let email = '';
    let password = '';
    let userName = '';
    let errorMessage = '';

    const handleSubmit = async () => {
        // Prepare the data to be sent
        const data = {
            userName,
            email,
            password
        };
        console.log(data); 
        try {
            const response = await fetch('http://localhost/bankmanagement/bank/backend/modules/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            // Error Checking / Error response
            const result = await response.text();
            console.log(result); 

            if (response.ok) {
                console.log('Registered succesfully');
                // window.location.href = '/Login'; 
            }
        } catch (error) {
            console.error('Error:', error);
        }
    };
</script>


<Navbar />
<main class="flex min-h-screen bg-blue-50 justify-center items-center">
    <div class="bg-white shadow rounded-lg w-full max-w-md p-6">
        <form on:submit|preventDefault={handleSubmit} class="w-full">
            <h2 class="text-3xl font-extrabold mb-4 text-center text-blue-700">Welcome to Dymas Bank</h2>
            <p class="block mb-4 text-center text-gray-700">Fill in your details to create an account.</p>
            
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    bind:value={userName} 
                    required 
                    class="mt-1 block w-full p-2 border border-gray-300 rounded" 
                    placeholder="Enter your username" />
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    bind:value={email} 
                    required 
                    class="mt-1 block w-full p-2 border border-gray-300 rounded" 
                    placeholder="Enter your email" />
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    bind:value={password} 
                    required 
                    class="mt-1 block w-full p-2 border border-gray-300 rounded" 
                    placeholder="Enter your password" />
            </div>
            
            <button 
                type="submit" 
                class="w-full bg-blue-700 text-white py-2 rounded hover:bg-blue-800 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                Register
            </button>
            
            <p class="mt-4 text-center text-sm text-gray-600">
                Already Have an Account? 
                <button 
                    type="button" 
                    class="text-blue-500 hover:underline" 
                    on:click={() => window.location.href='/Login'}>
                    Login Now
                </button>
            </p>
        </form>
    </div>
</main>

