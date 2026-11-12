<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\PatientTooth;
use App\Models\Payment;
use App\Models\TreatmentPrice;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPrices = [
            ['name' => 'Dolgu', 'default_price' => 1800],
            ['name' => 'Kanal Tedavisi', 'default_price' => 4500],
            ['name' => 'İmplant', 'default_price' => 18000],
            ['name' => 'Çekim', 'default_price' => 1200],
            ['name' => 'Temizlik', 'default_price' => 1500],
            ['name' => 'Diğer', 'default_price' => 1000],
        ];

        foreach ($defaultPrices as $item) {
            TreatmentPrice::firstOrCreate(
                ['name' => $item['name']],
                ['default_price' => $item['default_price']]
            );
        }

        if (Patient::exists()) {
            return;
        }

        $patient1 = Patient::create([
            'tc_no' => '12345678901',
            'full_name' => 'Ayşe Yılmaz',
            'phone' => '0532 111 22 33',
            'email' => 'ayse@example.com',
            'birth_date' => '1991-05-14',
            'gender' => 'female',
            'medical_notes' => 'Penisilin alerjisi mevcut.',
        ]);

        PatientTooth::create([
            'patient_id' => $patient1->id,
            'tooth_number' => 16,
            'status' => PatientTooth::STATUS_TREATED,
            'treatment_type' => 'Kanal Tedavisi',
            'price' => 4500.00,
            'notes' => '3 kanallı endodontik tedavi tamamlandı.',
            'treated_at' => now()->subDays(12)->toDateString(),
        ]);

        Payment::create([
            'patient_id' => $patient1->id,
            'amount' => 2000.00,
            'payment_method' => Payment::METHOD_CARD,
            'payment_date' => now()->subDays(10),
            'notes' => 'Kanal tedavisi ön ödemesi',
        ]);

        PatientTooth::create([
            'patient_id' => $patient1->id,
            'tooth_number' => 36,
            'status' => PatientTooth::STATUS_PLANNED,
            'treatment_type' => 'Kompozit Dolgu',
            'notes' => 'Oklüzal çürük temizlenecek.',
        ]);

        PatientTooth::create([
            'patient_id' => $patient1->id,
            'tooth_number' => 48,
            'status' => PatientTooth::STATUS_MISSING,
            'treatment_type' => 'Çekim',
            'notes' => 'Gömülü 20 yaş dişi çekildi.',
            'treated_at' => now()->subMonths(3)->toDateString(),
        ]);

        Appointment::create([
            'patient_id' => $patient1->id,
            'appointment_date' => today()->setTime(14, 30),
            'status' => Appointment::STATUS_PENDING,
            'description' => '36 numaralı diş dolgu işlemi',
        ]);

        $patient2 = Patient::create([
            'tc_no' => '98765432109',
            'full_name' => 'Mehmet Kaya',
            'phone' => '0544 333 44 55',
            'birth_date' => '1984-11-02',
            'gender' => 'male',
            'medical_notes' => 'Hipertansiyon (düzenli ilaç kullanımı).',
        ]);

        Appointment::create([
            'patient_id' => $patient2->id,
            'appointment_date' => today()->setTime(11, 0),
            'status' => Appointment::STATUS_COMPLETED,
            'description' => 'Genel muayene ve diş taşı temizliği',
        ]);
    }
}
