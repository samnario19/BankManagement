<script lang="ts">
	import Navbar from '../components/navbar.svelte';
  
	let email = '';
	let password = '';
	let isLoading = false; // New state variable to control loader visibility
  
	function handleLogin() {
		console.log('Logging in as teller with', email, password);
	  
		isLoading = true; // Set loading state to true
  
		// Make a POST request to the teller login endpoint
		fetch('http://localhost/bankmanagement/bank/backend/modules/login-teller.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json' // Set content type to JSON
			},
			body: JSON.stringify({
				"teller-email": email, // Correct key name as per your PHP script
				"teller-password": password // Correct key name as per your PHP script
			})
		})
		.then(response => response.json())
		.then(data => {
			console.log(data); // Log the entire response
			if (data.message === "Login successful") {
				console.log('Teller ID:', data.userId); // Ensure this matches the PHP response
				localStorage.setItem('teller-id', data.userId); // Store the teller ID
				window.location.href = '/TellerDash'; // Redirect to the Teller Dashboard
			} else {
				console.error(data.message);
			}
			isLoading = false; // Reset loading state
		})
		.catch(error => {
			console.error('Error:', error);
			isLoading = false; // Reset loading state
		});
	}
  
	function handleSubmit() {
		handleLogin(); 
	}
</script>

<Navbar />
<main class="flex min-h-screen bg-blue-50 justify-center items-center">
	<div class="bg-white shadow rounded-lg w-full max-w-md p-6">
		<form on:submit|preventDefault={handleSubmit} class="w-full">
			<h2 class="text-3xl font-extrabold mb-4 text-center text-gradient">Teller Login</h2>
			<p class="text-center mb-6 text-gray-600">Enter your teller credentials</p>
			
			<div class="mb-4">
				<label for="email" class="block text-sm font-medium text-gray-700">Email</label>
				<input type="email" id="email" bind:value={email} required class="mt-1 block w-full p-2 border border-gray-300 rounded" />
			</div>
			
			<div class="mb-4">
				<label for="password" class="block text-sm font-medium text-gray-700">Password</label>
				<input type="password" id="password" bind:value={password} required class="mt-1 block w-full p-2 border border-gray-300 rounded" />
			</div>
			
			<button type="submit" class="w-full bg-blue-700 text-white p-2 rounded hover:bg-blue-800">Log In</button>
			
			<!-- User Login Button -->
			<p class="mt-4 text-center">
				<button
					type="button"
					class="w-full bg-transparent text-green-700 p-2 rounded border-2 border-green-700 hover:bg-green-700 hover:text-white hover:border-transparent"
					on:click={() => window.location.href='/Login'}
				>
					User Login
				</button>
			</p>
		</form>
	</div>
</main>
