@if(session('success') || session('error'))
  <div class="flash-message {{ session('success') ? 'bg-green-100 border border-green-400 text-green-700 dark:bg-green-800 dark:border-green-500 dark:text-green-100' : 'bg-red-100 border border-red-400 text-red-700 dark:bg-red-800 dark:border-red-500 dark:text-red-100' }} px-4 py-3 rounded mb-4" role="alert">
    <div class="flex items-center gap-2">
      <strong>{{ session('success') ? 'Success!' : 'Error!' }}</strong>
      <span class="block sm:inline">{{ session('success') ?? session('error') }}</span>
    </div>
  </div>
  <script>
    setTimeout(() => {
      const message = document.querySelector('[role="alert"]');
      if (message) message.remove();
    }, 3000);
  </script>
@endif