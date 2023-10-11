CREATE DATABASE 1st_project;

USE 1st_project;

CREATE TABLE user_table(
	user_id INT PRIMARY KEY AUTO_INCREMENT
	,monthly_salary INT NOT NULL
	,daily_salary INT NOT NULL
	,Input_date DATE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE todolist_table(
	id INT PRIMARY KEY AUTO_INCREMENT
	,title VARCHAR(100) NOT null
	,memo VARCHAR(100)
	,amount_used int NOT NULL
	,create_date DATE NOT NULL DEFAULT CURRENT_TIMESTAMP
	,modify_date DATE NOT NULL DEFAULT CURRENT_TIMESTAMP
	,delete_date DATE
	,category_id CHAR(1) NOT NULL
);

CREATE TABLE category_table(
	category_id CHAR(1) PRIMARY KEY
	,category_name VARCHAR(20) NOT NULL
);


COMMIT;

INSERT INTO user_table (
 	monthly_salary
 	,daily_salary
)
 	
VALUES (
 	3000000
 	,100000
);

INSERT INTO category_table (
 	category_id
 	,category_name
)
 	
VALUES (
 	'1'
 	,'activity'
);


INSERT INTO todolist_table (
 	title
 	,amount_used
 	,category_id
)
VALUES
 	('옷삼',30000,'0')
 	,('밥삼',6000,'0')
 	,('걍 돈 버림',10000,'2')
 	,('햄버거 먹음',8000,'0')
 	,('곱도리탕 부심',12000,'0')
 	,('돈 많아서 걍 씀',10000,'2')
 	,('거지라서 사탕 사먹음',300,'1')
 	,('어쩔티비',10000,'2');
 	
 	
 	
INSERT INTO todolist_table (
 	title
 	,amount_used
 	,category_id
 	,create_date
 	,modify_date
)
VALUES
 	('옷삼',30000,'0',20230909,20230909)
 	,('밥삼',6000,'0',20230909,20230909)
 	,('걍 돈 버림',10000,'2',20230909,20230909)
 	,('햄버거 먹음',8000,'0',20230909,20230909)
 	,('곱도리탕 부심',12000,'0',20230909,20230909)
 	,('돈 많아서 걍 씀',10000,'2',20230909,20230909)
 	,('거지라서 사탕 사먹음',300,'1',20230909,20230909)
 	,('어쩔티비',10000,'2',20230909,20230909);
 	
 	
SELECT todo.id, cate.category_name, todo.title, todo.amount_used
FROM todolist_table todo
JOIN category_table cate
ON todo.category_id = cate.category_id
WHERE todo.create_date = '2023-09-01';