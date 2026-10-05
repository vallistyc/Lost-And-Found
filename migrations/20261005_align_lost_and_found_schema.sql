USE lost_and_found;

ALTER TABLE users
    CHANGE COLUMN nama nama_user VARCHAR(100) NOT NULL,
    CHANGE COLUMN password password_user VARCHAR(255) NOT NULL,
    ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER role;

ALTER TABLE laporan
    CHANGE COLUMN judul nama_kategori VARCHAR(100) NOT NULL,
    ADD COLUMN catatan_admin VARCHAR(255) NULL AFTER status_laporan;
