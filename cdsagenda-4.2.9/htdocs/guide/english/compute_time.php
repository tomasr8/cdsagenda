<?
include "guideHeader.php";
?>

<STRONG class=headline><A HREF="index.php">top</A> > create&nbsp;and&nbsp;modify > <A HREF="modify.php">modify an agenda</A> > <A HREF="session_toolbar.php">session toolbar</A> > compute talks starting time</STRONG></TD></TR><TR>

<TD colspan=3>
<BR>
<SMALL><FONT color=red>Description:</FONT></SMALL>
<BLOCKQUOTE><SMALL>
This option is reachable from the <A HREF="session_toolbar.php">session toolbar</A> in the modification area.<BR><BR>
If you use this feature, the program will update the starting time of each talk by adding to the starting time of the preceding talk its duration. The starting time of the first talk in the session is not modified.<BR><BR>
This can be use to move a talk among others in a session. Set the time of the talk so that it goes just at the right place, then use "Compute talks starting time".<BR><BR>
You can also specify a default free time between 2 talks. Here are some examples:<BR>
<BR>
<CENTER>
<TABLE>
<TR>
<TD rowspan=2>
<SMALL>before (original configuration):</SMALL><BR><BR>
<IMG SRC="<?print ${IMAGES_WWW};?>/timing1.jpg">
</TD>
<TD>
<SMALL>after (free time set to 0 mn):</SMALL><BR><BR>
<IMG SRC="<?print ${IMAGES_WWW};?>/timing_starting1.jpg">
</TD>
</TR>
<TR>
<TD>
<BR><BR><SMALL>after (free time set to 5 mn):</SMALL><BR><BR>
<IMG SRC="<?print ${IMAGES_WWW};?>/timing_starting2.jpg">
</TD>
</TR>
</TABLE>
</CENTER>
</SMALL></BLOCKQUOTE>

<SMALL><FONT color=green>See also:</FONT></SMALL>
<BLOCKQUOTE><SMALL>

<A HREF="compute_duration.php">compute talks duration</A><BR><BR>
</SMALL></BLOCKQUOTE>
</TD>
</TR>
</TABLE>
<BR><BR>
<HR>
</BODY>
</HTML>
