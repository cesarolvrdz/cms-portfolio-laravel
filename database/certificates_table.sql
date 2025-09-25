-- Tabla para certificados/cursos
CREATE TABLE certificates (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    institution VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(500),
    pdf_url VARCHAR(500),
    issue_date DATE,
    expiry_date DATE,
    credential_id VARCHAR(100),
    credential_url VARCHAR(500),
    skills TEXT[], -- Array de habilidades destacadas
    category VARCHAR(100) DEFAULT 'general', -- Categoría del certificado
    order_position INTEGER DEFAULT 0,
    is_featured BOOLEAN DEFAULT false, -- Para destacar certificados importantes
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Índices para mejor rendimiento
CREATE INDEX idx_certificates_active ON certificates(is_active);
CREATE INDEX idx_certificates_featured ON certificates(is_featured);
CREATE INDEX idx_certificates_category ON certificates(category);
CREATE INDEX idx_certificates_order ON certificates(order_position);
CREATE INDEX idx_certificates_issue_date ON certificates(issue_date DESC);

-- RLS (Row Level Security) para Supabase
ALTER TABLE certificates ENABLE ROW LEVEL SECURITY;

-- Política para lectura pública
CREATE POLICY "Certificates are viewable by everyone"
ON certificates FOR SELECT
USING (is_active = true);

-- Política para operaciones administrativas (insertar, actualizar, eliminar)
CREATE POLICY "Enable all operations for service role"
ON certificates FOR ALL
USING (auth.role() = 'service_role');

-- Comentarios para documentación
COMMENT ON TABLE certificates IS 'Tabla para almacenar certificados y cursos completados';
COMMENT ON COLUMN certificates.skills IS 'Array de habilidades/tecnologías destacadas en el certificado';
COMMENT ON COLUMN certificates.category IS 'Categoría del certificado (programming, design, marketing, etc.)';
COMMENT ON COLUMN certificates.is_featured IS 'Marca certificados importantes para destacar en el portafolio';
COMMENT ON COLUMN certificates.credential_id IS 'ID único del certificado proporcionado por la institución';
COMMENT ON COLUMN certificates.credential_url IS 'URL para verificar el certificado online';
