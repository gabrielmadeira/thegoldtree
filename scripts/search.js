function searchStart(id) {
	var nameOut = "";
	document.getElementById('loadingBoxDiv').style.display = "initial";
	setTimeout(function(){
		if (id == "n") {
			id = document.getElementById('searchInputId').value;
		}else{
			document.getElementById('searchInputId').value = id;
		}
		document.getElementById('inputGraph').value = 'digraph{ '+digraphStyle+' }'; 
		
		upSearchRelIds = [];
		downSearchRelIds = [];
		
		
		var verifyID = 0;
		var dataString = { id: id, op: "5" };
		$.ajax({
		type: "post",
		url: "scripts/operations.php",
		data: dataString,
		//cache: false,
		async: false,
		success: function(html){	
			if (html[0] != "0"){
				verifyID = 1;
				nameOut = html;

			}
		}
		});
		if (verifyID == 1) {
			document.getElementById('academicArea').innerHTML = "<span class=\"academic-area-name\">"+nameOut+"</span> Tree";
			UpSearch(id,'n',parseInt(document.getElementById('searchInputLevel').value)); 
			DownSearch(id,'n',parseInt(document.getElementById('searchInputLevel').value)); 
			tryDraw();
		}else{
			alert("This ID does not exist!");
		}
		
		document.getElementById('loadingBoxDiv').style.display = "none";
	}, 500);
}

function UpSearch(aut, relid, level) {
	if ((level >= 0) && !(upSearchRelIds.includes(relid))){
		var dataString = { author: aut, op: "1", relid: relid };
		$.ajax({
		type: "post",
		url: "scripts/operations.php",
		data: dataString,
		//cache: false,
		async: false,
		success: function(html){		
			var data = JSON.parse(html);
			var name = data[0];
			var arro = data[1];
			var typeNT = data[2];
			var id_author_previous = data[3];
			var year = data[4];
			if (typeNT == "m"){
				var type = "M";
				var color = "#55f";
			}else{
				var type = "D";
				var color = "#f77";
			} 
			if (relid != "n") {
				document.getElementById("inputGraph").value = document.getElementById("inputGraph").value.slice(0, -1);
				document.getElementById("inputGraph").value += aut+" [labelType=\"html\" label=\"<a class='aTipo' onclick='searchStart("+aut+");'>"+name+"</a>\"];";
				document.getElementById("inputGraph").value += aut+" -> "+id_author_previous+" [labelType=\"html\" label=\"<a class='aTipo' style='color: "+color+" !important;' onclick='searchDetails("+relid+");'>"+type+"-"+year+"</a>\" ];";
				document.getElementById("inputGraph").value += "}";
			}else{
				document.getElementById("inputGraph").value = document.getElementById("inputGraph").value.slice(0, -1);
				document.getElementById("inputGraph").value += aut+" [label=\""+name+"\" style=\"fill: #999; font-weight: bold;\"];";
				document.getElementById("inputGraph").value += "}";
			}
			upSearchRelIds.push(relid);
			for (var i = 0; i < arro.length; i++) { 
				UpSearch(arro[i].adv_id,arro[i].rel_id,level-1);
			}
		}
		});
	}
}


function DownSearch(adv, relid, level) {
	if ((level >= 0) && !(downSearchRelIds.includes(relid))){
		var dataString = { advisor: adv, op: "2", relid: relid };
		$.ajax({
		type: "post",
		url: "scripts/operations.php",
		data: dataString,
		//cache: false,
		async: false,
		success: function(html){		
			var data = JSON.parse(html);
			var name = data[0];
			var arra = data[1];
			var typeNT = data[2];
			var id_advisor_previous = data[3];
			var year = data[4];
			if (typeNT == "m"){
				var type = "M";
				var color = "#55f";
			}else{
				var type = "D";
				var color = "#f77";
			} 
			if (relid != "n") {
				document.getElementById("inputGraph").value = document.getElementById("inputGraph").value.slice(0, -1);
				document.getElementById("inputGraph").value += adv+" [labelType=\"html\" label=\"<a class='aTipo' onclick='searchStart("+adv+");'>"+name+"</a>\"];";
				document.getElementById("inputGraph").value += id_advisor_previous+" -> "+adv+" [labelType=\"html\" label=\"<a class='aTipo' style='color: "+color+" !important;' onclick='searchDetails("+relid+");'>"+type+"-"+year+"</a>\" ];";
				document.getElementById("inputGraph").value += "}";
			}else{
				document.getElementById("inputGraph").value = document.getElementById("inputGraph").value.slice(0, -1);
				document.getElementById("inputGraph").value += adv+" [label=\""+name+"\" style=\"fill: #999; font-weight: bold;\"];";
				document.getElementById("inputGraph").value += "}";
			}
			downSearchRelIds.push(relid);
			for (var i = 0; i < arra.length; i++) { 
				DownSearch(arra[i].aut_id,arra[i].rel_id,level-1);
			}
		}
		});
	}
}

