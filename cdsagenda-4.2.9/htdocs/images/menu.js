<!--

// portable (?) menu package originally developped for the CERN public pages
// by Yves Perrin / EP

var isNS4 = (document.layers) ? true : false; 
var isIE4 = (document.all && !document.getElementById) ? true : false; 
var isIE5 = (document.all && document.getElementById) ? true : false; 
var isNS6 = (!document.all && document.getElementById) ? true : false;
var isMAC = (navigator.appVersion.indexOf("Mac")!=-1) ? true : false;
 

var mouseX = 0;
var mouseY = 0;
var menuYoffset = 20;
var menudivs = new Array();


function checkwhere(e) {
  scrollLeft = 0;
  scrollTop = 0;

  if (isNS4 || isNS6) {  // NS6 supports pageY but not scrollTop!!!
    mouseX = e.pageX;
    mouseY = e.pageY;
  } else {
    if (isIE4) {
      mouseX = event.clientX + document.body.scrollLeft; // take scrolling into account;
      mouseY = event.clientY +  document.body.scrollTop; // take scrolling into account;
    } else {
      if (isIE5) { // NS6 not here because does NOT support scrollXXX!
        if (!e) { e = window.event; }   // for IE5
        // for Explorer in standards compliance mode the scrollXXX properties
        // belong to document.documentElement instead of document.body
        if (document.documentElement && document.documentElement.scrollTop) {
          scrollLeft = document.documentElement.scrollLeft;
          scrollTop  = document.documentElement.scrollTop;
        } else {
          if (document.body) {
            scrollLeft = document.body.scrollLeft;
            scrollTop  = document.body.scrollTop;
          }
        }
        mouseX = e.clientX + scrollLeft;
        mouseY = e.clientY + scrollTop;
      }
    }
  }

  for(var menuKey in menudivs) {
    if (menudivs[menuKey].menuOn && !(menudivs[menuKey].onAnchor)) {

      if (!((mouseX >= menudivs[menuKey].anchorLeft) && (mouseX <= (menudivs[menuKey].anchorLeft+menudivs[menuKey].menuWidth)) 
            && (mouseY >= menudivs[menuKey].anchorTop) && (mouseY <= (menudivs[menuKey].anchorTop+menudivs[menuKey].anchorHeight+menudivs[menuKey].menuHeight)))
         ) {
        hideMenu(menudivs[menuKey].id);
      }
    }
  }

  // status="mouseX = "+mouseX+", mouseY = "+mouseY+", anchorLeft = "+menudivs[0].anchorLeft+", anchorTop = "+menudivs[0].anchorTop; 
}

function getAnchorLocation(menuId) {
  if (isNS4) {
    // anchorIdStr = menudivs[menuId].anchorId;
    anchor = eval("document.anchors."+menudivs[menuId].anchorId); 
    menudivs[menuId].anchorLeft = anchor.x;
    menudivs[menuId].anchorTop  = anchor.y;
  } else {
    if (isIE4) {
      anchor = eval("document.all."+menudivs[menuId].anchorId); 
    } else {
      if (isIE5 || isNS6) {
        anchor = document.getElementById(menudivs[menuId].anchorId);
      }
    }
    currentObject = anchor;
    anchorLeft = 0;
    anchorTop = 0;
    while (currentObject.offsetParent) {
      anchorLeft += currentObject.offsetLeft;
      anchorTop  += currentObject.offsetTop;
      if (isIE5 && isMAC) {
        leftM = currentObject.offsetParent.leftMargin;
        if (!leftM) leftM = 0;
        anchorLeft += parseInt(leftM);
        topM = currentObject.offsetParent.topMargin;
        if (!topM) topM = 0;
        anchorTop += parseInt(topM);
      }
      currentObject = currentObject.offsetParent;
    }
    menudivs[menuId].anchorLeft = anchorLeft;
    menudivs[menuId].anchorTop  = anchorTop;
  }
  // alert('langLeft ='+ langLeft+ ' and langTop =' +langTop); 
}

function getMenuZone(menuId) {  // two rectangles in which the menu should be shown.
  if (isNS4) {
    menuLayer = eval("document.layers."+menuId); 
    menudivs[menuId].menuWidth  = parseInt(menuLayer.clip.width);
    menudivs[menuId].menuHeight = parseInt(menuLayer.clip.height);
    // span = eval("document.anchors["+menudivs[menuId].spanId+"]"); 
    menudivs[menuId].anchorWidth  = 0; // parseInt(span.clip.width);
    menudivs[menuId].anchorHeight = 17; // parseInt(span.clip.height);
  } else {
    if (isIE4) {
      menuLayer = eval("document.all."+menuId); 
      menudivs[menuId].menuWidth  = parseInt(menuLayer.offsetWidth);
      menudivs[menuId].menuHeight = parseInt(menuLayer.offsetHeight);
      span = eval("document.all["+menudivs[menuId].spanId+"]"); 
      menudivs[menuId].anchorWidth  = parseInt(span.offsetWidth);
      menudivs[menuId].anchorHeight = parseInt(span.offsetHeight);
    } else {
      if (isIE5 || isNS6) {
        menuLayer = document.getElementById(menuId);
        menudivs[menuId].menuWidth  = parseInt(menuLayer.offsetWidth);
        menudivs[menuId].menuHeight = parseInt(menuLayer.offsetHeight);
        span = document.getElementById(menudivs[menuId].spanId);
        menudivs[menuId].anchorWidth  = parseInt(span.offsetWidth);
        menudivs[menuId].anchorHeight = parseInt(span.offsetHeight);
      }
    }
  }
  // alert('menuWidth ='+ menuWidth + ' and menuHeight =' + menuHeight); 
  // alert('langWidth ='+ langWidth + ' and langHeight =' + langHeight); 
} 

function setOnAnchor(menuId) {
  getAnchorLocation(menuId);
  getMenuZone(menuId);
  menudivs[menuId].menuOn = true;
  menudivs[menuId].onAnchor = true;
  showMenu(menuId);
}  

function clearOnAnchor(menuId) {
  menudivs[menuId].onAnchor = false;
}  

function showMenu(menuId) {
  if (isNS4) {
    thisLayerProperties = eval("document.layers."+menuId); 
    showval = "show";
  } else {
    showval = "visible";
    if (isIE4) {
      thisLayer = eval("document.all."+menuId); 
      thisLayerProperties = thisLayer.style;
    } else {
      if (isIE5 || isNS6) {
        thisLayer = document.getElementById(menuId);
        thisLayerProperties = thisLayer.style;
      }
    }
  } 
  thisLayerProperties.left = menudivs[menuId].anchorLeft;
  thisLayerProperties.top  = menudivs[menuId].anchorTop + menudivs[menuId].anchorHeight;
  thisLayerProperties.visibility = showval;

  menudivs[menuId].menuOn = true;
}

function hideMenu(menuId) {
  if (isNS4) {
    thisLayerProperties = eval("document.layers."+menuId); 
    hideval = "hide";
  } else {
    hideval = "hidden";
    if (isIE4) {
      thisLayer = eval("document.all."+menuId); 
      thisLayerProperties = thisLayer.style;
    } else {
      if (isIE5 || isNS6) {
        thisLayer = document.getElementById(menuId);
        thisLayerProperties = thisLayer.style;
      }
    }
  } 
  thisLayerProperties.visibility = hideval;
  menudivs[menuId].menuOn = false;
}

document.onmousemove = checkwhere;
if(document.captureEvents) {document.captureEvents(Event.MOUSEMOVE);}

//-->