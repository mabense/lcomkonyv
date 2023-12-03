<?php

function variadic(...$arr) {
    if(!in_array("valami", $arr)) {
        return "false";
    }
    return "true";
}

echo variadic("valami") . PHP_EOL;
echo variadic("hello", "valami") . PHP_EOL;
echo variadic("asd", "qwertz") . PHP_EOL;