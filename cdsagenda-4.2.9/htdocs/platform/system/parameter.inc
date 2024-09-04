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
	define( "PARAMETER_INCLUDED", TRUE );

        class parameter {

        // Questo file deve essere importato con path assoluto, in quanto un path relativo dovrebbe essere dinamico, cioe
        // dovrebbe essere il path relativo al file php che di volta in volta importa questa classe

        var $fileName = "/usr/local/lib/php/platform/parameter.conf";
        var $className = "parameter";

        // No constructor needed

        /* setToAnother     change the parameter file
         *
         * @param fileName  file to use as paramter file
         */
        function setToAnother( $fileName )
        {
            $this->fileName = $fileName;
        }

        /* exists       check if the file exists
         *
         *
         */
        function exists()
        {
            return file_exists( $this->fileName );
        }

        function retrieveParameter( $paramName )
        {
            $functionName = "retrieveParameter()";

            // Look for the variable into the GLOBAL scope
            //      if the parameter is already defined in an include file, we use it
            if ( isset( $GLOBALS[ $paramName ] ))
                return $GLOBALS[ $paramName ];
            // Insert here other ways to retrieve parameter
                    // LDAP
                    // Database
                    // ...
            
            // Look inside the parameter file
            
            if ( !file_exists( $this->fileName ))
            {
		// FIXME: now creating a log it will make a loop with the class logManager when logManager calls parameter
                // $log = new logManager;
                // $log->init( TRUE );
                // $log->logError($this->className, $functionName, "Parameter file :" . $paramName . " not found" );
                return false;
            }
            $paramFile = fopen( $this->fileName, "r" );
            $found = false;
            while ( !feof( $paramFile )&&( !$found ) )
            {
                $param = fgets( $paramFile, 255 );
                $pos = strpos( $param, $paramName );
                if ( $pos )
                {
                    // Copy the rest of the line
                    $readParam = substr( $param, $pos + strlen( $paramName ) + 3, strlen( $param ) - 1 - strlen( $paramName ) - 5 );
                    // NON LOGGATA
                    // if ( !fclose( $paramFile ) )
                        //$log->logError($this->className, $functionName, "Parameter file :" . $paramName . " cannot be closed" );
                    return $readParam;
                }
            }
            if ( !$found)
            {
                // $log = new logManager;
                // $log->init( TRUE );
                // NON LOGGATA
                // $log->logError($this->className, $functionName, "Parameter " . $paramName . " not found in parameter file " . $this->fileName );
                // if ( !fclose( $paramFile ) )
                // $log->logError($this->className, $functionName, "Parameter file :" . $paramName . " cannot be closed" );
                return false;
            }
        }
    }
?>