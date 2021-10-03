<?php
namespace App\Service;

class GlauqueMarkdownParser
{
    public static function parse($text)
    {
        $text = preg_replace('/\[(.+)\]\((https?:\/\/.+)\)/', "<a href=\"$2\">$1</a>", $text);
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
        // Check the length of the string
        $strlen = strlen($html);
        // Initialize the variables
        $add_tag = false;
        $rm_tag = false;
        $add_tmp = '';
        $rm_tmp = '';
        $tag_arr[] = '';
        $closing_text = '';

        // Loop through all the characters using the string length
        for ($i = 0; $i <= $strlen; $i++) {
            // Get the character at the current pointer location
            $char = substr($html, $i, 1);

            // Check if it's a open tag <>
            if ($char === '<') {
                $add_tag = true;
                $add_tmp = '';
            }
            // Check if it's a closing tag </>
            else if ($char === '/') {
                $add_tag = false;
                $rm_tag = true;
            }
            // Check if end of the tag or a space in the tag (tag attributes are not needed)
            // If the end of an open tag, add it to the array
            // If the end of a closing tag, find the open tag in the array and remove it
            else if ($char === ' ' || $char === '>') {
                if ($add_tag) {
                    $add_tag = false;
                    $tag_arr[] = $add_tmp;
                }
                else if ($rm_tag) {
                    $rm_tag = false;
                    array_splice($tag_arr, array_search($rm_tmp, $tag_arr));
                }
            }
            // If tags haven't ended, keep appending the character to create the tag
            else if ($add_tag) {
                $add_tmp.= $char;
            }
            else if ($rm_tag) {
                $rm_tmp.= $char;
            }
        }

        // Count how many open tags there are
        $arr_size = count($tag_arr);

        // If there are open tags, reverse the array and output them
        // Otherwise, return the original string
        if ($arr_size > 0) {
            $reversed = array_reverse($tag_arr);
            for ($j = 0; $j < $arr_size; $j++) {
                $closing_text.= "</" . $reversed[$j] . ">";
            }

            return $html . $closing_text;
        }
        else {
            return $html;
        }
    }
}

?>