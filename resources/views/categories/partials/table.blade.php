<div class="bg-white rounded-xl shadow overflow-hidden">

    <table class="w-full">

        <thead class="bg-gray-100">

            <tr>

                <th class="text-left p-4">
                    Nama
                </th>

                <th class="text-left p-4">
                    Jenis
                </th>

                <th class="text-right p-4">
                    Budget
                </th>

                <th class="text-center p-4">
                    Aksi
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($categories as $category)
                <tr class="border-t">

                    <td class="p-4">
                        {{ $category->name }}
                    </td>

                    <td class="p-4">

                        @if ($category->type == 'income')
                            <span class="px-2 py-1 rounded bg-green-100 text-green-700">
                                Income
                            </span>
                        @else
                            <span class="px-2 py-1 rounded bg-red-100 text-red-700">
                                Expense
                            </span>
                        @endif

                    </td>

                    <td class="p-4 text-right">

                        Rp {{ number_format($category->budget_limit, 0, ',', '.') }}

                    </td>

                    <td class="p-4 text-center">

                        <button class="text-blue-600 hover:underline">
                            Edit
                        </button>

                        <button class="ml-3 text-red-600 hover:underline">
                            Hapus
                        </button>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="4" class="p-10 text-center text-gray-500">
                        Belum ada kategori.
                    </td>

                </tr>
            @endforelse

        </tbody>

    </table>

</div>

<div class="mt-6">

    {{ $categories->links() }}

</div>
