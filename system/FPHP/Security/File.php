<?php

namespace FPHP\Security;

use FPHP\Security;

/**
 * 
 * @author anthony
 * 
 */
class File extends Security
{
    public static function sanitizeFilename($filename)
    {
        $bad = array("<!--", "-->", "'", "<", ">", '"', '&', '$', '=', ';', '?',
                    '/', "%20", "%22",
                    "%3c",      // <
                    "%253c",    // <
                    "%3e",      // >
                    "%0e",      // >
                    "%28",      // (
                    "%29",      // )
                    "%2528",    // (
                    "%26",      // &
                    "%24",      // $
                    "%3f",      // ?
                    "%3b",      // ;
                    "%3d"       // =
                );

        
        $filename = str_replace($bad, '', $filename);
        return stripslashes($filename);
    }
}