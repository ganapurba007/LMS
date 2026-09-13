<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            if ($user->isSiswa()) {
                $material = \App\Models\Material::where('class_id', $user->class_id)->latest()->first();
                $assignment = \App\Models\Assignment::where('class_id', $user->class_id)->latest()->first();
                $quiz = \App\Models\Quiz::where('class_id', $user->class_id)->latest()->first();

                Notification::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'title' => 'Materi Pembelajaran Baru',
                    ],
                    [
                        'type' => 'new_material',
                        'message' => 'Guru telah menambahkan materi pembelajaran baru.',
                        'related_url' => $material ? route('student.materials.show', $material) : route('student.materials.index'),
                        'is_read' => false,
                        'created_at' => now()->subMinutes(15),
                    ]
                );

                Notification::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'title' => 'Tugas Baru Diterbitkan',
                    ],
                    [
                        'type' => 'new_assignment',
                        'message' => 'Tugas baru telah tersedia untuk kelas Anda. Segera periksa dan kumpulkan sebelum tenggat waktu.',
                        'related_url' => $assignment ? route('student.assignments.show', $assignment) : route('student.assignments.index'),
                        'is_read' => false,
                        'created_at' => now()->subHours(2),
                    ]
                );

                Notification::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'title' => 'Kuis Online Siap Dikerjakan',
                    ],
                    [
                        'type' => 'new_quiz',
                        'message' => 'Kuis online baru telah dibuka. Segera kerjakan kuis Anda.',
                        'related_url' => $quiz ? route('student.quizzes.show', $quiz) : route('student.quizzes.index'),
                        'is_read' => true,
                        'read_at' => now()->subDay(),
                        'created_at' => now()->subDay(),
                    ]
                );
            } else {
                Notification::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'title' => 'Jawaban Tugas Baru Dikirim',
                    ],
                    [
                        'type' => 'new_assignment',
                        'message' => 'Siswa Budi Santoso telah mengumpulkan tugas "Latihan Hukum Newton".',
                        'related_url' => route('admin.submissions.index'),
                        'is_read' => false,
                        'created_at' => now()->subMinutes(30),
                    ]
                );

                Notification::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'title' => 'Komentar Diskusi Kelas',
                    ],
                    [
                        'type' => 'comment',
                        'message' => 'Siti Rahma bertanya pada diskusi materi "Fisika Kuantum".',
                        'related_url' => route('admin.materials.index'),
                        'is_read' => false,
                        'created_at' => now()->subHours(3),
                    ]
                );
            }
        }
    }
}
