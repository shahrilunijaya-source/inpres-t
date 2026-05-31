<?php

namespace Database\Seeders;

use App\Models\Citizen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CitizenSeeder extends Seeder
{
    // Malay personal names (given). Father names drawn separately so bin/binti
    // never appends a Chinese or Indian surname.
    private const MALAY_MALE_GIVEN = [
        'Ahmad', 'Muhammad', 'Mohd Faizal', 'Ali', 'Hassan', 'Hussain', 'Ibrahim',
        'Ismail', 'Mohd Yusof', 'Abdul Razak', 'Hafiz', 'Iskandar', 'Khairul Anwar',
        'Mohd Rizal', 'Syafiq', 'Zulkifli', 'Abdul Aziz', 'Hakim', 'Adam', 'Daniel Haziq',
    ];

    private const MALAY_FEMALE_GIVEN = [
        'Siti Aminah', 'Nurul Aisyah', 'Aisyah', 'Fatimah', 'Khadijah', 'Aminah', 'Hajar',
        'Mariam', 'Sarah Nadia', 'Nadia', 'Nur Aina', 'Sofia', 'Iman', 'Maisarah',
        'Nur Hidayah', 'Syazwani', 'Liyana', 'Raihan', 'Farhana', 'Najwa',
    ];

    // Father given names for bin/binti — Malay only.
    private const MALAY_FATHER_NAMES = [
        'Abdullah', 'Hassan', 'Yusof', 'Ibrahim', 'Abdul Razak', 'Ahmad',
        'Ismail', 'Osman', 'Abdul Rahman', 'Mohd Salleh', 'Karim', 'Mahmud',
    ];

    // Chinese names: surname first, then given. No bin/binti.
    private const CHINESE_SURNAMES = [
        'Tan', 'Lim', 'Lee', 'Ng', 'Wong', 'Chong', 'Chan', 'Goh', 'Teo', 'Ong', 'Yeoh', 'Chua',
    ];

    private const CHINESE_MALE_GIVEN = [
        'Wei Ming', 'Kok Wai', 'Boon Hock', 'Jun Jie', 'Wei Jie', 'Chee Keong',
        'Hong Sheng', 'Yong Sheng', 'Kah Wai', 'Zi Hao',
    ];

    private const CHINESE_FEMALE_GIVEN = [
        'Mei Ling', 'Pei Shan', 'Hui Yi', 'Sze Mei', 'Bee Lan', 'Wan Jing',
        'Xin Yi', 'Li Wen', 'Hui Min', 'Jia Yi',
    ];

    // Indian names: given a/l (male) or a/p (female) father.
    private const INDIAN_MALE_GIVEN = [
        'Raj Kumar', 'Suresh', 'Vikram', 'Arun', 'Mohan', 'Ganesh', 'Dinesh', 'Kalai',
    ];

    private const INDIAN_FEMALE_GIVEN = [
        'Priya', 'Kavitha', 'Shanti', 'Devi', 'Meena', 'Lakshmi', 'Anjali', 'Geetha',
    ];

    private const INDIAN_FATHER_NAMES = [
        'Devan', 'Naidu', 'Ramasamy', 'Muthu', 'Subramaniam', 'Krishnan', 'Samy', 'Ravi',
    ];

    private const STATES = [
        ['name' => 'Selangor', 'code' => '10', 'postcodes' => ['40000', '40150', '46000', '46050', '47301', '47810', '48000']],
        ['name' => 'Kuala Lumpur', 'code' => '14', 'postcodes' => ['50000', '50300', '50450', '51000', '52100', '53100', '57000']],
        ['name' => 'Johor', 'code' => '01', 'postcodes' => ['80000', '80100', '81100', '83000', '85000', '86000']],
        ['name' => 'Pulau Pinang', 'code' => '07', 'postcodes' => ['10000', '10250', '10450', '11700', '11960', '13700']],
        ['name' => 'Perak', 'code' => '08', 'postcodes' => ['30000', '30450', '31350', '32000', '34000', '35000']],
        ['name' => 'Kedah', 'code' => '02', 'postcodes' => ['05000', '05100', '06000', '08000', '09000']],
        ['name' => 'Sabah', 'code' => '12', 'postcodes' => ['88000', '88300', '89000', '90000', '91000']],
        ['name' => 'Sarawak', 'code' => '13', 'postcodes' => ['93000', '94000', '95000', '96000', '97000', '98000']],
        ['name' => 'Pahang', 'code' => '06', 'postcodes' => ['25000', '25200', '26000', '27000', '28000']],
        ['name' => 'Negeri Sembilan', 'code' => '05', 'postcodes' => ['70000', '70200', '71000', '72000', '73000']],
        ['name' => 'Melaka', 'code' => '04', 'postcodes' => ['75000', '75200', '76100', '77000', '78000']],
        ['name' => 'Terengganu', 'code' => '11', 'postcodes' => ['20000', '20200', '21000', '22000', '23000']],
        ['name' => 'Kelantan', 'code' => '03', 'postcodes' => ['15000', '15200', '16000', '17000', '18000']],
    ];

    private const STREETS = [
        'Jalan Bukit Bintang', 'Jalan Tun Razak', 'Jalan Ampang', 'Jalan Cheras',
        'Jalan Klang Lama', 'Jalan Damansara', 'Jalan Sultan Ismail', 'Jalan Pudu',
        'Lorong Maarof', 'Persiaran Hampshire', 'Jalan SS 2/24', 'Jalan PJU 7/3',
        'Jalan USJ 9/5G', 'Jalan Permata', 'Jalan Sri Permaisuri',
    ];

    public function run(): void
    {
        $rows = [];

        for ($i = 0; $i < 200; $i++) {
            $gender = fake()->boolean() ? 'M' : 'F';

            $dob = Carbon::now()
                ->subYears(random_int(0, 75))
                ->subDays(random_int(0, 364))
                ->startOfDay();

            $state = self::STATES[array_rand(self::STATES)];
            $postcode = $state['postcodes'][array_rand($state['postcodes'])];

            $ic = $this->generateIc($dob, $state['code'], $gender);

            // Pick ethnicity (Malaysia-representative mix), then build a name that
            // is internally consistent — bin/binti only attaches a Malay father name,
            // Chinese names are surname-first, Indian names use a/l (male) / a/p (female).
            $roll = random_int(1, 100);
            $ethnicity = $roll <= 60 ? 'malay' : ($roll <= 85 ? 'chinese' : 'indian');

            $fullName = match ($ethnicity) {
                'malay' => $gender === 'M'
                    ? self::MALAY_MALE_GIVEN[array_rand(self::MALAY_MALE_GIVEN)]
                        . ' bin ' . self::MALAY_FATHER_NAMES[array_rand(self::MALAY_FATHER_NAMES)]
                    : self::MALAY_FEMALE_GIVEN[array_rand(self::MALAY_FEMALE_GIVEN)]
                        . ' binti ' . self::MALAY_FATHER_NAMES[array_rand(self::MALAY_FATHER_NAMES)],
                'chinese' => self::CHINESE_SURNAMES[array_rand(self::CHINESE_SURNAMES)] . ' '
                    . ($gender === 'M'
                        ? self::CHINESE_MALE_GIVEN[array_rand(self::CHINESE_MALE_GIVEN)]
                        : self::CHINESE_FEMALE_GIVEN[array_rand(self::CHINESE_FEMALE_GIVEN)]),
                'indian' => $gender === 'M'
                    ? self::INDIAN_MALE_GIVEN[array_rand(self::INDIAN_MALE_GIVEN)]
                        . ' a/l ' . self::INDIAN_FATHER_NAMES[array_rand(self::INDIAN_FATHER_NAMES)]
                    : self::INDIAN_FEMALE_GIVEN[array_rand(self::INDIAN_FEMALE_GIVEN)]
                        . ' a/p ' . self::INDIAN_FATHER_NAMES[array_rand(self::INDIAN_FATHER_NAMES)],
            };

            $street = self::STREETS[array_rand(self::STREETS)];
            $houseNo = random_int(1, 250);
            $address = "No. {$houseNo}, {$street}";

            $rows[] = [
                'ic' => $ic,
                'full_name' => $fullName,
                'dob' => $dob,
                'gender' => $gender,
                'address' => $address,
                'postcode' => $postcode,
                'state' => $state['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Dedupe on IC just in case
        $unique = collect($rows)->unique('ic')->values()->all();
        Citizen::insert($unique);

        $this->command?->info('Seeded ' . count($unique) . ' citizens.');
    }

    /**
     * Format: YYMMDD-PB-####
     * YY/MM/DD = DOB. PB = state code. #### = serial (last digit gender: odd=M, even=F).
     */
    private function generateIc(Carbon $dob, string $stateCode, string $gender): string
    {
        $datePart = $dob->format('ymd');
        $serial = str_pad((string) random_int(100, 999), 3, '0', STR_PAD_LEFT);
        $lastDigit = $gender === 'M' ? [1, 3, 5, 7, 9][array_rand([1, 3, 5, 7, 9])] : [0, 2, 4, 6, 8][array_rand([0, 2, 4, 6, 8])];

        return "{$datePart}-{$stateCode}-{$serial}{$lastDigit}";
    }
}
