-- Migration script to update template names from old to new format
-- Run this script to update existing database values to the new naming convention

-- Update wp_pylons table (page-specific settings)
UPDATE wp_pylons 
SET header_desired = CASE header_desired
    WHEN 'homeservice_header_1' THEN 'header1'
    WHEN 'homeservice_header_2' THEN 'header2'
    WHEN 'homeservice_header_3' THEN 'header3'
    WHEN 'casino_header_1' THEN 'header1'
    WHEN 'casino_header_2' THEN 'header1'
    ELSE header_desired
END
WHERE header_desired IN ('homeservice_header_1', 'homeservice_header_2', 'homeservice_header_3', 'casino_header_1', 'casino_header_2');

UPDATE wp_pylons 
SET footer_desired = CASE footer_desired
    WHEN 'homeservice_footer_1' THEN 'footer1'
    WHEN 'homeservice_footer_2' THEN 'footer2'
    WHEN 'homeservice_footer_3' THEN 'footer3'
    WHEN 'casino_footer_1' THEN 'footer1'
    WHEN 'casino_footer_2' THEN 'footer1'
    ELSE footer_desired
END
WHERE footer_desired IN ('homeservice_footer_1', 'homeservice_footer_2', 'homeservice_footer_3', 'casino_footer_1', 'casino_footer_2');

UPDATE wp_pylons 
SET sidebar_desired = CASE sidebar_desired
    WHEN 'homeservice_sidebar_1' THEN 'sidebar1'
    WHEN 'homeservice_sidebar_2' THEN 'sidebar2'
    WHEN 'casino_sidebar_1' THEN 'sidebar1'
    ELSE sidebar_desired
END
WHERE sidebar_desired IN ('homeservice_sidebar_1', 'homeservice_sidebar_2', 'casino_sidebar_1');

-- Only update anteheader if the column exists
-- UPDATE wp_pylons 
-- SET anteheader_desired = CASE anteheader_desired
--     WHEN 'homeservice_anteheader_1' THEN 'anteheader1'
--     WHEN 'homeservice_anteheader_2' THEN 'anteheader2'
--     WHEN 'casino_anteheader_1' THEN NULL
--     ELSE anteheader_desired
-- END
-- WHERE anteheader_desired IN ('homeservice_anteheader_1', 'homeservice_anteheader_2', 'casino_anteheader_1');

-- Update wp_zen_sitespren table (site-wide defaults)
UPDATE wp_zen_sitespren 
SET site_default_header = CASE site_default_header
    WHEN 'homeservice_header_1' THEN 'header1'
    WHEN 'homeservice_header_2' THEN 'header2'
    WHEN 'homeservice_header_3' THEN 'header3'
    WHEN 'casino_header_1' THEN 'header1'
    WHEN 'casino_header_2' THEN 'header1'
    ELSE site_default_header
END
WHERE site_default_header IN ('homeservice_header_1', 'homeservice_header_2', 'homeservice_header_3', 'casino_header_1', 'casino_header_2');

UPDATE wp_zen_sitespren 
SET site_default_footer = CASE site_default_footer
    WHEN 'homeservice_footer_1' THEN 'footer1'
    WHEN 'homeservice_footer_2' THEN 'footer2'
    WHEN 'homeservice_footer_3' THEN 'footer3'
    WHEN 'casino_footer_1' THEN 'footer1'
    WHEN 'casino_footer_2' THEN 'footer1'
    ELSE site_default_footer
END
WHERE site_default_footer IN ('homeservice_footer_1', 'homeservice_footer_2', 'homeservice_footer_3', 'casino_footer_1', 'casino_footer_2');

UPDATE wp_zen_sitespren 
SET site_default_sidebar = CASE site_default_sidebar
    WHEN 'homeservice_sidebar_1' THEN 'sidebar1'
    WHEN 'homeservice_sidebar_2' THEN 'sidebar2'
    WHEN 'casino_sidebar_1' THEN 'sidebar1'
    ELSE site_default_sidebar
END
WHERE site_default_sidebar IN ('homeservice_sidebar_1', 'homeservice_sidebar_2', 'casino_sidebar_1');

-- Only update anteheader if the column exists
-- UPDATE wp_zen_sitespren 
-- SET site_default_anteheader = CASE site_default_anteheader
--     WHEN 'homeservice_anteheader_1' THEN 'anteheader1'
--     WHEN 'homeservice_anteheader_2' THEN 'anteheader2'
--     WHEN 'casino_anteheader_1' THEN NULL
--     ELSE site_default_anteheader
-- END
-- WHERE site_default_anteheader IN ('homeservice_anteheader_1', 'homeservice_anteheader_2', 'casino_anteheader_1');