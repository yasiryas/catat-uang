<div x-show="openDelete" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center">

    <div @click.outside="openDelete=false" class="bg-white rounded-xl w-full max-w-md p-6">

        <h2 class="text-xl font-bold">
            Hapus Kategori
        </h2>

        <p class="mt-3">

            Yakin ingin menghapus

            <strong x-text="form.name"></strong> ?

        </p>

        <div class="flex justify-end gap-3 mt-6">

            <button @click="openDelete=false" class="border px-4 py-2 rounded-lg">
                Batal
            </button>

            <button class="bg-red-600 text-white px-4 py-2 rounded-lg">
                Hapus
            </button>

        </div>

    </div>

</div>
