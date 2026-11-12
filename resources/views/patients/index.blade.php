@extends('layouts.app')

@section('title', 'Kontrol Paneli | Diş Kliniği')

@section('content')
<div class="bg-slate-50 rounded-2xl space-y-6">
    <!-- Üst Başlık, Geniş Arama Çubuğu ve Yeni Hasta Ekle Butonu -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Klinik Kontrol Paneli</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                {{ now()->locale('tr')->isoFormat('D MMMM YYYY, dddd') ?? now()->format('d.m.Y') }} • Günlük randevu ve tedavi özeti
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1 lg:max-w-2xl lg:justify-end">
            <!-- Genişletilmiş Büyüteç İkonlu Canlı Arama Çubuğu -->
            <div class="relative flex-1 max-w-lg">
                <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input
                    id="dashboard-patient-search"
                    type="text"
                    autocomplete="off"
                    placeholder="Hızlı hasta ara (Ad Soyad, Telefon, TC No)..."
                    class="w-full pl-11 pr-4 py-2.5 text-sm bg-white border border-slate-200 rounded-xl shadow-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition"
                >
                <div
                    id="dashboard-search-results"
                    class="hidden absolute left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-40 divide-y divide-slate-100 max-h-72 overflow-y-auto"
                ></div>
            </div>

            <!-- Belirgin Yeni Hasta Ekle Butonu -->
            <button
                type="button"
                onclick="document.getElementById('new-patient-modal').classList.remove('hidden')"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white text-sm font-semibold rounded-xl shadow-sm transition shrink-0"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                </svg>
                <span>Yeni Hasta Ekle</span>
            </button>
        </div>
    </div>

    <!-- 1. Üst Kısım: İstatistik Kartları (KPI Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Bugünkü Randevular -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-5 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Bugünkü Randevular</p>
                <p class="text-3xl font-bold text-slate-900 mt-1.5">{{ $todayAppointments->count() }}</p>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $todayAppointments->where('status', 'pending')->count() }} bekliyor
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
            </div>
        </div>

        <!-- Toplam Hasta Sayısı -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-5 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Toplam Hasta</p>
                <p class="text-3xl font-bold text-slate-900 mt-1.5">{{ $totalPatientsCount }}</p>
                <p class="text-xs text-emerald-600 font-medium mt-1">Kayıtlı aktif hasta</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
        </div>

        <!-- Bekleyen İşlemler (Planlanan Tedaviler) -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-5 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Bekleyen İşlemler</p>
                <p class="text-3xl font-bold text-slate-900 mt-1.5">{{ $pendingTreatmentsCount }}</p>
                <p class="text-xs text-blue-600 font-medium mt-1">Planlanmış diş tedavisi</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                </svg>
            </div>
        </div>

        <!-- Bugün Tamamlanan -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-5 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Bugün Tamamlanan</p>
                <p class="text-3xl font-bold text-slate-900 mt-1.5">{{ $completedTodayCount }}</p>
                <p class="text-xs text-slate-500 mt-1">Tamamlanan seans</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- 2. Gelişmiş Düzen (Sol 2 Birim, Sağ 1 Birim) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Sol Kolon (Geniş Alan): Günün Randevuları Tablosu -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                    <h2 class="text-base font-bold text-slate-900">Günün Randevuları</h2>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700">
                    {{ $todayAppointments->count() }} Randevu
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-3">Saat</th>
                            <th class="px-6 py-3">Hasta Adı</th>
                            <th class="px-6 py-3">Yapılacak İşlem</th>
                            <th class="px-6 py-3">Durum</th>
                            <th class="px-6 py-3 text-right">Aksiyonlar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse ($todayAppointments as $appointment)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 font-semibold text-slate-900">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        {{ $appointment->appointment_date->format('H:i') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-900">{{ $appointment->patient->full_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $appointment->patient->phone }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 max-w-xs">
                                    <span class="line-clamp-1">{{ $appointment->description ?: 'Genel Muayene' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($appointment->status === 'completed')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Geldi
                                        </span>
                                    @elseif ($appointment->status === 'no_show')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Gelmedi
                                        </span>
                                    @elseif ($appointment->status === 'postponed')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-600/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span>
                                            Ertelendi
                                        </span>
                                    @elseif ($appointment->status === 'cancelled')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-600/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                            İptal Edildi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Bekliyor
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('patients.show', $appointment->patient) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg transition"
                                        >
                                            <span>Hastaya Git</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                            </svg>
                                        </a>

                                        @if (in_array($appointment->status, ['pending', 'postponed'], true))
                                            <button
                                                type="button"
                                                data-open-cancel-modal
                                                data-appointment-id="{{ $appointment->id }}"
                                                data-patient-name="{{ $appointment->patient->full_name }}"
                                                data-default-next="{{ $appointment->appointment_date->copy()->addDay()->format('Y-m-d\TH:i') }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50/70 hover:bg-rose-100 rounded-lg transition"
                                            >
                                                İptal / Ertele
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-slate-100 text-slate-400 mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-600">Bugün için planlanmış randevu bulunmuyor.</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Yeni randevuları hasta detay ekranından oluşturabilirsiniz.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sağ Kolon (Dar Alan): Hızlı Erişim & Son Hastalar -->
        <div class="lg:col-span-1 space-y-5">
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Son Hastalar</h2>
                        <p class="text-xs text-slate-500">Hızlı profil ve odontogram erişimi</p>
                    </div>
                    <button
                        type="button"
                        onclick="document.getElementById('new-patient-modal').classList.remove('hidden')"
                        title="Yeni Hasta Ekle"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Ekle</span>
                    </button>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($recentPatients as $patient)
                        <div class="px-5 py-3.5 flex items-center justify-between gap-3 hover:bg-slate-50 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ mb_strtoupper(mb_substr($patient->full_name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('patients.show', $patient) }}" class="block text-sm font-semibold text-slate-900 hover:text-sky-600 truncate">
                                        {{ $patient->full_name }}
                                    </a>
                                    <div class="text-xs text-slate-500 truncate">{{ $patient->phone }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-1 shrink-0">
                                <a
                                    href="{{ route('patients.show', $patient) }}"
                                    title="Profili Gör"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-sky-600 hover:bg-sky-50 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>

                                <button
                                    type="button"
                                    title="Hasta Bilgilerini Düzenle"
                                    data-patient="{{ json_encode([
                                        'id' => $patient->id,
                                        'full_name' => $patient->full_name,
                                        'phone' => $patient->phone,
                                        'tc_no' => $patient->tc_no,
                                        'birth_date' => $patient->birth_date?->format('Y-m-d'),
                                        'gender' => $patient->gender,
                                        'email' => $patient->email,
                                        'medical_notes' => $patient->medical_notes,
                                        'update_url' => route('patients.update', $patient),
                                    ]) }}"
                                    onclick="openEditPatientModal(JSON.parse(this.dataset.patient))"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                    </svg>
                                </button>

                                <form
                                    action="{{ route('patients.destroy', $patient) }}"
                                    method="POST"
                                    onsubmit="return confirm('{{ addslashes($patient->full_name) }} isimli hastayı ve tüm kayıtlarını silmek istediğinize emin misiniz?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        title="Hastayı Çıkar / Sil"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-slate-500">
                            Henüz hasta kaydı bulunmuyor.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Hasta Ekleme Modalı -->
