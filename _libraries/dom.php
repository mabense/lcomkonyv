<?php
require_once(LIB_DIR . "feedback_log.php");
require_once(LIB_DIR . "session.php");
require_once(LIB_DIR . "public_func.php");

final class StringTarget
{
    const TEXT_CONTENT = "txt";
    const NAME_SUBSTR = "%%";
    const FOR_SUBSTR = "for%%";
    // default: setAttribute
    const VALUE = "value";
    const ID = "id";
    const NAME = "name";
    const LABEL = "for";
}

final class TargetedString
{
    var $targetId, $string, $targetType, $substr;
    public function __construct($elementID, $string, $StringTarget = StringTarget::TEXT_CONTENT, $substr = null)
    {
        $this->targetId = $elementID;
        $this->string = $string;
        $this->targetType = $StringTarget;
        $this->substr = $substr;
    }
}


function newDOMDocument($baseTemplatePath)
{
    $GLOBALS["dom"] = new DOMDocument("1.0", "utf8");
    global $dom;
    libxml_use_internal_errors(true);
    mb_internal_encoding("utf8");
    header("Content-type: text/html; charset=utf8");
    return $dom->loadHTMLFile(htmlspecialchars($baseTemplatePath, ENT_HTML5, "UTF-8"));
}


function domGetElementByTagName($name)
{
    global $dom;
    if ($list = $dom->getElementsByTagName($name)) {
        return $list->item(0);
    }
    return false;
}


function domSetStrings(TargetedString ...$TargetedStrings)
{
    foreach ($TargetedStrings as $targ) {
        domSetString(
            $targ->targetId,
            $targ->string,
            $targ->targetType,
            $targ->substr
        );
    }
}


function domSetString($domElementId, $string, $StringTarget = StringTarget::TEXT_CONTENT, $substring = null)
{
    // $dom = new DOMDocument();
    global $dom;
    if ($domElement = $dom->getElementById($domElementId)) {
        switch ($StringTarget) {
            case StringTarget::TEXT_CONTENT:
                if (isset($substring)) {
                    $string = str_replace(
                        $substring,
                        $string,
                        $domElement->textContent
                    );
                }
                $domElement->textContent = $string;
                break;
                // case StringTarget::NAME_SUBSTR:
                //     $new = str_replace(
                //         StringTarget::NAME_SUBSTR,
                //         $string,
                //         $domElement->getAttribute("name")
                //     );
                //     $domElement->setAttribute("name", $new);
                //     break;
                // case StringTarget::FOR_SUBSTR:
                //     $domElement->setAttribute("for", $new);
                //     break;
                // case StringTarget::VALUE:
                //     $domElement->setAttribute("value", $string);
                //     break;
            default:
                $new = ($substring == null)
                    ? $string
                    : str_replace(
                        $substring,
                        $string,
                        $domElement->getAttribute($StringTarget)
                    );
                $domElement->setAttribute($StringTarget, $new);
                break;
        }
    }
}


function domSetTitle($pageTitle = "", $fullTitle = "")
{
    global $dom;
    if ($contentTitle = $dom->getElementById("contentTitle")) {
        $contentTitle->textContent = ($fullTitle !== "") ? $fullTitle : $pageTitle;
    }
    if ($titleTag = domGetElementByTagName("title")) {
        $titleTag->textContent = DisplayString::MAIN_TITLE;
        if ($pageTitle !== "") {
            $titleTag->textContent .= DisplayString::TITLE_SEPARATOR . $pageTitle;
        }
    }
}


function domAddStyle($stylesheet)
{
    global $dom;
    if ($head = domGetElementByTagName("head")) {
        $cssLink = $dom->createElement("link");
        $cssLink->setAttribute("rel", "stylesheet");
        $cssLink->setAttribute("href", $stylesheet);
        $head->appendChild($cssLink);
    }
}


