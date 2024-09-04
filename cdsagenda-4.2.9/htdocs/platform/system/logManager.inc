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

require_once 'parameter.inc';

    define( "LOGMANAGER_INCLUDED", TRUE );

    /* logManager
     *
     * methods to manager the log files
     *
     */
        class logManager {

        // log activation flags
        var $logLog;
        var $logDebug;
        var $logError;
        var $logExec;
        var $logSession;

        // log files location
        var $logFilePath;

        // disbled
        var $toEmails;

        // Constructor
        function logManager()
        {
           // if there are no parameter retrieve the default value from the configuration file
           $this->param = new parameter;
           // If called with a parameter do the initialization
           $this->init( $this->param->retrieveParameter( "sysLogActive" ));
        }

        /*  init
         *
         *  called by old code that doesn't use contructor
         *  called by the constructor when instantiated by new code
         *
         *  @param sysLogActive if true save also into sysLog
         */

        function init( $sysLogActive )
        {
            global $DOCUMENT_ROOT;
            global $firstExecute;

            $this->toEmails = false;
            if ( !isset( $sysLogActive ))
                  // not jet instantiated by the constructor
                  $sysLogActive = true;

            //error_reporting( E_ERROR| E_WARNING| E_PARSE| E_NOTICE| E_CORE_ERROR| E_CORE_WARNING| E_COMPILE_ERROR| E_COMPILE_WARNING| E_USER_ERROR| E_USER_WARNING| E_USER_NOTICE );

            $this->logLog = $this->param->retrieveParameter( "logLog" );
            $this->logDebug = $this->param->retrieveParameter( "logDebug" );
            $this->logError = $this->param->retrieveParameter( "logError" );
            $this->logExec = $this->param->retrieveParameter( "logExec" );
            $this->logSession = $this->param->retrieveParameter( "logSession" );

            $this->logFilePath = $this->param->retrieveParameter( "logFilePath" );
            // FIX: retrieve from the parameter.txt
            $php_error_level = $this->param->retrieveParameter( "errorLogLevel" );

            if ( $sysLogActive )
                define_syslog_variables();
            $dateStr = @date("dmY");
            error_reporting( $php_error_level );
        }

        function logExec( $className, $functionName, $errorMsg)
        {
            global $PHP_SELF;
            $logLevel = 3;
            $currentTime = @getdate( time() );
            if ( $this->logExec )
                error_log( $className . "." . $functionName . "[" . $currentTime["hours"] . ":" . $currentTime["minutes"] . ":" . $currentTime["seconds"] ."][" . $GLOBALS[ "REMOTE_ADDR" ] .  "]" . $errorMsg . "\n", 3, $this->logFilePath . "Exec" . date("dmY") . ".log" );
        }

        function logSession( $className, $functionName, $errorMsg)
        {
            global $PHP_SELF;
            $logLevel = 4;
            $currentTime = @getdate( time() );
            if ( $this->logSession )
                error_log( $className . "." . $functionName . "[" . $currentTime["hours"] . ":" . $currentTime["minutes"] . ":" . $currentTime["seconds"] ."][" . $GLOBALS[ "REMOTE_ADDR" ] .  "]" . $errorMsg . "\n", 3, $this->logFilePath . "Session" . date("dmY") . ".log" );
        }

        function logError( $className, $functionName, $errorMsg)
        {
            global $PHP_SELF,$php_errormsg;
            $logLevel = 2;
            $currentTime = @getdate( time() );
            if ( $this->logError )
                error_log( $className . "." . $functionName . "[" . $currentTime["hours"] . ":" . $currentTime["minutes"] . ":" . $currentTime["seconds"] ."][" . $GLOBALS[ "REMOTE_ADDR" ] .  "]" . $errorMsg . "\n", 3, $this->logFilePath . "Error" . date("dmY") . ".log" );
        }

        function logDebug( $className, $functionName, $errorMsg)
        {
            global $PHP_SELF,$php_errormsg;
            $logLevel = 1;
            $currentTime = @getdate( time() );
            if ( $this->logDebug )
                error_log( $className . "." . $functionName . "[" . $currentTime["hours"] . ":" . $currentTime["minutes"] . ":" . $currentTime["seconds"] ."][" . $GLOBALS[ "REMOTE_ADDR" ] .  "]" . $errorMsg . "\n", 3, $this->logFilePath  . "Debug" . date("dmY") . ".log" );
        }

        function emails( $errorType )
        {
            $functionName = "emails()";
            $param = new parameter();

            switch ( $errorType )
            {
                case "emergency":
                    $emailFile = $param->retrieveParameter( "emergencyGroupFile" );
                    if ( !$emailFile )
                        @fputs( $this->logFiles[0], $functionName, "could not send emergencyEmails");
                    break;
                case "average":
                    $emailFile = $param->retrieveParameter( "averageGroupFile" );
                    if ( !$emailFile )
                        @fputs( $this->logFiles[0], $functionName, "could not send averageEmails");
                    break;
                case "low":
                    $emailFile = $param->retrieveParameter( "lowGroupFile" );
                    if ( !$emailFile )
                        @fputs( $this->logFiles[0], $functionName, "could not send lowEmails");
                    break;
            }
        }
     }
?>