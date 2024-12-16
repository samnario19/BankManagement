<script>
    import Sidebar from '../components/sidebar.svelte';
    let email = '';
    let subject = '';
    let message = '';

    const handleSubmit = () => {
        if (email && subject && message) {
            alert("Message sent!");
            email = '';
            subject = '';
            message = '';
        } else {
            alert("Please fill out all fields.");
        }
    };
</script>

<main class="flex">
    <Sidebar />
    <div class="ml-64 flex-grow p-6">
        <div class="min-h-screen bg-blue-50 p-6">
            <div class="ml- flex-grow flex justify-center items-center p-6">
        <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-md">
            <!-- Header -->
            <div class="flex items-center mb-6">
                <h5 class="text-xl font-semibold text-gray-700">Contact Us</h5>
            </div>

            <!-- Form -->
            <form on:submit|preventDefault={handleSubmit}>
                <!-- Email Input -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-600">Your email</label>
                    <input
                        id="email"
                        type="email"
                        class="w-full p-2 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="name@company.com"
                        bind:value={email}
                        required
                    />
                </div>

                <!-- Subject Input -->
                <div class="mb-4">
                    <label for="subject" class="block text-sm font-medium text-gray-600">Subject</label>
                    <input
                        id="subject"
                        type="text"
                        class="w-full p-2 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Let us know how we can help you"
                        bind:value={subject}
                        required
                    />
                </div>

                <!-- Message Textarea -->
                <div class="mb-4">
                    <label for="message" class="block text-sm font-medium text-gray-600">Your message</label>
                    <textarea
                        id="message"
                        class="w-full p-2 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Your message..."
                        rows="4"
                        bind:value={message}
                        required
                    ></textarea>
                </div>

                <!-- Submit Button -->
                <div class="mb-4">
                    <button
                        type="submit"
                        class="w-full py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                    >
                        Send message
                    </button>
                </div>
            </form>

            <!-- Contact Information -->
            <div class="text-center text-sm text-gray-500">
                <p class="text-blue-500 hover:underline">Dymas.Thriftech@gmail.com</p>
                <p class="text-blue-500 hover:underline">09266792206</p>
            </div>
        </div>
    </div>
</main>