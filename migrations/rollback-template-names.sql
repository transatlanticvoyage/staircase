-- Rollback script to revert template names from new to old format
-- Only run this if you need to revert the migration

-- Revert wp_pylons table (page-specific settings)
UPDATE wp_pylons 
SET header_desired = CASE header_desired
    WHEN 'header1' THEN 'homeservice_header_1'
    WHEN 'header2' THEN 'homeservice_header_2'
    WHEN 'header3' THEN 'homeservice_header_3'
    ELSE header_desired
END
WHERE header_desired IN ('header1', 'header2', 'header3');

UPDATE wp_pylons 
SET footer_desired = CASE footer_desired
    WHEN 'footer1' THEN 'homeservice_footer_1'
    WHEN 'footer2' THEN 'homeservice_footer_2'
    WHEN 'footer3' THEN 'homeservice_footer_3'
    ELSE footer_desired
END
WHERE footer_desired IN ('footer1', 'footer2', 'footer3');

UPDATE wp_pylons 
SET sidebar_desired = CASE sidebar_desired
    WHEN 'sidebar1' THEN 'homeservice_sidebar_1'
    WHEN 'sidebar2' THEN 'homeservice_sidebar_2'
    ELSE sidebar_desired
END
WHERE sidebar_desired IN ('sidebar1', 'sidebar2');

-- Only revert anteheader if the column exists
-- UPDATE wp_pylons 
-- SET anteheader_desired = CASE anteheader_desired
--     WHEN 'anteheader1' THEN 'homeservice_anteheader_1'
--     WHEN 'anteheader2' THEN 'homeservice_anteheader_2'
--     ELSE anteheader_desired
-- END
-- WHERE anteheader_desired IN ('anteheader1', 'anteheader2');

-- Revert wp_zen_sitespren table (site-wide defaults)
UPDATE wp_zen_sitespren 
SET site_default_header = CASE site_default_header
    WHEN 'header1' THEN 'homeservice_header_1'
    WHEN 'header2' THEN 'homeservice_header_2'
    WHEN 'header3' THEN 'homeservice_header_3'
    ELSE site_default_header
END
WHERE site_default_header IN ('header1', 'header2', 'header3');

UPDATE wp_zen_sitespren 
SET site_default_footer = CASE site_default_footer
    WHEN 'footer1' THEN 'homeservice_footer_1'
    WHEN 'footer2' THEN 'homeservice_footer_2'
    WHEN 'footer3' THEN 'homeservice_footer_3'
    ELSE site_default_footer
END
WHERE site_default_footer IN ('footer1', 'footer2', 'footer3');

UPDATE wp_zen_sitespren 
SET site_default_sidebar = CASE site_default_sidebar
    WHEN 'sidebar1' THEN 'homeservice_sidebar_1'
    WHEN 'sidebar2' THEN 'homeservice_sidebar_2'
    ELSE site_default_sidebar
END
WHERE site_default_sidebar IN ('sidebar1', 'sidebar2');

-- Only revert anteheader if the column exists
-- UPDATE wp_zen_sitespren 
-- SET site_default_anteheader = CASE site_default_anteheader
--     WHEN 'anteheader1' THEN 'homeservice_anteheader_1'
--     WHEN 'anteheader2' THEN 'homeservice_anteheader_2'
--     ELSE site_default_anteheader
-- END
-- WHERE site_default_anteheader IN ('anteheader1', 'anteheader2');