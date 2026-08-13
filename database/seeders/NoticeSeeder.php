<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Database\Seeder;

class NoticeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();
        if (! $user) {
            return;
        }

        $classIds = ClassRoom::query()->pluck('id');

        $notices = [
            ['title' => 'Parent meeting this Friday', 'audience' => 'all', 'publish_date' => now()->subDays(2), 'expiry_date' => now()->addDays(5)],
            ['title' => 'Science fair registration open', 'audience' => 'students', 'publish_date' => now()->subDay(), 'expiry_date' => now()->addDays(12)],
            ['title' => 'Staff training workshop', 'audience' => 'staff', 'publish_date' => now()->subDays(3), 'expiry_date' => now()->addDays(7)],
            ['title' => 'Class routine update', 'audience' => 'specific_class', 'publish_date' => now()->subDay(), 'expiry_date' => now()->addDays(10)],
            ['title' => 'Library return deadline extended', 'audience' => 'all', 'publish_date' => now()->subDays(4), 'expiry_date' => null],
            ['title' => 'Exam form fill-up reminder', 'audience' => 'students', 'publish_date' => now(), 'expiry_date' => now()->addDays(15)],
            ['title' => 'Accounts office closed tomorrow', 'audience' => 'all', 'publish_date' => now()->subDays(1), 'expiry_date' => now()->addDays(1)],
            ['title' => 'Expired sports day notice', 'audience' => 'all', 'publish_date' => now()->subDays(20), 'expiry_date' => now()->subDays(2)],
            ['title' => 'Future admission circular', 'audience' => 'all', 'publish_date' => now()->addDays(5), 'expiry_date' => now()->addDays(25)],
            ['title' => 'Class test seating plan', 'audience' => 'specific_class', 'publish_date' => now()->subDays(2), 'expiry_date' => now()->addDays(3)],
        ];

        foreach ($notices as $index => $notice) {
            Notice::query()->updateOrCreate([
                'title' => $notice['title'],
            ], [
                'description' => fake()->paragraphs(2, true),
                'audience' => $notice['audience'],
                'class_id' => $notice['audience'] === 'specific_class' && $classIds->isNotEmpty()
                    ? $classIds->random()
                    : null,
                'publish_date' => $notice['publish_date']->toDateString(),
                'expiry_date' => $notice['expiry_date']?->toDateString(),
                'attachment' => null,
                'created_by' => $user->id,
                'created_at' => now()->subDays(10 - $index),
                'updated_at' => now()->subDays(10 - $index),
            ]);
        }
    }
}
