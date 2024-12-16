<script lang="ts">
	import Navbar from '../components/navbar.svelte';
  
	let email = '';
	let password = '';
	let isLoading = false; // New state variable to control loader visibility
  
	function handleLogin() {
	  console.log('Logging in with', email, password);
	  
	  isLoading = true; // Set loading state to true
  
	  // Make a POST request to the login endpoint
	  fetch('http://localhost/bankmanagement/bank/backend/modules/login.php', {
		method: 'POST',
		headers: {
		  'Content-Type': 'application/json'
		},
		body: JSON.stringify({ email, password })
	  })
	  .then(response => response.json())
	  .then(data => {
		console.log(data); // Log the entire response
		if (data.message === "Login successful") {
		  console.log('User ID:', data.userId); // Ensure this matches the PHP response
		  localStorage.setItem('user-id', data.userId); // Store the user ID
		  window.location.href = '/Dashboard';
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
		<h2 class="text-3xl font-extrabold mb-4 text-center text-gradient">Welcome to Dymas Bank System</h2>
		<p class="text-center mb-6 text-gray-600">Please enter your credentials to continue Banking</p>
		
		<div class="mb-4">
		  <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
		  <input type="email" id="email" bind:value={email} required class="mt-1 block w-full p-2 border border-gray-300 rounded" />
		</div>
		
		<div class="mb-4">
		  <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
		  <input type="password" id="password" bind:value={password} required class="mt-1 block w-full p-2 border border-gray-300 rounded" />
		</div>
		
		<button type="submit" class="w-full bg-blue-700 text-white p-2 rounded hover:bg-blue-800">Log In</button>
			<!-- Teller Button -->
			<!-- <p class="mt-4 text-center">
				<button
				  type="button"
				  class="w-full bg-transparent text-green-700 p-2 rounded border-2 border-green-700 hover:bg-green-700 hover:text-white hover:border-transparent"
				  on:click={() => window.location.href='/Teller-Login'}
				>
				  Teller Login
				</button>
			  </p> -->
		
		<p class="mt-4 text-center">Don't Have an Account? 
		  <button type="button" class="text-blue-500" on:click={() => window.location.href='/register'}>Register</button>
		</p>
    
	  </form>
	</div>
  </main>
  