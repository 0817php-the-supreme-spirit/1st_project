<?php

    $monthly = 3000000;

    // 한 달 일 수를 계산하는 date('t")
    $days = date('t'); 

    // 한달 급여를 일수로 나누기
    $daily = $monthly / $days;

    // 나눈 값이 정수로 출력되게
    $daily = (int)$daily;

    echo date('Y-m-d H:i:s'); 
    echo "\n";
    echo "한 달 급여: $monthly";
    echo "\n";
    echo "하루 사용 가능 금액: $daily";


?>