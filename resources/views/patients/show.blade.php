@extends('layouts.app')

@section('title', $patient->full_name . ' | Hasta Odontogramı')

@section('content')
@php
    $toothNames = [
        11 => 'Üst Sağ Orta Kesici', 12 => 'Üst Sağ Yan Kesici', 13 => 'Üst Sağ Kanin (Köpek)',
        14 => 'Üst Sağ 1. Küçük Azı', 15 => 'Üst Sağ 2. Küçük Azı', 16 => 'Üst Sağ 1. Büyük Azı',
        17 => 'Üst Sağ 2. Büyük Azı', 18 => 'Üst Sağ 3. Büyük Azı (20 Yaş)',
        21 => 'Üst Sol Orta Kesici', 22 => 'Üst Sol Yan Kesici', 23 => 'Üst Sol Kanin (Köpek)',
        24 => 'Üst Sol 1. Küçük Azı', 25 => 'Üst Sol 2. Küçük Azı', 26 => 'Üst Sol 1. Büyük Azı',
        27 => 'Üst Sol 2. Büyük Azı', 28 => 'Üst Sol 3. Büyük Azı (20 Yaş)',
        31 => 'Alt Sol Orta Kesici', 32 => 'Alt Sol Yan Kesici', 33 => 'Alt Sol Kanin (Köpek)',
        34 => 'Alt Sol 1. Küçük Azı', 35 => 'Alt Sol 2. Küçük Azı', 36 => 'Alt Sol 1. Büyük Azı',
        37 => 'Alt Sol 2. Büyük Azı', 38 => 'Alt Sol 3. Büyük Azı (20 Yaş)',
        41 => 'Alt Sağ Orta Kesici', 42 => 'Alt Sağ Yan Kesici', 43 => 'Alt Sağ Kanin (Köpek)',
        44 => 'Alt Sağ 1. Küçük Azı', 45 => 'Alt Sağ 2. Küçük Azı', 46 => 'Alt Sağ 1. Büyük Azı',
        47 => 'Alt Sağ 2. Büyük Azı', 48 => 'Alt Sağ 3. Büyük Azı (20 Yaş)',
    ];

    $toothShapes = [
        'upper_central' => [
            'outline' => 'M -12,-5 C -12,-10 -5,-11 0,-11 C 5,-11 12,-10 12,-5 C 13,2 8,10 0,10 C -8,10 -13,2 -12,-5 Z',
            'fissure' => 'M -6,-1 Q 0,1 6,-1',
        ],
        'upper_lateral' => [
            'outline' => 'M -9.5,-4 C -9.5,-8.5 -4,-9.5 0,-9.5 C 4,-9.5 9.5,-8.5 9.5,-4 C 10,2 6.5,9 0,9 C -6.5,9 -10,2 -9.5,-4 Z',
            'fissure' => 'M -5,-1 Q 0,1 5,-1',
        ],
        'upper_canine' => [
            'outline' => 'M -10.5,-3 C -10,-8 -4,-11 0,-11 C 4,-11 10,-8 10.5,-3 C 11,3 6,10.5 0,10.5 C -6,10.5 -11,3 -10.5,-3 Z',
            'fissure' => 'M 0,-6 L 0,5 M -5,0 L 5,0',
        ],
        'upper_premolar' => [
            'outline' => 'M -10.5,-3.5 C -10.5,-10 -4.5,-12.5 0,-12.5 C 4.5,-12.5 10.5,-10 10.5,-3.5 C 10.5,4 5.5,12.5 0,12.5 C -5.5,12.5 -10.5,4 -10.5,-3.5 Z',
            'fissure' => 'M -6,0 Q 0,1.5 6,0',
        ],
        'upper_molar_1' => [
            'outline' => 'M -15,-7 C -15,-14 -7,-16 0,-15.5 C 7,-15 15,-13 15,-6 C 15.5,3 12,14 3,15.5 C -6,16.5 -15,12 -15,-7 Z',
            'fissure' => 'M -9,-2 Q 0,1 9,-1 M 0,-9 Q -1,0 1,9',
        ],
        'upper_molar_2' => [
            'outline' => 'M -13.5,-6 C -13.5,-13 -6,-14.5 0,-14.5 C 7,-14.5 13.5,-12 13.5,-5 C 14,3 10.5,13 2,14.5 C -6,15 -13.5,11 -13.5,-6 Z',
            'fissure' => 'M -8,-1 L 8,-1 M 0,-8 L 0,8',
        ],
        'upper_molar_3' => [
            'outline' => 'M -12,-5 C -12,-11 -5,-12.5 0,-12.5 C 6,-12.5 12,-10 12,-4 C 12,4 7,12 0,12.5 C -7,12.5 -12,4 -12,-5 Z',
            'fissure' => 'M -6,0 L 6,0 M 0,-6 L 0,6',
        ],
        'lower_incisor' => [
            'outline' => 'M -7,0 C -7,-7 -3.5,-10 0,-10 C 3.5,-10 7,-7 7,0 C 7,7 3.5,10 0,10 C -3.5,10 -7,7 -7,0 Z',
            'fissure' => 'M -4,0 L 4,0',
        ],
        'lower_canine' => [
            'outline' => 'M -9,0 C -9,-7 -4,-11.5 0,-11.5 C 4,-11.5 9,-7 9,0 C 9,7 4,11.5 0,11.5 C -4,11.5 -9,7 -9,0 Z',
            'fissure' => 'M -4,0 L 4,0 M 0,-5 L 0,5',
        ],
        'lower_premolar_1' => [
            'outline' => 'M -10,0 C -10,-7 -5,-11.5 0,-11.5 C 5,-11.5 10,-7 10,0 C 10,7 5,11.5 0,11.5 C -5,11.5 -10,7 -10,0 Z',
            'fissure' => 'M -5,0 Q 0,1.5 5,0',
        ],
        'lower_premolar_2' => [
            'outline' => 'M -10.5,0 C -10.5,-7.5 -5.5,-12 0,-12 C 5.5,-12 10.5,-7.5 10.5,0 C 10.5,7.5 5.5,12 0,12 C -5.5,12 -10.5,7.5 -10.5,0 Z',
            'fissure' => 'M -5,0 Q 0,2 5,0 M 0,-4 L 0,5',
        ],
        'lower_molar_1' => [
            'outline' => 'M -16.5,-4 C -16.5,-11 -10,-13.5 0,-13.5 C 10,-13.5 16.5,-11 16.5,-4 C 17,4 15,13 5,13.5 C -5,14 -17,12 -16.5,-4 Z',
            'fissure' => 'M -10,0 L 10,0 M -3,-8 L -3,8 M 5,-7 L 5,7',
        ],
        'lower_molar_2' => [
            'outline' => 'M -15,-4 C -15,-10.5 -9,-12.5 0,-12.5 C 9,-12.5 15,-10.5 15,-4 C 15.5,4 13,12 4,12.5 C -5,13 -15.5,11 -15,-4 Z',
            'fissure' => 'M -9,0 L 9,0 M 0,-8 L 0,8',
        ],
        'lower_molar_3' => [
            'outline' => 'M -13.5,-3 C -13.5,-9.5 -7.5,-11.5 0,-11.5 C 7.5,-11.5 13.5,-9.5 13.5,-3 C 13.5,4 10,11 0,11.5 C -10,11.5 -13.5,9 -13.5,-3 Z',
            'fissure' => 'M -7,0 L 7,0 M 0,-6 L 0,6',
        ],
    ];

    // Yüklenen referans fotoğraftaki (FDI U-ark) yerleşim koordinatları
    $rightHalfLayout = [
        // Üst Sağ (Q1: 11 - 18)
        11 => ['cx' => 283, 'cy' => 76,  'angle' => -8,  'lx' => 277, 'ly' => 46,  'shape' => 'upper_central'],
        12 => ['cx' => 257, 'cy' => 84,  'angle' => -22, 'lx' => 242, 'ly' => 56,  'shape' => 'upper_lateral'],
        13 => ['cx' => 234, 'cy' => 102, 'angle' => -38, 'lx' => 205, 'ly' => 79,  'shape' => 'upper_canine'],
        14 => ['cx' => 215, 'cy' => 127, 'angle' => -56, 'lx' => 179, 'ly' => 110, 'shape' => 'upper_premolar'],
        15 => ['cx' => 200, 'cy' => 156, 'angle' => -72, 'lx' => 159, 'ly' => 146, 'shape' => 'upper_premolar'],
        16 => ['cx' => 187, 'cy' => 194, 'angle' => -83, 'lx' => 141, 'ly' => 192, 'shape' => 'upper_molar_1'],
        17 => ['cx' => 181, 'cy' => 236, 'angle' => -89, 'lx' => 135, 'ly' => 238, 'shape' => 'upper_molar_2'],
        18 => ['cx' => 182, 'cy' => 277, 'angle' => -94, 'lx' => 136, 'ly' => 281, 'shape' => 'upper_molar_3'],

        // Alt Sağ (Q4: 48 - 41)
        48 => ['cx' => 187, 'cy' => 358, 'angle' => 94,  'lx' => 141, 'ly' => 362, 'shape' => 'lower_molar_3'],
        47 => ['cx' => 192, 'cy' => 400, 'angle' => 86,  'lx' => 145, 'ly' => 404, 'shape' => 'lower_molar_2'],
        46 => ['cx' => 202, 'cy' => 444, 'angle' => 76,  'lx' => 153, 'ly' => 450, 'shape' => 'lower_molar_1'],
        45 => ['cx' => 216, 'cy' => 482, 'angle' => 63,  'lx' => 169, 'ly' => 494, 'shape' => 'lower_premolar_2'],
        44 => ['cx' => 231, 'cy' => 509, 'angle' => 48,  'lx' => 186, 'ly' => 529, 'shape' => 'lower_premolar_1'],
        43 => ['cx' => 249, 'cy' => 529, 'angle' => 32,  'lx' => 212, 'ly' => 558, 'shape' => 'lower_canine'],
        42 => ['cx' => 269, 'cy' => 542, 'angle' => 16,  'lx' => 248, 'ly' => 577, 'shape' => 'lower_incisor'],
        41 => ['cx' => 289, 'cy' => 548, 'angle' => 5,   'lx' => 282, 'ly' => 587, 'shape' => 'lower_incisor'],
    ];

    $allTeethLayout = [];
    foreach ($rightHalfLayout as $num => $cfg) {
        $allTeethLayout[$num] = array_merge($cfg, ['mirror' => false]);
        // Q1 (11-18) -> Q2 (21-28), Q4 (41-48) -> Q3 (31-38)
        $mirrorNum = $num < 20 ? $num + 10 : $num - 10;
        $allTeethLayout[$mirrorNum] = [
            'cx' => 600 - $cfg['cx'],
            'cy' => $cfg['cy'],
            'angle' => -$cfg['angle'],
            'lx' => 600 - $cfg['lx'],
            'ly' => $cfg['ly'],
            'shape' => $cfg['shape'],
            'mirror' => true,
        ];
    }
