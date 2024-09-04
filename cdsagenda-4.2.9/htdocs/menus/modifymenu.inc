<?
require_once("$AGE/platform/authentication/sessinit.inc");

function CreateModifyMenu()
{
  global $authentication,$userid,$ida,$stylesheet,$db;

  $sql = "SELECT status
          FROM AGENDA
          WHERE id='$ida'";
  $res = $db->query($sql);
  $row = $res->fetchRow();
  $status = $row['status'];

  $userid_email = getEmail($userid);
  $text = "
<!-- `modifymenu.inc' starts here -->
<!-- $Id: modifymenu.inc,v 1.1.1.1.4.4 2004/07/29 10:06:06 tbaron Exp $ -->
" . CreateMenuHeader( "ModifyMenu" ) . "
		&nbsp;<font size=-1 class=headline><B>MODIFY</B></font><BR>";

  if ($status == "close") {
      $text .= "<font size=-1 color=green>&nbsp;&nbsp;&nbsp;This agenda is <b>closed</B>.<br>&nbsp;&nbsp;&nbsp;If you want to modify it, please contact the category administrator<br></font>";
  }
  else {
      $text .= "<FORM action=\"access.php\" method=POST target=\"modification\" onSubmit=\"window.open('access.php?ida=$ida&stylesheet=$stylesheet&AN='+this.AN.value);return false;\"><INPUT type=hidden name=ida value=$ida><INPUT type=hidden name=stylesheet value=$stylesheet>";
      if ( $authentication && canModifyAgenda($userid,$ida) ){
          // USER LOGGED
          $text .= "<font size=-1>&nbsp;&nbsp;&nbsp;You are allowed to modify this agenda<BR><BR>\n";
          $text .= "&nbsp;&nbsp;&nbsp;<INPUT type=submit name=modify value=modify></font>\n";
      }
      else {
          // Access without authentication
          $text .= "<font size=-1>&nbsp;&nbsp;&nbsp;Please enter your modification<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;number for this agenda,<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;then press \"Enter\":<BR><BR>\n";
          $text .= "&nbsp;&nbsp;&nbsp;<INPUT type=password size=10 name=AN></font>\n";
      }
      $text .= "</FORM>";
  }
  
  $text .= CreateMenuFooter() . "
<!-- `modifymenu.inc' ends here -->";
  
  return $text;
}
?>