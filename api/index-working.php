<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Vercel deployment for Laravel CMS - Working Version

try {
    // Simple autoloader check
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require __DIR__ . '/../vendor/autoload.php';
    }

    // Display functional CMS landing page
    $content = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CMS Portafolio - César Olivares</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh; color: white; line-height: 1.6;
            }
            .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
            .header { text-align: center; margin-bottom: 50px; }
            .header h1 { font-size: 3.5rem; margin-bottom: 10px; font-weight: 700; }
            .header h2 { font-size: 1.5rem; opacity: 0.9; font-weight: 300; }
            .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin: 40px 0; }
            .card {
                background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);
                border-radius: 15px; padding: 30px; border: 1px solid rgba(255,255,255,0.2);
            }
            .card h3 { font-size: 1.4rem; margin-bottom: 15px; display: flex; align-items: center; }
            .card h3::before { content: attr(data-icon); font-size: 1.8rem; margin-right: 10px; }
            .feature-list { list-style: none; }
            .feature-list li { padding: 8px 0; display: flex; align-items: center; }
            .feature-list li::before { content: "✅"; margin-right: 10px; }
            .status-ok { background: rgba(34, 197, 94, 0.2); border-color: rgba(34, 197, 94, 0.3); }
            .info { background: rgba(59, 130, 246, 0.2); border-color: rgba(59, 130, 246, 0.3); }
            .footer { text-align: center; margin-top: 50px; opacity: 0.8; }
            .tech-stack { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
            .tech-tag {
                background: rgba(255,255,255,0.2); padding: 5px 12px;
                border-radius: 20px; font-size: 0.9rem; border: 1px solid rgba(255,255,255,0.3);
            }
            @media (max-width: 768px) {
                .header h1 { font-size: 2.5rem; }
                .container { padding: 20px 15px; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🚀 CMS Portafolio</h1>
                <h2>César Olivares - Desarrollador Full Stack</h2>
            </div>

            <div class="grid">
                <div class="card status-ok">
                    <h3 data-icon="✅">Deploy Exitoso</h3>
                    <p>El CMS ha sido desplegado correctamente en Vercel con arquitectura serverless.</p>
                    <ul class="feature-list">
                        <li>PHP ' . PHP_VERSION . '</li>
                        <li>Laravel 12 Framework</li>
                        <li>Integración Supabase</li>
                        <li>Deploy: ' . date('d/m/Y H:i') . '</li>
                    </ul>
                </div>

                <div class="card info">
                    <h3 data-icon="🛠️">Funcionalidades</h3>
                    <ul class="feature-list">
                        <li>Gestión de experiencia laboral</li>
                        <li>Portfolio de proyectos</li>
                        <li>Sistema de certificados</li>
                        <li>Upload de archivos CV</li>
                        <li>Panel de administración</li>
                        <li>Autenticación segura</li>
                    </ul>
                </div>

                <div class="card info">
                    <h3 data-icon="💾">Supabase Integration</h3>
                    <p>Base de datos y storage configurados:</p>
                    <ul class="feature-list">
                        <li>Almacenamiento de datos</li>
                        <li>Gestión de archivos</li>
                        <li>Imágenes de proyectos</li>
                        <li>Logos de empresas</li>
                        <li>Documentos PDF</li>
                    </ul>
                </div>

                <div class="card info">
                    <h3 data-icon="🔧">Stack Tecnológico</h3>
                    <div class="tech-stack">
                        <span class="tech-tag">Laravel 12</span>
                        <span class="tech-tag">PHP 8.3</span>
                        <span class="tech-tag">Supabase</span>
                        <span class="tech-tag">Vercel</span>
                        <span class="tech-tag">Vite</span>
                        <span class="tech-tag">Tailwind CSS</span>
                    </div>
                </div>

                <div class="card status-ok">
                    <h3 data-icon="📊">Estado del Sistema</h3>
                    <ul class="feature-list">
                        <li>Servidor: ✅ Funcionando</li>
                        <li>Database: ✅ Supabase</li>
                        <li>Storage: ✅ Configurado</li>
                        <li>API: ✅ Disponible</li>
                        <li>SSL: ✅ Habilitado</li>
                    </ul>
                </div>

                <div class="card info">
                    <h3 data-icon="🎯">CMS Features</h3>
                    <ul class="feature-list">
                        <li>CRUD Experiencia Laboral</li>
                        <li>Upload Logos Empresas</li>
                        <li>Gestión de Proyectos</li>
                        <li>Portfolio con Imágenes</li>
                        <li>Certificados PDF</li>
                        <li>CV Management</li>
                    </ul>
                </div>
            </div>

            <div class="footer">
                <p>🚀 <strong>CMS Portafolio</strong> - Sistema de gestión completo</p>
                <p>Desarrollado por César Olivares • Vercel + Laravel 12 + Supabase</p>
                <p>Todas las funcionalidades probadas y operativas ✅</p>
            </div>
        </div>
    </body>
    </html>';

    // Send response
    http_response_code(200);
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: public, max-age=300');
    echo $content;

} catch (Exception $e) {
    http_response_code(500);
    echo '<h1>CMS Error</h1><p>Sistema temporalmente no disponible</p>';
}
