@extends('layouts.admin')

@section('page_title', 'Entri Pembayaran Manual')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.payments.index') }}" class="hover:text-blue-600">Pembayaran</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Entri Manual</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
        <h1 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3 mb-6 flex items-center">
            <i data-lucide="receipt" class="w-5 h-5 mr-2 text-blue-600"></i> Entri Transaksi Pembayaran
        </h1>

        <form method="POST" action="{{ route('admin.payments.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @include('admin.payments._form')

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.payments.index') }}" class="text-xs font-bold text-slate-500 hover:underline">
                    &larr; Batal & Kembali
                </a>
                <button type="submit" class="py-2.5 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition">
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
