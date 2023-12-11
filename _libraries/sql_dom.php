<?php
require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "dom.php");


function sqlTableParams(
    $sqlQuery,
    $sqlTypes,
    $sqlParams,
    $assocColumnsHeaders = [],
    $onClickRoute = "",
    $keyAttributes = [],
    $tableId = "contentTable"
) {
    global $dom;
    $tableTag = $dom->getElementById($tableId);
    if (!isset($tableTag)) {
        domDeleteElementById($tableId);
        return false;
    }
    $stmt = sqlPrepareBindExecute(
        $sqlQuery,
        $sqlTypes,
        $sqlParams,
        __FUNCTION__
    );
    $result = $stmt->get_result();
    if (!$result) {
        domDeleteElementById($tableId);
        return false;
    }
    sqlTableHead($tableTag, $assocColumnsHeaders);
    sqlTableRows($result, $tableTag, $assocColumnsHeaders, $onClickRoute, $keyAttributes);
    return $stmt;
}


function sqlTable(
    $sql,
    $assocColumnsHeaders = [],
    $onClickRoute = "",
    $keyAttributes = [],
    $tableId = "contentTable"
) {
    global $dom;
    $tableTag = $dom->getElementById($tableId);
    $stmt = sqlPrepareExecute(
        $sql,
        __FUNCTION__
    );
    $result = $stmt->get_result();
    if (!$result) {
        return false;
    }
    sqlTableHead($tableTag, $assocColumnsHeaders);
    sqlTableRows($result, $tableTag, $assocColumnsHeaders, $onClickRoute, $keyAttributes);
    return $stmt;
}


function sqlTableHead($table, $assocColumnsHeaders)
{
    $dom = $table->ownerDocument;
    $thRow = $dom->createElement("tr");
    foreach ($assocColumnsHeaders as $header) {
        $th = $dom->createElement("th");
        $th->textContent = $header;
        $thRow->appendChild($th);
    }
    $table->appendChild($thRow);
}


function sqlTableRows($sqlResult, $table, $assocColumnsHeaders, $onClickRoute, $keyAttributes)
{
    if ($sqlResult->num_rows == 0) {
        sqlTableEmptyRow($table, count($assocColumnsHeaders));
    } else {
        $tableKeys = [];
        $i = 0;
        while ($row = $sqlResult->fetch_assoc()) {
            sqlTableRow($table, $row, $assocColumnsHeaders, $onClickRoute, $i);
            $rowKey = [];
            foreach ($keyAttributes as $key) {
                $rowKey[$key] = $row[$key];
            }
            switch ($onClickRoute) {
                case "locations":
                    movePushLocation($rowKey);
                    break;
                case "book":
                    movePushBook($rowKey);
                    break;
                default:
                    // movePushLocation($rowKey);
            }
            $tableKeys[$i] = $rowKey;
            $i++;
        }
        setTableAllKeys($table->getAttribute("id"), $tableKeys);
    }

    echo var_dump(getMoveLocs());
    echo "<br />";
    echo var_dump(getMoveBooks());
    echo "<br />";
    echo var_dump(getMoveState());
}


function sqlTableRow($table, $queryAssocRow, $assocColumnsHeaders, $onClickRoute, $rowIndex)
{
    // $table = (new DOMDocument())->createElement("table");
    $dom = $table->ownerDocument;
    $tdRow = $dom->createElement("tr");
    if ($onClickRoute != "") {
        $trRoute = "../" . findPage($onClickRoute) .
            "/index.php?table=" . $table->getAttribute("id") . "&row=" . $rowIndex;
        $tdRow->setAttribute("onclick", "window.location='" . $trRoute . "';");
    }
    $tdRow->setAttribute(
        "class",
        ($rowIndex % 2 == 0) ? "even_row" : "odd_row"
    );
    foreach ($assocColumnsHeaders as $column => $header) {
        $td = $dom->createElement("td");
        $td->textContent = $queryAssocRow[$column];
        $tdRow->appendChild($td);
    }
    $table->appendChild($tdRow);
}


function sqlTableEmptyRow($table, $columnCount)
{
    $dom = $table->ownerDocument;
    $tr = $dom->createElement("tr");
    $tr->setAttribute(
        "class",
        "none_row"
    );
    for ($i = 0; $i < $columnCount; $i++) {
        $td = $dom->createElement("td");
        $nbsp = $dom->createElement("pre");
        $td->appendChild($nbsp);
        $tr->appendChild($td);
    }
    $table->appendChild($tr);
}
