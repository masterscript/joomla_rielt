jQuery.noConflict();
var hdc;
var hdc2;
var hdc3;
function byId(e){return document.getElementById(e);}
function drawPoly(coOrdStr)
{
    var mCoords = coOrdStr.split(",");//alert(mCoords);
    var i, n;
    n = mCoords.length;

    hdc.beginPath();
    hdc.moveTo(mCoords[0], mCoords[1]);
    for (i=2; i<n; i+=2)
    {
        hdc.lineTo(mCoords[i], mCoords[i+1]);
    }
    hdc.lineTo(mCoords[0], mCoords[1]);
	hdc.fill();
    hdc.stroke();
	
}
function drawPoly2(coOrdStr)
{
    var mCoords = coOrdStr.split(",");//alert(mCoords);
    var i, n;
    n = mCoords.length;

    hdc2.beginPath();
    hdc2.moveTo(mCoords[0], mCoords[1]);
    for (i=2; i<n; i+=2)
    {
        hdc2.lineTo(mCoords[i], mCoords[i+1]);
    }
    hdc2.lineTo(mCoords[0], mCoords[1]);
	hdc2.fill();	
}

function myHover(element)
{
    var hoveredElement = element;
    var coordStr = element.getAttribute("coords");
    var areaType = element.getAttribute("shape");

    switch (areaType)
    {
        case "polygon":
        case "poly":
            drawPoly(coordStr);
    }
		var coords = jQuery(element).attr("coords");					
		var arrcoords = coords.split(",");
		var x = 0;
		var y = 9999999;
		for(var i=0; i<=arrcoords.length-1; i++){
			if(i%2==0){
				x = x+Number(arrcoords[i]);
			}else{
				if(y > Number(arrcoords[i])){
					y = Number(arrcoords[i]);
				}
				
			}
			
		}
		
		x = parseInt(x/(arrcoords.length/2));		
		jQuery(".vspluvashka-etazh").css({"left": x, "top": y-3});
		/*jQuery(".vspluvashka .info-kv-num").text(jQuery(element).attr("date-num"));*/
		jQuery(".vspluvashka-etazh .info-kv-free").text(jQuery(element).attr("data-kv"));
		jQuery(".vspluvashka-etazh .info-etazh").text(jQuery(element).attr("data-floor"));
		var data_sec = jQuery(element).attr("data-sec");
		
		if(data_sec != ""){
			jQuery(".vspluvashka-etazh .info-section").text("Секция: "+data_sec);
		}
		
		jQuery(".vspluvashka-etazh").show();
}
function kvHover(element)
{	
	jQuery(".vspluvashka").hide();
    var hoveredElement = element;
    var coordStr = element.getAttribute("coords");
    var areaType = element.getAttribute("shape");
	if(jQuery(element).hasClass("bron")){
		hdc2.fillStyle = "rgba(251, 116, 0, 0.30)";
	}else if(jQuery(element).hasClass("kv-ok")){
		
		hdc2.fillStyle = "rgba(0, 128, 0, 0.30)";
	}else{
		hdc2.fillStyle = "rgba(255, 0, 0, 0.30)";
	}	
    switch (areaType)
    {
        case "polygon":
        case "poly":
            drawPoly2(coordStr);
    }
	if(jQuery(element).hasClass("kv-ok")){
		
		var coords = jQuery(element).attr("coords");					
		var arrcoords = coords.split(",");
		var x = 0;
		var y = 0;
		for(var i=0; i<=arrcoords.length-1; i++){
			if(i%2==0){
				x = x+Number(arrcoords[i]);
			}else{
				y = y+ Number(arrcoords[i]);
			}
			
		}
		
		x = parseInt(x/(arrcoords.length/2));
		y = parseInt(y/(arrcoords.length/2));
		jQuery(".vspluvashka").css({"left": x, "top": y});
		/*jQuery(".vspluvashka .info-kv-num").text(jQuery(element).attr("date-num"));*/
		jQuery(".vspluvashka .info-kv-rooms").text(jQuery(element).attr("date-rooms"));
		jQuery(".vspluvashka .info-kv-s").text(jQuery(element).attr("date-s-obch"));
		jQuery(".vspluvashka .info-kv-price").text(jQuery(element).attr("date-price"));
		jQuery(".vspluvashka").show();
	}
}
function myLeave()
{
    var canvas = byId("myCanvas");
    hdc.clearRect(0, 0, canvas.width, canvas.height);
	jQuery(".vspluvashka-etazh").hide();
}
function kvLeave()
{
    var canvas = byId("etazhCanvas");
    hdc2.clearRect(0, 0, canvas.width, canvas.height);
	
}
function myInit(){
    var img = byId("map_img");
    var x,y, w,h;

    x = img.offsetLeft;
    y = img.offsetTop;
    w = img.clientWidth;
    h = img.clientHeight;

    var imgParent = img.parentNode;
    var can = byId("myCanvas");
    imgParent.appendChild(can);

    can.style.zIndex = 1;

    can.style.left = x+"px";
    can.style.top = y+"px";

    can.setAttribute("width", w+"px");
    can.setAttribute("height", h+"px");

    hdc = can.getContext("2d");

    hdc.fillStyle = "rgba(255, 255, 255, 0.50)";
    //hdc.strokeStyle = "rgba(255, 255, 255, 0.80)";
	hdc.strokeStyle = "green";
	hdc.lineWidth = 2;	

}
function myInit2(){
    var img = byId("mymapetazh");
    var x,y, w,h;

    x = img.offsetLeft;
    y = img.offsetTop;
    w = img.clientWidth;
    h = img.clientHeight;

    var imgParent = img.parentNode;
    var can = byId("etazhCanvas");
    imgParent.appendChild(can);

    can.style.zIndex = 1;

    can.style.left = x+"px";
    can.style.top = y+"px";

    can.setAttribute("width", w+"px");
    can.setAttribute("height", h+"px");
    hdc2 = can.getContext("2d");		
}
function myInit3(){
    var img = byId("mymapetazh");
    var x,y, w,h;

    x = img.offsetLeft;
    y = img.offsetTop;
    w = img.clientWidth;
    h = img.clientHeight;

    var imgParent = img.parentNode;
    var can = byId("etazhCanvasred");
    imgParent.appendChild(can);

    can.style.zIndex = 1;

    can.style.left = x+"px";
    can.style.top = y+"px";

    can.setAttribute("width", w+"px");
    can.setAttribute("height", h+"px");
    hdc3 = can.getContext("2d");
		jQuery(".area-kv").each(function (i) {
			
				    
					var coordStr = this.getAttribute("coords");
					var areaType = this.getAttribute("shape");
					if(jQuery(this).hasClass("kv-no")){
						hdc3.fillStyle = "rgba(255, 0, 0, 0.30)";
					}else if(jQuery(this).hasClass("bron")){
						hdc3.fillStyle = "rgba(251, 116, 0, 0.50)";
					}else{
						hdc3.fillStyle = "rgba(0, 128, 0, 0.50)";
					}
						var mCoords = coordStr.split(",");//alert(mCoords);
						var i, n;
						n = mCoords.length;

						hdc3.beginPath();
						hdc3.moveTo(mCoords[0], mCoords[1]);
						for (i=2; i<n; i+=2)
						{
							hdc3.lineTo(mCoords[i], mCoords[i+1]);
						}
						hdc3.lineTo(mCoords[0], mCoords[1]);
						hdc3.fill();	
			
		})	
}
	function get_flats(){
		var wrap  = jQuery("#collection-list");
		var form = jQuery("#object_filter");
		var filter_data = form.serialize();		

		jQuery("#loading-filter").show();
		jQuery.ajax({
			url: "index.php?option=com_flats&task=get_flats_dom&format=raw", // путь к обработчику
			type: "POST", // метод передачи
			data: {form_data: filter_data},
			dataType: "json",
			success: function(json){
				jQuery("#loading-filter").hide();
				if(json.status=="ok"){
					wrap.html(json.html); 
						jQuery('#inner-content').slimScroll({
							size: '12px',
							height: 598,
							color: '#898989',
							opacity: 1,
							alwaysVisible: false,
							borderRadius: 0,
							railVisible: true,
							railColor: '#d8d8d8',
							railOpacity: 1,
							railBorderRadius: 0,
						});	
				}else{
					alert("Ошибка");
				}
			}
		});
	}
	

	function etazh_info(catid, dom, sekciya, etazh, svobodnux, mytitle, n = 0){
		
		var wrap  = jQuery("#etazh_info");
		
		
		jQuery.ajax({
			url: "/index.php?option=com_flats&task=etazh_info&format=raw", // путь к обработчику
			type: "POST", // метод передачи
			data: {form_data: "catid="+catid+"&dom="+dom+"&sekciya="+sekciya+"&etazh="+etazh+"&svobodnux="+svobodnux},
			dataType: "json",
			success: function(json){
				if(json.status=="ok"){
					wrap.html(json.html); 
					var loadimg = jQuery("#mymapetazh");
					loadimg.load(function() {
						if(n ==0){
							jQuery.fancybox([{ href: "#etazh_info", title: mytitle }]);
						}else{
							 jQuery('#etazh_info').parent().parent().parent().find(".child").text(mytitle);
						}
						myInit2();
						myInit3();
					});
						
				}else{
					alert("Ошибка");
				}
			}
		});
		return false;
	}
	window.onload = function () {
		myInit();
		get_flats();
	}
	jQuery(document).on("click", "[href$=\'#ch_direction\']", function(e){
		e.preventDefault();
		
		var type = jQuery(this).attr("data-type");
		var col = jQuery(this).attr("data-col");
		
		jQuery("#order_type").val(type);
		jQuery("#order_col").val(col);
				
		get_flats();
		
	})

