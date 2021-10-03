<?php
namespace App\Service;

class GlauqueMarkdownParser
{
    public static function parse($text)
    {
        $text = preg_replace('/\*\*(.*?)\*\*/', "<b>$1</b>", $text);
        $text = preg_replace('/\*(.*?)\*/', "<em>$1</em>", $text);
        $text = preg_replace('/__(.*?)__/', "<u>$1</u>", $text);
        $text = preg_replace('/#(.*?)$/m', "<h3>$1</h3>\n", $text);
        $text = str_replace("\n", "<br />", $text);
        return $text;
    }

    public static function safeTruncateHtml($html, $length) {
        if(strlen($html) <= $length) return $html;

        $html = substr($html, 0, $length);
        if(false !== ($breakpoint = strrpos($html, " "))) {
            $html = substr($html, 0, $breakpoint);
        }

        return GlauqueMarkdownParser::restoreTags($html) . "...";
    }
    
    public static function restoreTags($html)
    {
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        for ($i=0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                $html .= '</'.$openedtags[$i].'>';
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        return $html;
    }
}

?>