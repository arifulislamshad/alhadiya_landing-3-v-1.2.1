-- Add missing columns for device tracking (edit prefix as needed)
ALTER TABLE kyPQs_device_tracking
  ADD COLUMN language VARCHAR(50) DEFAULT NULL,
  ADD COLUMN timezone VARCHAR(100) DEFAULT NULL,
  ADD COLUMN connection_type VARCHAR(50) DEFAULT NULL,
  ADD COLUMN battery_level DECIMAL(5,2) DEFAULT NULL,
  ADD COLUMN battery_charging TINYINT(1) DEFAULT NULL,
  ADD COLUMN memory_info DECIMAL(5,2) DEFAULT NULL,
  ADD COLUMN cpu_cores INT DEFAULT NULL,
  ADD COLUMN touchscreen_detected TINYINT(1) DEFAULT NULL;
-- If you use a different prefix, replace 'kyPQs_' with your own.