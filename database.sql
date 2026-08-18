-- Database: skillswap
CREATE DATABASE IF NOT EXISTS skillswap CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE skillswap;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    bio TEXT,
    experience_level ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') DEFAULT 'Beginner',
    profile_picture VARCHAR(255) DEFAULT 'default-avatar.png',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255)
);

-- Skills Table (Skills users OFFER)
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    experience_level ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Skill Wants Table (Skills users WANT to learn)
CREATE TABLE skill_wants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    experience_level ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Matches Table (Stores match scores between users)
CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id_1 INT NOT NULL,
    user_id_2 INT NOT NULL,
    match_score INT NOT NULL DEFAULT 0,
    status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id_1) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id_2) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_match (user_id_1, user_id_2)
);

-- Messages Table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Notifications Table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('match', 'message', 'system') DEFAULT 'system',
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert Default Categories
INSERT INTO categories (name, description) VALUES
('Programming', 'Software development and coding skills'),
('Design', 'Graphic design, UI/UX, and creative skills'),
('Marketing', 'Digital marketing and sales skills'),
('Writing', 'Content writing, copywriting, and editing'),
('Data Science', 'Data analysis, machine learning, and statistics'),
('Languages', 'Foreign language learning and teaching'),
('Music', 'Musical instruments, vocals, and production'),
('Photography', 'Photography and videography skills'),
('Business', 'Entrepreneurship, management, and finance'),
('Cooking', 'Culinary arts and food preparation');






-- ============================================
-- SkillSwap Database Schema
-- Digital Skill Marketplace Without Monetary Transactions
-- ============================================

CREATE DATABASE IF NOT EXISTS skillswap CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE skillswap;

-- ============================================
-- TABLE: settings
-- ============================================
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- TABLE: categories
-- ============================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'bi-grid',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABLE: users
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    is_verified TINYINT(1) DEFAULT 0,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- TABLE: profiles
-- ============================================
CREATE TABLE profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    bio TEXT,
    location VARCHAR(100),
    phone VARCHAR(20),
    profile_picture VARCHAR(255) DEFAULT 'default-avatar.png',
    experience_level ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') DEFAULT 'Beginner',
    availability ENUM('Full-time', 'Part-time', 'Weekends only', 'Flexible') DEFAULT 'Flexible',
    linkedin_url VARCHAR(255),
    github_url VARCHAR(255),
    website_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_profile (user_id)
);

-- ============================================
-- TABLE: skills (master list of available skills)
-- ============================================
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_skill_category (name, category_id)
);

-- ============================================
-- TABLE: user_skills (skills users OFFER)
-- ============================================
CREATE TABLE user_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skill_id INT NOT NULL,
    experience_level ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') NOT NULL,
    description TEXT,
    years_of_experience INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_skill (user_id, skill_id)
);

-- ============================================
-- TABLE: wanted_skills (skills users WANT to learn)
-- ============================================
CREATE TABLE wanted_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skill_id INT NOT NULL,
    experience_level ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') NOT NULL,
    description TEXT,
    urgency ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_wanted_skill (user_id, skill_id)
);

-- ============================================
-- TABLE: matches (intelligent matching results)
-- ============================================
CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id_1 INT NOT NULL,
    user_id_2 INT NOT NULL,
    match_score INT NOT NULL DEFAULT 0,
    status ENUM('pending', 'accepted', 'declined', 'completed') DEFAULT 'pending',
    user_1_response ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    user_2_response ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    matched_skill_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id_1) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id_2) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (matched_skill_id) REFERENCES skills(id) ON DELETE SET NULL,
    UNIQUE KEY unique_match_pair (user_id_1, user_id_2)
);

-- ============================================
-- TABLE: exchange_requests
-- ============================================
CREATE TABLE exchange_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    requester_id INT NOT NULL,
    receiver_id INT NOT NULL,
    offered_skill_id INT NOT NULL,
    requested_skill_id INT NOT NULL,
    message TEXT,
    status ENUM('pending', 'accepted', 'declined', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    start_date DATE NULL,
    end_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (offered_skill_id) REFERENCES skills(id) ON DELETE CASCADE,
    FOREIGN KEY (requested_skill_id) REFERENCES skills(id) ON DELETE CASCADE
);

