<?php
/*
  jpcache.php v1.1.1 [2001-06-13]
  Copyright  2001 Jean-Pierre Deckers <jpcache@weirdpier.com>

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

/*
 Credits:

    Most things taken from 
        phpCache        <nathan@0x00.org> (http://www.0x00.org/phpCache)
        gzdoc.php       <catoc@163.net> and <jlim@natsoft.com.my> 
        jr-cache.php    <jr-jrcache@quo.to>

    Inspired by the following threads:
        http://www.aota.net/ubb/Forum15/HTML/000738-1.html
        http://www.aota.net/ubb/Forum15/HTML/000746-1.html
        http://www.aota.net/ubb/Forum15/HTML/000749-1.html

 Note:
    I do not claim anything. 
    Just a first try to 'release' something under the GPL.

 More info on http://www.weirdpier.com/jpcache/

*/
 
/******************************************************************************/

$CACHE_TIME=900;            // Default: 900 - number seconds to cache
$CACHE_DEBUG=0;             // Default: 0 - Turn debugging on/off
$SINGLE_SITE=1;             // Default: 1 - No servername in file
$CACHE_ON=1;                // Default: 1 - Turn caching on/off
$USE_GZIP=0;                // Default: 1 - Whether or not to use GZIP
$CACHE_POST=1;              // Default: 1 - Should POST's be cached
    
define(CACHE_DIR, "$TMPDIR");  // Default: /tmp - Default cache directory
define(CACHE_GC, 1);        // Default: 1 - Probability of garbage collection (i.e: 1%)
    
define(VERSION, "1.1.1");   // Version of jpcache

/******************************************************************************/

/* This resets the cache state */
function jpcache_reset() 
{
    global $cache_absfile, $cache_data, $cache_variables;
    $cache_absfile  =NULL;
    $cache_data     =array();
    $cache_variables=array();
}

/* Saves a variable state between caching */
function cache_variable($vn) 
{
    global $cache_variables;
    cache_debug("Adding $vn to the variable store");
    $cache_variables[]=$vn;
}

/* Take a wild guess... */
function cache_debug($s) 
{
    global $CACHE_DEBUG;
    static $debugline;

    if ($CACHE_DEBUG) 
        {
            $debugline++;
            header("X-Debug-$debugline: $s");
        }
}

/* Generates the key for the request */
function cache_default_key() 
{
    global $HTTP_POST_VARS, $HTTP_GET_VARS;
    return md5("POST=" . serialize($HTTP_POST_VARS) . " GET=" . serialize($HTTP_GET_VARS));
}

/* Returns the default object used by the helper functions */
function cache_default_object() 
{
    global $SCRIPT_URI, $SERVER_NAME, $SCRIPT_NAME, $SINGLE_SITE;

    if ($SINGLE_SITE)
        {
            $name=$SCRIPT_NAME;
        } 
    else 
        {
            $name=$SCRIPT_URI;
        }

    if ($name=="") 
        {
            $name="http://$SERVER_NAME/$SCRIPT_NAME";
        }
    return $name;
}

/* Caches the current page based on the page name and the GET/POST
        variables.  All must match or else it will not be fectched
        from the cache! */
function cache_all($cachetime=120) 
{
    $key=cache_default_key();
    $object=cache_default_object();
    return cache($cachetime, $object, $key);
}

/* Obtain a lock on the cache storage, this can be stripped out
        and changed to a different handler like a database or
        whatever */
function cache_lock($file, $open=TRUE) 
{
    static $fp;
    if ($open) 
        {
            $fp=fopen($file, "r");
            $ret=flock($fp, LOCK_EX);
        } 
    else 
        {
            if (!$fp)
                {
                    cache_debug("Invalid file handle?!");
                    $fp=NULL;
                    return;
                }
            flock($fp, LOCK_UN) or die("Cannot Release Lock");
            fclose($fp);
            $fp=NULL;
        }
    return $ret;

}

/* This is the function that writes out the cache */
function cache_write($file, $data) 
{
    $fp=@fopen($file, "w");
    if (!$fp) 
        {
            cache_debug("Failed to open for write out to $file");
            return FALSE;
        }
    fwrite($fp, $data, strlen($data));
    fclose($fp);
    return TRUE;
}

/* This function reads in the cache */
function cache_read($file) 
{
    $fp=@fopen($file, "r");
    if (!$fp) 
        {
            return NULL;
        }
    flock($fp, LOCK_SH);
    $buff="";
    while (($tmp=fread($fp, 4096))) 
        {
            $buff.=$tmp;
        }
    fclose($fp);
    return $buff;
}

