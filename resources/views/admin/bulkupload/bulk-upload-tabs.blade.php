<div class="bulk-upload-tabs flex items-center gap-1 border border-gray-200 dark:border-neutral-600 rounded-md px-1 py-1 bg-white dark:bg-neutral-800">
    
    <a href="{{ route('data_entry_operators.bulkuploadsales') }}"
        class="px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        {{ request()->routeIs('data_entry_operators.bulkuploadsales') 
            ? 'bg-blue-600 text-white' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700' }}">
        Sales
    </a>
    <a href="{{ route('data_entry_operators.bulkuploadpurchase') }}"
        class="px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        {{ request()->routeIs('data_entry_operators.bulkuploadpurchase') 
            ? 'bg-blue-600 text-white' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700' }}">
        Purchase
    </a>
    <a href="{{ route('data_entry_operators.bulkuploadbankstatement') }}"
        class="px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        {{ request()->routeIs('data_entry_operators.bulkuploadbankstatement') 
            ? 'bg-blue-600 text-white' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700' }}">
        Bank
    </a>
    <a href="{{ route('cn.index') }}"
        class="px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        {{ request()->routeIs('cn.index') 
            ? 'bg-blue-600 text-white' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700' }}">
        Credit Note
    </a>
    <a href="{{ route('dn.index') }}"
        class="px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        {{ request()->routeIs('dn.index') 
            ? 'bg-blue-600 text-white' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700' }}">
        Debit Note
    </a>
    <a href="{{ route('journal.index') }}"
        class="px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        {{ request()->routeIs('journal.index') 
            ? 'bg-blue-600 text-white' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700' }}">
        Journal
    </a>
</div>