function tryDraw() {
  if (oldInputGraphValue !== inputGraph.value) {
		var render = dagreD3.render();
		var g;

    inputGraph.setAttribute("class", "");
    oldInputGraphValue = inputGraph.value;
    try {
      g = graphlibDot.read(inputGraph.value);
    } catch (e) {
      inputGraph.setAttribute("class", "error");
      throw e;
    }
    if (!g.graph().hasOwnProperty("marginx") &&
        !g.graph().hasOwnProperty("marginy")) {
      g.graph().marginx = 20;
      g.graph().marginy = 20;
    }
    g.graph().transition = function(selection) {
      return selection.transition().duration(500);
    };
		d3.select("svg g").call(render, g);
		
		
		var svg = d3.select("svg"),
			inner = d3.select("svg g"),
			zoom = d3.zoom().on("zoom", function() {
				inner.attr("transform", d3.event.transform);
		});
		
		svg.call(zoom);
		
		var zoomScale = 1;

		var graphWidth = g.graph().width + 80;
		var graphHeight = g.graph().height + 0;

		var width = parseInt(svg.style("width").replace(/px/, ""));
		var height = parseInt(svg.style("height").replace(/px/, ""));


		zoomScale = Math.max( Math.min(width / graphWidth, height / graphHeight));

		xCenterOffset = ((svg.style("width").slice(0, svg.style("width").length-2)) - g.graph().width) / 2;
		// inner.attr("transform", "translate(" + xCenterOffset + ", 20)");
		// inner.attr("transform", "scale(" + zoomScale + ")");

		// console.log("translate:"+xCenterOffset);
		// console.log("scale:"+zoomScale);
		//svg.attr("height", g.graph().height + 40);

		svg.call(zoom.transform, d3.zoomIdentity.translate(xCenterOffset, height/2));
		svg.call(zoom.transform, d3.zoomIdentity.scale(zoomScale, height/2));
  }
}

function searchNames() {
	if (document.getElementById("searchBoxInput").value.length > 10){
		var str = document.getElementById("searchBoxInput").value;
		var dataString = { str: str.toUpperCase(), op: "3" };
		
		$.ajax({
		type: "post",
		url: "scripts/operations.php",
		data: dataString,
		//cache: false,
		async: false,
		success: function(html){		
			var data = JSON.parse(html);
			document.getElementById("searchBoxList").innerHTML = "";
			for (var i = 0; i < data.length; i++) { 
				document.getElementById("searchBoxList").innerHTML +=
					//document.getElementById('academicArea').innerHTML = 'ACADÊMICO <font color=red>"+data[i].name.toUpperCase()+"</font> SELECIONADO';
					"<a onclick=\"document.getElementById('searchInputId').value = '"+data[i].id+"'; sbox.style.display = 'none'; verifyFields(); \" class=\"search-box-list-line\">"+data[i].name.toUpperCase()+"</a>";
			}
			
		}
		});
	}
}


function searchDetails(rel) {
	var dataString = { relid: rel, op: "4" };
	$.ajax({
	type: "post",
	url: "scripts/operations.php",
	data: dataString,
	//cache: false,
	async: false,
	success: function(html){		
		var data = JSON.parse(html);
		if ((data.author == "") || (data.author == "undefined") || (data.author == null)) {
			data.author = "Not Available";
		}
		if ((data.advisor == "") || (data.advisor == "undefined") || (data.advisor == null)) {
			data.advisor = "Not Available";
		}
		if ((data.title == "") || (data.title == "undefined") || (data.title == null)) {
			data.title = "Not Available";
		}
		if ((data.type == "") || (data.type == "undefined") || (data.type == null)) {
			data.type = "Not Available";
		}else{
			if (data.type == "m"){
				data.type = "Master's";
			}else{
				data.type = "Doctorate";
			}
		}
		
		if ((data.year == "") || (data.year == "undefined") || (data.year == null)) {
			data.year = "Not Available";
		}
		if ((data.url == "") || (data.url == "undefined") || (data.url == null)) {
			data.url = "Not Available";
		}else{
			data.url = "<a target='_BLANK' href='"+data.url+"'>"+data.url+"</a>";
		}
		if ((data.citation == "") || (data.citation == "undefined") || (data.citation == null)) {
			data.citation = "Not Available";
		}
		if ((data.topic == "") || (data.topic == "undefined") || (data.topic == null)) {
			data.topic = "Not Available";
		}
		if ((data.author_lattes == "") || (data.author_lattes == "undefined") || (data.author_lattes == null)) {
			data.author_lattes = "Not Available";
		}else{
			data.author_lattes = "<a target='_BLANK' href='"+data.author_lattes+"'>"+data.author_lattes+"</a>";
		}
		if ((data.institution_acron == "") || (data.institution_acron == "undefined") || (data.institution_acron == null)) {
			data.institution_acron = "Not Available";
		}
		if ((data.institution_name == "") || (data.institution_name == "undefined") || (data.institution_name == null)) {
			data.institution_name = "Not Available";
		}
		document.getElementById("detailsAuthor").innerHTML = data.author;
		document.getElementById("detailsAdvisor").innerHTML = data.advisor;
		document.getElementById("detailsTitle").innerHTML = data.title;
		document.getElementById("detailsType").innerHTML = data.type;
		document.getElementById("detailsYear").innerHTML = data.year;
		document.getElementById("detailsUrl").innerHTML = data.url;
		document.getElementById("detailsCitation").innerHTML = data.citation;
		document.getElementById("detailsTopic").innerHTML = data.topic;
		document.getElementById("detailsLattes").innerHTML = data.author_lattes;
		document.getElementById("detailsInstitutionAcron").innerHTML = data.institution_acron;
		document.getElementById("detailsInstitutionName").innerHTML = data.institution_name;

		document.getElementById("detailsTypeEx").innerHTML = data.type;
		document.getElementById("detailsAuthorEx").innerHTML = data.author;
	}
	});

	document.getElementById('searchDetails').style.display = "block";
}
