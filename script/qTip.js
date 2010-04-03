//////////////////////////////////////////////////////////////////
// qTip - CSS Tool Tips - by Craig Erskine
// http://qrayg.com | http://solardreamstudios.com
//
// Inspired by code from Travis Beckham
// http://www.squidfingers.com | http://www.podlob.com
//////////////////////////////////////////////////////////////////
/*   	function getElementsByClassName
	Written by Jonathan Snook, http://www.snook.ca/jonathan
*/
function getElementsByClassName(oElm, strTagName, strClassName){
	var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
	var arrReturnElements = new Array();
	strClassName = strClassName.replace(/\-/g, "\\-");
	var oRegExp = new RegExp("(^|\\s)" + strClassName + "(\\s|$)");
	var oElement;
	for(var i=0; i<arrElements.length; i++){
		oElement = arrElements[i];		
		if(oRegExp.test(oElement.className)){
			arrReturnElements.push(oElement);
		}	
	}
	return (arrReturnElements)
}
// Array support for the push method in IE 5
if(typeof Array.prototype.push != "function"){
	Array.prototype.push = ArrayPush;
	function ArrayPush(value){
		this[this.length] = value;
	}
}
/*
Examples of how to call the function:	
	To get all a elements in the document with a "info-links" class:
    getElementsByClassName(document, "a", "info-links");    
	To get all div elements within the element named "container", with a "col" and a "left" class:
    getElementsByClassName(document.getElementById("container"), "div", ["col", "left"]);
*/
//

var tags = new Array("a","span"); //Which tag do you want to qTip-ize? Keep it lowercase!//
var qTipX = -30; //This is qTip's X offset//
var qTipY = 25; //This is qTip's Y offset//



//There's No need to edit anything below this line//
tooltip = {
  name : "qTip",
  offsetX : qTipX,
  offsetY : qTipY,
  tip : null
}

tooltip.init = function () {
	var tipNameSpaceURI = "http://www.w3.org/1999/xhtml";
	if(!tipContainerID){ var tipContainerID = "qTip";}
	var tipContainer = document.getElementById(tipContainerID);

	if(!tipContainer) {
	  tipContainer = document.createElementNS ? document.createElementNS(tipNameSpaceURI, "div") : document.createElement("div");
		tipContainer.setAttribute("id", tipContainerID);
	  document.getElementsByTagName("body").item(0).appendChild(tipContainer);
	}

	if (!document.getElementById) return;
	this.tip = document.getElementById (this.name);
	if (this.tip) document.onmousemove = function (evt) {tooltip.move (evt)};

	var a, sTitle;
	for (var z=0; z < tags.length; ++z)
	{
	var qTipTag = tags[z];
	var anchors = getElementsByClassName(document, qTipTag, "dday-title");
	/* var anchors = document.getElementsByTagName (qTipTag); */

		for (var i = 0; i < anchors.length; i ++) {
			a = anchors[i];
			sTitle = a.getAttribute("title");
			if(sTitle) {
				a.setAttribute("tiptitle", sTitle);
				a.removeAttribute("title");
				a.onmouseover = function() {tooltip.show(this.getAttribute('tiptitle'))};
				a.onmouseout = function() {tooltip.hide()};
			}
		}
	}
}
tooltip.move = function (evt) {
	var x=0, y=0;
	if (document.all) {//IE
		x = (document.documentElement && document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft;
		y = (document.documentElement && document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop;
		x += window.event.clientX;
		y += window.event.clientY;
		
	} else {//Good Browsers
		x = evt.pageX;
		y = evt.pageY;
	}
	this.tip.style.left = (x + this.offsetX) + "px";
	this.tip.style.top = (y + this.offsetY) + "px";
}

tooltip.show = function (text) {
	if (!this.tip) return;
	this.tip.innerHTML = text;
	this.tip.style.display = "block";
}

tooltip.hide = function () {
	if (!this.tip) return;
	this.tip.innerHTML = "";
	this.tip.style.display = "none";
}

window.onload = function () {
	tooltip.init ();
}