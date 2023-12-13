<?php
require_once(LIB_DIR . "session.php");
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
    haveSession();
    if (getMoveState() == MoveState::NOT_SELECTED) {
        switch ($tableId) {
            case "placeList":
                setMoveLocSql($sqlQuery, $sqlTypes, $sqlParams);
                break;
            case "bookList":
                setMoveBookSql($sqlQuery, $sqlTypes, $sqlParams);
                break;
        }
    }
    $stmt = sqlPrepareBindExecute(
        $sqlQuery,
        $sqlTypes,
        $sqlParams,
        __FUNCTION__
    );
    $result = $stmt->get_result();
    if (!$result) {
        domTable([], $assocColumnsHeaders, $onClickRoute, $keyAttributes, $tableId);
        return false;
    }
    domTable($result->fetch_all(MYSQLI_ASSOC), $assocColumnsHeaders, $onClickRoute, $keyAttributes, $tableId);
    return $stmt;
}


function sqlTable(
    $sql,
    $assocColumnsHeaders = [],
    $onClickRoute = "",
    $keyAttributes = [],
    $tableId = "contentTable"
) {
    haveSession();
    if (getMoveState() == MoveState::NOT_SELECTED) {
        switch ($tableId) {
            case "placeList":
                setMoveLocSql($sql);
                break;
            case "bookList":
                setMoveBookSql($sql);
                break;
        }
    }
    // $result = (new mysqli_stmt(0))->get_result();
    $stmt = sqlPrepareExecute(
        $sql,
        __FUNCTION__
    );
    $result = $stmt->get_result();
    if (!$result) {
        domTable([], $assocColumnsHeaders, $onClickRoute, $keyAttributes, $tableId);
        return false;
    }
    domTable($result->fetch_all(MYSQLI_ASSOC), $assocColumnsHeaders, $onClickRoute, $keyAttributes, $tableId);
    return $stmt;
}


function domTable(
    $dataAssocRows = [],
    $assocColumnsHeaders = [],
    $onClickRoute = "",
    $keyAttributes = [],
    $tableId = "contentTable"
) {
    // haveSession();
    // if (getMoveState() == MoveState::SELECTING) {
    //     $result = null;
    //     $sql = [
    //         "sql" => "",
    //         "types" => "",
    //         "params" => []
    //     ];
    //     switch ($tableId) {
    //         case "placeList":
    //             $sql = getMoveLocSql();
    //             break;
    //         case "bookList":
    //             $sql = getMoveBookSql();
    //             break;
    //     }
    // }
    global $dom;
    // $dom = new DOMDocument();
    $tableTag = $dom->getElementById($tableId);
    if (!isset($tableTag)) {
        return false;
    }

    if (getMoveState() != MoveState::SELECTING) {
        domTableHead($tableTag, $assocColumnsHeaders);
    }
    sqlTableRows($dataAssocRows, $tableTag, $assocColumnsHeaders, $onClickRoute, $keyAttributes);
}


function domTableHead($table, $assocColumnsHeaders)
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


function sqlTableRows($assocRows, $table, $assocColumnsHeaders, $onClickRoute, $keyAttributes, $isSql = true)
{
    if (sizeof($assocRows) == 0) {
        sqlTableEmptyRow($table, count($assocColumnsHeaders));
    } else {
        $tableKeys = [];
        $i = 0;
        while ($row = array_shift($assocRows)) {
            sqlTableRow($table, $row, $assocColumnsHeaders, $onClickRoute, $i);
            $rowKey = [];
            foreach ($keyAttributes as $key) {
                $rowKey[$key] = $row[$key];
            }
            // switch ($onClickRoute) {
            //     case "locations":
            //         movePushLocation($row);
            //         break;
            //     case "book":
            //         movePushBook($row);
            //         break;
            //     default:
            //         // movePushLocation($row);
            // }
            $tableKeys[$i] = $rowKey;
            $i++;
        }
        setTableAllKeys($table->getAttribute("id"), $tableKeys);
    }

    // echo ">> ";
    // echo var_dump(getMoveLocSql()) . "<br />";
    // echo ">> ";
    // echo  var_dump(getMoveBookSql()) . "<br />";
    // echo var_dump(getMoveState()) . "<br />";
    // echo "<br />";

    // echo ">> ";
    // echo var_dump(moveLocsGetAll()) . "<br />";
    // echo ">> ";
    // echo  var_dump(moveBooksGetAll()) . "<br />";
    // echo var_dump(getMoveState()) . "<br />";
    // echo "<br />";
}


function sqlTableRow($table, $queryAssocRow, $assocColumnsHeaders, $onClickRoute, $rowIndex)
{
    // $dom = new DOMDocument();
    $dom = $table->ownerDocument;

    $elem = null;
    if (getMoveState() == MoveState::SELECTING) {
        $tableMark = "%id%";
        $rowMark = "%num%";
        $tableID = $table->getAttribute("id");

        $inputOldID = "check";
        $labelOldID = "forCheck";
        $inputNewID = $tableID . "-" . $rowIndex;
        $labelNewID = "for-" . $inputNewID;

        domAppendTemplateTo($table->getAttribute("id"), TEMPLATE_DIR . "table_select_row.htm");
        $elem = $dom->getElementById($labelOldID);
        $elem->setAttribute("class", "row");
        $cell = $dom->createElement("span");
        $dataArr = [];
        foreach ($assocColumnsHeaders as $column => $header) {
            $data = $queryAssocRow[$column];
            if (isset($data) && $data != "" && $data != 0)
                array_push($dataArr, $queryAssocRow[$column]);
        }
        $cell->textContent = implode(" : ", $dataArr);
        $elem->appendChild($cell);

        domSetStrings(
            new TargetedString($labelOldID, $labelNewID, StringTarget::ID),
            new TargetedString($inputOldID, $inputNewID, StringTarget::ID)
        );
        domSetStrings(
            new TargetedString($labelNewID, $inputNewID, StringTarget::FOR),
            new TargetedString($inputNewID, $tableID, StringTarget::NAME, $tableMark),
            new TargetedString($inputNewID, $rowIndex, StringTarget::NAME, $rowMark)
        );
    } else {
        $elem = $dom->createElement("tr");
        if ($onClickRoute != "") {
            $trRoute = "../" . findPage($onClickRoute) .
                "/index.php?table=" . $table->getAttribute("id") . "&row=" . $rowIndex;
            $elem->setAttribute("onclick", "window.location='" . $trRoute . "';");
        }
        foreach ($assocColumnsHeaders as $column => $header) {
            $cell = $dom->createElement("td");
            $cell->textContent = $queryAssocRow[$column];
            $elem->appendChild($cell);
        }
        $table->appendChild($elem);
    }

    $elem->setAttribute(
        "class",
        ($rowIndex % 2 == 0) ? "even_row" : "odd_row"
    );
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
