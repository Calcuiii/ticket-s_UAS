<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Insert Categories
        DB::table('categories')->insert([
            [
                'name' => 'Musik',
                'slug' => 'musik',
                'description' => 'Event kategori musik dan konser',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Olahraga',
                'slug' => 'olahraga',
                'description' => 'Event kategori olahraga dan turnamen',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teknologi',
                'slug' => 'teknologi',
                'description' => 'Event kategori teknologi dan seminar IT',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Seni & Budaya',
                'slug' => 'seni-budaya',
                'description' => 'Event kategori seni dan budaya',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Kuliner',
                'slug' => 'kuliner',
                'description' => 'Event kategori kuliner dan food festival',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        // Insert Events
        DB::table('events')->insert([
            [
                'category_id' => 1,
                'title' => 'Festival Musik Surabaya 2024',
                'slug' => 'festival-musik-surabaya-2024',
                'description' => 'Festival musik terbesar di Surabaya',
                'location' => 'Surabaya Convention Center',
                'event_date' => '2024-12-25 18:00:00',
                'event_end_date' => '2024-12-25 23:00:00',
                'ticket_price' => 150000,
                'total_stock' => 500,
                'available_stock' => 500,
                'status' => 'published',
                'organizer_name' => 'EO Surabaya',
                'banner_url' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 1,
                'title' => 'Konser Dewa 19 Reunion',
                'slug' => 'konser-dewa-19-reunion',
                'description' => 'Konser reuni band legendaris Dewa 19',
                'location' => 'Gelora Bung Tomo Surabaya',
                'event_date' => '2024-11-30 19:00:00',
                'event_end_date' => '2024-11-30 22:00:00',
                'ticket_price' => 250000,
                'total_stock' => 1000,
                'available_stock' => 850,
                'status' => 'published',
                'organizer_name' => 'Promotor Nusantara',
                'banner_url' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 2,
                'title' => 'Turnamen Futsal Piala Walikota',
                'slug' => 'turnamen-futsal-piala-walikota',
                'description' => 'Turnamen futsal antar klub se-Jawa Timur',
                'location' => 'GOR Pancasila Surabaya',
                'event_date' => '2024-12-10 08:00:00',
                'event_end_date' => '2024-12-10 17:00:00',
                'ticket_price' => 25000,
                'total_stock' => 300,
                'available_stock' => 300,
                'status' => 'published',
                'organizer_name' => 'Dispora Surabaya',
                'banner_url' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 3,
                'title' => 'Tech Conference Jawa Timur 2024',
                'slug' => 'tech-conference-jawa-timur-2024',
                'description' => 'Konferensi teknologi terbesar di Jawa Timur',
                'location' => 'Hotel Shangri-La Surabaya',
                'event_date' => '2024-12-15 09:00:00',
                'event_end_date' => '2024-12-15 17:00:00',
                'ticket_price' => 350000,
                'total_stock' => 200,
                'available_stock' => 150,
                'status' => 'published',
                'organizer_name' => 'Tech Community Surabaya',
                'banner_url' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 4,
                'title' => 'Pameran Seni Rupa Nusantara',
                'slug' => 'pameran-seni-rupa-nusantara',
                'description' => 'Pameran seni rupa seniman seluruh Indonesia',
                'location' => 'Balai Pemuda Surabaya',
                'event_date' => '2024-12-20 10:00:00',
                'event_end_date' => '2024-12-22 20:00:00',
                'ticket_price' => 50000,
                'total_stock' => 400,
                'available_stock' => 400,
                'status' => 'published',
                'organizer_name' => 'Dinas Kebudayaan Surabaya',
                'banner_url' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 5,
                'title' => 'Surabaya Food Festival 2024',
                'slug' => 'surabaya-food-festival-2024',
                'description' => 'Festival kuliner terbesar khas Surabaya',
                'location' => 'Taman Surya Surabaya',
                'event_date' => '2024-12-28 11:00:00',
                'event_end_date' => '2024-12-28 21:00:00',
                'ticket_price' => 35000,
                'total_stock' => 600,
                'available_stock' => 600,
                'status' => 'published',
                'organizer_name' => 'Dinas Pariwisata Surabaya',
                'banner_url' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 3,
                'title' => 'Workshop Laravel & Docker',
                'slug' => 'workshop-laravel-docker',
                'description' => 'Workshop intensif Laravel dan Docker untuk developer',
                'location' => 'Coworking Space Surabaya',
                'event_date' => '2024-12-05 09:00:00',
                'event_end_date' => '2024-12-05 16:00:00',
                'ticket_price' => 200000,
                'total_stock' => 50,
                'available_stock' => 20,
                'status' => 'published',
                'organizer_name' => 'Laravel Indonesia Community',
                'banner_url' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 2,
                'title' => 'Marathon Surabaya 2024',
                'slug' => 'marathon-surabaya-2024',
                'description' => 'Event lari marathon tahunan kota Surabaya',
                'location' => 'Start: Tugu Pahlawan Surabaya',
                'event_date' => '2024-12-08 05:00:00',
                'event_end_date' => '2024-12-08 12:00:00',
                'ticket_price' => 100000,
                'total_stock' => 750,
                'available_stock' => 500,
                'status' => 'published',
                'organizer_name' => 'Komunitas Lari Surabaya',
                'banner_url' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}