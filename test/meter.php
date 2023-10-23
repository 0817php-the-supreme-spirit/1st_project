<?php
$percentage = 5; // 게이지 바의 퍼센트 값 (이 값은 동적으로 설정 가능)

// 퍼센트에 따라 색상을 설정
if ($percentage < 30) {
    $color = 'green';
} elseif ($percentage < 70) {
    $color = 'yellow';
} else {
    $color = 'red';
}
?>

<meter value="<?php echo $percentage; ?>" min="0" max="100" low="30" high="70" optimum="100" style="width: 100%; height: 20px;">
    <?php echo $percentage; ?>%
</meter>

<style>
    /* 게이지 바 색상을 동적으로 설정 */
    meter[value]::-webkit-meter-bar {
        background: #ccc;
    }

    meter[value]::-webkit-meter-optimum-value {
        background: <?php echo $color; ?>;
    }

    meter[value]::-webkit-meter-suboptimum-value {
        background: <?php echo $color; ?>;
    }

    meter[value]::-webkit-meter-even-less-good-value {
        background: <?php echo $color; ?>;
    }
</style>