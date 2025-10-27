<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class JosephSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::updateOrCreate(
            ['email' => 'admin@incadev.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'Principal',
                'full_name' => 'Admin Principal',
                'password' => Hash::make('password'),
                'role' => ['admin'],
                'status' => 'active',
                'timezone' => 'America/Lima',
            ]
        );

        /**
         * DOCUMENTS
         */

        // Verificar que existe la tabla y obtener un usuario para asignar
        if (!Schema::hasTable('documents')) {
            $this->command->warn('La tabla documents no existe. Saltando DocumentSeeder.');
            return;
        }

        // Obtener un usuario admin o el primero disponible
        $userId = DB::table('users')->where('email', 'admin@incadev.com')->value('id')
            ?? DB::table('users')->first()?->id
            ?? 1;

        $this->command->info('Iniciando seeder de documentos...');

        // Documentos de ejemplo con diferentes categorías
        $documents = [
            // DOCUMENTOS ACADÉMICOS
            [
                'title' => 'Reglamento Académico 2025',
                'category' => 'academic',
                'entity_type' => 'general',
                'entity_id' => null,
                'status' => 'active',
                'file_path' => 'documents/DOC-001/v1.0/Reglamento_Academico_2025.pdf',
                'version' => 2, // v1.0
                'versions' => [
                    [
                        'version_number' => 2,
                        'file_name' => 'Reglamento_Academico_2025.pdf',
                        'notes' => 'Versión inicial del reglamento académico 2025',
                    ],
                ],
            ],
            [
                'title' => 'Manual del Estudiante',
                'category' => 'academic',
                'entity_type' => 'general',
                'entity_id' => null,
                'status' => 'active',
                'file_path' => 'documents/DOC-002/v1.5/Manual_del_Estudiante.pdf',
                'version' => 3, // v1.5
                'versions' => [
                    [
                        'version_number' => 2,
                        'file_name' => 'Manual_del_Estudiante.pdf',
                        'notes' => 'Primera versión del manual',
                    ],
                    [
                        'version_number' => 3,
                        'file_name' => 'Manual_del_Estudiante.pdf',
                        'notes' => 'Actualización con nuevas políticas de convivencia',
                    ],
                ],
            ],
            [
                'title' => 'Plan de Estudios Ingeniería',
                'category' => 'academic',
                'entity_type' => 'general',
                'entity_id' => null,
                'status' => 'active',
                'file_path' => 'documents/DOC-003/v2.0/Plan_de_Estudios_Ingenieria.pdf',
                'version' => 4, // v2.0
                'versions' => [
                    [
                        'version_number' => 2,
                        'file_name' => 'Plan_de_Estudios_Ingenieria.pdf',
                        'notes' => 'Plan de estudios inicial',
                    ],
                    [
                        'version_number' => 3,
                        'file_name' => 'Plan_de_Estudios_Ingenieria.pdf',
                        'notes' => 'Actualización de créditos académicos',
                    ],
                    [
                        'version_number' => 4,
                        'file_name' => 'Plan_de_Estudios_Ingenieria.pdf',
                        'notes' => 'Revisión completa del plan - v2.0',
                    ],
                ],
            ],

            // DOCUMENTOS ADMINISTRATIVOS
            [
                'title' => 'Políticas de RRHH 2025',
                'category' => 'administrative',
                'entity_type' => 'general',
                'entity_id' => null,
                'status' => 'active',
                'file_path' => 'documents/DOC-004/v1.0/Politicas_de_RRHH_2025.pdf',
                'version' => 2, // v1.0
                'versions' => [
                    [
                        'version_number' => 2,
                        'file_name' => 'Politicas_de_RRHH_2025.pdf',
                        'notes' => 'Políticas actualizadas para el año 2025',
                    ],
                ],
            ],
            [
                'title' => 'Procedimientos de Compras',
                'category' => 'administrative',
                'entity_type' => 'general',
                'entity_id' => null,
                'status' => 'active',
                'file_path' => 'documents/DOC-005/v1.5/Procedimientos_de_Compras.pdf',
                'version' => 3, // v1.5
                'versions' => [
                    [
                        'version_number' => 2,
                        'file_name' => 'Procedimientos_de_Compras.pdf',
                        'notes' => 'Primera versión del manual de compras',
                    ],
                    [
                        'version_number' => 3,
                        'file_name' => 'Procedimientos_de_Compras.pdf',
                        'notes' => 'Actualización con nuevos proveedores autorizados',
                    ],
                ],
            ],
            [
                'title' => 'Manual de Procesos Internos',
                'category' => 'administrative',
                'entity_type' => 'general',
                'entity_id' => null,
                'status' => 'active',
                'file_path' => 'documents/DOC-006/v1.0/Manual_de_Procesos_Internos.pdf',
                'version' => 2, // v1.0
                'versions' => [
                    [
                        'version_number' => 2,
                        'file_name' => 'Manual_de_Procesos_Internos.pdf',
                        'notes' => 'Documentación de procesos operativos',
                    ],
                ],
            ],

            // DOCUMENTOS LEGALES
            [
                'title' => 'Contrato Marco Docentes',
                'category' => 'legal',
                'entity_type' => 'general',
                'entity_id' => null,
                'status' => 'active',
                'file_path' => 'documents/DOC-007/v2.0/Contrato_Marco_Docentes.pdf',
                'version' => 4, // v2.0
                'versions' => [
                    [
                        'version_number' => 2,
                        'file_name' => 'Contrato_Marco_Docentes.pdf',
                        'notes' => 'Versión inicial del contrato marco',
                    ],
                    [
                        'version_number' => 3,
                        'file_name' => 'Contrato_Marco_Docentes.pdf',
                        'notes' => 'Ajustes en cláusulas de renovación',
                    ],
                    [
                        'version_number' => 4,
                        'file_name' => 'Contrato_Marco_Docentes.pdf',
                        'notes' => 'Revisión legal completa - v2.0',
                    ],
                ],
            ],
            [
                'title' => 'Política de Protección de Datos',
                'category' => 'legal',
                'entity_type' => 'general',
                'entity_id' => null,
                'status' => 'active',
                'file_path' => 'documents/DOC-008/v1.0/Politica_de_Proteccion_de_Datos.pdf',
                'version' => 2, // v1.0
                'versions' => [
                    [
                        'version_number' => 2,
                        'file_name' => 'Politica_de_Proteccion_de_Datos.pdf',
                        'notes' => 'Cumplimiento de normativa de protección de datos',
                    ],
                ],
            ],
            [
                'title' => 'Convenio Institucional SUNEDU',
                'category' => 'legal',
                'entity_type' => 'general',
                'entity_id' => null,
                'status' => 'active',
                'file_path' => 'documents/DOC-009/v1.5/Convenio_Institucional_SUNEDU.pdf',
                'version' => 3, // v1.5
                'versions' => [
                    [
                        'version_number' => 2,
                        'file_name' => 'Convenio_Institucional_SUNEDU.pdf',
                        'notes' => 'Convenio inicial con SUNEDU',
                    ],
                    [
                        'version_number' => 3,
                        'file_name' => 'Convenio_Institucional_SUNEDU.pdf',
                        'notes' => 'Renovación del convenio con nuevas cláusulas',
                    ],
                ],
            ],
        ];

        // Insertar documentos
        foreach ($documents as $docData) {
            // Verificar si ya existe
            $exists = DB::table('documents')
                ->where('title', $docData['title'])
                ->exists();

            if ($exists) {
                $this->command->warn("⚠ Documento '{$docData['title']}' ya existe. Saltando...");
                continue;
            }

            // Extraer versiones para insertarlas después
            $versions = $docData['versions'];
            unset($docData['versions']);

            // Insertar documento
            $documentId = DB::table('documents')->insertGetId([
                'title' => $docData['title'],
                'category' => $docData['category'],
                'entity_type' => $docData['entity_type'],
                'entity_id' => $docData['entity_id'],
                'version' => $docData['version'],
                'status' => $docData['status'],
                'file_path' => $docData['file_path'],
                'download_count' => 0,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insertar versiones
            if (Schema::hasTable('document_versions')) {
                foreach ($versions as $index => $versionData) {
                    DB::table('document_versions')->insert([
                        'document_id' => $documentId,
                        'version_number' => $versionData['version_number'],
                        'file_name' => $versionData['file_name'],
                        'storage_path' => str_replace(
                            ['v1.0', 'v1.5', 'v2.0'],
                            'v' . number_format($versionData['version_number'] / 2, 1),
                            $docData['file_path']
                        ),
                        'mime_type' => 'application/pdf',
                        'file_size' => rand(100000, 5000000), // Tamaño simulado
                        'uploaded_by_user_id' => $userId,
                        'uploaded_at' => now()->subDays(count($versions) - $index),
                        'checksum' => md5($versionData['file_name'] . time()),
                        'notes' => $versionData['notes'],
                        'linked_type' => null,
                        'linked_id' => null,
                        'created_at' => now()->subDays(count($versions) - $index),
                    ]);
                }
            }

            // Crear historial
            if (Schema::hasTable('document_history')) {
                foreach ($versions as $index => $versionData) {
                    DB::table('document_history')->insert([
                        'document_id' => $documentId,
                        'version' => $versionData['version_number'],
                        'change_description' => $versionData['notes'],
                        'changed_by' => $userId,
                        'created_at' => now()->subDays(count($versions) - $index),
                    ]);
                }
            }

            $this->command->info("✓ Documento '{$docData['title']}' creado con " . count($versions) . " versión(es)");
        }

        $this->command->info('✅ DocumentSeeder completado exitosamente');

        /**
         * PAYMENTS
         */


        if (! Schema::hasTable('payments') || ! Schema::hasTable('invoices')) {
            return;
        }

        DB::transaction(function () {
            $periodId = $this->ensureAcademicPeriod();
            $methods = $this->ensurePaymentMethods();
            $students = $this->ensureStudents();
            $enrollments = $this->ensureEnrollments($students, $periodId);
            $invoices = $this->ensureInvoices($students, $enrollments);
            $this->ensurePayments($invoices, $methods, $students);
        });
    }

    private function ensureAcademicPeriod(): ?int
    {
        if (! Schema::hasTable('academic_periods')) {
            return null;
        }

        $existing = DB::table('academic_periods')->where('name', '2025-II')->first();

        if ($existing) {
            return $existing->id;
        }

        return DB::table('academic_periods')->insertGetId([
            'academic_period_id' => 20252,
            'name' => '2025-II',
            'start_date' => now()->startOfYear()->toDateString(),
            'end_date' => now()->endOfYear()->toDateString(),
            'status' => 'open',
            'created_at' => now(),
        ]);
    }

    private function ensurePaymentMethods(): array
    {
        if (! Schema::hasTable('payment_methods')) {
            return [];
        }

        $definitions = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];

        $ids = [];

        foreach ($definitions as $key => $name) {
            $existing = DB::table('payment_methods')->where('name', $name)->first();

            if ($existing) {
                $ids[$key] = $existing->id;
                continue;
            }

            $ids[$key] = DB::table('payment_methods')->insertGetId([
                'name' => $name,
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $ids;
    }

    private function ensureStudents(): array
    {
        if (! Schema::hasTable('students')) {
            return [];
        }

        $records = [
            'maria' => ['first_name' => 'María', 'last_name' => 'González', 'email' => 'maria.gonzalez@example.com'],
            'carlos' => ['first_name' => 'Carlos', 'last_name' => 'Ramírez', 'email' => 'carlos.ramirez@example.com'],
            'ana' => ['first_name' => 'Ana', 'last_name' => 'Martínez', 'email' => 'ana.martinez@example.com'],
            'luis' => ['first_name' => 'Luis', 'last_name' => 'Fernández', 'email' => 'luis.fernandez@example.com'],
            'sofia' => ['first_name' => 'Sofía', 'last_name' => 'Torres', 'email' => 'sofia.torres@example.com'],
        ];

        $ids = [];

        $sequence = 1;

        foreach ($records as $key => $data) {
            $existing = DB::table('students')->where('email', $data['email'])->first();

            if ($existing) {
                $ids[$key] = $existing->id;
                continue;
            }

            $ids[$key] = DB::table('students')->insertGetId([
                'student_id' => 3000 + $sequence++,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'document_number' => (string) fake()->unique()->numberBetween(70000000, 79999999),
                'status' => 'active',
                'created_at' => now(),
            ]);
        }

        return $ids;
    }

    private function ensureEnrollments(array $students, ?int $periodId): array
    {
        if (! Schema::hasTable('enrollments')) {
            return [];
        }

        $ids = [];
        $sequence = 1;
        $now = now();

        foreach ($students as $key => $studentId) {
            $existing = DB::table('enrollments')->where('student_id', $studentId)->first();

            if ($existing) {
                $ids[$key] = $existing->id;
                continue;
            }

            $payload = [
                'enrollment_id' => 7000 + $sequence++,
                'student_id' => $studentId,
                'academic_period_id' => $periodId,
                'enrollment_type' => 'regular',
                'enrollment_date' => $now->copy()->subMonths(3)->toDateString(),
                'status' => 'active',
                'created_at' => $now,
            ];

            $ids[$key] = DB::table('enrollments')->insertGetId($payload);
        }

        return $ids;
    }

    private function ensureInvoices(array $students, array $enrollments): array
    {
        $samples = $this->paymentSamples();
        $ids = [];

        foreach ($samples as $sample) {
            $studentId = $students[$sample['student_key']] ?? null;
            $enrollmentId = $enrollments[$sample['student_key']] ?? null;

            $existing = DB::table('invoices')->where('invoice_number', $sample['invoice_number'])->first();

            $payload = [
                'enrollment_id' => $enrollmentId,
                'invoice_number' => $sample['invoice_number'],
                'issue_date' => $sample['issue_date']->toDateString(),
                'total_amount' => $sample['total'],
                'status' => $sample['invoice_status'],
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('invoices', 'student_id')) {
                $payload['student_id'] = $studentId;
            }

            if ($existing) {
                DB::table('invoices')->where('id', $existing->id)->update($payload);
                $ids[$sample['invoice_number']] = $existing->id;
                continue;
            }

            $payload['created_at'] = now();

            $ids[$sample['invoice_number']] = DB::table('invoices')->insertGetId($payload);
        }

        return $ids;
    }

    private function ensurePayments(array $invoices, array $methods, array $students): void
    {
        $samples = $this->paymentSamples();

        foreach ($samples as $sample) {
            $invoiceId = $invoices[$sample['invoice_number']] ?? null;
            $methodId = $methods[$sample['method_key']] ?? null;
            $studentId = $students[$sample['student_key']] ?? null;

            $payload = [
                'invoice_id' => $invoiceId,
                'payment_method_id' => $methodId,
                'amount' => $sample['amount'],
                'payment_date' => $sample['payment_date']->toDateString(),
                'status' => $sample['payment_status'],
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('payments', 'student_id')) {
                $payload['student_id'] = $studentId;
            }

            if (Schema::hasColumn('payments', 'student_name')) {
                $payload['student_name'] = $sample['student_name'];
            }

            if (Schema::hasColumn('payments', 'invoice_number')) {
                $payload['invoice_number'] = $sample['invoice_number'];
            }

            $existingQuery = DB::table('payments')
                ->when($invoiceId, fn ($query) => $query->where('invoice_id', $invoiceId));

            if (! $invoiceId && Schema::hasColumn('payments', 'invoice_number')) {
                $existingQuery->where('invoice_number', $sample['invoice_number']);
            }

            if (! $invoiceId && ! Schema::hasColumn('payments', 'invoice_number')) {
                $existingQuery
                    ->whereDate('payment_date', $sample['payment_date']->toDateString())
                    ->where('amount', $sample['amount']);
            }

            $existing = $existingQuery->first();

            if ($existing) {
                DB::table('payments')->where('id', $existing->id)->update($payload);
                continue;
            }

            $payload['created_at'] = now();
            DB::table('payments')->insert($payload);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function paymentSamples(): array
    {
        return [
            [
                'student_key' => 'maria',
                'student_name' => 'María González',
                'invoice_number' => 'INV-2025-001',
                'total' => 450.00,
                'invoice_status' => 'Paid',
                'issue_date' => Carbon::parse('2025-09-05'),
                'amount' => 450.00,
                'payment_status' => 'Completed',
                'payment_date' => Carbon::parse('2025-09-10'),
                'method_key' => 'transfer',
            ],
            [
                'student_key' => 'carlos',
                'student_name' => 'Carlos Ramírez',
                'invoice_number' => 'INV-2025-002',
                'total' => 380.00,
                'invoice_status' => 'Paid',
                'issue_date' => Carbon::parse('2025-09-08'),
                'amount' => 380.00,
                'payment_status' => 'Completed',
                'payment_date' => Carbon::parse('2025-09-12'),
                'method_key' => 'card',
            ],
            [
                'student_key' => 'ana',
                'student_name' => 'Ana Martínez',
                'invoice_number' => 'INV-2025-003',
                'total' => 450.00,
                'invoice_status' => 'Pending',
                'issue_date' => Carbon::parse('2025-09-12'),
                'amount' => 450.00,
                'payment_status' => 'Pending',
                'payment_date' => Carbon::parse('2025-00-15'),
                'method_key' => 'cash',
            ],
            [
                'student_key' => 'luis',
                'student_name' => 'Luis Fernández',
                'invoice_number' => 'INV-2025-004',
                'total' => 520.00,
                'invoice_status' => 'Paid',
                'issue_date' => Carbon::parse('2025-09-15'),
                'amount' => 520.00,
                'payment_status' => 'Completed',
                'payment_date' => Carbon::parse('2025-09-18'),
                'method_key' => 'transfer',
            ],
            [
                'student_key' => 'sofia',
                'student_name' => 'Sofía Torres',
                'invoice_number' => 'INV-2025-005',
                'total' => 380.00,
                'invoice_status' => 'Pending',
                'issue_date' => Carbon::parse('2025-10-03'),
                'amount' => 380.00,
                'payment_status' => 'Pending',
                'payment_date' => Carbon::parse('2025-10-05'),
                'method_key' => 'card',
            ],
            [
                'student_key' => 'maria',
                'student_name' => 'María González',
                'invoice_number' => 'INV-2025-010',
                'total' => 560.00,
                'invoice_status' => 'Paid',
                'issue_date' => Carbon::parse('2025-10-10'),
                'amount' => 560.00,
                'payment_status' => 'Completed',
                'payment_date' => Carbon::parse('2025-10-13'),
                'method_key' => 'transfer',
            ],
            [
                'student_key' => 'carlos',
                'student_name' => 'Carlos Ramírez',
                'invoice_number' => 'INV-2025-011',
                'total' => 420.00,
                'invoice_status' => 'Paid',
                'issue_date' => Carbon::parse('2025-10-31'),
                'amount' => 420.00,
                'payment_status' => 'Completed',
                'payment_date' => Carbon::parse('2025-11-04'),
                'method_key' => 'card',
            ],
            [
                'student_key' => 'ana',
                'student_name' => 'Ana Martínez',
                'invoice_number' => 'INV-2025-012',
                'total' => 610.00,
                'invoice_status' => 'Paid',
                'issue_date' => Carbon::parse('2025-11-28'),
                'amount' => 610.00,
                'payment_status' => 'Completed',
                'payment_date' => Carbon::parse('2025-12-02'),
                'method_key' => 'cash',
            ],
        ];
    }
}