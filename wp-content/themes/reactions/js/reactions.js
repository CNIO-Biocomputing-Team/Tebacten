function showEvidences(idCompuesto,type){
    var ajaxDisplay = document.getElementById('evidences');
	ajaxDisplay.innerHTML = "Searching for evidences. Please wait. This usually takes less than a minute, depending on the number of evidences";
	
	var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
            ajaxDisplay = document.getElementById('evidences');
			ajaxDisplay.innerHTML = ajaxRequest.responseText;
			//$("#overlay").unmask("Loading…");
		}
	}
	
	var queryString = "?idCompuesto="+idCompuesto+"&type="+type;
	ajaxRequest.open("GET", "http://tebacten.bioinfo.cnio.es/wp-content/themes/reactions/scripts/returnEvidences.php" + queryString, true);
	ajaxRequest.send(null); 
}

function searchEvidences(whatToSearch,type){
	var ajaxDisplay = document.getElementById('evidences');
	ajaxDisplay.innerHTML = "Searching for evidences. Please wait. This usually takes less than a minute, depending on the number of evidences";
	
    var termToSearch=$("#tags").val();
    if (termToSearch==""){
    	alert("Please insert a term to search.");
    	return false;
    }
	var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
            var ajaxDisplay = document.getElementById('evidences');
			ajaxDisplay.innerHTML = ajaxRequest.responseText;
			//$("#overlay").unmask("Loading…");
		}
	}
	
	var queryString = "?textminingName="+termToSearch+"&type="+type+"&whatToSearch="+whatToSearch;
	ajaxRequest.open("GET", "http://tebacten.bioinfo.cnio.es/wp-content/themes/reactions/scripts/returnEvidences.php" + queryString, true);
	//alert ("http://localhost/reactions/wp-content/themes/reactions/scripts/returnEvidences.php" + queryString);
	ajaxRequest.send(null); 
}

function addOrganism(){
	//Recorremos el bucle desde el inicio para ver cual es el máximo numero posible para abrirlo:
	found=0;
	i=0;
	maxNumVisible=0;
	while ((i<26)&&(found==0)){
		display=$("#organism_ajax_"+i).css("display");
		
		if (display=="none"){
			
			maxNumVisible=i;
			found=1;
		}
		i=i+1;
	}
	
	divToOpen="organism_ajax_"+maxNumVisible;
	$("#"+divToOpen).attr("style","display:block");
	
}

function deleteOrganism(id){
	$("#organism_ajax_"+id).attr("style","display:none");
}

function addCompound(){
	//Recorremos el bucle desde el inicio para ver cual es el máximo numero posible para abrirlo:
	found=0;
	i=0;
	maxNumVisible=0;
	while ((i<26)&&(found==0)){
		display=$("#newCompound_"+i).css("display");
		
		if (display=="none"){
			
			maxNumVisible=i;
			found=1;
		}
		i=i+1;
	}
	
	divToOpen="newCompound_"+maxNumVisible;
	$("#"+divToOpen).attr("style","display:block");
	
}
function deleteCompound(divToClose){
	$("#"+divToClose).attr("style","display:none");
}
function addEnzyme(id){

	found=0;
	i=0;
	maxNumVisible=0;
	while ((i<26)&&(found==0)){
		display=$("#enzyme_"+i).css("display");
		if (display=="none"){
			maxNumVisible=i;
			found=1;
		}
		i=i+1;
	}
	
	divToOpen="enzyme_"+maxNumVisible;
	$("#"+divToOpen).attr("style","display:block");
}
function deleteEnzyme(divToClose){
	divToClose="enzyme_"+divToClose;
	$("#"+divToClose).attr("style","display:none");
}

