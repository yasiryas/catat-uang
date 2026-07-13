@extends('layouts.app')

@section('title', 'Kategori')

@section('content')

    <div x-data="categoryPage()">

        <div class="flex items-center justify-between mb-6">

            <div>
                <h1 class="text-2xl font-bold">
                    Data Kategori
                </h1>

                <p class="text-gray-500">
                    Kelola kategori pemasukan dan pengeluaran.
                </p>
            </div>

            <button @click="create()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                + Tambah
            </button>

        </div>

        @include('categories.partials.table')

        @include('categories.partials.modal-form')

        @include('categories.partials.modal-delete')

    </div>

@endsection
