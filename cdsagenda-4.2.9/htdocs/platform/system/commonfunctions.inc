<?
/*
    dbAccess.inc give abstract database access to mysql and postgres
    Copyright (C) 2000/2001 Grassi Simone (for Cesena.net)
    contact email: simonegrassi@yahoo.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
                // Send the HTTP header for the authentication
                 // @params
                 //        cancelText: text sent to the user if cancel is pressed
                 // retVal
                 //         "cancel": pressed the cancel button
                 //   "false": if an error occur
                 //   "string": the given password
                 function sendHTTPauthentication( $cancelText, &$login, &$pass )
                 {
                          if(!isset($PHP_AUTH_USER))
                         {
                                 if ( !Header("WWW-Authenticate: Basic realm=\"My Realm\"" ))
                                         return false;
                                 if ( !Header("HTTP/1.0 401 Unauthorized" ))
                                         return false;
                                 echo $cancelText;

                                 return "cancel";
                            }
                         $login = $PHP_AUTH_USER;
                         $pass = $PHP_AUTH_PW;
                 }

                // Suppose $Template the instance of template.inc
                function outError( $msg, $code, &$Template, $emailTO = "" )
                {
                    $Template->set_var( "error_msg", $msg );
                    $Template->set_var( "error_code", $CODE_ERROR[ "__FILE__" ] . " " . $code );
                    $Template->set_var( "error_emailAdmin", ( $emailTO == "" ? $GLOBALS[ "admin_email" ] : $emailTO ));
                    $Template->set_var( "error_imageDIR", $GLOBALS[ "AGE_WWW" ] . "/" . $GLOBALS[ "IMAGES" ] );
                    $Template->pparse( "final_page", "error" );
                }

                // Return a random string of $len characters by composing successive random numbers
                function randStr( $len )
                {
                                $totLen = 0;
                                $outStr = "";
                                srand((double)microtime()*1000000);
                                while ( strlen( $outStr ) < $len )
                                        $outStr .= rand();
                                return substr( $outStr, 0, $len );
                }
?>