<div id="new-patient-modal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Yeni Hasta Kaydı</h3>
            <button type="button" onclick="document.getElementById('new-patient-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('patients.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Ad Soyad *</label>
                    <input type="text" name="full_name" required class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Telefon *</label>
                    <input type="text" name="phone" required placeholder="05XX XXX XX XX" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">TC Kimlik No</label>
                    <input type="text" name="tc_no" maxlength="11" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Doğum Tarihi</label>
                    <input type="date" name="birth_date" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Cinsiyet</label>
                    <select name="gender" class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
                        <option value="">Belirtilmemiş</option>
                        <option value="female">Kadın</option>
                        <option value="male">Erkek</option>
                        <option value="other">Diğer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">E-posta</label>
                    <input type="email" name="email" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kronik Rahatsızlık / Alerji / Notlar</label>
                <textarea name="medical_notes" rows="3" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('new-patient-modal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl">İptal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-sm">Hastayı Kaydet</button>
            </div>
        </form>
    </div>
</div>

<!-- İptal / Ertele 2 Seçenek Modalı -->
<div id="dash-cancel-modal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Gelmeyecek / İptal İşlemi</h3>
                <p id="dash-cancel-patient" class="text-xs text-slate-500">—</p>
            </div>
            <button type="button" onclick="document.getElementById('dash-cancel-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-xs text-slate-600">Lütfen bu randevu için bir işlem seçin:</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <button type="button" id="dash-confirm-cancel-btn" class="px-4 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-800 text-white text-xs font-semibold transition">
                    1. Tamamen İptal Et
                </button>
                <button type="button" id="dash-show-postpone-btn" class="px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold transition">
                    2. Ertele (Yeni Tarih Seç)
                </button>
            </div>
            <div id="dash-postpone-box" class="hidden pt-3 border-t border-slate-100 space-y-3">
                <div>
                    <label for="dash-postpone-date" class="block text-xs font-semibold text-slate-700 mb-1">Yeni Randevu Tarih & Saati</label>
                    <input id="dash-postpone-date" type="datetime-local" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 outline-none">
                </div>
                <button type="button" id="dash-confirm-postpone-btn" class="w-full px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition">
                    Ertele & Kaydet
                </button>
            </div>
        </div>
    </div>
</div>

@include('patients.partials.edit-patient-modal')

@push('scripts')
<script>
(() => {
    const cancelModal = document.getElementById('dash-cancel-modal');
    const cancelPatientEl = document.getElementById('dash-cancel-patient');
    const postponeBox = document.getElementById('dash-postpone-box');
    const postponeDateInput = document.getElementById('dash-postpone-date');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const statusTemplate = @json(route('appointments.update-status', ['appointment' => '__ID__']));
    const rescheduleTemplate = @json(route('appointments.reschedule', ['appointment' => '__ID__']));
    let selectedApptId = null;

    document.querySelectorAll('[data-open-cancel-modal]').forEach((btn) => {
        btn.addEventListener('click', () => {
            selectedApptId = btn.dataset.appointmentId;
            cancelPatientEl.textContent = btn.dataset.patientName || '';
            postponeDateInput.value = btn.dataset.defaultNext || '';
            postponeBox.classList.add('hidden');
            cancelModal.classList.remove('hidden');
        });
    });

    document.getElementById('dash-show-postpone-btn')?.addEventListener('click', () => {
        postponeBox.classList.remove('hidden');
    });

    document.getElementById('dash-confirm-cancel-btn')?.addEventListener('click', async () => {
        if (!selectedApptId) return;
        const res = await fetch(statusTemplate.replace('__ID__', selectedApptId), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ status: 'cancelled' }),
        });
        if (res.ok) window.location.reload();
    });

    document.getElementById('dash-confirm-postpone-btn')?.addEventListener('click', async () => {
        if (!selectedApptId || !postponeDateInput.value) return;
        const res = await fetch(rescheduleTemplate.replace('__ID__', selectedApptId), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ appointment_date: postponeDateInput.value, status: 'postponed' }),
        });
        if (res.ok) window.location.reload();
    });

    const input = document.getElementById('dashboard-patient-search');
    const resultsBox = document.getElementById('dashboard-search-results');
    if (!input || !resultsBox) return;

    let timer = null;
    const searchUrl = @json(route('patients.search'));

    input.addEventListener('input', () => {
        const q = input.value.trim();
        clearTimeout(timer);

        if (q.length < 2) {
            resultsBox.classList.add('hidden');
            resultsBox.innerHTML = '';
            return;
        }

        timer = setTimeout(async () => {
            try {
                const res = await fetch(`${searchUrl}?q=${encodeURIComponent(q)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const payload = await res.json();
                const items = payload.data || [];

                if (!items.length) {
                    resultsBox.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500">Eşleşen hasta bulunamadı.</div>';
                    resultsBox.classList.remove('hidden');
                    return;
                }

                resultsBox.innerHTML = items.map((p) => `
                    <a href="${p.url}" class="flex items-center justify-between px-4 py-2.5 hover:bg-sky-50 transition">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">${escapeHtml(p.full_name)}</div>
                            <div class="text-xs text-slate-500">Tel: ${escapeHtml(p.phone)}${p.tc_no ? ` • TC: ${escapeHtml(p.tc_no)}` : ''}</div>
                        </div>
                        <span class="text-xs font-semibold text-sky-600">Profili Gör →</span>
                    </a>
                `).join('');
                resultsBox.classList.remove('hidden');
            } catch {
                resultsBox.innerHTML = '<div class="px-4 py-3 text-sm text-rose-600">Arama sırasında hata oluştu.</div>';
                resultsBox.classList.remove('hidden');
            }
        }, 200);
    });

    document.addEventListener('click', (e) => {
        if (!resultsBox.contains(e.target) && e.target !== input) {
            resultsBox.classList.add('hidden');
        }
    });

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, (m) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[m]));
    }
})();
</script>
@endpush
@endsection
