CREATE TABLE character_images (
    image_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    image_data MEDIUMBLOB NOT NULL,
    mime_type VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE characters
    ADD COLUMN character_img_id INT UNSIGNED NULL,
    ADD CONSTRAINT fk_characters_image
        FOREIGN KEY (character_img_id) REFERENCES character_images(image_id)
        ON DELETE SET NULL;