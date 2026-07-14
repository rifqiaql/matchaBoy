<div x-data="{
        date: new Date().toISOString().split('T')[0],
        get formattedDate() {
            if (!this.date) return 'Pilih Tanggal';
            const d = new Date(this.date);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        }
    }"
    class="relative inline-block">

    <label class="bg-white px-3 py-1.5 rounded-lg shadow-sm text-sm border border-gray-200 text-gray-600 flex items-center gap-2 cursor-pointer hover:bg-gray-50 transition-colors relative overflow-hidden group">

        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500 group-hover:text-dark-matcha transition-colors">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
        </svg>

        <span class="font-medium" x-text="formattedDate"></span>

        <input type="date" x-model="date" @change="$dispatch('date-selected', date)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
    </label>
</div>
