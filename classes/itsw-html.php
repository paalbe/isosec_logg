<?php
class ITSW_html
{
    private $dir;
    public function __construct($dir = null) {
        $this->dir = $dir;
    }

    public function getTemplate($file, $tag)
    {
        $path = $this->dir . '/' . $file;
        $templ = $this->getFile($path);
        return $this->getTemplatePart($templ, $tag, false);
    }

    public function getTemplatePart(&$templ, $tag, $cut, $placeholder = true)
    {
        $pattern = '/^<!--[ ]*#(start|end) ' . $tag . '[ ]+-->.*(\r\n|\n|\r)/m';
        $match;
        preg_match_all($pattern, $templ, $match, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
         if (count($match) != 2 || $match[0][1][0] != 'start' || $match[1][1][0] != 'end') {
            $this->errorExit("Incomplete or missing tag \"$tag\"", 'ITSW_html->getTemplatePart');
         }
         $start = $match[0][2][1] + 1;
         $end = $match[1][0][1];
         $res = substr($templ, $start, $end - $start);
         if ($cut) {
             $end = $match[0][0][1];
             $top = substr($templ, 0, $end);
             $start = $match[1][2][1];
             $bottom = substr($templ, $start);
             if ($placeholder) {
                $templ = $top . '{' . $tag . '}' . $bottom;
             } else {
                $templ = $top . $bottom;
             }
         }
         return $res;
    }
    public function replaceSymbols($dict, $html, $remove_unresolved = true) {
        $pattern = [];
        foreach ($dict as $k => $v) {
            if (is_string($v)) {
                $pattern[] = '~[{]' . $k . '[}]~';
                $replacement[] = $v;
            }
        }
        $res = $this->myReplace($pattern , $replacement, $html);
        if ($res === null) {
            $this->errorExit('Det oppstod en feil: (preq_replace, rc=' . preg_last_error() . ')', 'ITSW_html->replaceSymbols');
        }
        if ($remove_unresolved) {
            $res = $this->myReplace('/[{][\w0-9_-]+[}]/' , '', $res);  // Remove tags with no value
            $rc = preg_last_error();
        }
        return $res;
    }

    private function getFile($path)
    {
        if (!file_exists($path)) {
            $this->errorExit("Missing file:  $path", 'ITSW_html->getFile');
        }
        $res = file_get_contents($path);
        if ($res === false) {
            $this->errorExit("Error reading file:  $path", 'ITSW_html->getFile');
        }
        return $res;
    }

    private function myReplace($pattern, $repl, $subj) {
        $fix = false;
        if (is_array($repl)) {
            foreach($repl as $val) {
                if (strpos($val, '\\\\') !== false) {
                    $fix = true;
                }
            }
        } else if (strpos($repl, '\\\\')  !== false) {
            $fix = true;
        }
        if ($fix) {
            $repl = str_replace('\\\\', '\\\\\\\\',  $repl);
        }

        $res = preg_replace($pattern , $repl, $subj);
        return $res;
    }

    private function errorExit($msg) {
        throw new Exception($msg);
    }
}
