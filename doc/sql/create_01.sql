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
 	,input_date
)
 	
VALUES (
 	3000000
 	,100000
 	,20230501
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
 	('옷삿다',30000,'0')
 	,('니가알던 내가 아냐',6000,'0')
 	,('걍 닌텐도 지름',10000,'2')
 	,('마제소바 머금 먹음',8000,'0')
 	,('마라탕을 손으로  부심',12000,'0')
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
where todo.create_date = 20231012
AND todo.delete_date IS NULL;

SELECT todo.id, cate.category_name, todo.title, todo.amount_used
FROM todolist_table todo
JOIN category_table cate
ON todo.category_id = cate.category_id
WHERE cate.category_name = 'life'
AND todo.id = 9;

SELECT *
FROM todolist_table todo
JOIN category_table cate
ON todo.category_id = cate.category_id

alter table todolist_table alter column memo set DEFAULT '메모 없음';

flush PRIVILEGES;

VALUES (20000)
INSERT INTO user_table ( monthly_salary )

SELECT *
FROM user_table
WHERE MONTH(input_date) = NOW();

SELECT * from user_table
where input_date between date_format(now(), '%Y-%m-01') and date_format(now(), '%Y-%m-%d');




SELECT todo.id ,cate.category_name ,todo.title ,todo.amount_used
FROM todolist_table todo
JOIN category_table cate
ON
todo.category_id = cate.category_id
WHERE
todo.create_date = 20231011;

SELECT daily_salary, DATE_FORMAT(input_date,'%Y-%m') AS input_month
FROM user_table
GROUP BY input_month
HAVING input_month = 202305;

SELECT *
FROM todolist_table
WHERE create_date = CURDATE();

SELECT DATE_FORMAT(create_date,'%Y-%m') AS create_month, sum(amount_used) AS total_amount
FROM todolist_table todo
JOIN user_table usta
IN todo.create_date = usta.Input_date
GROUP BY create_month;

SELECT DATE_FORMAT(input_date,'%Y-%m') AS input_month, monthly_salary
FROM user_table
GROUP BY input_month;


SELECT DATE_FORMAT(todo.create_date,'%Y-%m') AS create_month, DATE_FORMAT(input_date,'%Y-%m') AS input_month, sum(todo.amount_used) AS total_amount, FORMAT(usta.monthly_salary,0)
FROM todolist_table todo
JOIN user_table usta
ON DATE_FORMAT(todo.create_date,'%Y-%m') = DATE_FORMAT(usta.input_date,'%Y-%m')
GROUP BY create_month;

SELECT id
FROM todolist_table
ORDER BY id DESC

SELECT todo.create_date, sum(todo.amount_used) AS amount_used_sum, usta.daily_salary, DATE_FORMAT(todo.create_date,'%Y-%m') AS create_month
FROM todolist_table todo
JOIN user_table usta
ON DATE_FORMAT(todo.create_date,'%Y-%m') = DATE_FORMAT(usta.input_date,'%Y-%m')
GROUP BY todo.create_date

SELECT SUM(amount_used) AS amount_sum
FROM todolist_table
WHERE create_date = 20231011 
and delete_date IS NULL;