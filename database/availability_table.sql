-- ===== TABLA DE DISPONIBILIDAD =====
-- Esta tabla gestiona el estado de disponibilidad del portafolio

CREATE TABLE availability (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    status VARCHAR(20) NOT NULL DEFAULT 'available', -- 'available', 'busy', 'unavailable'
    response_time VARCHAR(50) NOT NULL DEFAULT '24 horas', -- Tiempo estimado de respuesta
    custom_message TEXT, -- Mensaje personalizado opcional
    availability_details JSONB, -- Detalles adicionales en formato JSON
    show_calendar_link BOOLEAN DEFAULT true, -- Mostrar enlace de calendario
    calendar_url TEXT, -- URL del calendario si aplica
    last_updated TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    is_active BOOLEAN DEFAULT true, -- Para mantener solo un registro activo
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Insertar registro inicial de disponibilidad
INSERT INTO availability (
    status,
    response_time,
    custom_message,
    availability_details,
    show_calendar_link,
    is_active
) VALUES (
    'available',
    '24 horas',
    'Actualmente disponible para nuevos proyectos',
    '{"preferred_contact": "email", "timezone": "America/Mexico_City", "working_hours": "9:00 AM - 6:00 PM"}',
    true,
    true
);

-- Trigger para actualizar updated_at
CREATE OR REPLACE FUNCTION update_availability_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_availability_updated_at
    BEFORE UPDATE ON availability
    FOR EACH ROW
    EXECUTE FUNCTION update_availability_updated_at();

-- Política de seguridad para availability (RLS)
ALTER TABLE availability ENABLE ROW LEVEL SECURITY;

-- Política para lectura pública (para el portafolio)
CREATE POLICY "availability_select_policy" ON availability
    FOR SELECT USING (true);

-- Política para escritura solo con service role
CREATE POLICY "availability_write_policy" ON availability
    FOR ALL USING (auth.role() = 'service_role');

-- Comentarios para documentación
COMMENT ON TABLE availability IS 'Gestiona el estado de disponibilidad del portafolio';
COMMENT ON COLUMN availability.status IS 'Estado actual: available, busy, unavailable';
COMMENT ON COLUMN availability.response_time IS 'Tiempo estimado de respuesta';
COMMENT ON COLUMN availability.custom_message IS 'Mensaje personalizado para mostrar';
COMMENT ON COLUMN availability.availability_details IS 'Detalles adicionales en formato JSON';
COMMENT ON COLUMN availability.show_calendar_link IS 'Si mostrar enlace de calendario';
COMMENT ON COLUMN availability.is_active IS 'Solo un registro debe estar activo';
