// A função searchStart conecta as funções de busca, a função de desenhar a árvore e a função para verificar se um determinado ID existe
function searchStart(id) {
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
			if (html != "0"){
				verifyID = 1;
			}
		}
		});
		if (verifyID == 1) {
			UpSearch(id,'n',parseInt(document.getElementById('searchInputLevel').value)); 
			DownSearch(id,'n',parseInt(document.getElementById('searchInputLevel').value)); 
			tryDraw();
		}else{
			alert("Esse ID não existe!");
		}
		
		document.getElementById('loadingBoxDiv').style.display = "none";
	}, 500);
}

//A função UpSearch busca recursivamente as relações na ordem aluno -> orientador -> orientador do orientador -> ...
// e então estrutura parte do código que será lido pela biblioteca dagre-d3 para gerar a árvore
function UpSearch(alu, relid, level) {
	if ((level >= 0) && !(upSearchRelIds.includes(relid))){
		var dataString = { aluno: alu, op: "1", relid: relid };
		$.ajax({
		type: "post",
		url: "scripts/operations.php",
		data: dataString,
		//cache: false,
		async: false,
		success: function(html){		
			var data = JSON.parse(html);
			var nome = data[0];
			var arro = data[1];
			var tipoNT = data[2];
			var id_aluno_anterior = data[3];
			var ano = data[4];
			if (tipoNT == "m"){
				var tipo = "M";
				var cor = "#55f";
			}else{
				var tipo = "D";
				var cor = "#f77";
			} 
			if (relid != "n") {
				document.getElementById("inputGraph").value = document.getElementById("inputGraph").value.slice(0, -1);
				document.getElementById("inputGraph").value += alu+" [labelType=\"html\" label=\"<a class='aTipo' onclick='searchStart("+alu+");'>"+nome+"</a>\"];";
				document.getElementById("inputGraph").value += alu+" -> "+id_aluno_anterior+" [labelType=\"html\" label=\"<a class='aTipo' style='color: "+cor+" !important;' onclick='searchDetails("+relid+");'>"+tipo+"-"+ano+"</a>\" ];";
				document.getElementById("inputGraph").value += "}";
			}else{
				document.getElementById("inputGraph").value = document.getElementById("inputGraph").value.slice(0, -1);
				document.getElementById("inputGraph").value += alu+" [label=\""+nome+"\" style=\"fill: #999; font-weight: bold;\"];";
				document.getElementById("inputGraph").value += "}";
			}
			upSearchRelIds.push(relid);
			for (var i = 0; i < arro.length; i++) { 
				UpSearch(arro[i].ori_id,arro[i].rel_id,level-1);
			}
		}
		});
	}
}

//A função DownSearch busca recursivamente as relações na ordem orientador -> aluno -> aluno do aluno -> ...
// e então estrutura parte do código que será lido pela biblioteca dagre-d3 para gerar a árvore
function DownSearch(ori, relid, level) {
	if ((level >= 0) && !(downSearchRelIds.includes(relid))){
		var dataString = { orientador: ori, op: "2", relid: relid };
		$.ajax({
		type: "post",
		url: "scripts/operations.php",
		data: dataString,
		//cache: false,
		async: false,
		success: function(html){		
			var data = JSON.parse(html);
			var nome = data[0];
			var arra = data[1];
			var tipoNT = data[2];
			var id_orientador_anterior = data[3];
			var ano = data[4];
			if (tipoNT == "m"){
				var tipo = "M";
				var cor = "#55f";
			}else{
				var tipo = "D";
				var cor = "#f77";
			} 
			if (relid != "n") {
				document.getElementById("inputGraph").value = document.getElementById("inputGraph").value.slice(0, -1);
				document.getElementById("inputGraph").value += ori+" [labelType=\"html\" label=\"<a class='aTipo' onclick='searchStart("+ori+");'>"+nome+"</a>\"];";
				document.getElementById("inputGraph").value += id_orientador_anterior+" -> "+ori+" [labelType=\"html\" label=\"<a class='aTipo' style='color: "+cor+" !important;' onclick='searchDetails("+relid+");'>"+tipo+"-"+ano+"</a>\" ];";
				document.getElementById("inputGraph").value += "}";
			}else{
				document.getElementById("inputGraph").value = document.getElementById("inputGraph").value.slice(0, -1);
				document.getElementById("inputGraph").value += ori+" [label=\""+nome+"\" style=\"fill: #999; font-weight: bold;\"];";
				document.getElementById("inputGraph").value += "}";
			}
			downSearchRelIds.push(relid);
			for (var i = 0; i < arra.length; i++) { 
				DownSearch(arra[i].alu_id,arra[i].rel_id,level-1);
			}
		}
		});
	}
}


