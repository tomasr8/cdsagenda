<?
include "guideHeader.php";
?>

<STRONG class=headline><A HREF="index.php">top</A> > create&nbsp;and&nbsp;modify > <A HREF="modify.php">modify an agenda</A> > <A HREF="session_toolbar.php">session toolbar</A> duration</STRONG></TD></TR><TR>

<TD colspan=3>
<BR>
<SMALL><FONT color=red>Description:</FONT></SMALL>
<BLOCKQUOTE><SMALL>
This option is reachable from the <A HREF="session_toolbar.php">session toolbar</A> in the modification area.<BR><BR>
If you use this feature, the program will update the duration of each talk in this session by comparing its starting time with the starting time of the next talk taking place in the same room. The duration of the last talk in the session is not modified. You can also specify a default free time between 2 talks. Here are some examples:<BR>
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
<IMG SRC="<?print ${IMAGES_WWW};?>/timing_duration1.jpg">
</TD>
</TR>
<TR>
<TD>
<BR><BR><SMALL>after (free time set to 5 mn):</SMALL><BR><BR>
<IMG SRC="<?print ${IMAGES_WWW};?>/timing_duration2.jpg">
</TD>
</TR>
</TABLE>
</CENTER>
</SMALL></BLOCKQUOTE>

<SMALL><FONT color=green>See also:</FONT></SMALL>
<BLOCKQUOTE><SMALL>

<A HREF="compute_time.php">compute talks starting time</A><BR><BR>
</SMALL></BLOCKQUOTE>
</TD>
</TR>
</TABLE>
<BR><BR>
<HR>
</BODY>
</HTML>
