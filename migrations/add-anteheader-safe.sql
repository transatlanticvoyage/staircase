-- Safe migration script for adding anteheader columns
-- This script checks if columns exist before adding them

-- For wp_pylons.anteheader_desired
SET @dbname = DATABASE();
SET @tablename = 'wp_pylons';
SET @columnname = 'anteheader_desired';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE table_name = @tablename
    AND table_schema = @dbname
    AND column_name = @columnname
  ) > 0,
  'SELECT "Column anteheader_desired already exists in wp_pylons" AS Status;',
  'ALTER TABLE wp_pylons ADD COLUMN anteheader_desired TEXT DEFAULT NULL;'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- For wp_zen_sitespren.site_default_anteheader
SET @tablename = 'wp_zen_sitespren';
SET @columnname = 'site_default_anteheader';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE table_name = @tablename
    AND table_schema = @dbname
    AND column_name = @columnname
  ) > 0,
  'SELECT "Column site_default_anteheader already exists in wp_zen_sitespren" AS Status;',
  'ALTER TABLE wp_zen_sitespren ADD COLUMN site_default_anteheader TEXT DEFAULT NULL;'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Verify the columns were added
SELECT 
    'wp_pylons.anteheader_desired' as Column_Check,
    IF(COUNT(*) > 0, 'EXISTS', 'MISSING') as Status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'wp_pylons' 
AND COLUMN_NAME = 'anteheader_desired'

UNION ALL

SELECT 
    'wp_zen_sitespren.site_default_anteheader' as Column_Check,
    IF(COUNT(*) > 0, 'EXISTS', 'MISSING') as Status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'wp_zen_sitespren' 
AND COLUMN_NAME = 'site_default_anteheader';