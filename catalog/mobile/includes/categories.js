// CategoriesNavigator class
var instance;

function CategoriesNavigator(filename, xml) {
	instance = this;
	var catNodes = new Array();
	getDoc(xml);
	this.filename = filename;
	this.cats = xmlDoc.getElementsByTagName("category");

	function getDoc(text) {
		if(window.ActiveXObject) {//Internet Explorer
			  xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
			  xmlDoc.async="false";
			  xmlDoc.loadXML(text);
		  }
		else
		  {
			  try //Firefox, Mozilla, Opera, etc.
			  {
				  parser=new DOMParser();
				  xmlDoc=parser.parseFromString(text,"text/xml");
			  }
			  catch(e)
			  {
				  alert(e.message);
				  return;
			  }
		}
	}

}

function CategoriesNavigator_run() {
	if(location.hash == '') {
		location.href = this.filename + "#root";
	}
	lastHash = location.hash;

	this.insertContent('iphone_content',location.hash.replace("#",""));
	setInterval("healthCheck()",100);
}

function CategoriesNavigator_select(nodeID){
	for(i = 0; i < this.cats.length; i++) 
		if(this.cats[i].getAttribute('id') == nodeID) {
			this.current = this.cats[i];
			return;
			}
}

function CategoriesNavigator_buildHTML(){
	function categoryHTML(cat) {
		id = cat.getAttribute('id');
		img = cat.getAttribute('image');
		name = cat.getAttribute('name');
		path = cat.getAttribute('path');
		var onclick  = 'catNav.replaceContent('+ id + ', this)';
		if(path != null)
			onclick  = 'window.location.href=\'' + path + '\'';
		
	 	ret  = '<tr onclick="' + onclick + '" class="categories">\n';
		if(img != null) {
		 	imgSrc = (img == '') ? '&nbsp;' : ('<img height="21" border="0" width="30" title="' + name + '" alt="' + name + '" src="images/'+ img + '"/>'); 
		 	ret  = ret + '	<td class="categories" width="1px">' + imgSrc + '</td>\n';
		}
	 	ret  = ret + '	<td class="categories">' + name + '</td>\n';
	 	ret  = ret + '	<td align="right" class="categories"><img height="30" border="0" width="30" alt="" src="' + mobile_img_dir + 'arrow_select.png"/></a></td>\n';
	 	ret  = ret + '</tr>\n';
		return ret;
	}
	function categoriesHTML(current) {
		html = '<table id="categoriesTable" class="categories" width="100%" cellpadding="0" cellspacing="0">\n';
		for(i=0; i < current.childNodes.length; i++) {
			var cat = current.childNodes[i];
			html = html + categoryHTML(cat);
		}
		html = html + "</table>\n";
		return html;
	}
	if(this.current.getAttribute('type') == 'text')
		return this.current.firstChild.data;
	else
		return categoriesHTML(this.current);
}

function  CategoriesNavigator_insertContent(panelName, catID){
	this.select(catID);
	document.getElementById(panelName).innerHTML = this.buildHTML();
}

function  CategoriesNavigator_replaceContent(catID, tr){
	var percent;
	var speed = 20;
	var newOpac = 0;
	var headerTitle;
	var isParent = (this.current.parentNode.getAttribute("id") ==  catID);
	var filename = this.filename;
		
	function createTempDiv() {
		percent = 100;
		mainDiv = document.getElementById('iphone_content');
		tempDiv = document.createElement('div');
		tempDiv.setAttribute('id',"iphone_temp_content");
		tempDiv.setAttribute('style','left: 100%;width: 100%; position: absolute;');

		if(isParent) {
			tempDiv.setAttribute('style','left: -100%;width: 100%; position: absolute;');
			percent = -100;
			speed = -1 * speed;
		}
		document.getElementById("iphone_content_body").appendChild(tempDiv);
	}
	function slide()
	{
		percent -= speed;
		mainDiv.style.left = isParent ? (100 + percent ) + "%" : (percent - 100 ) + "%";
	  	tempDiv.style.left = percent + "%";
	  	if((isParent && percent < 0) ||(!isParent && percent > 0)) 
	  		setTimeout(slide, 100);
	  	else 
	  		swapDivs();
	}
	
	function swapDivs() {
		mainDiv.innerHTML = tempDiv.innerHTML;
		mainDiv.style.left = "0";
		document.getElementById("iphone_content_body").removeChild(tempDiv);
		location.href = filename + "#" + catID;
		lastHash = location.hash; 
	}
	
	function changeTitle(title) {
		newOpac = 0;
		headerTitle = document.getElementById("headerTitle");
		headerTitle.style.opacity = '.' + newOpac;
		headerTitle.style.filter = "alpha(opacity='" + newOpac + "')";
		headerTitle.innerHTML = title;
  		setTimeout(fadeIn, 100);
	}
	
	function fadeIn() {
		newOpac = newOpac + 10;
		headerTitle.style.opacity = newOpac /100.00 ;
		headerTitle.style.filter = "alpha(opacity='" + newOpac + "')";
		if(newOpac < 100)
	  		setTimeout(fadeIn, 50);
	}
	
	if(tr != undefined)
		tr.className="categoriesselect";
		
	createTempDiv();
	this.insertContent("iphone_temp_content",catID);
 	setTimeout(slide, 200);
	changeTitle(this.current.getAttribute('name'));
}

CategoriesNavigator.prototype.select = CategoriesNavigator_select;
CategoriesNavigator.prototype.buildHTML = CategoriesNavigator_buildHTML;
CategoriesNavigator.prototype.insertContent = CategoriesNavigator_insertContent;
CategoriesNavigator.prototype.replaceContent = CategoriesNavigator_replaceContent;
CategoriesNavigator.prototype.run = CategoriesNavigator_run;


var lastHash;
function healthCheck() {
	if(location.hash != lastHash) {
		lastHash = location.hash;
		instance.replaceContent(location.hash.replace("#",""));
	}
}