// A função tryDraw lê uma variavel global, onde foi estruturado o código pelas funções UpSearch e DownSearch, e gera a árvore
// por meio da biblioteca dagre-d3
function tryDraw() {
  if (oldInputGraphValue !== inputGraph.value) {
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
  }
}
// Função utilizada na área de pesquisa por nomes
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
					"<a onclick=\"document.getElementById('searchInputId').value = '"+data[i].id+"'; sbox.style.display = 'none';\" class=\"search-box-list-line\">"+data[i].nome.toUpperCase()+"</a>";
			}
			
		}
		});
	}
}

// Função utilizada para buscar mais detalhes de determinados documentos
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
		if ((data.aluno == "") || (data.aluno == "undefined") || (data.aluno == null)) {
			data.aluno = "Indisponível";
		}
		if ((data.orientador == "") || (data.orientador == "undefined") || (data.orientador == null)) {
			data.orientador = "Indisponível";
		}
		if ((data.title == "") || (data.title == "undefined") || (data.title == null)) {
			data.title = "Indisponível";
		}
		if ((data.tipo == "") || (data.tipo == "undefined") || (data.tipo == null)) {
			data.tipo = "Indisponível";
		}else{
			if (data.tipo == "m"){
				data.tipo = "Mestrado";
			}else{
				data.tipo = "Doutorado";
			}
		}
		
		if ((data.ano == "") || (data.ano == "undefined") || (data.ano == null)) {
			data.ano = "Indisponível";
		}
		if ((data.url == "") || (data.url == "undefined") || (data.url == null)) {
			data.url = "Indisponível";
		}else{
			data.url = "<a target='_BLANK' href='"+data.url+"'>"+data.url+"</a>";
		}
		if ((data.citation == "") || (data.citation == "undefined") || (data.citation == null)) {
			data.citation = "Indisponível";
		}
		if ((data.topic == "") || (data.topic == "undefined") || (data.topic == null)) {
			data.topic = "Indisponível";
		}
		if ((data.author_lattes == "") || (data.author_lattes == "undefined") || (data.author_lattes == null)) {
			data.author_lattes = "Indisponível";
		}else{
			data.author_lattes = "<a target='_BLANK' href='"+data.author_lattes+"'>"+data.author_lattes+"</a>";
		}
		if ((data.network_acronym_str == "") || (data.network_acronym_str == "undefined") || (data.network_acronym_str == null)) {
			data.network_acronym_str = "Indisponível";
		}
		if ((data.network_name_str == "") || (data.network_name_str == "undefined") || (data.network_name_str == null)) {
			data.network_name_str = "Indisponível";
		}
		document.getElementById("detailsAluno").innerHTML = data.aluno;
		document.getElementById("detailsOrientador").innerHTML = data.orientador;
		document.getElementById("detailsTitle").innerHTML = data.title;
		document.getElementById("detailsTipo").innerHTML = data.tipo;
		document.getElementById("detailsAno").innerHTML = data.ano;
		document.getElementById("detailsUrl").innerHTML = data.url;
		document.getElementById("detailsCitation").innerHTML = data.citation;
		document.getElementById("detailsTopic").innerHTML = data.topic;
		document.getElementById("detailsLattes").innerHTML = data.author_lattes;
		document.getElementById("detailsNetAcronym").innerHTML = data.network_acronym_str;
		document.getElementById("detailsNetName").innerHTML = data.network_name_str;

		document.getElementById("detailsTipoEx").innerHTML = data.tipo;
		document.getElementById("detailsAlunoEx").innerHTML = data.aluno;
	}
	});

	document.getElementById('searchDetails').style.display = "block";
}