function validateForm(){
	
	//Primero quitamos el disabled de todos los textminingOrganismsName, 
	for (var i=0;i<10;i++){
		var selectorOrganism="input[name=textminingOrganismName_"+i+"]";
		$(selectorOrganism).removeAttr("disabled");
	}
	//Luego quitamos el disabled de todos los textminingCompoundName y de los textminingEnzymeName :
	for (var i=0;i<26;i++){
		var selectorCompound="input[name=textminingCompoundName_"+i+"]";
		var selectorEnzyme="input[name=textminingEnzymeName_"+i+"]";
		$(selectorCompound).removeAttr("disabled");
		$(selectorEnzyme).removeAttr("disabled");
	}
	//Luego tenemos que poner en disabled los campos ocultos de los organisms y para eso hay que recorrerlos todos y si su style display es none entonces añadimos el atributo disabled=disabled
	for (var i=0;i<10;i++){
		var selectorOrganism="input[name=textminingOrganismName_"+i+"]";
		var display=$(selectorOrganism).parent().parent().parent().parent().parent().css("display");
		//alert (selectorOrganism+" display: "+display);
		if (display=="none"){
			$(selectorOrganism).attr("disabled","disabled");//Ponemos a "disabled" los valores de los organismos que no se muestren y así no se tendrán en cuenta más adelante al parsearlos en modificarDatos.py
		}
		else if(display=="block"){
			//Si esta presente entonces no puede estar vacio…
			var value=$(selectorOrganism).val();
			if (value==""){
				alert ("Please insert an organism name to annotate or delete the orgnanism");
				return false;
			}
		}
	}
	//Luego tenemos que poner en disabled los campos ocultos y para eso hay que recorrerlos todos y si su style display es none entonces añadimos el atributo disabled=disabled
	for (var i=0;i<26;i++){
		var selectorCompound="input[name=textminingCompoundName_"+i+"]";
		var display=$(selectorCompound).parent().parent().parent().parent().parent().css("display");
		//alert (selectorCompound+" display: "+display);
		if (display=="none"){
			$(selectorCompound).attr("disabled","disabled");//Ponemos a "disabled" los valores de los compuestos que no se muestren y así no se tendrán en cuenta más adelante al parsearlos en modificarDatos.py
		}
		else if(display=="block"){
			//Si esta presente entonces no puede estar vacio…
			var value=$(selectorCompound).val();
			if (value==""){
				alert ("Please insert a compound name to annotate or delete the compound");
				return false;
			}
		}
	}
	
	//Hacemos lo mismo para los campos de las enzimas.
	//Tenemos que poner en disabled los campos ocultos y para eso hay que recorrerlos todos y si su style display es none entonces añadimos el atributo disabled=disabled
	for (var i=0;i<26;i++){
		var selectorEnzyme="input[name=textminingEnzymeName_"+i+"]";
		var display=$(selectorEnzyme).parent().parent().parent().parent().parent().css("display");
		if (display=="none"){
			$(selectorEnzyme).attr("disabled","disabled");//Ponemos a "disabled" los valores de los compuestos que no se muestren y así no se tendrán en cuenta más adelante al parsearlos en modificarDatos.py
		}
		else if(display=="block"){
			//Si esta presente entonces no puede estar vacio…
			var value=$(selectorEnzyme).val();
			if (value==""){
				alert ("Please insert an enzyme name to annotate or delete the enzyme");
				return false;
			}
		}
	}
	var textminingOrganismName=$("#textminingOrganismName").val();
	textminingOrganismName=$.trim(textminingOrganismName);
	
		
	
	//OJO!! NO ES OBLIGATORIO QUE SELECCIONAR UN ORGANISMO
	
	/*
	if(textminingOrganismName==""){
		alert("You have to choose an organism");
		return false;	
	}
	//Además el NCBI organism id debería tener un valor…
	var ncbiOrganismId=$("#idOrganismNCBI").val();
	if(ncbiOrganismId==""){
		alert("You have to search/select the tax_id of the organism");
		return false;	
	}
	*/
	
	
	
	
	
	
	
	$("#idOrganismNCBI").removeAttr("disabled");
	
	//comprobamos que no hay ninguna caja de texto vacía.
	for (i=0;i<26;i++){
		var selectorCompound="input[name=textminingCompoundName_"+i+"]";
		var display=$(selectorCompound).parent().parent().parent().parent().parent().css("display");
		if (display=="block"){
			value=$(selectorCompound).val()
			if (value==""){
				alert("You have a compound without name. Fill in the name or delete the compound");
				return false;
			}
			valueChebiIds=$("#listOfChebiIds_"+i).val();
			/*if (valueChebiIds==null){
				alert("You have a compound without ChebiID. Please Search/Select the ChebiID or delete that compound");
				return false;
			}
			*/
		}
	}
	
	
	
	
	//Comprobamos que no haya ningún listOfProteins_i que no tenga ningún valor asociado
	//OJO!! NO ES OBLIGATORIO QUE LA ENZIMA TENGA LISTA DE PROTEINAS. Esto puede ocurrir cuando no sabemos el organismo para el que estamos anotando!!
	/*
	for (i=0;i<26;i++){		
		var display=$("#listOfProteinsOutside_"+i).parent().parent().parent().parent().parent().css("display");
		if (display=="block"){
			var valor=$("#listOfProteins_"+i).val()
			if (valor==null){
				alert("Please select at least one protein for each enzyme or delete the enzyme");
				return false;
			}
		}
	}
	*/
}


