<?php

// 按位取反
function toggleNumAtPosition($number, $position)
{
    $mask = 1 << $position;
    return $number ^ $mask;
}

// 按位查值
function getValAtPosition($number, $position)
{
    $mask = 1 << $position;
    return $number & $mask;
}
