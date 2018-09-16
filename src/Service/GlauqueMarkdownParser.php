<?php
namespace App\Service;

class GlauqueMarkdownParser
{
    public static function parse($text)
    {
        $text = preg_replace('/\*\*(.*?)\*\*/', "<b>$1</b>", $text);
        $text = preg_replace('/\*(.*?)\*/', "<em>$1</em>", $text);
        $text = preg_replace('/__(.*?)__/', "<u>$1</u>", $text);
        $text = preg_replace('/#(.*?)\n/', "<h3>$1</h3>\n", $text);
        $text = str_replace("\n", "<br />", $text);
        return $text;
    }
}

?>