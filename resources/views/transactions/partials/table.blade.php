<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left p-4 font-medium text-slate-700">Tanggal</th>
                    <th class="text-left p-4 font-medium text-slate-700">Kategori</th>
                    <th class="text-left p-4 font-medium text-slate-700">Periode</th>
                    <th class="text-right p-4 font-medium text-slate-700">Jumlah</th>
                    <th class="text-center p-4 font-medium text-slate-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <template x-for="transaction in transactions" :key="transaction.id">
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4 whitespace-nowrap" x-text="transaction.date"></td>
                        <td class="p-4" x-text="transaction.category?.name ?? '-'"></td>
                        <td class="p-4" x-text="transaction.period?.name ?? '-'"></td>
                        <td class="p-4 text-right font-medium" :class="type === 'expense' ? 'text-red-600' : (type === 'income' ? 'text-emerald-600' : 'text-blue-600')" x-text="formatCurrency(transaction.amount)"></td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="edit(transaction)" class="text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors">Edit</button>
                                <button @click="confirmDelete(transaction)" class="text-red-600 hover:text-red-800 font-medium text-sm transition-colors">Hapus</button>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="transactions.length === 0">
                    <td colspan="5" class="p-10 text-center text-slate-500">Belum ada transaksi.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="px-4 py-3 border-t border-slate-200" x-show="transactions.length > 0">
        <nav class="flex items-center justify-between">
            <span class="text-sm text-slate-600" x-text="'Menampilkan ' + transactions.length + ' dari ' + pagination.total + ' transaksi'"></span>
            <div class="flex items-center gap-2">
                <button @click="loadTransactions(pagination.current_page - 1)" :disabled="pagination.current_page <= 1" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">Sebelumnya</button>
                <span class="px-3 py-1.5 text-sm text-slate-600" x-text="'Halaman ' + pagination.current_page + ' dari ' + pagination.last_page"></span>
                <button @click="loadTransactions(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">Selanjutnya</button>
            </div>
        </nav>
    </div>
</div>