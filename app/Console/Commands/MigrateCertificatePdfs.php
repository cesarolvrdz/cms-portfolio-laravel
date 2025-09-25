<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\UploadedFile;

class MigrateCertificatePdfs extends Command
{
    protected $signature = 'certificates:migrate-pdfs {--test : Run in test mode without actual upload}';
    protected $description = 'Migrate/add PDFs to existing certificates';

    protected $supabaseService;

    public function __construct(SupabaseServiceOptimized $supabaseService)
    {
        parent::__construct();
        $this->supabaseService = $supabaseService;
    }

    public function handle()
    {
        $this->info('🔍 Analizando certificados existentes...');

        try {
            // Obtener todos los certificados
            $certificates = $this->supabaseService->getCertificates(false);

            if (empty($certificates)) {
                $this->error('❌ No se encontraron certificados en la base de datos');
                return 1;
            }

            $this->info("📊 Encontrados " . count($certificates) . " certificados");

            // Separar certificados con y sin PDF
            $withPdf = [];
            $withoutPdf = [];

            foreach ($certificates as $cert) {
                if (!empty($cert['pdf_url']) && str_contains($cert['pdf_url'], 'supabase.co')) {
                    $withPdf[] = $cert;
                } else {
                    $withoutPdf[] = $cert;
                }
            }

            $this->info("✅ Con PDF en Supabase: " . count($withPdf));
            $this->info("⚠️  Sin PDF en Supabase: " . count($withoutPdf));

            if (!empty($withPdf)) {
                $this->newLine();
                $this->info("📋 Certificados que YA tienen PDF en Supabase:");
                foreach ($withPdf as $cert) {
                    $this->line("   • {$cert['title']} - {$cert['institution']}");
                    $this->line("     URL: {$cert['pdf_url']}");
                }
            }

            if (!empty($withoutPdf)) {
                $this->newLine();
                $this->info("📋 Certificados SIN PDF en Supabase:");
                foreach ($withoutPdf as $cert) {
                    $this->line("   • ID: {$cert['id']} - {$cert['title']} - {$cert['institution']}");
                    if (!empty($cert['pdf_url'])) {
                        $this->line("     PDF local/externo: {$cert['pdf_url']}");
                    } else {
                        $this->line("     Sin PDF");
                    }
                }

                $this->newLine();
                if ($this->confirm('¿Quieres agregar PDFs manualmente a estos certificados?')) {
                    $this->handleManualPdfUpload($withoutPdf);
                }
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function handleManualPdfUpload($certificates)
    {
        $this->newLine();
        $this->info('📁 Proceso de carga manual de PDFs');
        $this->line('Coloca tus archivos PDF en: storage/app/certificates/');

        // Crear directorio si no existe
        $certificatesPath = storage_path('app/certificates');
        if (!is_dir($certificatesPath)) {
            mkdir($certificatesPath, 0755, true);
            $this->info("📁 Directorio creado: {$certificatesPath}");
        }

        // Listar archivos PDF disponibles
        $pdfFiles = glob($certificatesPath . '/*.pdf');

        if (empty($pdfFiles)) {
            $this->warn('⚠️  No se encontraron archivos PDF en storage/app/certificates/');
            $this->info('   Coloca tus PDFs allí y ejecuta el comando nuevamente');
            return;
        }

        $this->info('📄 Archivos PDF encontrados:');
        foreach ($pdfFiles as $index => $file) {
            $this->line('   ' . ($index + 1) . '. ' . basename($file));
        }

        $this->newLine();

        foreach ($certificates as $cert) {
            $this->info("🔄 Procesando: {$cert['title']} - {$cert['institution']}");

            if ($this->confirm("¿Agregar PDF a este certificado?")) {
                $this->assignPdfToCertificate($cert, $pdfFiles);
            }

            $this->newLine();
        }
    }

    private function assignPdfToCertificate($certificate, $pdfFiles)
    {
        $this->info('📄 Archivos PDF disponibles:');
        foreach ($pdfFiles as $index => $file) {
            $this->line('   ' . ($index + 1) . '. ' . basename($file));
        }

        $choice = $this->ask('Selecciona el número del PDF (0 para saltar)');

        if ($choice == '0' || !is_numeric($choice)) {
            $this->line('   ⏭️  Saltando...');
            return;
        }

        $fileIndex = (int)$choice - 1;

        if (!isset($pdfFiles[$fileIndex])) {
            $this->error('   ❌ Selección inválida');
            return;
        }

        $selectedFile = $pdfFiles[$fileIndex];

        if ($this->option('test')) {
            $this->line("   🧪 [TEST MODE] Se subiría: " . basename($selectedFile));
            return;
        }

        try {
            // Crear UploadedFile temporal
            $originalName = basename($selectedFile);
            $tempPath = $selectedFile;

            $uploadedFile = new UploadedFile(
                $tempPath,
                $originalName,
                'application/pdf',
                null,
                true // test mode
            );

            // Generar nombre único
            $pdfName = 'certificate_' . $certificate['id'] . '_' . time() . '.pdf';

            $this->line('   ⬆️  Subiendo PDF a Supabase...');

            // Subir usando el servicio
            $pdfUrl = $this->supabaseService->uploadPdf($uploadedFile, $pdfName, 'certificates');

            if ($pdfUrl) {
                $this->line("   ✅ PDF subido exitosamente");
                $this->line("   🔗 URL: {$pdfUrl}");

                // Actualizar certificado en base de datos
                $updateData = ['pdf_url' => $pdfUrl];
                $updateResult = $this->supabaseService->updateCertificate($certificate['id'], $updateData);

                if ($updateResult) {
                    $this->line("   ✅ Certificado actualizado en base de datos");

                    // Preguntar si eliminar archivo local
                    if ($this->confirm('   🗑️  ¿Eliminar archivo local?')) {
                        unlink($selectedFile);
                        $this->line("   🗑️  Archivo local eliminado");
                    }
                } else {
                    $this->error("   ❌ Error actualizando certificado en base de datos");
                }
            } else {
                $this->error("   ❌ Error subiendo PDF");
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Error: " . $e->getMessage());
        }
    }
}
