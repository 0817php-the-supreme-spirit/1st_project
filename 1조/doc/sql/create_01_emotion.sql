USE diarydb;
CREATE TABLE IF NOT EXISTS emotion(
	
	em_id VARCHAR(2) PRIMARY KEY
	,em_name VARCHAR(15) NOT NULL
	,em_comment VARCHAR(300) 
	,em_path VARCHAR(30)
);