-- ============================================
-- TABLE: messages
-- ============================================
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    exchange_request_id INT NULL,
    content TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (exchange_request_id) REFERENCES exchange_requests(id) ON DELETE SET NULL
);

-- ============================================
-- TABLE: notifications
-- ============================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('match', 'message', 'exchange_request', 'exchange_accepted', 'exchange_declined', 'exchange_completed', 'review', 'system') DEFAULT 'system',
    reference_id INT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- TABLE: reviews
-- ============================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exchange_request_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    reviewee_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exchange_request_id) REFERENCES exchange_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewee_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (exchange_request_id, reviewer_id)
);

-- ============================================
-- TABLE: portfolio
-- ============================================
CREATE TABLE portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    project_url VARCHAR(255),
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- TABLE: reports
-- ============================================
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporter_id INT NOT NULL,
    reported_id INT NOT NULL,
    reason ENUM('spam', 'harassment', 'fake_profile', 'inappropriate_content', 'other') NOT NULL,
    description TEXT,
    status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reported_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('site_name', 'SkillSwap', 'Website name'),
('site_description', 'Digital Skill Marketplace Without Monetary Transactions', 'Website description'),
('items_per_page', '10', 'Number of items per page'),
('match_threshold', '30', 'Minimum match score to display'),
('enable_registration', '1', 'Allow new user registration'),
('maintenance_mode', '0', 'Enable maintenance mode');

-- Categories
INSERT INTO categories (name, description, icon) VALUES
('Web Development', 'Frontend, backend, and full-stack web development', 'bi-code-slash'),
('Mobile Development', 'iOS, Android, and cross-platform mobile apps', 'bi-phone'),
('Graphic Design', 'UI/UX design, branding, and visual design', 'bi-palette'),
('Digital Marketing', 'SEO, social media, content marketing', 'bi-megaphone'),
('Data Science', 'Data analysis, machine learning, statistics', 'bi-bar-chart'),
('Writing & Content', 'Copywriting, blogging, technical writing', 'bi-pen'),
('Languages', 'Foreign language learning and teaching', 'bi-translate'),
('Music & Audio', 'Music production, instruments, sound design', 'bi-music-note-beamed'),
('Photography & Video', 'Photography, videography, editing', 'bi-camera'),
('Business & Finance', 'Accounting, entrepreneurship, management', 'bi-briefcase'),
('Cooking & Culinary', 'Cooking, baking, food preparation', 'bi-egg-fried'),
('Fitness & Health', 'Personal training, nutrition, wellness', 'bi-heart-pulse');

-- Skills
INSERT INTO skills (category_id, name, description) VALUES
(1, 'PHP', 'Server-side scripting language for web development'),
(1, 'JavaScript', 'Programming language for web interactivity'),
(1, 'HTML & CSS', 'Markup and styling for web pages'),
(1, 'MySQL', 'Relational database management system'),
(1, 'React', 'JavaScript library for building user interfaces'),
(1, 'Node.js', 'JavaScript runtime for server-side development'),
(2, 'Flutter', 'UI toolkit for building natively compiled applications'),
(2, 'React Native', 'Framework for building native apps using React'),
(2, 'Swift', 'Programming language for iOS development'),
(2, 'Kotlin', 'Programming language for Android development'),
(3, 'Adobe Photoshop', 'Image editing and graphic design software'),
(3, 'Figma', 'Collaborative interface design tool'),
(3, 'Adobe Illustrator', 'Vector graphics and illustration software'),
(3, 'UI/UX Design', 'User interface and user experience design'),
(4, 'SEO', 'Search engine optimization techniques'),
(4, 'Social Media Marketing', 'Marketing through social media platforms'),
(4, 'Content Marketing', 'Creating and distributing valuable content'),
(4, 'Email Marketing', 'Marketing through email campaigns'),
(5, 'Python', 'Programming language for data science and AI'),
(5, 'R Programming', 'Language for statistical computing'),
(5, 'SQL', 'Query language for databases'),
(5, 'Machine Learning', 'Algorithms that improve through experience'),
(6, 'Copywriting', 'Writing text for advertising or marketing'),
(6, 'Technical Writing', 'Writing technical documentation'),
(6, 'Blogging', 'Writing and publishing blog content'),
(7, 'Spanish', 'Spanish language learning and teaching'),
(7, 'French', 'French language learning and teaching'),
(7, 'German', 'German language learning and teaching'),
(7, 'Mandarin', 'Mandarin Chinese language learning'),
(8, 'Guitar', 'Playing acoustic and electric guitar'),
(8, 'Piano', 'Playing piano and keyboard instruments'),
(8, 'Music Production', 'Creating and producing music tracks'),
(9, 'Photography', 'Capturing and editing photographs'),
(9, 'Video Editing', 'Editing and producing video content'),
(10, 'Financial Analysis', 'Analyzing financial data and reports'),
(10, 'Project Management', 'Planning and executing projects'),
(11, 'Baking', 'Baking bread, cakes, and pastries'),
(11, 'International Cuisine', 'Cooking dishes from around the world'),
(12, 'Personal Training', 'One-on-one fitness coaching'),
(12, 'Yoga Instruction', 'Teaching yoga practices');

