CREATE DATABASE IF NOT EXISTS portfolio_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE portfolio_db;

CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS profile (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    title VARCHAR(200) DEFAULT 'Computer Science Student',
    bio TEXT,
    email VARCHAR(150),
    profile_image VARCHAR(255),
    resume_file VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS skills (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(100),
    proficiency TINYINT UNSIGNED DEFAULT 50,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    technologies VARCHAR(500),
    category VARCHAR(100),
    github_url VARCHAR(255),
    demo_url VARCHAR(255),
    completion_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS education (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school VARCHAR(200) NOT NULL,
    degree VARCHAR(200),
    start_year YEAR,
    end_year YEAR,
    description TEXT
);

CREATE TABLE IF NOT EXISTS certificates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    issuer VARCHAR(200),
    certificate_date DATE,
    image VARCHAR(255),
    verification_url VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS social_links (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS site_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT
);

INSERT INTO profile (full_name, title, bio, email)
SELECT
    'Your Full Name',
    'Computer Science Student & Aspiring Developer',
    'I am a Computer Science student passionate about building useful software and learning new technologies.',
    'your.email@example.com'
WHERE NOT EXISTS (SELECT 1 FROM profile);

INSERT INTO skills (name, category, proficiency)
SELECT 'Java', 'Programming Languages', 75
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'Java');

INSERT INTO skills (name, category, proficiency)
SELECT 'HTML & CSS', 'Web Development', 80
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'HTML & CSS');

INSERT INTO skills (name, category, proficiency)
SELECT 'PHP', 'Web Development', 65
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'PHP');

INSERT INTO skills (name, category, proficiency)
SELECT 'MySQL', 'Database', 70
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'MySQL');