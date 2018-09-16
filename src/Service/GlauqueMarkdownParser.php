<?php
namespace App\Service;

class GlauqueMarkdownParser
{
    public static function parse($text)
    {
        $text = str_replace("\n", "<br />", $text);
        return $text;
    }
}

?>