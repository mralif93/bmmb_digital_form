<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'branch_name' => 'ALAM DAMAI',
                'weekend_start_day' => 'SATURDAY',
                'ti_agent_code' => 'FN12984',
                'address' => 'No. 1, Jalan Damai Raya 2, Alam Damai, 56000 Kuala Lumpur, Wilayah Persekutuan',
                'email' => 'sgar@muamalat.com.my',
                'state' => 'Wilayah Persekutuan Kuala Lumpur',
                'region' => 'Central 1',
            ],
            [
                'branch_name' => 'AMPANG POINT',
                'weekend_start_day' => 'SATURDAY',
                'ti_agent_code' => 'FN12985',
                'address' => 'Lot G-03, Ground Floor, Ampang Point Shopping Centre, Jalan Mamanda 3, 68000 Ampang, Selangor',
                'email' => 'ampangpoint@muamalat.com.my',
                'state' => 'Selangor',
                'region' => 'Central 1',
            ],
            [
                'branch_name' => 'BANDAR UTAMA',
                'weekend_start_day' => 'SATURDAY',
                'ti_agent_code' => 'FN12986',
                'address' => 'No. 1, Jalan Bandar Utama 1, Bandar Utama, 47800 Petaling Jaya, Selangor',
                'email' => 'bandarutama@muamalat.com.my',
                'state' => 'Selangor',
                'region' => 'Central 1',
            ],
            [
                'branch_name' => 'BANGSAR',
                'weekend_start_day' => 'SATURDAY',
                'ti_agent_code' => 'FN12987',
                'address' => 'No. 12, Jalan Telawi, Bangsar Baru, 59100 Kuala Lumpur, Wilayah Persekutuan',
                'email' => 'bangsar@muamalat.com.my',
                'state' => 'Wilayah Persekutuan Kuala Lumpur',
                'region' => 'Central 1',
            ],
            [
                'branch_name' => 'BUKIT BINTANG',
                'weekend_start_day' => 'SATURDAY',
                'ti_agent_code' => 'FN12988',
                'address' => 'Lot G-28, Ground Floor, Sungei Wang Plaza, Jalan Sultan Ismail, 50250 Kuala Lumpur, Wilayah Persekutuan',
                'email' => 'bukitbintang@muamalat.com.my',
                'state' => 'Wilayah Persekutuan Kuala Lumpur',
                'region' => 'Central 1',
            ],
            [
                'branch_name' => 'CHERAS',
                'weekend_start_day' => 'SATURDAY',
                'ti_agent_code' => 'FN12989',
                'address' => 'No. 123, Jalan Cheras, Taman Cheras, 56000 Kuala Lumpur, Wilayah Persekutuan',
                'email' => 'cheras@muamalat.com.my',
                'state' => 'Wilayah Persekutuan Kuala Lumpur',
                'region' => 'Central 1',
            ],
            [
                'branch_name' => 'KLANG',
                'weekend_start_day' => 'SATURDAY',
                'ti_agent_code' => 'FN12990',
                'address' => 'No. 45, Jalan Raya Timur, 41000 Klang, Selangor',
                'email' => 'klang@muamalat.com.my',
                'state' => 'Selangor',
                'region' => 'Central 2',
            ],
            [
                'branch_name' => 'SHAH ALAM',
                'weekend_start_day' => 'SATURDAY',
                'ti_agent_code' => 'FN12991',
                'address' => 'No. 78, Persiaran Sultan Ibrahim, 40000 Shah Alam, Selangor',
                'email' => 'shahalam@muamalat.com.my',
                'state' => 'Selangor',
                'region' => 'Central 2',
            ],
            [
                'branch_name' => 'SUBANG JAYA',
                'weekend_start_day' => 'SATURDAY',
                'ti_agent_code' => 'FN12992',
                'address' => 'No. 12, Jalan SS15/4, Subang Jaya, 47500 Petaling Jaya, Selangor',
                'email' => 'subangjaya@muamalat.com.my',
                'state' => 'Selangor',
                'region' => 'Central 2',
            ],
            [
                'branch_name' => 'PUCHONG',
                'weekend_start_day' => 'SATURDAY',
                'ti_agent_code' => 'FN12993',
                'address' => 'No. 56, Jalan Puteri 1/2, Bandar Puteri, 47100 Puchong, Selangor',
                'email' => 'puchong@muamalat.com.my',
                'state' => 'Selangor',
                'region' => 'Central 2',
            ],
            [
                'branch_name' => 'JOHOR BAHRU',
                'weekend_start_day' => 'FRIDAY',
                'ti_agent_code' => 'FN13001',
                'address' => 'No. 23, Jalan Ibrahim Sultan, 80000 Johor Bahru, Johor',
                'email' => 'johorbahru@muamalat.com.my',
                'state' => 'Johor',
                'region' => 'South',
            ],
            [
                'branch_name' => 'SENAI',
                'weekend_start_day' => 'FRIDAY',
                'ti_agent_code' => 'FN13002',
                'address' => 'No. 34, Jalan Senai, 81400 Senai, Johor',
                'email' => 'senai@muamalat.com.my',
                'state' => 'Johor',
                'region' => 'South',
            ],
            [
                'branch_name' => 'MELAKA',
                'weekend_start_day' => 'FRIDAY',
                'ti_agent_code' => 'FN13003',
                'address' => 'No. 67, Jalan Hang Tuah, 75300 Melaka, Melaka',
                'email' => 'melaka@muamalat.com.my',
                'state' => 'Melaka',
                'region' => 'South',
            ],
            [
                'branch_name' => 'SEREMBAN',
                'weekend_start_day' => 'FRIDAY',
                'ti_agent_code' => 'FN13004',
                'address' => 'No. 89, Jalan Tuanku Antah, 70000 Seremban, Negeri Sembilan',
                'email' => 'seremban@muamalat.com.my',
                'state' => 'Negeri Sembilan',
                'region' => 'South',
            ],
            [
                'branch_name' => 'IPOH',
                'weekend_start_day' => 'FRIDAY',
                'ti_agent_code' => 'FN13010',
                'address' => 'No. 12, Jalan Sultan Idris Shah, 30000 Ipoh, Perak',
                'email' => 'ipoh@muamalat.com.my',
                'state' => 'Perak',
                'region' => 'North',
            ],
            [
                'branch_name' => 'PENANG',
                'weekend_start_day' => 'FRIDAY',
                'ti_agent_code' => 'FN13011',
                'address' => 'No. 45, Jalan Masjid Kapitan Keling, 10200 Georgetown, Pulau Pinang',
                'email' => 'penang@muamalat.com.my',
                'state' => 'Pulau Pinang',
                'region' => 'North',
            ],
            [
                'branch_name' => 'ALOR SETAR',
                'weekend_start_day' => 'FRIDAY',
                'ti_agent_code' => 'FN13012',
                'address' => 'No. 78, Jalan Sultan Badlishah, 05000 Alor Setar, Kedah',
                'email' => 'alorsetar@muamalat.com.my',
                'state' => 'Kedah',
                'region' => 'North',
            ],
            [
                'branch_name' => 'KOTA BHARU',
                'weekend_start_day' => 'FRIDAY',
                'ti_agent_code' => 'FN13020',
                'address' => 'No. 123, Jalan Sultan Ibrahim, 15000 Kota Bharu, Kelantan',
                'email' => 'kotabharu@muamalat.com.my',
                'state' => 'Kelantan',
                'region' => 'East Coast',
            ],
            [
                'branch_name' => 'KUANTAN',
                'weekend_start_day' => 'FRIDAY',
                'ti_agent_code' => 'FN13021',
                'address' => 'No. 56, Jalan Besar, 25000 Kuantan, Pahang',
                'email' => 'kuantan@muamalat.com.my',
                'state' => 'Pahang',
                'region' => 'East Coast',
            ],
            [
                'branch_name' => 'TERENGGANU',
                'weekend_start_day' => 'FRIDAY',
                'ti_agent_code' => 'FN13022',
                'address' => 'No. 34, Jalan Sultan Zainal Abidin, 20000 Kuala Terengganu, Terengganu',
                'email' => 'terengganu@muamalat.com.my',
                'state' => 'Terengganu',
                'region' => 'East Coast',
            ],
            [
                'branch_name' => 'KOTA KINABALU',
                'weekend_start_day' => 'FRIDAY',
                'ti_agent_code' => 'FN13030',
                'address' => 'No. 67, Jalan Gaya, 88000 Kota Kinabalu, Sabah',
                'email' => 'kotakinabalu@muamalat.com.my',
                'state' => 'Sabah',
                'region' => 'East Malaysia',
            ],
            [
                'branch_name' => 'KUCHING',
                'weekend_start_day' => 'FRIDAY',
                'ti_agent_code' => 'FN13031',
                'address' => 'No. 89, Jalan Tun Abang Haji Openg, 93000 Kuching, Sarawak',
                'email' => 'kuching@muamalat.com.my',
                'state' => 'Sarawak',
                'region' => 'East Malaysia',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
