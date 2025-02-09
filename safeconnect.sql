DROP DATABASE IF EXISTS  safeconnect ; 
CREATE DATABASE IF NOT EXISTS safeconnect;
USE safeconnect;

CREATE TABLE IF NOT EXISTS client (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS specialist (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    certificate VARCHAR(255) NOT NULL,
    experience INT(2) NOT NULL,
    bio TEXT,
    blocked  TINYINT(1)  DEFAULT 0 , 
--     bankacc varchar(30)  , 
    status enum('pending' , 'approved' , 'decliend') , 
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




-- Table structure for 'plan'
CREATE TABLE IF NOT EXISTS plan (
    id INT(11) NOT NULL AUTO_INCREMENT,
    client_id INT(11) NOT NULL,
    specialist_id INT(11) NOT NULL,
    status ENUM('processing', 'ready', 'declined', 'approved') DEFAULT 'processing',
    due_date int ,
    name varchar(20) , 
    place varchar(50), 
    height varchar(50) , 
    width varchar(50) , 
    price_range varchar(50), 
    plan_file VARCHAR(255),
    blueprint varchar(250) , 
    comments varchar(50) , 
    payed TINYINT(1) DEFAULT  0 ,
    starting_date  DATE   DEFAULT  (current_date()) , 
    PRIMARY KEY (id),
    FOREIGN KEY (client_id) REFERENCES client(id),
    FOREIGN KEY (specialist_id) REFERENCES specialist(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS ratings (
	id int primary key AUTO_INCREMENT  , 
    rating int default(0)  not null   , 
	specialist_id   int  , 
    client_id  int  , 
    plan_id  int , 
    FOREIGN KEY (specialist_id) REFERENCES specialist(id) ,   
    FOREIGN KEY (client_id) REFERENCES client(id)  , 
	FOREIGN KEY (plan_id) REFERENCES plan(id) 
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; ; 


CREATE TABLE IF NOT EXISTS payment  (
	id int  AUTO_INCREMENT , 
    amount decimal DEFAULT 10000000 , 
	payment_type VARCHAR(50) NOT NULL DEFAULT 'credit' ,
    cvv VARCHAR(6)  ,  
    account_number VARCHAR(100), 
    expiry_date VARCHAR(50),
    client_id  int UNIQUE  , 
    primary key (id) , 
    FOREIGN KEY  (client_id) REFERENCES client(id)  ON DELETE cascade ON UPDATE CASCADE 
)   ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; ; 


CREATE TABLE IF NOT EXISTS contract (
    id INT(11) NOT NULL AUTO_INCREMENT,
    plan_id INT(11) NOT NULL,
    client_id INT(11) NOT NULL,
    specialist_id INT(11) NOT NULL,
    content TEXT NOT NULL,
    client_approved TINYINT(1) DEFAULT 0,
    specialist_approved TINYINT(1) DEFAULT 0,
    PRIMARY KEY (id),
	FOREIGN KEY (plan_id) REFERENCES plan(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (specialist_id) REFERENCES specialist(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- ALTER TABLE plan
-- ADD COLUMN payed TINYINT(1) DEFAULT 0 AFTER comments;

-- ALTER TABLE plan
-- ADD COLUMN starting_date  DATE   DEFAULT(current_date()) AFTER payed ;

-- -- Insert sample data into 'client' table
-- INSERT INTO client (name, email, password, phone) VALUES
-- ('John Doe', 'john.doe@example.com', 'password123', '555-1234'),
-- ('Jane Smith', 'jane.smith@example.com', 'password456', '555-5678'),
-- ('Robert Brown', 'robert.brown@example.com', 'password789', '555-8765'),
-- ('Emily Davis', 'emily.davis@example.com', 'password101112', '555-4321');

-- -- Insert sample data into 'specialist' table
-- INSERT INTO specialist (name, email, password, certificate, experience, bio) VALUES
-- ('Dr. Sarah Johnson', 'sarah.johnson@example.com', 'specpass123', 'PhD in Psychology', 15, 'A certified psychologist with over 15 years of experience in family therapy and child counseling.'),
-- ('Michael Taylor', 'michael.taylor@example.com', 'specpass456', 'Master\'s in Nutrition', 10, 'A nutritionist with extensive experience in helping clients achieve healthy eating habits.'),
-- ('Dr. Linda Moore', 'linda.moore@example.com', 'specpass789', 'PhD in Clinical Psychology', 20, 'An experienced clinical psychologist specializing in mental health issues and cognitive therapy.'),
-- ('David Anderson', 'david.anderson@example.com', 'specpass101112', 'Bachelor\'s in Physical Therapy', 5, 'A licensed physical therapist working with clients to improve mobility and recover from injuries.');

-- INSERT INTO   ratings  (rating , specialist_id) VALUES 
-- (5 , 1 ), 
-- (2 , 1 ) , 
-- (5 , 3 )   ,  
-- (5 , 2 )    , 
-- (5 , 4 )    ; 


-- INSERT INTO payment ( amount , specialist_id) values 
-- (500  , 1  ); 

-- -- Insert sample data into 'plan' table
-- INSERT INTO plan (client_id, specialist_id, status, due_date) VALUES
-- (1, 1, 'processing', '2024-10-30'),
-- (2, 2, 'ready', '2024-11-01'),
-- (3, 3, 'approved', '2024-11-15'),
-- (4, 4, 'declined', '2024-12-01');

-- -- Insert sample data into 'contract' table
-- INSERT INTO contract (client_id, specialist_id, content, client_approved, specialist_approved) VALUES
-- (1, 1, 'Contract details between John Doe and Dr. Sarah Johnson...', 1, 1),
-- (2, 2, 'Contract details between Jane Smith and Michael Taylor...', 0, 1),
-- (3, 3, 'Contract details between Robert Brown and Dr. Linda Moore...', 1, 1),
-- (4, 4, 'Contract details between Emily Davis and David Anderson...', 1, 0);