@endphp

<!-- Üst Başlık ve Geri Dönüş -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 sm:gap-4 mb-5 sm:mb-6">
    <div class="flex items-center gap-3 min-w-0">
        <a href="{{ route('patients.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition shrink-0">
            ←
        </a>
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 truncate">{{ $patient->full_name }}</h1>
            <p class="text-xs text-slate-500 truncate">
                Hasta #{{ $patient->id }}
                @if($patient->tc_no) • TC: {{ $patient->tc_no }} @endif
                • Tel: {{ $patient->phone }}
            </p>
        </div>
    </div>
    <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 text-xs">
        <span class="px-3 py-1.5 rounded-lg bg-yellow-100 text-yellow-800 font-semibold text-center">
            Sağlam: <span id="count-healthy">0</span>
        </span>
        <span class="px-3 py-1.5 rounded-lg bg-red-100 text-red-800 font-semibold text-center">
            İşlem Yapıldı: <span id="count-treated">0</span>
        </span>
        <span class="px-3 py-1.5 rounded-lg bg-blue-100 text-blue-800 font-semibold text-center">
            Planlanan: <span id="count-planned">0</span>
        </span>
        <span class="px-3 py-1.5 rounded-lg bg-slate-200 text-slate-700 font-semibold text-center">
            Eksik: <span id="count-missing">0</span>
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <!-- SOL PANEL: Hasta Bilgileri, Geçmiş İşlemler ve Randevular -->
    <div class="lg:col-span-4 space-y-5">
        <!-- Hasta Bilgileri -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between gap-2 mb-3">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">Hasta Bilgileri</h2>
                <div class="flex items-center gap-1.5">
                    <button
                        type="button"
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
                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg transition"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                        </svg>
                        <span>Düzenle</span>
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
                            title="Hastayı Sil"
                            class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition"
                        >
                            Sil
                        </button>
                    </form>
                </div>
            </div>
            <dl class="space-y-2.5 text-sm">
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500 shrink-0">Telefon</dt>
                    <dd class="font-medium text-slate-900 min-w-0 text-right break-all">{{ $patient->phone }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500 shrink-0">TC Kimlik No</dt>
                    <dd class="font-medium text-slate-900 min-w-0 text-right">{{ $patient->tc_no ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500 shrink-0">Doğum Tarihi</dt>
                    <dd class="font-medium text-slate-900 min-w-0 text-right">
                        {{ $patient->birth_date ? $patient->birth_date->format('d.m.Y') . ' (' . $patient->birth_date->age . ' yaş)' : '—' }}
                    </dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500 shrink-0">Cinsiyet</dt>
                    <dd class="font-medium text-slate-900 min-w-0 text-right">
                        {{ match($patient->gender) {
                            'male' => 'Erkek',
                            'female' => 'Kadın',
                            'other' => 'Diğer',
                            default => '—',
                        } }}
                    </dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500 shrink-0">E-posta</dt>
                    <dd class="font-medium text-slate-900 min-w-0 text-right break-all">{{ $patient->email ?: '—' }}</dd>
                </div>
            </dl>

            <div class="mt-4 pt-4 border-t border-slate-100">
                <div class="text-xs font-semibold text-rose-700 mb-1">Alerji / Kronik Hastalık / Tıbbi Notlar</div>
                <div class="text-xs text-slate-700 bg-rose-50/70 border border-rose-100 rounded-xl p-3 leading-relaxed">
                    {{ $patient->medical_notes ?: 'Kayıtlı tıbbi uyarı bulunmuyor.' }}
                </div>
            </div>
        </div>

        <!-- Geçmiş & Planlanan Diş İşlemleri -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">Diş İşlem Geçmişi</h2>
                <span class="text-xs text-slate-400">FDI Kayıtları</span>
            </div>
            <div id="tooth-history-list" class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
                @forelse ($toothHistory as $item)
                    <button
                        type="button"
                        data-tooth-trigger="{{ $item->tooth_number }}"
                        class="w-full text-left px-5 py-3 hover:bg-slate-50 transition flex items-start justify-between gap-3"
                    >
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-2.5 h-2.5 rounded-full" style="background-color: {{ $item->color }}"></span>
                                <span class="text-sm font-semibold text-slate-900">Diş #{{ $item->tooth_number }}</span>
                                <span class="text-xs text-slate-500">{{ $item->treatment_type ?: $item->status_label }}</span>
                            </div>
                            @if ($item->notes)
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $item->notes }}</p>
                            @endif
                        </div>
                        <span class="text-[11px] text-slate-400 shrink-0">
                            {{ $item->treated_at?->format('d.m.Y') ?: '—' }}
                        </span>
                    </button>
                @empty
                    <div class="px-5 py-6 text-center text-xs text-slate-400">
                        Henüz diş işlemi kaydedilmemiş. Şemadan bir dişe tıklayarak ekleyebilirsiniz.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Randevular -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">Randevular</h2>
                <span class="text-xs text-slate-400">{{ $patient->appointments->count() }} kayıt</span>
            </div>
            <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                @forelse ($patient->appointments as $appointment)
                    <div class="px-5 py-3 flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-semibold text-slate-900">
                                {{ $appointment->appointment_date->format('d.m.Y H:i') }}
                            </div>
                            <div class="text-xs text-slate-500">{{ $appointment->description ?: 'Genel Kontrol' }}</div>
                        </div>
                        <span class="text-[11px] font-medium px-2 py-0.5 rounded-full
                            @if($appointment->status === 'completed') bg-emerald-50 text-emerald-700
                            @elseif($appointment->status === 'no_show') bg-rose-50 text-rose-700
                            @elseif($appointment->status === 'postponed') bg-violet-50 text-violet-700
                            @elseif($appointment->status === 'cancelled') bg-slate-100 text-slate-700
                            @else bg-amber-50 text-amber-700 @endif">
                            {{ match($appointment->status) {
                                'completed' => 'Geldi',
                                'no_show' => 'Gelmedi',
                                'postponed' => 'Ertelendi',
                                'cancelled' => 'İptal',
                                default => 'Bekliyor',
                            } }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-6 text-center text-xs text-slate-400">
                        Kayıtlı randevu bulunmuyor.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- SAĞ / MERKEZ ALAN: İnteraktif SVG Odontogram -->
    <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-sm p-3.5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">İnteraktif Diş Şeması (FDI Odontogram)</h2>
                <p class="text-xs text-slate-500">Durumunu güncellemek istediğiniz dişin üzerine tıklayın.</p>
            </div>

            <!-- Renk Kodları Göstergesi -->
            <div class="flex flex-wrap items-center gap-3 text-xs font-medium">
                @foreach ($statusMeta as $key => $meta)
                    <div class="inline-flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded-sm border border-slate-700/30" style="background-color: {{ $meta['color'] }}"></span>
                        <span class="text-slate-700">{{ $meta['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- SVG Odontogram -->
        <div class="mt-3 sm:mt-4 flex justify-center">
            <svg
                id="odontogram-svg"
                viewBox="0 0 600 620"
                style="touch-action: manipulation;"
                class="w-full max-w-[560px] h-auto select-none"
                role="img"
                aria-label="FDI 11-48 İnteraktif Diş Şeması"
            >
                <!-- Kadran Etiketleri -->
                <text x="64" y="34" class="fill-slate-400 text-[11px] font-semibold tracking-wide">ÜST SAĞ (11-18)</text>
                <text x="536" y="34" text-anchor="end" class="fill-slate-400 text-[11px] font-semibold tracking-wide">ÜST SOL (21-28)</text>
                <text x="64" y="604" class="fill-slate-400 text-[11px] font-semibold tracking-wide">ALT SAĞ (41-48)</text>
                <text x="536" y="604" text-anchor="end" class="fill-slate-400 text-[11px] font-semibold tracking-wide">ALT SOL (31-38)</text>

                <!-- Referans Fotoğraftaki Orta Dikey ve Yatay Eksen Çizgileri -->
                <line x1="300" y1="18" x2="300" y2="604" stroke="#334155" stroke-width="1.2" />
                <line x1="74" y1="316" x2="526" y2="316" stroke="#334155" stroke-width="1.2" />

                <!-- 32 Diş (FDI 11-48 Ark Dizilimi) -->
                @foreach ($allTeethLayout as $number => $cfg)
                    @php
                        $toothData = $odontogram[$number];
                        $shape = $toothShapes[$cfg['shape']];
                        $transform = "translate({$cfg['cx']}, {$cfg['cy']}) rotate({$cfg['angle']})" . ($cfg['mirror'] ? ' scale(-1, 1)' : '');
                        $isMissing = $toothData['status'] === 'missing';
                    @endphp

                    <g
                        id="tooth-{{ $number }}"
                        class="tooth-node cursor-pointer group"
                        data-tooth-number="{{ $number }}"
                        data-status="{{ $toothData['status'] }}"
                        role="button"
                        tabindex="0"
                        aria-label="Diş {{ $number }} - {{ $toothNames[$number] }}"
                    >
                        <g transform="{{ $transform }}">
                            <!-- Diş Taç (Occlusal) Yüzeyi -->
                            <path
                                d="{{ $shape['outline'] }}"
                                fill="{{ $toothData['color'] }}"
                                stroke="#1E293B"
                                stroke-width="2.1"
                                stroke-linejoin="round"
                                class="tooth-crown transition-all duration-150 group-hover:brightness-95"
                            />
                            <!-- Anatomik Oklüzal Fissür Çizgisi -->
                            <path
                                d="{{ $shape['fissure'] }}"
                                fill="none"
                                stroke="rgba(15, 23, 42, 0.28)"
                                stroke-width="1"
                                stroke-linecap="round"
                                pointer-events="none"
                            />
                            <!-- Çekilmiş/Eksik Diş İşareti (X) -->
                            <path
                                d="M -5,-5 L 5,5 M 5,-5 L -5,5"
                                fill="none"
                                stroke="#F8FAFC"
                                stroke-width="2"
                                stroke-linecap="round"
                                pointer-events="none"
                                class="tooth-missing-mark {{ $isMissing ? '' : 'hidden' }}"
                            />
                        </g>

                        <!-- Dış FDI Diş Numarası -->
                        <text
                            x="{{ $cfg['lx'] }}"
                            y="{{ $cfg['ly'] }}"
                            text-anchor="middle"
                            dominant-baseline="central"
                            class="tooth-label fill-slate-700 text-[12px] font-semibold group-hover:fill-sky-600 transition-colors"
                        >{{ $number }}</text>
                    </g>
                @endforeach
            </svg>
        </div>
    </div>

    <!-- FİNANS VE CARİ DURUM PANELİ -->
    <div class="lg:col-span-12 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Finans & Hasta Cari Durumu</h2>
                <p class="text-xs text-slate-500">Tedavi maliyetleri, tahsilatlar ve anlık bakiye özeti</p>
            </div>
            <button
                type="button"
                id="open-payment-modal-btn"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl shadow-sm transition shrink-0"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>
                <span>Tahsilat Al</span>
            </button>
        </div>

        <!-- Bakiye Özeti Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-orange-200/80 bg-orange-50/70 p-4">
                <div class="text-xs font-semibold uppercase tracking-wider text-orange-700">Toplam Tedavi Tutarı</div>
                <div id="finance-total-debt" class="text-2xl font-bold text-orange-700 mt-1.5">
                    {{ number_format($financeSummary['total_debt'], 2, ',', '.') }} ₺
                </div>
                <div class="text-xs text-orange-600/80 mt-1">Diş işlemlerinden oluşan borç</div>
            </div>

            <div class="rounded-xl border border-emerald-200/80 bg-emerald-50/70 p-4">
                <div class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Yapılan Ödemeler</div>
                <div id="finance-total-paid" class="text-2xl font-bold text-emerald-700 mt-1.5">
                    {{ number_format($financeSummary['total_paid'], 2, ',', '.') }} ₺
                </div>
                <div class="text-xs text-emerald-600/80 mt-1">Toplam tahsil edilen tutar</div>
            </div>

            @php
                $hasDebt = $financeSummary['balance'] > 0;
            @endphp
            <div
                id="finance-balance-card"
                class="rounded-xl border p-4 transition-colors {{ $hasDebt ? 'border-rose-200 bg-rose-50/80' : 'border-emerald-200 bg-emerald-50/60' }}"
            >
                <div id="finance-balance-label" class="text-xs font-semibold uppercase tracking-wider {{ $hasDebt ? 'text-rose-700' : 'text-emerald-700' }}">
                    Kalan Bakiye
                </div>
                <div id="finance-balance-amount" class="text-2xl font-bold mt-1.5 {{ $hasDebt ? 'text-rose-700' : 'text-emerald-700' }}">
                    {{ number_format($financeSummary['balance'], 2, ',', '.') }} ₺
                </div>
                <div id="finance-balance-hint" class="text-xs mt-1 {{ $hasDebt ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ $hasDebt ? 'Tahsil edilmesi gereken bakiye' : 'Hastanın açık borcu bulunmuyor' }}
                </div>
            </div>
        </div>

        <!-- Finansal Hareketler (Cari Ekstre) -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800">Finansal Hareketler (Cari Ekstre)</h3>
                <span id="finance-ledger-count" class="text-xs font-medium text-slate-500">{{ count($ledger) }} Hareket</span>
            </div>

            <!-- Mobil Kart Listesi (< md) -->
            <div id="finance-ledger-mobile" class="md:hidden divide-y divide-slate-100">
                @forelse ($ledger as $row)
                    @php $isPayment = $row['type'] === 'payment'; @endphp
                    <div class="p-4 space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold shrink-0 {{ $isPayment ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $row['type_label'] }}
                                </span>
                                <span class="text-xs text-slate-500 truncate">{{ $row['date_formatted'] }}</span>
                            </div>
                            <span class="text-sm font-bold shrink-0 {{ $isPayment ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $isPayment ? '+' : '-' }}{{ number_format($row['amount'], 2, ',', '.') }} ₺
                            </span>
                        </div>
                        <div class="text-sm font-medium text-slate-900">{{ $row['title'] }}</div>
                        @if (!empty($row['notes']))
                            <div class="text-xs text-slate-500">{{ $row['notes'] }}</div>
                        @endif
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-xs text-slate-400">
                        Henüz finansal hareket kaydı bulunmuyor.
                    </div>
                @endforelse
            </div>

            <!-- Masaüstü Tablo (md+) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-semibold uppercase tracking-wider text-slate-500 bg-white">
                            <th class="px-5 py-3">Tarih</th>
                            <th class="px-5 py-3">Tür</th>
                            <th class="px-5 py-3">İşlem / Açıklama</th>
                            <th class="px-5 py-3">Not</th>
                            <th class="px-5 py-3 text-right">Tutar</th>
                        </tr>
                    </thead>
                    <tbody id="finance-ledger-body" class="divide-y divide-slate-100 text-sm">
                        @forelse ($ledger as $row)
                            @php $isPayment = $row['type'] === 'payment'; @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-3 whitespace-nowrap text-xs text-slate-500">{{ $row['date_formatted'] }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $isPayment ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $row['type_label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 font-medium text-slate-900">{{ $row['title'] }}</td>
                                <td class="px-5 py-3 text-xs text-slate-500 max-w-xs truncate">{{ $row['notes'] ?: '—' }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-right font-bold {{ $isPayment ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $isPayment ? '+' : '-' }}{{ number_format($row['amount'], 2, ',', '.') }} ₺
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-xs text-slate-400">
                                    Henüz finansal hareket kaydı bulunmuyor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Tahsilat Al Modalı -->
<div id="payment-modal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white rounded-t-2xl sm:rounded-2xl max-w-md w-full max-h-[92dvh] flex flex-col shadow-xl border border-slate-200 overflow-hidden safe-bottom">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="min-w-0 pr-2">
                <h3 class="text-base sm:text-lg font-bold text-slate-900">Tahsilat Al</h3>
                <p class="text-xs text-slate-500 truncate">{{ $patient->full_name }} • Kalan Bakiye: <span id="modal-current-balance" class="font-semibold text-slate-800">{{ number_format($financeSummary['balance'], 2, ',', '.') }} ₺</span></p>
            </div>
            <button type="button" id="close-payment-modal" class="w-9 h-9 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 text-2xl leading-none shrink-0">&times;</button>
        </div>

        <form id="payment-form" class="p-5 sm:p-6 space-y-4 overflow-y-auto">
            <div>
                <label for="payment-amount" class="block text-xs font-semibold text-slate-700 mb-1">Ödenecek Tutar (₺) *</label>
                <input
                    id="payment-amount"
                    name="amount"
                    type="number"
                    step="0.01"
                    min="0.01"
                    required
                    placeholder="Örn: 1500.00"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none"
                >
            </div>

            <div>
                <label for="payment-method" class="block text-xs font-semibold text-slate-700 mb-1">Ödeme Yöntemi *</label>
                <select
                    id="payment-method"
                    name="payment_method"
                    required
                    class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none"
                >
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method }}">{{ $method }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="payment-date" class="block text-xs font-semibold text-slate-700 mb-1">Ödeme Tarihi *</label>
                <input
                    id="payment-date"
                    name="payment_date"
                    type="datetime-local"
                    required
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none"
                >
            </div>

            <div>
                <label for="payment-notes" class="block text-xs font-semibold text-slate-700 mb-1">Not</label>
                <textarea
                    id="payment-notes"
                    name="notes"
                    rows="2"
                    placeholder="Örn: Peşinat, taksit 1..."
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none"
                ></textarea>
            </div>

            <div id="payment-form-error" class="hidden text-xs text-rose-600 bg-rose-50 border border-rose-200 rounded-xl px-3 py-2"></div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" id="cancel-payment-modal" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl">
                    Vazgeç
                </button>
                <button
                    type="submit"
                    id="save-payment-btn"
                    class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition disabled:opacity-60"
                >
                    Tahsilatı Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Diş Düzenleme Modalı -->
<div id="tooth-modal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white rounded-t-2xl sm:rounded-2xl max-w-md w-full max-h-[92dvh] flex flex-col shadow-xl border border-slate-200 overflow-hidden safe-bottom">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="min-w-0 pr-2">
                <h3 id="modal-tooth-title" class="text-base sm:text-lg font-bold text-slate-900">Diş #16 İşlem Paneli</h3>
                <p id="modal-tooth-subtitle" class="text-xs text-slate-500 truncate">Üst Sağ 1. Büyük Azı</p>
            </div>
            <button type="button" id="close-tooth-modal" class="w-9 h-9 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 text-2xl leading-none shrink-0">&times;</button>
        </div>

        <form id="tooth-form" class="p-5 sm:p-6 space-y-4 overflow-y-auto">
            <input type="hidden" id="form-tooth-number" name="tooth_number">

            <!-- Durum Seçimi -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Diş Durumu</label>
                <div class="grid grid-cols-2 gap-2.5">
                    @foreach ($statusMeta as $statusKey => $meta)
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="{{ $statusKey }}" class="peer sr-only">
                            <div class="flex items-center gap-2 px-3 py-2.5 rounded-xl border border-slate-200 peer-checked:border-slate-900 peer-checked:bg-slate-50 hover:bg-slate-50 transition">
                                <span class="w-3.5 h-3.5 rounded-full shrink-0 border border-slate-700/30" style="background-color: {{ $meta['color'] }}"></span>
                                <span class="text-xs font-semibold text-slate-800">
                                    {{ match($statusKey) {
                                        'planned' => 'Planlandı',
                                        'missing' => 'Eksik',
                                        default => $meta['label'],
                                    } }}
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- İşlem Tipi (Select - Klinik Fiyat Listesiyle) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="form-treatment-type" class="block text-xs font-semibold text-slate-700 mb-1">İşlem Tipi</label>
                    <select
                        id="form-treatment-type"
                        name="treatment_type"
                        class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none"
                    >
                        <option value="" data-default-price="">İşlem Seçiniz...</option>
                        @foreach ($treatmentPrices as $tp)
                            <option value="{{ $tp->name }}" data-id="{{ $tp->id }}" data-default-price="{{ $tp->default_price }}">
                                {{ $tp->name }} ({{ number_format($tp->default_price, 0, ',', '.') }} ₺)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="form-price" class="block text-xs font-semibold text-slate-700 mb-1">İşlem Ücreti (₺)</label>
                    <input
                        id="form-price"
                        name="price"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none"
                    >
                </div>
            </div>

            <!-- Hızlı İşlem Tipi Ekleme ve Fiyat Güncelleme -->
            <div class="rounded-xl bg-slate-50 border border-slate-200/80 p-3 space-y-2.5">
                <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            id="toggle-quick-add-treatment"
                            class="inline-flex items-center gap-1 px-2.5 py-1 font-semibold text-sky-700 bg-sky-100/80 hover:bg-sky-100 rounded-lg transition"
                        >
                            + Yeni İşlem Ekle
                        </button>
                        <button
                            type="button"
                            id="toggle-quick-edit-treatment"
                            class="hidden inline-flex items-center gap-1 px-2.5 py-1 font-semibold text-amber-700 bg-amber-100/80 hover:bg-amber-100 rounded-lg transition"
                        >
                            Seçili Fiyatı Güncelle
                        </button>
                    </div>
                    <a
                        href="{{ route('finance.index') }}#fiyat-listesi"
                        class="font-semibold text-slate-500 hover:text-sky-600 transition"
                    >
                        Fiyat Listesi →
                    </a>
                </div>

                <div id="quick-treatment-feedback" class="hidden text-xs font-medium px-2.5 py-1.5 rounded-lg"></div>

                <!-- Yeni İşlem Ekleme Kutusu -->
                <div id="quick-add-treatment-box" class="hidden grid grid-cols-1 sm:grid-cols-12 gap-2 pt-1">
                    <input
                        id="quick-add-treatment-name"
                        type="text"
                        placeholder="İşlem adı (örn: Zirkonyum)"
                        class="sm:col-span-6 px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg focus:border-sky-500 outline-none"
                    >
                    <input
                        id="quick-add-treatment-price"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="Fiyat (₺)"
                        class="sm:col-span-4 px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg focus:border-sky-500 outline-none"
                    >
                    <button
                        type="button"
                        id="quick-add-treatment-submit"
                        class="sm:col-span-2 px-2.5 py-1.5 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition"
                    >
                        Ekle
                    </button>
                </div>

                <!-- Seçili İşlemin Fiyatını Güncelleme Kutusu -->
                <div id="quick-edit-treatment-box" class="hidden grid grid-cols-1 sm:grid-cols-12 gap-2 pt-1">
                    <input
                        id="quick-edit-treatment-name"
                        type="text"
                        placeholder="İşlem adı"
                        class="sm:col-span-6 px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg focus:border-amber-500 outline-none"
                    >
                    <input
                        id="quick-edit-treatment-price"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="Yeni Fiyat (₺)"
                        class="sm:col-span-4 px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg focus:border-amber-500 outline-none"
                    >
                    <button
                        type="button"
                        id="quick-edit-treatment-submit"
                        class="sm:col-span-2 px-2.5 py-1.5 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition"
                    >
                        Kaydet
                    </button>
                </div>
            </div>

            <!-- İşlem Tarihi -->
            <div>
                <label for="form-treated-at" class="block text-xs font-semibold text-slate-700 mb-1">İşlem Tarihi</label>
                <input
                    id="form-treated-at"
                    name="treated_at"
                    type="date"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none"
                >
            </div>

            <!-- Doktor Notu -->
            <div>
                <label for="form-notes" class="block text-xs font-semibold text-slate-700 mb-1">Doktor Notu</label>
                <textarea
                    id="form-notes"
                    name="notes"
                    rows="3"
                    placeholder="Klinik gözlemler, seans notları..."
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none"
                ></textarea>
            </div>

            <div id="tooth-form-error" class="hidden text-xs text-rose-600 bg-rose-50 border border-rose-200 rounded-xl px-3 py-2"></div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" id="cancel-tooth-modal" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl">
                    Vazgeç
                </button>
                <button
                    type="submit"
                    id="save-tooth-btn"
                    class="px-5 py-2 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-sm transition disabled:opacity-60"
                >
                    Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

@include('patients.partials.edit-patient-modal')

@push('scripts')
<script>
(() => {
    const odontogramState = @json($odontogram);
    const toothNames = @json($toothNames);
    const upsertUrl = @json(route('patients.teeth.upsert', $patient));
    const paymentStoreUrl = @json(route('patients.payments.store', $patient));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const modal = document.getElementById('tooth-modal');
    const form = document.getElementById('tooth-form');
    const titleEl = document.getElementById('modal-tooth-title');
    const subtitleEl = document.getElementById('modal-tooth-subtitle');
    const toothInput = document.getElementById('form-tooth-number');
    const treatmentInput = document.getElementById('form-treatment-type');
    const priceInput = document.getElementById('form-price');
    const treatedAtInput = document.getElementById('form-treated-at');
    const notesInput = document.getElementById('form-notes');
    const errorBox = document.getElementById('tooth-form-error');
    const saveBtn = document.getElementById('save-tooth-btn');
    const historyList = document.getElementById('tooth-history-list');

    const paymentModal = document.getElementById('payment-modal');
    const paymentForm = document.getElementById('payment-form');
    const paymentAmountInput = document.getElementById('payment-amount');
    const paymentMethodInput = document.getElementById('payment-method');
    const paymentDateInput = document.getElementById('payment-date');
    const paymentNotesInput = document.getElementById('payment-notes');
    const paymentErrorBox = document.getElementById('payment-form-error');
    const savePaymentBtn = document.getElementById('save-payment-btn');

    let currentFinance = @json($financeSummary);

    const formatCurrency = (val) => {
        return Number(val || 0).toLocaleString('tr-TR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }) + ' ₺';
    };

    const updateFinanceUI = (finance, ledger) => {
        if (!finance) return;
        currentFinance = finance;

        document.getElementById('finance-total-debt').textContent = formatCurrency(finance.total_debt);
        document.getElementById('finance-total-paid').textContent = formatCurrency(finance.total_paid);
        document.getElementById('finance-balance-amount').textContent = formatCurrency(finance.balance);
        document.getElementById('modal-current-balance').textContent = formatCurrency(finance.balance);

        const hasDebt = Number(finance.balance) > 0;
        const card = document.getElementById('finance-balance-card');
        const label = document.getElementById('finance-balance-label');
        const amountEl = document.getElementById('finance-balance-amount');
        const hintEl = document.getElementById('finance-balance-hint');

        card.className = `rounded-xl border p-4 transition-colors ${hasDebt ? 'border-rose-200 bg-rose-50/80' : 'border-emerald-200 bg-emerald-50/60'}`;
        label.className = `text-xs font-semibold uppercase tracking-wider ${hasDebt ? 'text-rose-700' : 'text-emerald-700'}`;
        amountEl.className = `text-2xl font-bold mt-1.5 ${hasDebt ? 'text-rose-700' : 'text-emerald-700'}`;
        hintEl.className = `text-xs mt-1 ${hasDebt ? 'text-rose-600' : 'text-emerald-600'}`;
        hintEl.textContent = hasDebt ? 'Tahsil edilmesi gereken bakiye' : 'Hastanın açık borcu bulunmuyor';

        if (!Array.isArray(ledger)) return;
        document.getElementById('finance-ledger-count').textContent = `${ledger.length} Hareket`;
        const tbody = document.getElementById('finance-ledger-body');
        const mobileList = document.getElementById('finance-ledger-mobile');

        if (!ledger.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-xs text-slate-400">
                        Henüz finansal hareket kaydı bulunmuyor.
                    </td>
                </tr>`;
            if (mobileList) {
                mobileList.innerHTML = `<div class="px-4 py-8 text-center text-xs text-slate-400">Henüz finansal hareket kaydı bulunmuyor.</div>`;
            }
            return;
        }

        tbody.innerHTML = ledger.map((row) => {
            const isPayment = row.type === 'payment';
            return `
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-5 py-3 whitespace-nowrap text-xs text-slate-500">${escapeHtml(row.date_formatted)}</td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${isPayment ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'}">
                            ${escapeHtml(row.type_label)}
                        </span>
                    </td>
                    <td class="px-5 py-3 font-medium text-slate-900">${escapeHtml(row.title)}</td>
                    <td class="px-5 py-3 text-xs text-slate-500 max-w-xs truncate">${escapeHtml(row.notes || '—')}</td>
                    <td class="px-5 py-3 whitespace-nowrap text-right font-bold ${isPayment ? 'text-emerald-600' : 'text-rose-600'}">
                        ${isPayment ? '+' : '-'}${formatCurrency(row.amount)}
                    </td>
                </tr>`;
        }).join('');

        if (mobileList) {
            mobileList.innerHTML = ledger.map((row) => {
                const isPayment = row.type === 'payment';
                return `
                    <div class="p-4 space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold shrink-0 ${isPayment ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'}">
                                    ${escapeHtml(row.type_label)}
                                </span>
                                <span class="text-xs text-slate-500 truncate">${escapeHtml(row.date_formatted)}</span>
                            </div>
                            <span class="text-sm font-bold shrink-0 ${isPayment ? 'text-emerald-600' : 'text-rose-600'}">
                                ${isPayment ? '+' : '-'}${formatCurrency(row.amount)}
                            </span>
                        </div>
                        <div class="text-sm font-medium text-slate-900">${escapeHtml(row.title)}</div>
                        ${row.notes ? `<div class="text-xs text-slate-500">${escapeHtml(row.notes)}</div>` : ''}
                    </div>`;
            }).join('');
        }
    };

    const updateCounters = () => {
        const counts = { healthy: 0, treated: 0, planned: 0, missing: 0 };
        Object.values(odontogramState).forEach((t) => {
            if (counts[t.status] !== undefined) counts[t.status]++;
        });
        document.getElementById('count-healthy').textContent = counts.healthy;
        document.getElementById('count-treated').textContent = counts.treated;
        document.getElementById('count-planned').textContent = counts.planned;
        document.getElementById('count-missing').textContent = counts.missing;
    };

    const renderHistoryList = () => {
        const items = Object.values(odontogramState)
            .filter((t) => t.status !== 'healthy' || t.treatment_type || t.notes)
            .sort((a, b) => (b.treated_at || '').localeCompare(a.treated_at || ''));

        if (!items.length) {
            historyList.innerHTML = `
                <div class="px-5 py-6 text-center text-xs text-slate-400">
                    Henüz diş işlemi kaydedilmemiş. Şemadan bir dişe tıklayarak ekleyebilirsiniz.
                </div>`;
            return;
        }

        historyList.innerHTML = items.map((item) => `
            <button
                type="button"
                data-tooth-trigger="${item.tooth_number}"
                class="w-full text-left px-5 py-3 hover:bg-slate-50 transition flex items-start justify-between gap-3"
            >
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background-color: ${item.color}"></span>
                        <span class="text-sm font-semibold text-slate-900">Diş #${item.tooth_number}</span>
                        <span class="text-xs text-slate-500">${escapeHtml(item.treatment_type || item.status_label)}</span>
                    </div>
                    ${item.notes ? `<p class="text-xs text-slate-500 mt-1 line-clamp-2">${escapeHtml(item.notes)}</p>` : ''}
                </div>
                <span class="text-[11px] text-slate-400 shrink-0">${escapeHtml(item.treated_at_formatted || '—')}</span>
            </button>
        `).join('');
    };

    const openToothModal = (toothNumber) => {
        const data = odontogramState[toothNumber];
        if (!data) return;

        titleEl.textContent = `Diş #${toothNumber} İşlem Paneli`;
        subtitleEl.textContent = toothNames[toothNumber] || '';
        toothInput.value = toothNumber;

        const savedTreatment = data.treatment_type || '';
        const hasOption = [...treatmentInput.options].some((opt) => opt.value === savedTreatment);
        if (savedTreatment && !hasOption) {
            const customOpt = new Option(savedTreatment, savedTreatment, true, true);
            customOpt.dataset.dynamic = '1';
            treatmentInput.add(customOpt);
        }
        treatmentInput.value = savedTreatment;
        priceInput.value = data.price !== null && data.price !== undefined ? data.price : '';

        treatedAtInput.value = data.treated_at || '';
        notesInput.value = data.notes || '';
        errorBox.classList.add('hidden');

        const radio = form.querySelector(`input[name="status"][value="${data.status}"]`);
        if (radio) radio.checked = true;

        syncCatalogButtons();
        document.getElementById('quick-add-treatment-box').classList.add('hidden');
        document.getElementById('quick-edit-treatment-box').classList.add('hidden');
        document.getElementById('quick-treatment-feedback').classList.add('hidden');

        modal.classList.remove('hidden');
    };

    const closeToothModal = () => {
        modal.classList.add('hidden');
    };

    const applyToothUpdateToSvg = (tooth) => {
        const node = document.getElementById(`tooth-${tooth.tooth_number}`);
        if (!node) return;

        node.dataset.status = tooth.status;
        const crown = node.querySelector('.tooth-crown');
        const missingMark = node.querySelector('.tooth-missing-mark');

        if (crown) {
            crown.setAttribute('fill', tooth.color);
        }
        if (missingMark) {
            missingMark.classList.toggle('hidden', tooth.status !== 'missing');
        }
    };

    // SVG üzerindeki dişlere tıklama ve klavye erişilebilirliği
    document.querySelectorAll('.tooth-node').forEach((node) => {
        node.addEventListener('click', () => openToothModal(Number(node.dataset.toothNumber)));
        node.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openToothModal(Number(node.dataset.toothNumber));
            }
        });
    });

    // Sol işlem geçmişi listesinden dişe tıklama
    historyList.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-tooth-trigger]');
        if (btn) openToothModal(Number(btn.dataset.toothTrigger));
    });

    document.getElementById('close-tooth-modal').addEventListener('click', closeToothModal);
    document.getElementById('cancel-tooth-modal').addEventListener('click', closeToothModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeToothModal();
    });

    const toggleQuickAddBtn = document.getElementById('toggle-quick-add-treatment');
    const toggleQuickEditBtn = document.getElementById('toggle-quick-edit-treatment');
    const quickAddBox = document.getElementById('quick-add-treatment-box');
    const quickEditBox = document.getElementById('quick-edit-treatment-box');
    const quickFeedback = document.getElementById('quick-treatment-feedback');
    const quickAddNameInput = document.getElementById('quick-add-treatment-name');
    const quickAddPriceInput = document.getElementById('quick-add-treatment-price');
    const quickEditNameInput = document.getElementById('quick-edit-treatment-name');
    const quickEditPriceInput = document.getElementById('quick-edit-treatment-price');
    const storeTreatmentPriceUrl = @json(route('finance.treatment-prices.store'));
    const updateTreatmentPriceBaseUrl = @json(url('/finance/treatment-prices'));

    const showQuickFeedback = (text, isError = false) => {
        quickFeedback.textContent = text;
        quickFeedback.className = `text-xs font-medium px-2.5 py-1.5 rounded-lg ${
            isError ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'
        }`;
        quickFeedback.classList.remove('hidden');
    };

    const syncCatalogButtons = () => {
        const selectedOpt = treatmentInput.options[treatmentInput.selectedIndex];
        const tpId = selectedOpt?.dataset?.id || '';
        toggleQuickEditBtn.classList.toggle('hidden', !tpId);
        if (!tpId) {
            quickEditBox.classList.add('hidden');
        } else if (!quickEditBox.classList.contains('hidden')) {
            quickEditNameInput.value = selectedOpt.value;
            quickEditPriceInput.value = Number(selectedOpt.dataset.defaultPrice || 0).toFixed(2);
        }
    };

    toggleQuickAddBtn.addEventListener('click', () => {
        quickEditBox.classList.add('hidden');
        quickAddBox.classList.toggle('hidden');
        quickFeedback.classList.add('hidden');
        if (!quickAddBox.classList.contains('hidden')) {
            quickAddNameInput.focus();
        }
    });

    toggleQuickEditBtn.addEventListener('click', () => {
        const selectedOpt = treatmentInput.options[treatmentInput.selectedIndex];
        if (!selectedOpt?.dataset?.id) return;
        quickAddBox.classList.add('hidden');
        quickEditBox.classList.toggle('hidden');
        quickFeedback.classList.add('hidden');
        if (!quickEditBox.classList.contains('hidden')) {
            quickEditNameInput.value = selectedOpt.value;
            quickEditPriceInput.value = Number(selectedOpt.dataset.defaultPrice || 0).toFixed(2);
            quickEditPriceInput.focus();
        }
    });

    document.getElementById('quick-add-treatment-submit').addEventListener('click', async () => {
        const name = quickAddNameInput.value.trim();
        const priceVal = quickAddPriceInput.value;
        if (!name || priceVal === '') {
            showQuickFeedback('Lütfen işlem adı ve fiyat girin.', true);
            return;
        }

        try {
            const response = await fetch(storeTreatmentPriceUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ name, default_price: Number(priceVal) }),
            });
            const result = await response.json();
            if (!response.ok) {
                const msg = result.errors ? Object.values(result.errors).flat().join(' ') : (result.message || 'Eklenemedi.');
                showQuickFeedback(msg, true);
                return;
            }

            const tp = result.treatment_price;
            const formattedPrice = Number(tp.default_price).toLocaleString('tr-TR', { maximumFractionDigits: 0 });
            const opt = new Option(`${tp.name} (${formattedPrice} ₺)`, tp.name, true, true);
            opt.dataset.id = tp.id;
            opt.dataset.defaultPrice = Number(tp.default_price).toFixed(2);
            treatmentInput.add(opt);
            treatmentInput.value = tp.name;
            priceInput.value = Number(tp.default_price).toFixed(2);

            quickAddNameInput.value = '';
            quickAddPriceInput.value = '';
            quickAddBox.classList.add('hidden');
            syncCatalogButtons();
            showQuickFeedback(result.message || 'İşlem fiyat listesine eklendi.');
        } catch (err) {
            showQuickFeedback('Sunucu bağlantısı sırasında hata oluştu.', true);
        }
    });

    document.getElementById('quick-edit-treatment-submit').addEventListener('click', async () => {
        const selectedOpt = treatmentInput.options[treatmentInput.selectedIndex];
        const tpId = selectedOpt?.dataset?.id;
        const name = quickEditNameInput.value.trim();
        const priceVal = quickEditPriceInput.value;
        if (!tpId || !name || priceVal === '') {
            showQuickFeedback('Lütfen geçerli işlem adı ve fiyat girin.', true);
            return;
        }

        try {
            const response = await fetch(`${updateTreatmentPriceBaseUrl}/${tpId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ name, default_price: Number(priceVal) }),
            });
            const result = await response.json();
            if (!response.ok) {
                const msg = result.errors ? Object.values(result.errors).flat().join(' ') : (result.message || 'Güncellenemedi.');
                showQuickFeedback(msg, true);
                return;
            }

            const tp = result.treatment_price;
            const formattedPrice = Number(tp.default_price).toLocaleString('tr-TR', { maximumFractionDigits: 0 });
            selectedOpt.value = tp.name;
            selectedOpt.textContent = `${tp.name} (${formattedPrice} ₺)`;
            selectedOpt.dataset.defaultPrice = Number(tp.default_price).toFixed(2);
            priceInput.value = Number(tp.default_price).toFixed(2);

            quickEditBox.classList.add('hidden');
            showQuickFeedback(result.message || 'Fiyat güncellendi.');
        } catch (err) {
            showQuickFeedback('Sunucu bağlantısı sırasında hata oluştu.', true);
        }
    });

    // İşlem tipi seçildiğinde varsayılan ücreti otomatik doldur
    treatmentInput.addEventListener('change', () => {
        const selectedOpt = treatmentInput.options[treatmentInput.selectedIndex];
        const defaultPrice = selectedOpt?.dataset?.defaultPrice;
        if (defaultPrice !== undefined && defaultPrice !== '') {
            priceInput.value = Number(defaultPrice).toFixed(2);
        }
        syncCatalogButtons();
    });

    // AJAX Kaydetme (Sayfa yenilenmeden SVG rengini ve Cari paneli anında günceller)
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        errorBox.classList.add('hidden');
        saveBtn.disabled = true;
        saveBtn.textContent = 'Kaydediliyor...';

        const selectedStatus = form.querySelector('input[name="status"]:checked')?.value || 'healthy';
        const payload = {
            tooth_number: Number(toothInput.value),
            status: selectedStatus,
            treatment_type: treatmentInput.value.trim() || null,
            price: priceInput.value !== '' ? Number(priceInput.value) : null,
            treated_at: treatedAtInput.value || null,
            notes: notesInput.value.trim() || null,
        };

        try {
            const response = await fetch(upsertUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Kayıt sırasında bir hata oluştu.');
            }

            odontogramState[result.tooth.tooth_number] = result.tooth;
            applyToothUpdateToSvg(result.tooth);
            updateCounters();
            renderHistoryList();
            updateFinanceUI(result.finance, result.ledger);
            closeToothModal();
        } catch (err) {
            errorBox.textContent = err.message || 'Sunucu ile iletişim kurulamadı.';
            errorBox.classList.remove('hidden');
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Kaydet';
        }
    });

    // Tahsilat Al Modalı İşlemleri
    const openPaymentModal = () => {
        const now = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        paymentDateInput.value = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
        paymentAmountInput.value = Number(currentFinance.balance) > 0 ? Number(currentFinance.balance).toFixed(2) : '';
        paymentNotesInput.value = '';
        paymentErrorBox.classList.add('hidden');
        paymentModal.classList.remove('hidden');
    };

    const closePaymentModal = () => {
        paymentModal.classList.add('hidden');
    };

    document.getElementById('open-payment-modal-btn').addEventListener('click', openPaymentModal);
    document.getElementById('close-payment-modal').addEventListener('click', closePaymentModal);
    document.getElementById('cancel-payment-modal').addEventListener('click', closePaymentModal);
    paymentModal.addEventListener('click', (e) => {
        if (e.target === paymentModal) closePaymentModal();
    });

    paymentForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        paymentErrorBox.classList.add('hidden');
        savePaymentBtn.disabled = true;
        savePaymentBtn.textContent = 'Kaydediliyor...';

        const payload = {
            amount: Number(paymentAmountInput.value),
            payment_method: paymentMethodInput.value,
            payment_date: paymentDateInput.value,
            notes: paymentNotesInput.value.trim() || null,
        };

        try {
            const response = await fetch(paymentStoreUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Tahsilat kaydedilemedi.');
            }

            updateFinanceUI(result.finance, result.ledger);
            closePaymentModal();
        } catch (err) {
            paymentErrorBox.textContent = err.message || 'Sunucu ile iletişim kurulamadı.';
            paymentErrorBox.classList.remove('hidden');
        } finally {
            savePaymentBtn.disabled = false;
            savePaymentBtn.textContent = 'Tahsilatı Kaydet';
        }
    });

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, (m) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[m]));
    }

    updateCounters();
})();
</script>
@endpush
@endsection
