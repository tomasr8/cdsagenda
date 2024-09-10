<?php
// $Id: htable.php,v 1.1.1.1.4.4 2002/10/23 08:56:34 tbaron Exp $

// htable.php --- ???
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// htable.php is part of CDS Agenda.
//
// CDS Agenda is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// CDS Agenda is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with ; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// Commentary:
//
//
//

//----------------------------------------------------------------------
// FIXME: replace by PEAR accesses and make me work
//----------------------------------------------------------------------

require_once "../config/config.php";
require_once "../platform/template/template.inc";
// require_once "../platform/authentication/authorization.inc";
// require_once "../platform/archive/archive.inc";

$thisScript = "$AGE_WWW/modification/htable.php";

$Template = new Template( $PathTemplate );
$Template->set_file(array(
			  "mainpage"=>"htable.ihtml",
			  "error"=>"error.ihtml" ));

/***************************************************************************
 * Check if we are using Cookies to make persistent the ********************
 * agenda/password  authorizations *****************************************
 ***************************************************************************/

// // Some log found flag
// $thisFound = false;
// if ( !$showUserMenu && $activeCookiesForUserPW ) {
//   // Check for each kind of permission over the event with id $ida
//   $username = $idus = $showUserMenu = $authManager->whoIsLogged( $userAccessCookieName );
// }
// if ( !$showUserMenu ) {
//   // $showUserMenu = 0 mean no permission but $showUserMenu = -1 means no user logged
//   $showUserMenu = -1;
// }
// // Not authorized
// if (( !$showUserMenu ))
// {
//   if ( LOGGING )
//     $GLOBALS[ "log" ]->logDebug( __FILE__, __LINE__, " Cannot find the correct cookie for user authentication " );
//   outError( " You are not allowed to see this context (you are not a recognized user '$id' '$idu') ", "01", $Template );
//   exit;
// }


// WILL BE MOVED INSIDE THE PARAMETERS, ACTUALLY CHECK WITH THESE IFs
// ABOUT INFORMATION TO RETRIEVE
print_r($objList);
// if ($objList[$cat[$tp]]=="archive")
// {
//   $archive = new archive( $oldEventTrashPath );
//   if ( $tp == EVENT_DOCUMENTS )
//     {
//       // Retrieve all documents associated to an event
//       $resDoc = $archive->$funcList[$cat[$tp]]($ide, $idu, $type, "", $rec );
//       $event = new event( $ide );
//       $etitle = $event->title;
//     }
//   else if ( $tp == USER_DOCUMENTS )
//     if ($id != "")
//       //$userID, $actualUserID, $deleted = 0, $type = "", $fileName = ""
//       $resDoc = $archive->$funcList[$cat[$tp]]($id, $idu, 0, $type, "");
// }
// else if ($objList[$cat[$tp]]=="user")
// {
//   $archive = new user();
//   if ($id != "")
//     $resDoc = $archive->$funcList[$cat[$tp]]($id);
// }
// else if ($objList[$cat[$tp]]=="event")
// {
//   $archive = new event();
//   if ( $tp == EVENT_ROLE )
//     {
//       if ( $id != "" )
// 	$resDoc = $archive->$funcList[$cat[$tp]]($idu, $id, $rec); // in idu ci va l'user id dell'utente corrente
//       $event = new event( $id );
//       $etitle = $event->title;
//     }
// }

$str_help = "";
$str_tit = "";
$str_head = "";
$str_tab = "";

