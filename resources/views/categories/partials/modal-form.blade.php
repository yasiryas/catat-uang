<div x-show="openForm" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <div @click.outside="openForm=false" class="bg-white rounded-xl w-full max-w-lg p-6">

        <h2 class="text-xl font-bold mb-5"
            x-text="mode=='create'
                ? 'Tambah Kategori'
                : 'Edit Kategori'"></h2>

        <div class="space-y-4">

            <div>

                <label>Nama</label>

                <input x-model="form.name" class="w-full border rounded-lg p-3">

            </div>

            <div>

                <label>Jenis</label>

                <select x-model="form.type" class="w-full border rounded-lg p-3">

                    <option value="income">
                        Income
                    </option>

                    <option value="expense">
                        Expense
                    </option>

                </select>

            </div>

            <div>

                <label>Budget</label>

                <input type="number" x-model="form.budget_limit" class="w-full border rounded-lg p-3">

            </div>

        </div>

        <div class="flex justify-end gap-3 mt-6">

            <button @click="openForm=false" class="px-4 py-2 rounded-lg border">
                Batal
            </button>

            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                Simpan
            </button>

        </div>

    </div>

</div>
