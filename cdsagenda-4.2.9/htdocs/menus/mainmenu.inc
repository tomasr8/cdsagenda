<?
function CreateMainMenu()
{
    global $mainmenulinks;

    $textMenu = "
<!-- `mainmenu.inc' starts here -->
<!-- $Id: mainmenu.inc,v 1.1.1.1.4.3 2003/03/28 10:25:42 tbaron Exp $ -->
" . CreateMenuHeader("MainMenu") . "
    	&nbsp;<font size=-1 class=headline><B>USEFUL&nbsp;LINKS</B></font>

	<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">";

    $i = 1;
    foreach ($mainmenulinks as $title => $url) {
        $textMenu .= "
		addStaticMenuItem(\"mmtext$i\",\"$title\",\"$url\",document);";
        $i++;
    }

    $textMenu .= "
	</SCRIPT>
" . CreateMenuFooter() . "
<!-- `mainmenu.inc' ends here -->";

    return $textMenu;
}