if ( $resDoc != 0 )
{
  $str_tab .= "<table style=\"{{tabfontstili}}\" border=\"{tborder}\" bordercolor=\"{tabdcolor}\" bgcolor=\"{tabgcolor}\" align=\"{tabalign}\" cellpadding=\"{cpad}\" cellspacing=\"{cspace}\" width=\"{tabw}\" height=\"{tabh}\">";
  $Template->set_var("table", $str_tab);
  $str_head .= "<tr bgcolor=\"{tbgcolor}\" align=\"{talign}\">";
  $Template->set_var( "header", $str_head );

  // riga dei titoli
  for ($j=0; $j<count($tabFieldsTitle[ $cat[$tp] ]); $j++)
    {
      $help=$tabFieldsTitle[ $cat[$tp] ][ $j ];
      $str_tit .= "<th style=\"{{titstili}}\"><font color=\"{tabtitcol}\">".$help."</font></th>\n";
    }

  $Template->set_var("titoli","$str_tit");

  $sf = $tabFieldsParam[ $cat[$tp] ][ "fbgcolor" ][0];
  for ($i=0; $i<count($resDoc); $i++)
    {
      $sf = $tabFieldsParam[ $cat[$tp] ][ "fbgcolor" ][($i) % count( $tabFieldsParam[ $cat[$tp] ][ "fbgcolor" ] )];
      if (count($tabFields[ $cat[$tp] ])!=0) {
	$str_help .= "<tr bgcolor=\"".$sf."\">\n";

	// $tabFields � il numero di campi
	for ($j=0; $j<count($tabFields[ $cat[$tp] ]); $j++)
	  {
	    $help=$tabFields[ $cat[$tp] ][ $j ];

	    switch ($tabFieldsType[ $cat[$tp] ][ $j ]){
	    case "TEXT":{

	      // il nome della form � i primi 3 caratteri del nome del campo + l'indice i
	      //$whichf = substr($tabFieldsTitle[ $cat[$tp] ][ $j ],0,3) . $i;

	      // il nome del textbox � = al nome del campo relativo
	      $which = $tabFields[ $cat[$tp] ][ $j ] . $i;

	      $paramsval = explode( " ", $tabFieldsLinksParamsVal[ $cat[$tp] ][ $j ]);
	      $params = explode( " ", $tabFieldsLinksParams[ $cat[$tp] ][ $j ]);
	      $param_str = "";
	      $hlp = "?";
	      for ( $k=0; $k<count($params); $k++ ) {
		if ($params[$k] == "QUERY")
		  $param_str .= $hlp.$params[ $k ] . "=" . ereg_replace("&","__",$HTTP_SERVER_VARS["QUERY_STRING"]);
		else if (isset($resDoc[$i]->$params[$k]))
		  $param_str .= $hlp . $params[ $k ] . "=" . $resDoc[$i]->$params[$k];
		else {
		  $hp = $params[$k];
		  if (isset($$hp))
		    $param_str .= $hlp . $params[ $k ] . "=" . $$hp;
		  else $param_str .= $hlp . $params[ $k ] . "=" . $paramsval[$k];
		}
		$hlp = "&";
	      }
	      $str_help .= "\n\n<td align=\"{calign}\">\n<input type='text' name='" . $which . "' value='" . ($resDoc[ $i ]->$help == "" ? "":$resDoc[ $i ]->$help) . "'><input type=\"button\" onClick=\"act('".$tabFieldsLinks[ $cat[$tp] ][ $j ] . $param_str . "&idx=$i" . "'); document.forma.submit();\" value=\"{modvalue}\" style=\"{{modstili}}\"></td>\n\n";
	    } break;

	    case "STATIC":{
	      $str_help .= "<td align=\"{calign}\">".($resDoc[ $i ]->$help == "" ? "&nbsp;":$resDoc[ $i ]->$help)."</td>";
	    } break;

	    case "LINK":{
	      $paramsval = explode( " ", $tabFieldsLinksParamsVal[ $cat[$tp] ][ $j ]);
	      $params = explode( " ", $tabFieldsLinksParams[ $cat[$tp] ][ $j ]);
	      $param_str = "";
	      $hlp = "?";
	      for ( $k=0; $k<count($params); $k++ ) {
		if ($params[$k] == "QUERY")
		  $param_str .= $hlp.$params[ $k ] . "=" . ereg_replace("&","__",$HTTP_SERVER_VARS["QUERY_STRING"]);
		else if (isset($resDoc[$i]->$params[$k]))
		  $param_str .= $hlp . $params[ $k ] . "=" . $resDoc[$i]->$params[$k];
		else {
		  $hp = $params[$k];
		  if (isset($$hp))
		    $param_str .= $hlp . $params[ $k ] . "=" . $$hp;
		  else $param_str .= $hlp . $params[ $k ] . "=" . $paramsval[$k];
		}
		$hlp = "&";
	      }
	      if ($resDoc[ $i ]->$help == "")
		$str_help .= "<td align=\"{calign}\">&nbsp;<A HREF=\"" . $tabFieldsLinks[ $cat[$tp] ][ $j ] . $param_str . "\"></A></td>\n";
	      else $str_help .= "<td align=\"{calign}\"><A HREF=\"" . $tabFieldsLinks[ $cat[$tp] ][ $j ] . $param_str . "\" " . $tabFieldsTypeParam[ $cat[$tp] ][ $j ] . ">" . $resDoc[ $i ]->$help . "</A></td>\n";
	    } break;

	    case "CHECK":{
	      $paramsval = explode( " ", $tabFieldsLinksParamsVal[ $cat[$tp] ][ $j ]);
	      $params = explode( " ", $tabFieldsLinksParams[ $cat[$tp] ][ $j ]);
	      $param_str = "";
	      $hlp = "?";
	      for ( $k=0; $k<count($params); $k++ ) {
		if ($params[$k] == "QUERY")
		  $param_str .= $hlp.$params[ $k ] . "=" . ereg_replace("&","__",$HTTP_SERVER_VARS["QUERY_STRING"]);
		else if (isset($resDoc[$i]->$params[$k]))
		  $param_str .= $hlp . $params[ $k ] . "=" . $resDoc[$i]->$params[$k];
		else {
		  $hl = $params[$k];
		  if (isset($$hl))
		    $param_str .= $hlp . $params[ $k ] . "=" . $$hl;
		  else $param_str .= $hlp . $params[ $k ] . "=" . $paramsval[$k];
		}
		$hlp = "&";
	      }
	      // il nome del checkbox � = al nome del campo relativo
	      $which = $tabFields[ $cat[$tp] ][ $j ] . $i;

	      // il nome della form � i primi 3 caratteri del nome del campo + l'indice i
	      $whichf = substr($tabFieldsTitle[ $cat[$tp] ][ $j ],0,3) . $i;
	      if (($resDoc[ $i ]->$help == "" ? "&nbsp;":$resDoc[ $i ]->$help) == "0")
		$str_help .= "\n\n<td align=\"{calign}\">\n<input type='checkbox' name ='" . $which . "' onClick=\"act('".$tabFieldsLinks[ $cat[$tp] ][ $j ] . $param_str . "&idx_=$i');document.forma.submit();\"><input type=\"hidden\" name=\"selector\" value=\"" . $tabFields[ $cat[$tp] ][ $j ] . "\"></td>\n\n";
	      else
		$str_help .= "\n\n<td align=\"{calign}\">\n<input type='checkbox' CHECKED name ='" . $which . "' onClick=\"act('" . $tabFieldsLinks[ $cat[$tp] ][ $j ] . $param_str."&idx_=$i');document.forma.submit();\"><input type=\"hidden\" name=\"selector\" value=\"" . $tabFields[ $cat[$tp] ][ $j ] . "\"></td>\n\n";
	    } break;

	    /*case "BUTTON":{
						$paramsval = explode( " ", $tabFieldsLinksParamsVal[ $cat[$tp] ][ $j ]);
						$params = explode( " ", $tabFieldsLinksParams[ $cat[$tp] ][ $j ]);
						$param_str = "";
						$hlp = "?";
						for ( $k=0; $k<count($params); $k++ ) {
							if ($params[$k] == "QUERY")
								$param_str .= $hlp.$params[ $k ] . "=" . ereg_replace("&","__",$HTTP_SERVER_VARS["QUERY_STRING"]);
							else if (isset($resDoc[$i]->$params[$k]))
								$param_str .= $hlp . $params[ $k ] . "=" . $resDoc[$i]->$params[$k];
							else {
								$hl = $params[$k];
								if (isset($$hl))
									$param_str .= $hlp . $params[ $k ] . "=" . $$hl;
								else $param_str .= $hlp . $params[ $k ] . "=" . $paramsval[$k];
							}
							$hlp = "&";
						}
						$str_help .= "\n\n<td align=\"{calign}\">\n<input type=\"submit\" value=\"" . $tabFieldsTitle[ $cat[$tp] ][ $j ] . "\"></td>\n";
					} break;*/

	    case "BUTTON_DYN":{
	      $paramsval = explode( " ", $tabFieldsLinksParamsVal[ $cat[$tp] ][ $j ]);
	      $params = explode( " ", $tabFieldsLinksParams[ $cat[$tp] ][ $j ]);
	      $param_str = "";
	      $hlp = "?";
	      for ( $k=0; $k<count($params); $k++ ) {
		if ($params[$k] == "QUERY")
		  $param_str .= $hlp.$params[ $k ] . "=" . ereg_replace("&","__",$HTTP_SERVER_VARS["QUERY_STRING"]);
		else if (isset($resDoc[$i]->$params[$k]))
		  $param_str .= $hlp . $params[ $k ] . "=" . $resDoc[$i]->$params[$k];
		else {
		  $hl = $params[$k];
		  if (isset($$hl))
		    $param_str .= $hlp . $params[ $k ] . "=" . $$hl;
		  else $param_str .= $hlp . $params[ $k ] . "=" . $paramsval[$k];
		}
		$hlp = "&";
	      }
	      // controllo di abilitazione/disabilitazione del bottone
	      $ab = $tabFields[ $cat[$tp] ][ $j ] . "Flag";
	      if ( $resDoc[ $i ]->$ab == 1)
		$str_help .= "\n\n<td align=\"{calign}\">\n<input type=\"submit\" value=\"" . $tabFieldsTitle[ $cat[$tp] ][ $j ] . "\"></td>\n";
	      else $str_help .= "\n\n<td align=\"{calign}\">\n<input type=\"button\" DISABLED value=\"" . $tabFieldsTitle[ $cat[$tp] ][ $j ] . "\" onClick=\"if (this.checked == true) this.checked=false; else this.checked=true; alert('" . $tabFieldsAlarm[ $cat[$tp] ][ $j ] . "');\"></td>\n";
	    } break;

	    case "CHECK_DYN":{
	      $paramsval = explode( " ", $tabFieldsLinksParamsVal[ $cat[$tp] ][ $j ]);
	      $params = explode( " ", $tabFieldsLinksParams[ $cat[$tp] ][ $j ]);
	      $param_str = "";
	      $hlp = "?";
	      for ( $k=0; $k<count($params); $k++ ) {
		if ($params[$k] == "QUERY")
		  $param_str .= $hlp.$params[ $k ] . "=" . ereg_replace("&","__",$HTTP_SERVER_VARS["QUERY_STRING"]);
		else if (isset($resDoc[$i]->$params[$k]))
		  $param_str .= $hlp . $params[ $k ] . "=" . $resDoc[$i]->$params[$k];
		else {
		  $hl = $params[$k];
		  if (isset($$hl))
		    $param_str .= $hlp . $params[ $k ] . "=" . $$hl;
		  else $param_str .= $hlp . $params[ $k ] . "=" . $paramsval[$k];
		}
		$hlp = "&";
	      }
	      // il nome del checkbox � = al nome del campo relativo
	      $which = $tabFields[ $cat[$tp] ][ $j ] . $i;

	      // controllo di abilitazione/disabilitazione del check
	      $ab = $tabFields[ $cat[$tp] ][ $j ] . "Flag";
	      if ( $resDoc[ $i ]->$ab == 1) {
		// il nome della form � i primi 3 caratteri del nome del campo + l'indice i
		$whichf = substr($tabFieldsTitle[ $cat[$tp] ][ $j ],0,3) . $i;
		if (($resDoc[ $i ]->$help == "" ? "&nbsp;":$resDoc[ $i ]->$help) == "0")
		  $str_help .= "\n\n<td align=\"{calign}\">\n<input type='checkbox' name ='" . $which . "' onClick=\"act('".$tabFieldsLinks[ $cat[$tp] ][ $j ] . $param_str."&idx_=$i');document.forma.submit();\"><input type=\"hidden\" name=\"selector\" value=\"" . $tabFields[ $cat[$tp] ][ $j ] . "\"></td>\n\n";
		else
		  $str_help .= "\n\n<td align=\"{calign}\">\n<input type='checkbox' CHECKED name ='" . $which . "' onClick=\"act('".$tabFieldsLinks[ $cat[$tp] ][ $j ] . $param_str."&idx_=$i');document.forma.submit();\"><input type=\"hidden\" name=\"selector\" value=\"" . $tabFields[ $cat[$tp] ][ $j ] . "\"></td>\n\n";
	      }
	      else {
		// il nome della form � i primi 3 caratteri del nome del campo + l'indice i
		$whichf = substr($tabFieldsTitle[ $cat[$tp] ][ $j ],0,3) . $i;
		if (($resDoc[ $i ]->$help == "" ? "&nbsp;":$resDoc[ $i ]->$help) == "0")
		  $str_help .= "\n\n<td align=\"{calign}\">\n<input type='checkbox' DISABLED name ='" . $which . "' onClick=\"if (this.checked == true) this.checked=false; else this.checked=true; alert('" . $tabFieldsAlarm[ $cat[$tp] ][ $j ] . "');\"><input type=\"hidden\" name=\"selector\" value=\"" . $tabFields[ $cat[$tp] ][ $j ] . "\"></td>\n\n";
		else
		  $str_help .= "\n\n<td align=\"{calign}\">\n<input type='checkbox' DISABLED CHECKED name ='" . $which . "' onClick=\"if (this.checked == true) this.checked=false; else this.checked=true; alert('" . $tabFieldsAlarm[ $cat[$tp] ][ $j ] . "');\"><input type=\"hidden\" name=\"selector\" value=\"" . $tabFields[ $cat[$tp] ][ $j ] . "\"></td>\n\n";
	      }
	    } break;
	    case "TEXT_DYN":{

	      // il nome della form � i primi 3 caratteri del nome del campo + l'indice i
	      //$whichf = substr($tabFieldsTitle[ $cat[$tp] ][ $j ],0,3) . $i;

	      // il nome del textbox � = al nome del campo relativo
	      $which = $tabFields[ $cat[$tp] ][ $j ] . $i;

	      $paramsval = explode( " ", $tabFieldsLinksParamsVal[ $cat[$tp] ][ $j ]);
	      $params = explode( " ", $tabFieldsLinksParams[ $cat[$tp] ][ $j ]);
	      $param_str = "";
	      $hlp = "?";
	      for ( $k=0; $k<count($params); $k++ ) {
		if ($params[$k] == "QUERY")
		  $param_str .= $hlp.$params[ $k ] . "=" . ereg_replace("&","__",$HTTP_SERVER_VARS["QUERY_STRING"]);
		else if (isset($resDoc[$i]->$params[$k]))
		  $param_str .= $hlp . $params[ $k ] . "=" . $resDoc[$i]->$params[$k];
		else {
		  $hp = $params[$k];
		  if (isset($$hp))
		    $param_str .= $hlp . $params[ $k ] . "=" . $$hp;
		  else $param_str .= $hlp . $params[ $k ] . "=" . $paramsval[$k];
		}
		$hlp = "&";
	      }
	      // controllo di abilitazione/disabilitazione del check
	      $ab = $tabFields[ $cat[$tp] ][ $j ] . "Flag";
	      if ( $resDoc[ $i ]->$ab == 1)
		$str_help .= "\n\n<td align=\"{calign}\">\n<input type='text' name='" . $which . "' value='" . ($resDoc[ $i ]->$help == "" ? "":$resDoc[ $i ]->$help) . "'><input type=\"button\" onClick=\"act('".$tabFieldsLinks[ $cat[$tp] ][ $j ] . $param_str . "&idx=$i" . "'); document.forma.submit();\" value=\"{modvalue}\" style=\"{{modstili}}\"></td>\n\n";
	      else $str_help .= "\n\n<td align=\"{calign}\">\n<input type='text' DISABLED onFocus=\"te=this.value;\" onChange=\"this.value=te;\" name='" . $which . "' value='" . ($resDoc[ $i ]->$help == "" ? "":$resDoc[ $i ]->$help) . "'><input type=\"button\" DISABLED onClick=\"if (this.checked == true) this.checked=false; else this.checked=true; alert('" . $tabFieldsAlarm[ $cat[$tp] ][ $j ] . "');\" value=\"{modvalue}\" style=\"{{modstili}}\"></td>\n\n";
	    } break;
	    case "LINK_DYN":{
	      $paramsval = explode( " ", $tabFieldsLinksParamsVal[ $cat[$tp] ][ $j ]);
	      $params = explode( " ", $tabFieldsLinksParams[ $cat[$tp] ][ $j ]);
	      $param_str = "";
	      $hlp = "?";
	      for ( $k=0; $k<count($params); $k++ ) {
		if ($params[$k] == "QUERY")
		  $param_str .= $hlp.$params[ $k ] . "=" . ereg_replace("&","__",$HTTP_SERVER_VARS["QUERY_STRING"]);
		else if (isset($resDoc[$i]->$params[$k]))
		  $param_str .= $hlp . $params[ $k ] . "=" . $resDoc[$i]->$params[$k];
		else {
		  $hp = $params[$k];
		  if (isset($$hp))
		    $param_str .= $hlp . $params[ $k ] . "=" . $$hp;
		  else $param_str .= $hlp . $params[ $k ] . "=" . $paramsval[$k];
		}
		$hlp = "&";
	      }
	      // controllo di abilitazione/disabilitazione del check
	      $ab = $tabFields[ $cat[$tp] ][ $j ] . "Flag";
	      if ( $resDoc[ $i ]->$ab == 1){
		if ($resDoc[ $i ]->$help == "")
		  $str_help .= "<td align=\"{calign}\"><A HREF=\"" . $tabFieldsLinks[ $cat[$tp] ][ $j ] . $param_str . "\"></A></td>\n";
		else $str_help .= "<td align=\"{calign}\"><A HREF=\"" . $tabFieldsLinks[ $cat[$tp] ][ $j ] . $param_str . "\" " . $tabFieldsTypeParam[ $cat[$tp] ][ $j ] . ">" . $resDoc[ $i ]->$help . "</A></td>\n";
	      }
	      else
		$str_help .= "<td align=\"{calign}\"><U>" . $resDoc[ $i ]->$help . "</U></td>\n";
	    } break;
	    }
	  }
	$str_help .= "</tr>\n";
      }
    }
}
else {
  $str_help="<br><br><center><b><font size=\"+2\" color=\"red\">Non ci sono elementi!</font></b></center>";

  $Template->set_var("titoli", $str_tit);
  $Template->set_var( "header", $str_head );
  $Template->set_var("table", $str_tab);
}

$Template->set_var( "riga", $str_help );

$Template->set_var( "legenda", $tabLegenda[ $cat[ $tp ] ]);

$keys = array_keys( $tabFieldsParam[ $cat[ $tp ]]);
for ($n = 0; $n < count($tabFieldsParam[ $cat[ $tp ] ]); $n++)
     if ( $keys[$n][strlen($keys[$n]) - 1] == "_")
     $Template->set_var( ereg_replace("_","",$keys[$n]), "(".${$tabFieldsParam[ $cat[ $tp ]][ $keys[ $n ]]}.")");
     else $Template->set_var( $keys[$n], $tabFieldsParam[ $cat[ $tp ]][ $keys[ $n ]] );

     $Template->pparse( "finalpage","mainpage" );
     ?>
