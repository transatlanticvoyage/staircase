-- Add anteheader_desired column to wp_pylons if it doesn't exist
-- Note: MySQL doesn't support IF NOT EXISTS with ADD COLUMN
-- You need to check first if column exists
ALTER TABLE wp_pylons 
ADD COLUMN anteheader_desired TEXT DEFAULT NULL;

-- Add site_default_anteheader column to wp_zen_sitespren if it doesn't exist
ALTER TABLE wp_zen_sitespren 
ADD COLUMN site_default_anteheader TEXT DEFAULT NULL;

-- Verify the columns were added
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'wp_pylons' 
AND COLUMN_NAME = 'anteheader_desired';

SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'wp_zen_sitespren' 
AND COLUMN_NAME = 'site_default_anteheader';