-- Users (password: password123 for all sample users)
INSERT INTO users (full_name, email, password, role, is_active, is_verified) VALUES
('Admin User', 'admin@skillswap.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 1),
('John Smith', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, 1),
('Sarah Johnson', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, 1),
('Michael Brown', 'michael@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, 1),
('Emily Davis', 'emily@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, 1),
('David Wilson', 'david@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, 1),
('Lisa Anderson', 'lisa@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, 1),
('Robert Taylor', 'robert@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, 1),
('Jennifer Martinez', 'jennifer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, 1),
('James Thompson', 'james@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, 1);

-- Profiles
INSERT INTO profiles (user_id, bio, location, experience_level, availability, phone) VALUES
(1, 'Platform administrator managing SkillSwap.', 'New York, USA', 'Expert', 'Full-time', '+1-555-0101'),
(2, 'Full-stack developer with 5 years of experience. Love building web applications and teaching others.', 'San Francisco, USA', 'Advanced', 'Part-time', '+1-555-0102'),
(3, 'Creative designer passionate about UI/UX. Looking to learn programming skills.', 'Los Angeles, USA', 'Intermediate', 'Flexible', '+1-555-0103'),
(4, 'Data scientist specializing in Python and machine learning. Want to improve my design skills.', 'Chicago, USA', 'Advanced', 'Weekends only', '+1-555-0104'),
(5, 'Content writer and blogger. Interested in learning web development.', 'Boston, USA', 'Intermediate', 'Part-time', '+1-555-0105'),
(6, 'Mobile app developer specializing in Flutter and React Native.', 'Seattle, USA', 'Advanced', 'Flexible', '+1-555-0106'),
(7, 'Music producer and guitarist. Looking to learn digital marketing.', 'Austin, USA', 'Intermediate', 'Weekends only', '+1-555-0107'),
(8, 'Photographer and videographer. Want to learn web development.', 'Miami, USA', 'Advanced', 'Part-time', '+1-555-0108'),
(9, 'Digital marketing expert. Interested in learning data science.', 'Denver, USA', 'Intermediate', 'Full-time', '+1-555-0109'),
(10, 'Fitness trainer and yoga instructor. Looking to learn graphic design.', 'Portland, USA', 'Beginner', 'Flexible', '+1-555-0110');

-- User Skills (skills users OFFER)
INSERT INTO user_skills (user_id, skill_id, experience_level, description, years_of_experience) VALUES
(2, 1, 'Advanced', 'Expert in PHP development with Laravel and custom frameworks', 5),
(2, 2, 'Advanced', 'Strong JavaScript skills including ES6+ and DOM manipulation', 5),
(2, 3, 'Advanced', 'Proficient in HTML5, CSS3, and responsive design', 5),
(3, 12, 'Advanced', 'Expert in UI/UX design with Figma and Adobe tools', 4),
(3, 13, 'Intermediate', 'Good knowledge of vector graphics and illustration', 3),
(4, 20, 'Advanced', 'Expert Python developer for data analysis and ML', 6),
(4, 22, 'Advanced', 'Strong SQL skills for complex database queries', 5),
(5, 24, 'Intermediate', 'Experienced copywriter for marketing and blogs', 3),
(5, 25, 'Intermediate', 'Technical documentation writer for software products', 2),
(6, 7, 'Advanced', 'Expert Flutter developer with published apps', 4),
(6, 8, 'Intermediate', 'React Native development experience', 2),
(7, 32, 'Advanced', 'Professional guitarist with 10 years experience', 10),
(7, 34, 'Intermediate', 'Music production using FL Studio and Ableton', 3),
(8, 35, 'Advanced', 'Professional photographer with portfolio work', 7),
(8, 36, 'Intermediate', 'Video editing with Premiere Pro and DaVinci', 4),
(9, 16, 'Advanced', 'SEO expert with proven results', 5),
(9, 17, 'Intermediate', 'Social media marketing strategist', 4),
(10, 41, 'Advanced', 'Certified personal trainer', 6),
(10, 42, 'Intermediate', 'Yoga instructor with 200-hour certification', 3);

-- Wanted Skills (skills users WANT to learn)
INSERT INTO wanted_skills (user_id, skill_id, experience_level, description, urgency) VALUES
(2, 12, 'Beginner', 'Want to learn UI/UX design to improve my frontend skills', 'High'),
(2, 35, 'Beginner', 'Interested in learning photography basics', 'Medium'),
(3, 1, 'Beginner', 'Want to learn PHP to become a full-stack designer', 'High'),
(3, 2, 'Beginner', 'Need JavaScript for interactive prototypes', 'High'),
(4, 12, 'Beginner', 'Want to learn design principles for data visualization', 'Medium'),
(4, 13, 'Beginner', 'Interested in creating visual content', 'Low'),
(5, 1, 'Beginner', 'Want to build my own blog website', 'High'),
(5, 3, 'Beginner', 'Need HTML/CSS for web content formatting', 'High'),
(6, 20, 'Intermediate', 'Want to add data science to mobile apps', 'Medium'),
(6, 1, 'Beginner', 'Need backend skills for full-stack mobile dev', 'Medium'),
(7, 16, 'Beginner', 'Want to promote my music online', 'High'),
(7, 17, 'Beginner', 'Need social media skills for music marketing', 'Medium'),
(8, 1, 'Beginner', 'Want to build a photography portfolio website', 'High'),
(8, 2, 'Beginner', 'Need JavaScript for interactive galleries', 'Medium'),
(9, 20, 'Beginner', 'Want to analyze marketing data with Python', 'High'),
(9, 22, 'Intermediate', 'Need better SQL for marketing analytics', 'Medium'),
(10, 12, 'Beginner', 'Want to create fitness branding and logos', 'High'),
(10, 13, 'Beginner', 'Need design skills for social media content', 'Medium');

-- Matches (sample intelligent matches)
INSERT INTO matches (user_id_1, user_id_2, match_score, status, user_1_response, user_2_response, matched_skill_id, notes) VALUES
(2, 3, 85, 'accepted', 'accepted', 'accepted', 1, 'John offers PHP, Sarah wants PHP. Sarah offers design, John wants design. Perfect match!'),
(4, 9, 70, 'accepted', 'accepted', 'accepted', 20, 'Michael offers Python, Jennifer wants Python. Jennifer offers marketing, Michael is interested.'),
(5, 2, 60, 'pending', 'pending', 'pending', 1, 'Emily wants to learn PHP, John offers PHP. Potential match.'),
(6, 4, 55, 'pending', 'pending', 'pending', 20, 'David wants Python, Michael offers Python. David offers mobile dev.'),
(7, 9, 50, 'pending', 'pending', 'pending', 16, 'Lisa wants marketing, Jennifer offers marketing. Lisa offers music.'),
(8, 2, 65, 'accepted', 'accepted', 'pending', 1, 'Robert wants web dev, John offers web dev. Robert offers photography.'),
(10, 3, 75, 'pending', 'pending', 'pending', 12, 'James wants design, Sarah offers design. James offers fitness training.');

-- Exchange Requests
INSERT INTO exchange_requests (match_id, requester_id, receiver_id, offered_skill_id, requested_skill_id, message, status, start_date) VALUES
(1, 2, 3, 1, 12, 'Hi Sarah! I would love to teach you PHP and JavaScript in exchange for learning UI/UX design from you. Let us connect!', 'accepted', '2026-07-01'),
(2, 4, 9, 20, 16, 'Hello Jennifer! I can teach you Python for data analysis. In return, I would like to learn SEO and digital marketing strategies from you.', 'in_progress', '2026-07-05'),
(6, 8, 2, 35, 1, 'Hey John! I can offer photography lessons and tips. I would love to learn PHP to build my portfolio website.', 'pending', NULL);

-- Messages
INSERT INTO messages (sender_id, receiver_id, exchange_request_id, content, is_read) VALUES
(2, 3, 1, 'Hi Sarah! Thanks for accepting the match. When would you like to start our skill exchange?', 1),
(3, 2, 1, 'Hi John! I am excited to learn PHP from you. I am available on weekends. How about you?', 1),
(2, 3, 1, 'Weekends work perfectly for me too! Let us start this Saturday at 2 PM.', 0),
(4, 9, 2, 'Hi Jennifer! I have prepared some Python basics for our first session. Are you ready?', 1),
(9, 4, 2, 'Hi Michael! Yes, I am ready. I have also prepared some SEO fundamentals to share with you.', 1),
(8, 2, 3, 'Hey John! I saw our match. I would love to exchange photography skills for PHP lessons. What do you think?', 0);

-- Notifications
INSERT INTO notifications (user_id, type, reference_id, title, message, is_read) VALUES
(3, 'match', 1, 'New Match Found!', 'You have been matched with John Smith! Match score: 85%', 1),
(2, 'match', 1, 'New Match Found!', 'You have been matched with Sarah Johnson! Match score: 85%', 1),
(9, 'match', 2, 'New Match Found!', 'You have been matched with Michael Brown! Match score: 70%', 1),
(4, 'match', 2, 'New Match Found!', 'You have been matched with Jennifer Martinez! Match score: 70%', 1),
(3, 'message', 2, 'New Message', 'John Smith sent you a message about your skill exchange.', 0),
(2, 'message', 3, 'New Message', 'Sarah Johnson replied to your message.', 0),
(9, 'exchange_request', 2, 'Exchange Request Accepted', 'Michael Brown accepted your exchange request!', 1),
(4, 'exchange_request', 2, 'Exchange Request Accepted', 'You accepted Jennifer Martinez exchange request.', 1),
(2, 'exchange_request', 3, 'New Exchange Request', 'Robert Taylor sent you an exchange request.', 0),
(1, 'system', NULL, 'Welcome to SkillSwap', 'Thank you for joining SkillSwap! Start by adding your skills and finding matches.', 0);

-- Reviews
INSERT INTO reviews (exchange_request_id, reviewer_id, reviewee_id, rating, comment) VALUES
(1, 3, 2, 5, 'John is an amazing PHP teacher! He explained everything clearly and was very patient. Highly recommended!'),
(1, 2, 3, 5, 'Sarah is a fantastic designer. Her UI/UX lessons were eye-opening. I learned so much about design principles.');

-- Portfolio
INSERT INTO portfolio (user_id, title, description, project_url) VALUES
(2, 'E-Commerce Website', 'Built a full e-commerce platform using PHP and MySQL', 'https://github.com/john/ecommerce'),
(2, 'Task Manager App', 'A JavaScript-based task management application', 'https://github.com/john/taskmanager'),
(3, 'Mobile App UI Design', 'Complete UI design for a fitness tracking app', NULL),
(3, 'Brand Identity Project', 'Logo and brand identity for a local coffee shop', NULL),
(4, 'Sales Prediction Model', 'Machine learning model to predict retail sales', 'https://github.com/michael/sales-predict'),
(8, 'Wedding Photography', 'Portfolio of wedding photography from 2025', NULL),
(8, 'Travel Documentary', 'Video documentary of a trip to Japan', NULL);

-- Reports
INSERT INTO reports (reporter_id, reported_id, reason, description, status) VALUES
(5, 8, 'fake_profile', 'This user seems to have copied portfolio images from another photographer.', 'pending');

-- Update admin last login
UPDATE users SET last_login = NOW() WHERE id = 1;