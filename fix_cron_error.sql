-- Fix WordPress Cron Error
-- Delete the corrupted cron option from wp_options table
-- This will reset WordPress cron system

DELETE FROM wp_options WHERE option_name = 'cron';

-- Alternative: If you want to keep the cron but reset it
-- UPDATE wp_options SET option_value = 'a:0:{}' WHERE option_name = 'cron';

-- Note: Replace 'wp_' with your actual database prefix if different