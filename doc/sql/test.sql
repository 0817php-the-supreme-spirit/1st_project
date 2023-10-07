CREATE TABLE todolist (
    ID INT PRIMARY KEY AUTO_INCREMENT, -- 기본 키
    category ENUM('clothes', 'food', 'leisure', 'stupid') NOT NULL, -- 카테고리
    -- Category ENUM('C', 'F', 'L', 'S') NOT NULL,
    title VARCHAR(20) NOT NULL, -- 제목
    memo VARCHAR(200) NOT NULL, -- 메모
    created_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- 작성일자
    delete_fig CHAR(1) NOT NULL DEFAULT '0', -- 삭제 플래그
    -- Delete_Fig BOOLEAN DEFAULT 
    -- 기본 값 boolean으로 하면 0이 기본 값 0이면 삭제 안됨, 1이면 삭제됨
    amount_used INT NOT NULL -- 사용 금액
);

CREATE TABLE usersalary (
    ID INT PRIMARY KEY, -- 기본 키
    monthlysalary INT NOT NULL, -- 한달 급여
    dailysalary INT NOT NULL -- 일일 급여
);