function cache_storage($cacheobject) 
{
    return CACHE_DIR . "/" . $cacheobject;
}

/* Cache garbage collection */
function cache_gc() 
{
    $cache_dir=CACHE_DIR;
    cache_debug("Running gc");
    $dp=opendir($cache_dir);
    if (!$dp) 
        {
            cache_debug("Error opening $cache_dir for cleanup");
            return FALSE;
        }
    while (!(($de=readdir($dp))===FALSE)) 
        {
            // To get around strange php-strpos, add additional char
            if (strpos(" $de", "jpcache-")==1)
                {
                    $absfile=$cache_dir . "/" . $de;
                    $cachestuff=cache_read($absfile);
                    $thecache=unserialize($cachestuff);
                    if (is_array($thecache)) 
                        {
                            if ($thecache["cachetime"]!="0" && $thecache["expire"]<=time()) 
                                {
                                    cache_lock($absfile, TRUE);                        
                                    if (@unlink($absfile)) 
                                        {
                                            cache_debug("Deleted $absfile");
                                        } 
                                    else 
                                        {
                                            cache_debug("Failed to delete $absfile");
                                        }
                                    cache_lock($absfile, FALSE);                        
                                } 
                            else 
                                {
                                    cache_debug("$absfile expires in " . ($thecache["expire"]-time()));
                                }
                        } 
                    else 
                        {
                            cache_debug("$absfile is empty, being processed in another process?");
                        }
                }
        }
}

/* Caches $object based on $key for $cachetime, will return 0 if the object has expired 
       or the object does not exist. */
function cache($cachetime, $object, $key=NULL) 
{
    global $cache_absfile, $cache_file, $cache_data, $gzcontent, $CACHE_ON;
        
    if (!$CACHE_ON) 
        {
            cache_debug("Not caching, CACHE_ON is off");
            return 0;
        }
        
    $curtime=time();
    $cache_file=$object;
        
    // Make it a valid name
    $cache_file=eregi_replace("[^A-Z,0-9,=]", "_", $cache_file);
    $key=eregi_replace("[^A-Z,0-9,=]", "_", $key);
    
    cache_debug("Caching based on OBJECT=$cache_file KEY=$key");
                
    $cache_file="jpcache-" . $cache_file . ":" . $key;
        
    $cache_absfile=cache_storage($cache_file);
        
    // Can we access the cache_file ?
    if (($buff=cache_read($cache_absfile))) 
        {
            cache_debug("Opened the cache file");
            $cdata=unserialize($buff);
            if (is_array($cdata)) 
                {
                    $curco=$cdata["cache_object"];
                    if ($curco!=$cache_absfile) 
                        {
                            cache_debug("WTF?! That is not my cache file! got=$curco wanted=$cache_absfile");
                        }
                    else 
                        {
                            if ($cdata["cachetime"]=="0" || $cdata["expire"]>=$curtime) 
                                {
                                    // data not yet expired (or never expiring)
                        
                                    $expirein=$cdata["expire"]-$curtime+1;
                                    cache_debug("Cache expires in $expirein");
                        
                                    // restore variables
                                    if (is_array($cdata["variables"])) 
                                        {
                                            foreach ($cdata["variables"] as $k=>$v) 
                                                {
                                                    cache_debug("Restoring variable $k to value $v");
                                                    $GLOBALS[$k]=$v;
                                                }
                                        }
                        
                                    // restore gzcontent
                                    cache_debug("Restoring gzipped content");
                                    $gzcontent = $cdata["gzcontent"];
                        
                                    $ret=$expirein;
                                    if ($cdata["cachetime"]=="0") 
                                        {
                                            $ret="INFINITE";
                                        }
                                    jpcache_reset();
                                    return $ret; 
                                }
                        }
                }
        } 
    else 
        {
            // No cache file (yet) or unable to read
            cache_debug("No previous cache of $cache_absfile or unable to read");
        }
        
    // If we came here: start caching!
        
    // Create the file for this page and lock it
    $oldum=umask();
    umask(0022); 
    if (@readlink($cache_absfile)) 
        {
            cache_debug("$cache_absfile is a symlink! not caching!");
            $cache_absfile=NULL;
        } 
    else 
        {
            cache_debug("Created $cache_absfile, waiting for callback");
            @touch($cache_absfile);
            cache_lock($cache_absfile, TRUE);
        }
    umask($oldum);
        
    // Set expire and cachetime
    $cache_data["expire"]=$curtime + $cachetime;
    $cache_data["cachetime"]=$cachetime;
       
    return 0;
}

