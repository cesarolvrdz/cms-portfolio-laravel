-- Tabla para documentos CV
CREATE TABLE cv_documents (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL DEFAULT 'Mi CV',
    description TEXT,
    file_url VARCHAR(500) NOT NULL,
    version VARCHAR(50) DEFAULT '1.0',
    file_size INTEGER, -- Tamaño del archivo en bytes
    file_type VARCHAR(20) DEFAULT 'pdf',
    language VARCHAR(10) DEFAULT 'es', -- Idioma del CV (es, en, etc.)
    is_current BOOLEAN DEFAULT false, -- Solo uno puede ser current
    download_count INTEGER DEFAULT 0, -- Contador de descargas
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Índices para mejor rendimiento
CREATE INDEX idx_cv_current ON cv_documents(is_current);
CREATE INDEX idx_cv_language ON cv_documents(language);
CREATE INDEX idx_cv_created ON cv_documents(created_at DESC);

-- RLS (Row Level Security) para Supabase
ALTER TABLE cv_documents ENABLE ROW LEVEL SECURITY;

-- Política para lectura pública del CV actual
CREATE POLICY "Current CV is viewable by everyone"
ON cv_documents FOR SELECT
USING (is_current = true);

-- Política para operaciones administrativas (insertar, actualizar, eliminar)
CREATE POLICY "Enable all operations for service role"
ON cv_documents FOR ALL
USING (auth.role() = 'service_role');

-- Trigger para asegurar que solo un CV sea current por idioma
CREATE OR REPLACE FUNCTION ensure_single_current_cv()
RETURNS TRIGGER AS $$
BEGIN
    -- Si se está marcando como current
    IF NEW.is_current = true THEN
        -- Desactivar otros CV current del mismo idioma
        UPDATE cv_documents
        SET is_current = false
        WHERE is_current = true
        AND language = NEW.language
        AND id != NEW.id;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_ensure_single_current_cv
    BEFORE INSERT OR UPDATE ON cv_documents
    FOR EACH ROW
    EXECUTE FUNCTION ensure_single_current_cv();

-- Función para incrementar contador de descargas
CREATE OR REPLACE FUNCTION increment_download_count(cv_id INTEGER)
RETURNS void AS $$
BEGIN
    UPDATE cv_documents
    SET download_count = download_count + 1,
        updated_at = NOW()
    WHERE id = cv_id;
END;
$$ LANGUAGE plpgsql;

-- Comentarios para documentación
COMMENT ON TABLE cv_documents IS 'Tabla para almacenar diferentes versiones del CV';
COMMENT ON COLUMN cv_documents.is_current IS 'Solo un CV puede estar activo por idioma';
COMMENT ON COLUMN cv_documents.language IS 'Idioma del CV para soporte multiidioma';
COMMENT ON COLUMN cv_documents.download_count IS 'Contador de descargas para estadísticas';
COMMENT ON FUNCTION increment_download_count IS 'Función para incrementar contador de descargas';
