@extends('layouts.app')

@section('title', 'Profil')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection
