<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Material;
use App\Models\MaterialDiscussion;
use App\Models\Notification;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            if ($user->isSiswa()) {
                $material = Material::where('class_id', $user->class_id)->with('subject', 'instructor')->latest()->first();
                $assignment = Assignment::where('class_id', $user->class_id)->with('subject', 'instructor')->latest()->first();
                $quiz = Quiz::where('class_id', $user->class_id)->with('subject', 'instructor')->latest()->first();

                // 1. Notifikasi Materi Baru Sesuai Database
                if ($material) {
                    $materialTitle = $material->title;
                    $subjectName = $material->subject->name ?? 'Mata Pelajaran';
                    $instructorName = $material->instructor->name ?? 'Guru Pengampu';

                    Notification::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'type' => 'new_material',
                        ],
                        [
                            'title' => 'Materi Baru: ' . $materialTitle,
                            'message' => 'Guru ' . $instructorName . ' telah menerbitkan materi baru "' . $materialTitle . '" (' . $subjectName . ').',
                            'related_url' => route('student.materials.show', $material),
                            'is_read' => false,
                            'created_at' => now()->subMinutes(25),
                        ]
                    );
                }

                // 2. Notifikasi Tugas Baru Sesuai Database
                if ($assignment) {
                    $assignmentTitle = $assignment->title;
                    $subjectName = $assignment->subject->name ?? 'Mata Pelajaran';
                    $instructorName = $assignment->instructor->name ?? 'Guru Pengampu';

                    Notification::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'type' => 'new_assignment',
                        ],
                        [
                            'title' => 'Tugas Baru: ' . $assignmentTitle,
                            'message' => 'Guru ' . $instructorName . ' telah menerbitkan tugas baru "' . $assignmentTitle . '" (' . $subjectName . '). Segera periksa dan kumpulkan jawaban Anda.',
                            'related_url' => route('student.assignments.show', $assignment),
                            'is_read' => false,
                            'created_at' => now()->subHours(2),
                        ]
                    );
                }

                // 3. Notifikasi Kuis Online Sesuai Database
                if ($quiz) {
                    $quizTitle = $quiz->title;
                    $subjectName = $quiz->subject->name ?? 'Mata Pelajaran';
                    $instructorName = $quiz->instructor->name ?? 'Guru Pengampu';

                    Notification::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'type' => 'new_quiz',
                        ],
                        [
                            'title' => 'Kuis Baru: ' . $quizTitle,
                            'message' => 'Kuis online baru "' . $quizTitle . '" (' . $subjectName . ') telah dibuka oleh ' . $instructorName . '. Segera kerjakan kuis Anda.',
                            'related_url' => route('student.quizzes.show', $quiz),
                            'is_read' => true,
                            'read_at' => now()->subDay(),
                            'created_at' => now()->subDay(),
                        ]
                    );
                }

                // 4. Notifikasi Balasan Komentar di Ruang Diskusi Sesuai Database
                $discussion = null;
                if ($material) {
                    $guruUser = User::whereHas('role', fn($q) => $q->where('name', 'guru'))->first();

                    // Pastikan ada komentar induk dari siswa
                    $studentComment = MaterialDiscussion::firstOrCreate(
                        [
                            'material_id' => $material->id,
                            'user_id' => $user->id,
                            'parent_id' => null,
                        ],
                        [
                            'comment' => 'Mohon izin bertanya, apakah rumus dan konsep di materi ini akan keluar pada kuis minggu ini?',
                            'created_at' => now()->subHours(2),
                        ]
                    );

                    // Buat balasan menjorok dari guru
                    if ($guruUser) {
                        $discussion = MaterialDiscussion::firstOrCreate(
                            [
                                'material_id' => $material->id,
                                'parent_id' => $studentComment->id,
                                'user_id' => $guruUser->id,
                            ],
                            [
                                'comment' => 'Tentu saja, silakan pelajari kembali modul materi pada bagian studi kasus dan rangkumannya ya.',
                                'created_at' => now()->subMinutes(25),
                            ]
                        );
                        $discussion->load('material', 'user');
                    }
                }

                if ($discussion) {
                    $discMaterialTitle = $discussion->material->title ?? 'Materi Pembelajaran';
                    $commenterName = $discussion->user->name ?? 'Guru';
                    $isTeacher = ($discussion->user->role->name ?? '') === 'guru';
                    $snippet = Str::limit($discussion->comment, 60);

                    Notification::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'type' => 'comment',
                        ],
                        [
                            'title' => ($isTeacher ? 'Balasan Guru: ' : 'Balasan Komentar: ') . $discMaterialTitle,
                            'message' => ($isTeacher ? 'Guru ' : '') . $commenterName . ' membalas komentar Anda di materi "' . $discMaterialTitle . '": "' . $snippet . '"',
                            'related_url' => route('student.materials.show', $discussion->material) . '#discussion-item-' . $discussion->id,
                            'is_read' => false,
                            'created_at' => now()->subMinutes(25),
                        ]
                    );
                }
            } else {
                // Notifikasi Guru Sesuai Database
                $latestAssignment = Assignment::latest()->first();
                $latestDiscussion = MaterialDiscussion::whereHas('user.role', fn($q) => $q->where('name', 'siswa'))
                    ->with('material', 'user')
                    ->latest()
                    ->first();

                if ($latestAssignment) {
                    Notification::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'type' => 'new_assignment',
                        ],
                        [
                            'title' => 'Tugas Dikumpulkan: ' . $latestAssignment->title,
                            'message' => 'Siswa telah mengumpulkan jawaban untuk tugas "' . $latestAssignment->title . '".',
                            'related_url' => route('admin.submissions.index'),
                            'is_read' => false,
                            'created_at' => now()->subMinutes(30),
                        ]
                    );
                }

                if ($latestDiscussion) {
                    $discTitle = $latestDiscussion->material->title ?? 'Materi Pembelajaran';
                    $discUser = $latestDiscussion->user->name ?? 'Siswa';
                    $discSnippet = Str::limit($latestDiscussion->comment, 60);

                    Notification::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'type' => 'comment',
                        ],
                        [
                            'title' => 'Diskusi Kelas: ' . $discTitle,
                            'message' => $discUser . ' menulis pertanyaan di ruang diskusi materi "' . $discTitle . '": "' . $discSnippet . '"',
                            'related_url' => route('student.materials.show', $latestDiscussion->material) . '#discussion-list',
                            'is_read' => false,
                            'created_at' => now()->subHours(1),
                        ]
                    );
                }
            }
        }
    }
}
