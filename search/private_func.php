<?php

function interpretInput($rawInput, $tableNameInSql)
{
    $output = [
        "conditions" => "",
        "types" => "",
        "params" => []
    ];

    $fullCond = "TRUE";
    $posTerms = [];
    $negTerms = [];
    $toBeNegated = false;
    if ($quotedPhrases = explode("\"", $rawInput)) {
        foreach ($quotedPhrases as $i => $phrase) {
            if ($i % 2 == 0 || $i == sizeof($quotedPhrases) - 1) {
                if (substr($phrase, -1, 1) == "-") {
                    $phrase = substr($phrase, 0, -1);
                    $toBeNegated = true;
                } else {
                    $toBeNegated = false;
                }
                if ($soloWords = explode(" ", $phrase)) {
                    foreach ($soloWords as $word) {
                        if (strlen($word) > 0) {
                            if (substr($word, 0, 1) == "-") {
                                array_push($negTerms, substr($word, 1));
                            } else {
                                array_push($posTerms, $word);
                            }
                        }
                    }
                }
            } else {
                if ($toBeNegated) {
                    array_push($negTerms, $phrase);
                } else {
                    array_push($posTerms, $phrase);
                }
            }
        }
    }
    $hasPosTerms = sizeof($posTerms) > 0;
    $hasNegTerms = sizeof($negTerms) > 0;

    if ($hasPosTerms || $hasNegTerms) {
        $fullCond = "";
    }

    $isFirst = true;
    $cond = "";
    foreach ($posTerms as $term) {
        $output["types"] .= "s";
        if ($isFirst) {
            $isFirst = false;
            $cond = "";
        } else {
            $cond .= " OR ";
        }
        $cond .= "$tableNameInSql LIKE ?";
        array_push($output["params"], "%" . $term . "%");
    }
    $fullCond .= $hasPosTerms ? "($cond)" : "";

    if ($hasPosTerms && $hasNegTerms) {
        $fullCond .= " AND ";
    }

    $isFirst = true;
    $cond = "";
    foreach ($negTerms as $term) {
        if ($isFirst) {
            $isFirst = false;
            $cond = "";
        } else {
            $cond .= " AND ";
        }
        $cond .= "$tableNameInSql NOT LIKE ?";
        $output["types"] .= "s";
        array_push($output["params"], "%" . $term . "%");
    }
    $fullCond .= $hasNegTerms ? "($cond)" : "";

    $output["conditions"] = $fullCond;

    return $output;
}
