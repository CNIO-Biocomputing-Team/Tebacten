function showEvidences(idCompuesto,type){
    
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
	
	var queryString = "?idCompuesto="+idCompuesto+"&type=enzyme";
	ajaxRequest.open("GET", "http://localhost/reactions/wp-content/themes/reactions/scripts/returnEvidences.php" + queryString, true);
	ajaxRequest.send(null); 
}

function addNewCompoundEnzyme(compoundOrEnzyme,numberToAdd){
	if(compoundOrEnzyme=="compound"){
		var divToOpen="curate_compound_new_"+numberToAdd;
		var numberToHide=numberToAdd-1;
		var buttonToHide="add_compound_"+numberToHide;
	}
	else if (compoundOrEnzyme=="enzyme"){
		var divToOpen="curate_enzyme_new_"+numberToAdd;
		//Cuando añadimos un nuevo enzima o componente tenemos que ocultar ese botón de añadir
		var numberToHide=numberToAdd-1;
		var buttonToHide="add_enzyme_"+numberToHide;
	}
	$("#"+divToOpen).attr("style","display:block");
	$("#"+buttonToHide).attr("style","display:none");
	
}

function deleteCompound(inputOutputNew,numberToDel){

	var divToClose="curate_compound_"+inputOutputNew+"_"+numberToDel;
	//Cuando borramos un compuesto tenemos que mostrar el botón de añadir anterior
	var numberToShow=numberToDel-1;
	var buttonToShow="add_compound_"+numberToShow;
	
	$("#"+divToClose).attr("style","display:none");
	$("#"+buttonToShow).attr("style","display:block");
	
	//OJO, para hacer que el input text del componente que se elimina, no solo no se vea sino que se ponga disabled=disabled. Hacemos
	$("#"+divToClose)
	
}

function deleteEnzyme(inputOutputNew,numberToDel){
	var divToClose="curate_enzyme_"+inputOutputNew+"_"+numberToDel;
	if (inputOutputNew==""){
		divToClose="curate_enzyme_0";
	}
	var numberToShow=numberToDel-1;
	var buttonToShow="add_enzyme_"+numberToShow;
	
	$("#"+divToClose).attr("style","display:none");
	$("#"+divToClose).attr("disabled","disabled");
	$("#"+buttonToShow).attr("style","display:block");
}

function validateForm(){
	//Entre otras cosas tenemos que poner en disabled los campos ocultos y para eso hay que recorrerlos todos y si su style display es none entonces añadimos el atributo disabled=disabled
	var strTmp="";
	for (var i=0;i<24;i++){
		if ($("#compound_"+i).css("display")=="None"){
			alert ("El primero con None es: "+i);
			return false;
		}
		else{
			alert($("#compound_"+i).css("display")=="None");
		}
	}
	alert(strTmp);
	return false;
}