/* Does the actual caching */
function writecache($gzcontent)
{
    global $cache_absfile, $cache_data, $cache_variables, $CACHE_ON;
        
    if (!$CACHE_ON) 
        {
            cache_debug("Not caching, CACHE_ON is off");
            return 0;
        }
        
    if ($cache_absfile!=NULL)
        {
            $variables=array();
            foreach ($cache_variables as $vn) 
                {
                    if (isset($GLOBALS[$vn])) 
                        {
                            $val=$GLOBALS[$vn];
                            cache_debug("Setting variable $vn to $val");
                            $variables["$vn"]=$val;
                        }
                }
            // Fill cache_data
            $cache_data["gzcontent"]=$gzcontent;            
            $cache_data["cache_object"]=$cache_absfile;
            $cache_data["variables"]=$variables;
            $datas=serialize($cache_data);
            // write data
            cache_write($cache_absfile, $datas);
            // unlock cachefile
            cache_lock($cache_absfile, FALSE);
        }
}

/* getEncoding() 
 *
 * Are we capable of receiving gzipped data ?
 * Returns the encoding that is accepted. Maybe additional check for Mac ?
 */
function getEncoding()
{ 
    global $HTTP_ACCEPT_ENCODING;
    if (headers_sent() || connection_aborted())
        { 
            return 0; 
        } 
    if (strpos($HTTP_ACCEPT_ENCODING,'x-gzip') !== false)
        {
            return "x-gzip";
        }
    if (strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false)
        {
            return "gzip";
        }
    return 0; 
}

/* jpCacheInit()
 *
 * Checks some global variables and might decide to disable caching
 * and calls appropriate initialization-methods
 */
function jpCacheInit()
{
    global $CACHE_TIME, $CACHE_ON, $CACHE_POST, $HTTP_POST_VARS, $cachetimeout;

    // Override default CACHE_TIME ?
    if (isset($cachetimeout))
        {
            $CACHE_TIME=$cachetimeout;
        }

    // Force cache off when POST occured when you don't want it cached
    if (!$CACHE_POST && (count($HTTP_POST_VARS) > 0)) 
        {
            $CACHE_ON = 0;
            $CACHE_TIME = -1;
        }
        
    // A cachetimeout of -1 disables writing, only ETag and content encoding if possible
    if ($CACHE_TIME == -1)
        {
            $CACHE_ON=0;
        }
        
    // Reset cache
    jpcache_reset();
}

/* jpCacheGC()
 *
 * Handles the garbagecollection call
 */
function jpCacheGC()
{
    // Should we garbage collect ?
    if (CACHE_GC>0) 
        {
            mt_srand(time(NULL));
            $precision=100000;
            $r=(mt_rand()%$precision)/$precision;
            if ($r<=(CACHE_GC/100)) 
                {
                    cache_gc();
                }
        }
}

/* jpCacheEnd()
 *
 * This one is called by the callback-funtion of the ob_start
 */
function jpCacheEnd($contents)
{
    cache_debug("Callback happened");
    global $size, $crc32, $USE_GZIP;

    $size = strlen($contents);
    $crc32 = crc32($contents);

    if ($USE_GZIP) {
        $gzcontent = gzcompress($contents, 9);
    } 
    else {
        $gzcontent = $contents;
    }
        
    // cache these thingies, as they are on original content
    // which is lost after this
    cache_variable("size");
    cache_variable("crc32");
        
    // write the cache
    writecache($gzcontent);
        
    // Return flushed data
    //return jpCacheFlush($gzcontent, $size, $crc32);
}

/* jpCacheFlush()
 *
 * Responsible for final flushing everything.
 * Sets ETag-headers and returns "Not modified" when possible
 *
 * When ETag doesn't match (or is invalid), it is tried to send
 * the gzipped data. If that is also not possible, we sadly have to
 * uncompress (assuming $USE_GZIP is on)
 */
function jpCacheFlush($gzcontents, $size, $crc32)
{
    global $HTTP_SERVER_VARS, $USE_GZIP;
        
    // First check if we can send last-modified
    $ret = NULL;
        
    // Are we gzipping ?
    if ($USE_GZIP) 
        {
            $ret = gzuncompress($gzcontents);
        } 
    else 
        {
            $ret=$gzcontents;
        }
    return $ret;
}
?>