function domMakeToolbar($pages)
{
    handleNewLang();

    $dom = new DOMDocument();
    global $dom;
    if (is_array($pages)) {
        $toolbar = $dom->getElementById("toolbar");
        foreach ($pages as $page) {
            $route = "../" . findPage($page);
            $title = pageToDisplayText($page);
            $aTag = $dom->createElement("a");
            $aTag->setAttribute("href", $route);
            $aTag->textContent = $title;
            $toolbar->appendChild($aTag);
        }
    }
    $langPick = $dom->createElement("select");
    $langPick->setAttribute("class", "select-js");
    $route = "./?newlang=";
    $langPick->setAttribute("onchange", "window.location='$route' + this.value;");
    $lang = getLang();
    foreach (LANG_ASSOC as $code => $name) {
        $opt = $dom->createElement("option");
        $opt->setAttribute("value", $code);
        $opt->textContent = $name;
        if ($code == $lang) {
            $opt->setAttribute("selected", "selected");
        }
        $langPick->appendChild($opt);
    }
    $toolbar->appendChild($langPick);
}


function domMakeToolbarLoggedIn()
{
    domMakeToolbar([
        "log_out",
        "search",
        "locations",
        "authors"
    ]);
}


function domDeleteElementById($id)
{
    // $dom = new DOMDocument();
    global $dom;
    $elem = $dom->getElementById($id);
    if (isset($elem)) {
        $elem->parentNode->removeChild($elem);
    }
}


function domContentTableFrom($assocArray, $tableId = "contentTable")
{
    global $dom;
    $table = $dom->getElementById($tableId);
    // $table = $dom->createElement("table");
    $table->setAttribute("id", "detailedTable");

    $isOddRow = true;
    foreach ($assocArray as $key => $val) {
        $tr = $dom->createElement("tr");
        $tr->setAttribute("class", $isOddRow ? "odd_row" : "even_row");
        $tdKey = $dom->createElement("td");
        $tdKey->textContent = toDisplayText($key);
        $tdVal = $dom->createElement("td");
        $tdVal->textContent = toDisplayText($val);
        $tr->appendChild($tdKey);
        $tr->appendChild($tdVal);
        $table->appendChild($tr);
        $isOddRow = !$isOddRow;
    }

    // $contentTag->appendChild($table);
}


function domAppendTemplateTo($elementID, $template, $clear = false)
{
    global $dom;
    if (file_exists($template)) {
        $element = $dom->getElementById($elementID);
        $tmpNode = new DOMDocument();
        $charsetMeta = "<meta charset=\"utf8\" />" . PHP_EOL;
        $templateHtml = file_get_contents($template);
        $tmpNode->loadHTML($charsetMeta . $templateHtml);
        $tmpContent = $element->ownerDocument->importNode($tmpNode->documentElement, true);
        $element->appendChild($tmpContent);
    }
    // $tmpNode = new DOMDocument();
    // $tmpNode->loadHtmlFile($template);
    // if ($clear) {
    //     while ($element->hasChildNodes()) {
    //         $element->removeChild($element->firstChild);
    //     }
    // }
    // $encodingMeta = $tmpNode->createElement("meta");
    // $encodingMeta->setAttribute("charset", "utf8");
    // $tmpNode->appendChild($encodingMeta);
    // $tmpContent = $element->ownerDocument->importNode($tmpNode->documentElement, true);
    // $element->appendChild($tmpContent);
}


function domPopFeedback()
{
    global $dom;
    $feedback = getFeedbackLog();
    resetFeedbackLog();
    if ($feedback !== false) {
        $feedbackTag = $dom->getElementById("feedback");
        foreach ($feedback as $line) {
            $message = $line[0];
            $isError = $line[1];
            $div = $dom->createElement("div");
            $classList = $div->getAttribute("class");
            $class = $isError ? "errorMsg" : "feedbackMsg";
            $div->setAttribute("class", $classList . " " . "row" . " " . $class);
            $div->textContent = $message;
            $feedbackTag->appendChild($div);
        }
    }
}
