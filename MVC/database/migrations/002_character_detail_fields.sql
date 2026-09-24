ALTER TABLE characters
    ADD COLUMN IF NOT EXISTS equipment TEXT NULL AFTER intelligence,
    ADD COLUMN IF NOT EXISTS additional_skills TEXT NULL AFTER equipment;
