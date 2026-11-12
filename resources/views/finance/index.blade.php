@extends('layouts.app')

@section('title', 'Finans & Ay Sonu Muhasebe | Diş Kliniği')

@section('content')
@php
    $formatMoney = fn ($value) => number_format((float) $value, 2, ',', '.') . ' ₺';
@endphp

<!-- Üst Başlık, Ay Seçici ve Hızlı Aksiyonlar -->
<div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Finans &amp; Ay Sonu Muhasebe</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            <span class="font-semibold text-slate-700">{{ $monthLabel }}</span> dönemi kasa akışı, gelir-gider dengesi ve hasta alacakları
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-2.5">
        <!-- Ay Seçici -->
        <div class="inline-flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-sm">
            <a
                href="{{ route('finance.index', ['month' => $prevMonth]) }}"
                title="Önceki Ay"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </a>
            <form method="GET" action="{{ route('finance.index') }}" class="flex items-center">
                <input
                    type="month"
                    name="month"
                    value="{{ $monthInput }}"
                    onchange="this.form.submit()"
                    class="px-2 py-1 text-sm font-semibold text-slate-800 bg-transparent border-0 focus:outline-none cursor-pointer"
                >
            </form>
            <a
                href="{{ route('finance.index', ['month' => $nextMonth]) }}"
                title="Sonraki Ay"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
            @if ($monthInput !== now()->format('Y-m'))
                <a
                    href="{{ route('finance.index') }}"
                    class="ml-1 px-2.5 py-1 text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg transition"
                >
                    Bu Ay
                </a>
            @endif
        </div>

        <button
            type="button"
            onclick="openQuickPaymentModal()"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tahsilat (Gelir) Ekle
        </button>

        <button
            type="button"
            onclick="document.getElementById('new-expense-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-sm transition"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
            </svg>
            Gider Ekle
        </button>

        <a
            href="#fiyat-listesi"
            class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-sm font-semibold text-sky-700 bg-sky-50 border border-sky-200/80 hover:bg-sky-100 rounded-xl shadow-sm transition"
        >
            Fiyat Listesi ({{ $treatmentPrices->count() }})
        </a>

        <button
            type="button"
            onclick="window.print()"
            class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl shadow-sm transition"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
            </svg>
            Yazdır
        </button>
    </div>
</div>

@if ($errors->any())
    <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-800">
        <div class="font-semibold mb-1">İşlem kaydedilemedi:</div>
        <ul class="list-disc list-inside space-y-0.5 text-xs">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- 1. Üst Kısım: Ay Sonu KPI Kartları -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <!-- Aylık Gelir -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-5 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Aylık Gelir (Tahsilat)</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1.5">{{ $formatMoney($monthlyIncome) }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $incomeCount }} tahsilat kaydı</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
            </svg>
        </div>
    </div>

    <!-- Aylık Gider -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-5 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Aylık Gider</p>
            <p class="text-2xl font-bold text-rose-600 mt-1.5">{{ $formatMoney($monthlyExpense) }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $expenseCount }} gider kalemi</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6 9 12.75l4.286-4.286a11.948 11.948 0 0 1 4.306 6.43l.776 2.898m0 0 3.182-5.511m-3.182 5.51-5.511-3.181" />
            </svg>
        </div>
    </div>

    <!-- Ay Sonu Net Kâr / Zarar -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-5 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ay Sonu Net Kasa</p>
            <p class="text-2xl font-bold mt-1.5 {{ $monthlyNet >= 0 ? 'text-sky-700' : 'text-rose-700' }}">
                {{ $monthlyNet > 0 ? '+' : '' }}{{ $formatMoney($monthlyNet) }}
            </p>
            <p class="text-xs text-slate-400 mt-1">
                @if ($monthlyIncome > 0)
                    Kâr marjı: %{{ number_format($profitMargin, 1, ',', '.') }}
                @else
                    Bu ay henüz tahsilat yok
                @endif
            </p>
        </div>
        <div class="w-12 h-12 rounded-xl {{ $monthlyNet >= 0 ? 'bg-sky-50 text-sky-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3" />
            </svg>
        </div>
    </div>

    <!-- Açık Hasta Alacağı -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-5 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Açık Hasta Alacağı</p>
            <p class="text-2xl font-bold text-amber-600 mt-1.5">{{ $formatMoney($totalReceivable) }}</p>
            <p class="text-xs text-slate-400 mt-1">
                {{ $debtorPatients->count() }} borçlu hasta • Ay tedavisi: {{ $formatMoney($monthlyBilled) }}
            </p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
    </div>
