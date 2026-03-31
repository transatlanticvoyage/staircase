-- Add site_default_anteheader column to sitespren table in Supabase
-- Run this in Supabase SQL Editor

-- Add the column
ALTER TABLE sitespren 
ADD COLUMN IF NOT EXISTS site_default_anteheader TEXT;

-- Add documentation comment
COMMENT ON COLUMN sitespren.site_default_anteheader IS 'Default anteheader template to use site-wide (e.g., anteheader1, anteheader2)';

-- Verify the column was added successfully
SELECT 
    column_name, 
    data_type, 
    is_nullable,
    column_default,
    col_description(pgc.oid, pa.attnum) as comment
FROM information_schema.columns
JOIN pg_class pgc ON pgc.relname = 'sitespren'
JOIN pg_attribute pa ON pa.attrelid = pgc.oid AND pa.attname = 'site_default_anteheader'
WHERE table_schema = 'public'
AND table_name = 'sitespren'
AND column_name = 'site_default_anteheader';