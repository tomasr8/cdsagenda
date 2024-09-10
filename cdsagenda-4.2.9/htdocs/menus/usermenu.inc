<?
require_once("$AGE/platform/authentication/sessinit.inc");

function CreateUserMenu()
{
  global $authentication,$AGE_WWW,$ida,$stylesheet,$fid;

  $userid_email = getEmail($_SESSION["userid"]);
  $userMenuString = "
<!-- `usermenu.inc' starts here -->\n
<!-- $Id: usermenu.inc,v 1.1.1.1.4.3 2003/03/28 10:25:43 tbaron Exp $ -->
" . CreateMenuHeader( "UserMenu" ) . "
		&nbsp;<font size=-1 class=headline><B>USER $userid_email</B></font>
       <SCRIPT TYPE=\"text/javascript\" language=\"Javascript1.2\">\n";

      $userMenuString .= "      addStaticMenuItem(\"item7UserMenu\",\"why login?\",\"${AGE_WWW}/guide/english/login.php\",document,\"#9cf\");\n";

      if ( $userid_email == "guest" ) {
          // User not logged
          // log form
          $userMenuString .= "      addStaticMenuItem(\"item1UserMenu\",\"login\",\"${AGE_WWW}/userLog.php?nextpage=".urlencode( $GLOBALS[ "PHP_SELF" ] )."&nextquery=".urlencode( $GLOBALS[ "QUERY_STRING" ] )."\",document,\"#9cf\");\n";
      }
      else {
          $userMenuString .= "      addStaticMenuItem(\"item1UserMenu\",\"information\",\"${AGE_WWW}/info.php?nextpage=".urlencode( $GLOBALS[ "PHP_SELF" ] )."&nextquery=".urlencode( $GLOBALS[ "QUERY_STRING" ] )."\",document,\"#9cf\");\n";
          //$userMenuString .= "      addStaticMenuItem(\"item2UserMenu\",\"documents\",\"${AGE_WWW}/modification/htable.php?tp=0&id=$userid\",document,\"#9cf\");\n";
          //$userMenuString .= "
          //addStaticMenuItem(\"item3UserMenu\",\"roles\",\"${AGE_WWW}/modification/htable.php?tp=2&id=$userid\",document,\"#9cf\");\n";

          if (isSuperuser($userid)) {
              $userMenuString .= "      addStaticMenuItem(\"item6UserMenu\",\"manage groups\",\"${AGE_WWW}/manager/adminGroups.php?nextpage=".urlencode( $GLOBALS[ "PHP_SELF" ] )."&nextquery=".urlencode( $GLOBALS[ "QUERY_STRING" ] )."\",document,\"#9cf\");\n";
          }
              
          if ((preg_match("/List\.php/", $GLOBALS[ "PHP_SELF" ]) || preg_match("/displayLevel\.php/", $GLOBALS[ "PHP_SELF" ])) && canManageCategory($userid,($fid!=""?$fid:"0"))) {
              $userMenuString .= "      addStaticMenuItem(\"item4UserMenu\",\"manage category\",\"${AGE_WWW}/manager/admin.php?fid=$fid\",document,\"#9cf\");\n";
          }
          if ( $evt ) {
              $userMenuString .= "
		    <b>About this agenda show:</b><BR>\n
		    <a href=\"${AGE_WWW}/modification/htable.php?tp=1&idu=$userid&ide=$ida&id=$ida&rec=1&type=\" target=\"_TOP\">Documents</a><BR>\n
		    <a href=\"${AGE_WWW}/modification/htable.php?tp=3&id=$ida&rec=1\" target=\"_TOP\">Roles</a><BR>\n";
          }
          $userMenuString .= "      addStaticMenuItem(\"item5UserMenu\",\"logout\",\"${AGE_WWW}/userLog.php?action=logout&nextpage=".urlencode( $GLOBALS[ "PHP_SELF" ] )."&nextquery=".urlencode( $GLOBALS[ "QUERY_STRING" ] )."\",document,\"#9cf\");\n";
      }
  
  $userMenuString .= "
	</SCRIPT>
" . CreateMenuFooter() . "
<!-- `usermenu.inc' ends here -->\n";
  return  $userMenuString;
}
?>