function confirmDelete(){
	confirmation=confirm("Are you sure you want to delete this evidence??");
	if (!confirmation){
		return false;
	}
	else{
		return true;
	}
}

function insertTaxonomy(id,idEvidence){
	
    var selector='textminingOrganismName_'+id;
    organismName=$("#"+selector+" option:selected").text();
    /*if (organismName=="Select an organism"){
    	alert ("Please fill in the organism name");
    	$('#overlayTaxonomy_'+id).unmask();
    	return false;
    	
    }*/
    
	
	var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			var display='organism_ajax_'+id;
            var ajaxDisplay = document.getElementById(display);
			ajaxDisplay.innerHTML = ajaxRequest.responseText;
			$('#overlayTaxonomy_'+id).unmask();
		}
	}
	
	var queryString ="?organismName="+organismName+"&selectNumber="+id+"&idEvidence="+idEvidence;
	ajaxRequest.open("GET", "http://tebacten.bioinfo.cnio.es/wp-content/themes/reactions/scripts/returnTaxonomy.py" + queryString, true);
	ajaxRequest.send(null);
}

function insertChebiIds(id){
    var selector='textminingCompoundName_'+id;
    compoundName=$("#"+selector).val();
	if (compoundName==""){
		alert("Please fill in the compound name");
		$("#overlayCompounds_"+id).unmask();
		return false;
	}
	var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			var display='listaCompoundsIds_'+id;
            var ajaxDisplay = document.getElementById(display);
			ajaxDisplay.innerHTML = ajaxRequest.responseText;
			$("#overlayCompounds_"+id).unmask();
		}
	}
	
	var queryString ="?compoundName="+compoundName+"&id="+id;
	ajaxRequest.open("GET", "http://tebacten.bioinfo.cnio.es/wp-content/themes/reactions/scripts/returnchebids.py/" + queryString, true);
	ajaxRequest.send(null); 
}

function insertProteinsOfEnzyme (id,typeSearch){
	var queryString="?";
	//Primero vemos el tipo de búsqueda que queremos hacer: "selected", "conventioned" o "all" y según esto actuamos de una forma u otra.
	var selector='textminingEnzymeName_'+id;
    enzymeName=$("#"+selector).val();
    if (enzymeName==""){
    	alert("please type an enzyme to search its proteins");
    	$("#overlayEnzymes_"+id).unmask();
		return false;	
    }
    queryString+="&enzymeName="+enzymeName
	if (typeSearch=="selected"){
		
		taxid=$("#idOrganismNCBI_"+id).val();
		if (taxid==""){
			alert("please select an organism before searching for proteins");
			$("#overlayEnzymes_"+id).unmask();
			return false;
		}
		if (taxid==null){
			alert("You can't search in selected organism without selecting one above");
			$("#overlayEnzymes_"+id).unmask();
			return false;
		}
		queryString+="&taxid="+taxid
	}
	else if(typeSearch=="conventioned"){
		//Tenemos que recuperar todas las options del select "textminingOrganismName_0"
		var stringOrganisms="";
		var insertedOrganism=false;
		$("#textminingOrganismName_0 option").each(function()
			{
    			texminingOrganismName = $(this).val();
    			if (texminingOrganismName!=""){
    				stringOrganisms=stringOrganisms+texminingOrganismName+"_";
    				insertedOrganism=true;
    			}
			});
		if (insertedOrganism==false){
			alert("You can't search in conventioned species if no one have been conventioned");
			$("#overlayEnzymes_"+id).unmask();
			return false;
		}
		else{
			//Eliminamos el ; final si hemos insertado al menos textminingOrganismName al stringOrganisms
			var newStringOrganisms = stringOrganisms.substring(0, stringOrganisms.length-1);
			queryString+="&listOrganisms="+newStringOrganisms;
		}
	}
	
	var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			var display='listOfProteinsOutside_'+id;
            var ajaxDisplay = document.getElementById(display);
			ajaxDisplay.innerHTML = ajaxRequest.responseText;
			$("#overlayEnzymes_"+id).unmask();
		}
	}
	
	queryString +="&selectNumber="+id+"&typeSearch="+typeSearch;
	ajaxRequest.open("GET", "http://tebacten.bioinfo.cnio.es/wp-content/themes/reactions/scripts/returnProteinsOfEnzyme.py/" + queryString, true);
	ajaxRequest.send(null); 
}