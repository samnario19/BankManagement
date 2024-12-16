<script lang="ts">
  let userName = "Nandy Nario";

</script>
  
<aside class="w-64 h-screen bg-gray-100 p-4 fixed top-0 left-0">
  <div class="text-center mb-6">
    <span class="text-xl font-semibold text-black block">
      DYMAS BANK
    </span>
    <span class="text-sm text-gray-800">{userName}</span>
  </div>
    <ul class="space-y-4">
      <li>
        <a href="/Dashboard" class="block">
          <button
            class="w-full text-left py-2 px-4 rounded-md focus:outline-none hover:bg-blue-100 hover:text-blue-600">
            <span class="mr-2">📑</span> Dashboard
          </button>
        </a>
      </li>
      <li>
        <a href="/Transfer" class="block">
          <button
            class="w-full text-left py-2 px-4 rounded-md focus:outline-none hover:bg-blue-100 hover:text-blue-600">
            <span class="mr-2">💰</span> Transaction
          </button>
        </a>
      </li>
      <li>
        <a href="/Contact" class="block">
        <button
          class="w-full text-left py-2 px-4 rounded-md focus:outline-none hover:bg-blue-100 hover:text-blue-600">
          <span class="mr-2">☎</span> Contact Us
        </button>
      </a>
      </li>
      <li>
        <a href="/Login" class="block">
        <button
          class="w-full text-left py-2 px-4 rounded-md focus:outline-none hover:bg-blue-100 hover:text-blue-600">
          <span class="mr-2">↪</span> Logout
        </button>
      </a>
      </li>
    </ul>
</aside>
