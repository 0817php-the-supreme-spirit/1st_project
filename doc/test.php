<?php

    $monthly = 3000000;
    $user = 400000;

    // 한 달 일 수를 계산하는 date('t")
    $days = date('t'); 

    // 한달 급여를 일수로 나누기
    $daily = $monthly / $days;

    // 나눈 값이 정수로 출력되게
    $daily = (int)$daily;

    // 사용 금액이 일일 금액의 몇 퍼센트를 초과했는지
    $percent = ($user / $daily) * 100;

    $percent = (int)$percent;
    
    echo date('Y-m-d H:i:s'); 
    echo "\n";
    echo "한 달 급여: $monthly";
    echo "\n";
    echo "하루 사용 가능 금액: $daily";
    echo "\n";
    echo $percent;
    echo "\n";

    if ($percent <= 100) {
        echo "현재 일일 금액을 초과하지 않았습니다.";
    } else {
        $user = $percent - 100;
        echo "현재 일일 금액의 {$user}% 초과하여 사용하셨습니다.";
    }


?>