</div>

<!-- 2. Ana Düzen: Sol 2 Birim (Hareketler + 6 Aylık Özet) | Sağ 1 Birim (Kasa Dağılımı + Alacaklar) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Sol Kolon (2 birim) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Gelir & Gider Hareketleri Defteri -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ $monthLabel }} Gelir &amp; Gider Hareketleri</h2>
                    <p class="text-xs text-slate-500">Hasta tahsilatları ve klinik giderlerinin kronolojik dökümü</p>
                </div>

                <div class="inline-flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs font-semibold">
                    <button type="button" data-ledger-filter="all" class="ledger-filter-btn px-3 py-1.5 rounded-lg bg-white text-slate-900 shadow-sm transition">
                        Tümü ({{ $ledger->count() }})
                    </button>
                    <button type="button" data-ledger-filter="income" class="ledger-filter-btn px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition">
                        Gelirler ({{ $incomeCount }})
                    </button>
                    <button type="button" data-ledger-filter="expense" class="ledger-filter-btn px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition">
                        Giderler ({{ $expenseCount }})
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Tarih</th>
                            <th class="px-6 py-3 font-semibold">Tür / Kategori</th>
                            <th class="px-6 py-3 font-semibold">Açıklama / Hasta</th>
                            <th class="px-6 py-3 font-semibold">Yöntem</th>
                            <th class="px-6 py-3 font-semibold text-right">Tutar</th>
                            <th class="px-6 py-3 font-semibold text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="finance-ledger-body">
                        @forelse ($ledger as $entry)
                            <tr data-entry-type="{{ $entry['type'] }}" class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-3.5 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $entry['date_formatted'] }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    @if ($entry['type'] === 'income')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                                            Gelir
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/70">
                                            {{ $entry['category'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5">
                                    @if ($entry['patient_id'])
                                        <a href="{{ route('patients.show', $entry['patient_id']) }}" class="font-semibold text-slate-900 hover:text-sky-600 transition">
                                            {{ $entry['title'] }}
                                        </a>
                                    @else
                                        <div class="font-semibold text-slate-900">{{ $entry['title'] }}</div>
                                    @endif
                                    <div class="text-xs text-slate-500">{{ $entry['subtitle'] }}</div>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs font-medium text-slate-600">
                                    {{ $entry['method'] }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-right font-bold {{ $entry['type'] === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $entry['type'] === 'income' ? '+' : '-' }}{{ $formatMoney($entry['amount']) }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-right">
                                    <form
                                        action="{{ $entry['delete_url'] }}"
                                        method="POST"
                                        onsubmit="return confirm('Bu {{ $entry['type'] === 'income' ? 'tahsilat' : 'gider' }} kaydını silmek istediğinize emin misiniz?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            title="Kaydı Sil"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400">
                                    {{ $monthLabel }} dönemi için kayıtlı gelir veya gider hareketi bulunmuyor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Son 6 Aylık Ay Sonu Muhasebe Özeti -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Son 6 Ay – Ay Sonu Kapanış Tablosu</h2>
                <p class="text-xs text-slate-500">Detayını incelemek istediğiniz aya tıklayabilirsiniz</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Dönem</th>
                            <th class="px-6 py-3 font-semibold text-right">Gelir</th>
                            <th class="px-6 py-3 font-semibold text-right">Gider</th>
                            <th class="px-6 py-3 font-semibold text-right">Ay Sonu Net</th>
                            <th class="px-6 py-3 font-semibold text-right">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($monthlyHistory as $row)
                            <tr
                                onclick="window.location='{{ route('finance.index', ['month' => $row['month']]) }}'"
                                class="cursor-pointer transition {{ $row['is_selected'] ? 'bg-sky-50/70' : 'hover:bg-slate-50/80' }}"
                            >
                                <td class="px-6 py-3.5 font-semibold text-slate-900">
                                    {{ $row['label'] }}
                                    @if ($row['is_selected'])
                                        <span class="ml-1.5 text-[10px] font-bold px-2 py-0.5 rounded-full bg-sky-100 text-sky-700">Seçili</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-right font-medium text-emerald-600">{{ $formatMoney($row['income']) }}</td>
                                <td class="px-6 py-3.5 text-right font-medium text-rose-600">{{ $formatMoney($row['expense']) }}</td>
                                <td class="px-6 py-3.5 text-right font-bold {{ $row['net'] >= 0 ? 'text-slate-900' : 'text-rose-700' }}">
                                    {{ $row['net'] > 0 ? '+' : '' }}{{ $formatMoney($row['net']) }}
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    @if ($row['net'] > 0)
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Kâr</span>
                                    @elseif ($row['net'] < 0)
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700">Zarar</span>
                                    @else
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">Başa Baş</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Klinik İşlem & Fiyat Listesi Yönetimi -->
        <div id="fiyat-listesi" class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden scroll-mt-20">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Klinik İşlem &amp; Fiyat Listesi</h2>
                    <p class="text-xs text-slate-500">Odontogram işlem seçiminde kullanılan standart tedavi türleri ve varsayılan ücretler</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                    {{ $treatmentPrices->count() }} işlem tanımlı
                </span>
            </div>

            <!-- Yeni İşlem Tipi Ekleme Formu -->
            <form action="{{ route('finance.treatment-prices.store') }}" method="POST" class="px-6 py-4 bg-slate-50/70 border-b border-slate-100">
                @csrf
                <input type="hidden" name="month" value="{{ $monthInput }}">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Yeni İşlem Adı *</label>
                        <input
                            type="text"
                            name="name"
                            required
                            placeholder="Örn: Zirkonyum Kaplama"
                            class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none"
                        >
                    </div>
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Varsayılan Ücret (₺) *</label>
                        <input
                            type="number"
                            name="default_price"
                            step="0.01"
                            min="0"
                            required
                            placeholder="0.00"
                            class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none"
                        >
                    </div>
                    <div class="sm:col-span-2">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-1 px-3.5 py-2 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-sm transition"
                        >
                            + Ekle
                        </button>
                    </div>
                </div>
            </form>

            <!-- Mevcut İşlem ve Fiyatları Güncelleme Tablosu -->
            <div class="overflow-x-auto">
                @foreach ($treatmentPrices as $tp)
                    <form id="update-tp-{{ $tp->id }}" action="{{ route('finance.treatment-prices.update', $tp) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="month" value="{{ $monthInput }}">
                    </form>
                @endforeach

                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 font-semibold">İşlem Adı</th>
                            <th class="px-6 py-3 font-semibold w-44">Varsayılan Ücret (₺)</th>
                            <th class="px-6 py-3 font-semibold text-right w-40">Aksiyon</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($treatmentPrices as $tp)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-3">
                                    <input
                                        type="text"
                                        name="name"
                                        form="update-tp-{{ $tp->id }}"
                                        value="{{ $tp->name }}"
                                        required
                                        class="w-full px-3 py-1.5 text-sm font-medium text-slate-900 bg-white border border-slate-200 rounded-lg focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none"
                                    >
                                </td>
                                <td class="px-6 py-3">
                                    <input
                                        type="number"
                                        name="default_price"
                                        form="update-tp-{{ $tp->id }}"
                                        value="{{ number_format((float) $tp->default_price, 2, '.', '') }}"
                                        step="0.01"
                                        min="0"
                                        required
                                        class="w-full px-3 py-1.5 text-sm font-semibold text-slate-900 bg-white border border-slate-200 rounded-lg focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none text-right"
                                    >
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <button
                                            type="submit"
                                            form="update-tp-{{ $tp->id }}"
                                            class="px-3 py-1.5 text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg transition"
                                        >
                                            Güncelle
                                        </button>

                                        <form
                                            action="{{ route('finance.treatment-prices.destroy', $tp) }}"
                                            method="POST"
                                            onsubmit="return confirm('{{ addslashes($tp->name) }} işlemini fiyat listesinden silmek istediğinize emin misiniz?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="month" value="{{ $monthInput }}">
                                            <button
                                                type="submit"
                                                title="İşlemi Sil"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-xs text-slate-400">
                                    Henüz tanımlı işlem bulunmuyor. Yukarıdan yeni işlem ekleyebilirsiniz.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sağ Kolon (1 birim) -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Ödeme Yöntemi Kasa Özeti -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-5">
            <h2 class="text-base font-bold text-slate-900">Kasa &amp; Banka Dağılımı</h2>
            <p class="text-xs text-slate-500 mt-0.5 mb-4">{{ $monthLabel }} yöntem bazlı net durum</p>

            <div class="space-y-3">
                @foreach ($methodBreakdown as $method => $stats)
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-slate-800">{{ $method }}</span>
                            <span class="text-sm font-bold {{ $stats['net'] >= 0 ? 'text-slate-900' : 'text-rose-600' }}">
                                {{ $stats['net'] > 0 ? '+' : '' }}{{ $formatMoney($stats['net']) }}
                            </span>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs">
                            <span class="text-emerald-600 font-medium">Gelir: {{ $formatMoney($stats['income']) }}</span>
                            <span class="text-rose-600 font-medium">Gider: {{ $formatMoney($stats['expense']) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Gider Kategori Dağılımı -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-5">
            <h2 class="text-base font-bold text-slate-900">Gider Kalemleri</h2>
            <p class="text-xs text-slate-500 mt-0.5 mb-4">{{ $monthLabel }} kategori dağılımı</p>

            @forelse ($categoryTotals as $cat)
                <div class="mb-3.5 last:mb-0">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-semibold text-slate-700">{{ $cat['category'] }} ({{ $cat['count'] }})</span>
                        <span class="font-bold text-slate-900">{{ $formatMoney($cat['total']) }}</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-rose-500" style="width: {{ min(100, max(4, $cat['percent'])) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-4">Bu ay gider kaydı girilmedi.</p>
            @endforelse
        </div>

        <!-- Açık Bakiyesi Olan Hastalar -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Tahsilat Bekleyen Hastalar</h2>
                <p class="text-xs text-slate-500">Açık cari bakiyesi olan hastalar</p>
            </div>

            <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                @forelse ($debtorPatients as $debtor)
                    <div class="px-5 py-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/70 transition">
                        <div class="min-w-0">
                            <a href="{{ route('patients.show', $debtor) }}" class="block text-sm font-semibold text-slate-900 hover:text-sky-600 truncate">
                                {{ $debtor->full_name }}
                            </a>
                            <div class="text-xs text-slate-500 truncate">{{ $debtor->phone }}</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200/70 px-2.5 py-1 rounded-lg">
                                {{ $formatMoney($debtor->balance) }}
                            </span>
                            <button
                                type="button"
                                onclick="openQuickPaymentModal({{ $debtor->id }}, {{ number_format((float) $debtor->balance, 2, '.', '') }})"
                                title="Bu Hastadan Tahsilat Al"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition text-xs font-bold"
                            >
                                +
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-xs text-slate-400">
                        Açık bakiyesi olan hasta bulunmuyor.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Yeni Gider Ekleme Modalı -->
<div id="new-expense-modal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Klinik Gideri Ekle</h3>
            <button type="button" onclick="document.getElementById('new-expense-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
        </div>
        <form action="{{ route('finance.expenses.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Gider Başlığı / Açıklama *</label>
                <input type="text" name="title" required placeholder="Örn: Zirkonyum laboratuvar faturası" class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori *</label>
                    <select name="category" required class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:border-sky-500 outline-none bg-white">
                        @foreach (\App\Models\Expense::CATEGORIES as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tutar (₺) *</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00" class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:border-sky-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Ödeme Yöntemi *</label>
                    <select name="payment_method" required class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:border-sky-500 outline-none bg-white">
                        @foreach (\App\Models\Payment::METHODS as $method)
                            <option value="{{ $method }}">{{ $method }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tarih &amp; Saat *</label>
                    <input type="datetime-local" name="expense_date" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:border-sky-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Notlar</label>
                <textarea name="notes" rows="2" placeholder="Fatura no, tedarikçi vb." class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:border-sky-500 outline-none"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('new-expense-modal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Vazgeç</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-sm">Gideri Kaydet</button>
            </div>
        </form>
    </div>
</div>

<!-- Hızlı Tahsilat (Gelir) Ekleme Modalı -->
<div id="new-payment-modal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Hızlı Tahsilat (Gelir) Ekle</h3>
            <button type="button" onclick="document.getElementById('new-payment-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
        </div>
        <form action="{{ route('finance.payments.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Hasta Seçin *</label>
                <select id="quick-payment-patient" name="patient_id" required class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:border-sky-500 outline-none bg-white">
                    <option value="">Hasta seçiniz...</option>
                    @foreach ($allPatients as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->full_name }} ({{ $p->phone }}){{ $p->balance > 0 ? ' — Bakiye: ' . $formatMoney($p->balance) : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahsilat Tutarı (₺) *</label>
                    <input id="quick-payment-amount" type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00" class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:border-sky-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Ödeme Yöntemi *</label>
                    <select name="payment_method" required class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:border-sky-500 outline-none bg-white">
                        @foreach (\App\Models\Payment::METHODS as $method)
                            <option value="{{ $method }}">{{ $method }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tarih &amp; Saat *</label>
                <input type="datetime-local" name="payment_date" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:border-sky-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Açıklama / Not</label>
                <textarea name="notes" rows="2" placeholder="Taksit, peşinat vb." class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:border-sky-500 outline-none"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('new-payment-modal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Vazgeç</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm">Tahsilatı Kaydet</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.openQuickPaymentModal = function (patientId = '', defaultAmount = '') {
        const modal = document.getElementById('new-payment-modal');
        const patientSelect = document.getElementById('quick-payment-patient');
        const amountInput = document.getElementById('quick-payment-amount');

        if (patientSelect) patientSelect.value = patientId ? String(patientId) : '';
        if (amountInput) amountInput.value = defaultAmount ? String(defaultAmount) : '';
        modal.classList.remove('hidden');
    };

    document.querySelectorAll('.ledger-filter-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            const filter = btn.dataset.ledgerFilter;
            document.querySelectorAll('.ledger-filter-btn').forEach((other) => {
                other.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
                other.classList.add('text-slate-600');
            });
            btn.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
            btn.classList.remove('text-slate-600');

            document.querySelectorAll('#finance-ledger-body tr[data-entry-type]').forEach((row) => {
                const show = filter === 'all' || row.dataset.entryType === filter;
                row.classList.toggle('hidden', !show);
            });
        });
    });

    ['new-expense-modal', 'new-payment-modal'].forEach((id) => {
        const el = document.getElementById(id);
        el?.addEventListener('click', (e) => {
            if (e.target === el) el.classList.add('hidden');
        });
    });
</script>
@endpush
