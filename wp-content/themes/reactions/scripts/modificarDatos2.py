#!/usr/bin/python
# -*- coding: utf-8 -*-
import sys
import os
import re
import MySQLdb
import cgi
import urllib
from types import ListType
import cgitb; cgitb.enable();
sys.stderr = sys.stdout
from Bio import Entrez
from Bio import SwissProt
from Bio import ExPASy

Entrez.email = "acanada@cnio.es"
if not Entrez.email:
    print "you must add your email address"
    sys.exit(2)
import Constants
DB_READ_USER, DB_READ_PWD, DB_WRITE_USER, DB_WRITE_PWD, DB_JABBA_DB = Constants.initConfig()
charmap = {
    "\"":"\\\"",
    # ...
}
################################################################################################
#########Script que recoge del popup los datos de la evidencia curada en la que se han##########
################modificado/añadido/eliminado compuestos/enzimas… etc y que #####################
####################una vez parseados los guarda en la base de datos############################
################################################################################################

NUM_MAX_ORGANISMS=10
NUM_MAX_COMPOUNDS=26
NUM_MAX_ENZYMES=26

def commonOutput():
    print "Content-type: text/html\n\n"
    print """
    <html>
    <head>
    <title>INB</title>
    </head>
    <body>
    <center>
    Sorry, we're updating some Website functionalities.<br/>
    Please, come back later.<br/>

    """

def get_scientifc_name_from_tax_id(taxid):
    """once we have the taxid, we can fetch the record"""
    search = Entrez.efetch(id = taxid, db = "taxonomy", retmode = "xml")
    record=Entrez.read(search)
    try:
    	scientificName=record[0]["ScientificName"]
    except:
    	tmpString="Organism:<input id=\"textminingOrganismName\" type=\"text\" NAME=\"textminingOrganismName\" maxlenght=\"255\" size=\"20\" value=\""+organismName+"\" onChange=\"insertTaxonomy();\">(<a href=\"#\" onClick=\"insertTaxonomy();\">Click to search</a>)It has been a problem searching in NCBI Taxonomy. Please try again\n"
    	print tmpString
    	sys.exit()
    #sys.exit()
    return scientificName
    
commonOutput()
#RECOGIDA DE DATOS:
fs = cgi.FieldStorage()
redirectionOKcurated="http://tebacten.bioinfo.cnio.es/curate-evidence/?"
redirectionKOcurated="http://tebacten.bioinfo.cnio.es/curate-evidence/error?"
idEvidence=fs['idEvidence'].value
metodo=fs['metodo'].value
idEvidencesOrganisms=fs['textminingOrganismName_0'].value
idOrganismNCBI="unknown" 
#print "<br/>"+str(metodo)
#DB_CONN = MySQLdb.connect(host= "jabba.cnio.es", port=3306, user = "tebacten", passwd= "tebacten", db= "tebacten", charset="utf8", init_command="set names utf8")
DB_CONN = MySQLdb.connect(host= "jabba.cnio.es", port=3306, user = DB_READ_USER, passwd= DB_READ_PWD, db= DB_JABBA_DB, charset="utf8", init_command="set names utf8")
cur = DB_CONN.cursor()	


if metodo=="curate":

	#Al curar una evidencia, por la parte del organismo implicado, evidences_organisms. Necesitamos tener o no tener el id_organismo, así como la evidencia.
	print "<br/>"+str(idEvidence)
	print "<br/>"+str(idEvidencesOrganisms)
	sys.exit()
	
	"""
	arrayEvidencesOrganismsAddedModified=[]
	#En arrayEvidencesOrganismsAddedModified tendremos los organisms que formaran parte de esa evidencia, El resto no. Para ello tenemos que recorrer todos los compuestos de la evidencia y
	#Creamos un array con los id_evidences_compounds de las entradas que contienen esta evidencia para compararlos:
	arrayExistentEvidencesOrganisms=[]
	selectEvidencesOrganisms="select id_evidences_organisms from evidences_organisms where id_evidence='"+str(idEvidence)+"'"
	cur.execute(selectEvidencesOrganisms)
	while(1):
		row = cur.fetchone()
		if row==None: 
			break
		idEvidencesOrganisms=row[0]
		arrayExistentEvidencesOrganisms.append(idEvidencesOrganisms)
	
	for i in range (NUM_MAX_ORGANISMS):
		
		#Primero miramos a ver si ha llegado el textminingOrganismName:
		try:
			textminingOrganismName=fs['textminingOrganismName_'+str(i)].value
			existeTextminingOrganismName=True
		except:
			existeTextminingOrganismName=False
			continue
		
		if existeTextminingOrganismName==True:
			try:
				strain=fs['strain_'+str(i)].value
			except:
				#Si no viene el strain es porque tenemos un texminingOrganismName sin strain, es decir, ""
				strain=""
			#Lo guardamos. Primero vemos si existe una entrada en la tabla organisms para ese organismo.
			selectOrganisms="select id_organism from organisms where textmining_organism_name like '"+str(textminingOrganismName)+"'"
			#print "<br/>"+str(selectOrganisms)
			try:
				cur.execute(selectOrganisms)
				#Existe una entrada en la tabla organisms para ese organismo
				rowSelectOrganisms = cur.fetchone()
				idOrganism=rowSelectOrganisms[0]
			except:
				#No existe la entrada para ese textmining_organism_name. La creamos.
				sqlInsertOrganism="insert into organisms (id_organism,id_organism_ncbi,ncbi_organism_name,textmining_organism_name) values (NULL,NULL,NULL,'"+str(textminingOrganismName)+"')"
				cur.execute(sqlInsertOrganism)
				DB_CONN.commit()
				selectMaxOrganism="select max(id_organism) from organisms";
				cur.execute(selectMaxOrganism)
				rowMaxOrganism = cur.fetchone()
				idOrganism=rowMaxOrganism[0]
			#Ya tenemos el idOrganism si este existe. Ahora buscamos a ver si hay información del taxid
			try:
				idOrganismNCBI=fs['idOrganismNCBI_'+str(i)].value
				existeIdOrganismNCBI=True
			except:
				existeIdOrganismNCBI=False
			if existeIdOrganismNCBI==True:
				#Si existeIdOrganismNCBI entonces cogemos el valor del NCBI organism id y lo guardamo en la tabla.
				sqlUpdateOrganism="update organisms set id_organism_ncbi='"+str(idOrganismNCBI)+"' , ncbi_organism_name='"+str(textminingOrganismName)+"' where id_organism="+str(idOrganism)
				#print "<br>"+str(sqlUpdateOrganism)
				cur.execute(sqlUpdateOrganism)
				DB_CONN.commit()
	
			#Al llegar tenemos un organismo o no organismo. En el caso de que exista el organismo entonces tenemos que guardar una entrada en la tabla de evidences_organisms.
			#Para ello tenemos todos los datos excepto el strain que estará en cualquier caso, así que primero vemos si existe esa entrada para esa evidencia y ese organismo y en caso de que exista la modificamos y si no existe la creamos
			
			selectEvidencesOrganisms="select id_evidences_organisms from evidences_organisms where id_evidence='"+idEvidence+"' and id_organism="+str(idOrganism)+""
			try:
				cur.execute(selectEvidencesOrganisms)
				#Existe la entrada en tabla evidences_organisms.
				rowSelectEvidencesOrganisms = cur.fetchone()
				idEvidencesOrganisms=rowSelectEvidencesOrganisms[0]
				existeEvidencesOrganisms=True
			except:
				existeEvidencesOrganisms=False
			if existeEvidencesOrganisms==True:
				#Modificamos los datos de la entrada en la tabla
				sqlUpdateEvidencesOrganisms="update evidences_organisms set id_organism="+str(idOrganism)+" , strain='"+str(strain)+"' where id_evidences_organisms="+str(idEvidencesOrganisms)+""
				cur.execute(sqlUpdateEvidencesOrganisms)
				DB_CONN.commit()
				arrayEvidencesOrganismsAddedModified.append(idEvidencesOrganisms)
			else:
				#Si no existe la entrada entonces tenemos que crear una nueva entrada en la tabla evidences_organisms, para esa evidencia y para ese organismo
				sqlInsertEvidencesOrganisms="insert into evidences_organisms (id_evidences_organisms, id_evidence,id_organism,strain) values (NULL,'"+str(idEvidence)+"',"+str(idOrganism)+",'"+str(strain)+"')"
				cur.execute(sqlInsertEvidencesOrganisms)
				DB_CONN.commit()
				selectMaxEvidencesOrganisms="select max(id_evidences_organisms) from evidences_organisms";
				cur.execute(selectMaxEvidencesOrganisms)
				rowMaxEvidencesOrganisms = cur.fetchone()
				idEvidencesOrganisms=rowMaxEvidencesOrganisms[0]
				arrayEvidencesOrganismsAddedModified.append(idEvidencesOrganisms)
			
	#Si no estan en el arrayEvidencesOrganismsAddedModified entonces eliminarlos de la tabla evidences_organisms ya que esos organismos se han eliminado de la evidencia
	#print "<br/> el arrayEvidencesOrganismsAddedModified contiene: "+str(arrayEvidencesOrganismsAddedModified)		
	#print "<br/> el arrayExistentEvidencesOrganisms contiene: "+str(arrayExistentEvidencesOrganisms)		
	for idEvidencesOrganisms in arrayExistentEvidencesOrganisms:
		#print "<br>"+str(idEvidencesOrganisms)
		if idEvidencesOrganisms in arrayEvidencesOrganismsAddedModified:
			#si el idEvidencesOrganisms se encuentra entre los existentes en la base de datos lo dejamos
			pass
		else:
			#si el idEvidencesOrganisms no se encuentra entre los existentes en la base de datos lo eliminamos
			deleteSQL="delete from evidences_organisms where id_evidences_organisms="+str(idEvidencesOrganisms)
			#print "<br/>"+str(deleteSQL)
			try:
				rc = cur.execute( deleteSQL)
				DB_CONN.commit()
			except MySQLdb.OperationalError, e:
				print "Location: "+str(redirectionKOcurated)+"error=deleteOldCompounds&idEvidence="+idEvidence+" \n\n"
				sys.exit()
	"""		
	#HASTA AQUI LA PARTE DE ORGANISMS Y EVIDENCES_ORGANISMS.
	

	#Recuperamos datos de los compounds
	#Hay que ir guardando un array con los idEvidencesCompounds añadidos/modificados, porque una vez añadidos/modificados tendremos
	#que eliminar el resto de compuestos que formaban parte de la evidencia.
	#En arrayEvidencesCompoundsAddedModified tendremos los compuestos que formaran parte de esa evidencia, El resto no. Para ello tenemos que recorrer todos los compuestos de la evidencia y
	arrayEvidencesCompoundsAddedModified=[]
	arrayEvidencesEnzymesAddedModified=[]
	#print "<br/>Analizamos los compuestos uno a uno conforme los recibimos. OJO!! puede no venir ningún compuesto!!<br/>"
	#Creamos un array con los id_evidences_compounds de las entradas que contienen esta evidencia para compararlos:
	arrayExistentEvidencesCompounds=[]
	selectEvidencesCompounds="select id_evidences_compounds from evidences_compounds where id_evidence='"+str(idEvidence)+"'"
	#print "<br/>"+selectEvidencesCompounds
	cur.execute(selectEvidencesCompounds)
	while(1):
		row = cur.fetchone()
		if row==None: 
			break
		idEvidencesCompounds=row[0]
		arrayExistentEvidencesCompounds.append(idEvidencesCompounds)
	#print "<br/> el arrayExistentEvidencesCompounds contiene: "+str(arrayExistentEvidencesCompounds)			
	
	for i in range (NUM_MAX_COMPOUNDS):
		try:
			textminingCompoundName=fs['textminingCompoundName_'+str(i)].value
		except:
			continue
		try:
			idChebi=fs['listOfChebiIds_'+str(i)].value
		except:
			idChebi=""
		try:
			inputOutput=fs['inputOutput_'+str(i)].value
		except:
			inputOutput=""
		if inputOutput=="input":
			inputOutput="0"
		elif inputOutput=="output":
			inputOutput="1"
		else:
			inputOutput=""
		#print "<br>******"+str(inputOutput)+"******"	
		
		#Aqui tenemos un componente con su textminingCompoundName, su inputOutput, su idChebi y tenemos que ver si ese compound ya pertenece a la evidencia o ha sido modificado
		#Lo que nos llega es la informacion asociada a una evidencia. Tenemos que ver si dicha evidencia existe y sino existe la creamos.
		#Para ello primero vemos cual es el idCompound de ese compuesto y sino existe lo creamos y lo guardamos en idCompound.
		selectSQL="select id_compound, textmining_compound_name from compounds where textmining_compound_name='"+str(textminingCompoundName)+"'"
		#print "<br/>"+selectSQL
		try:
			cur.execute(selectSQL)
			row = cur.fetchone()
			#Existe el compound, cogemos su id_compound
			idCompound=row[0]
			#print "<br/>"+str(row[0])
			#print "<br/>"+str(row[1])
		except:
			#print "<br/>no existe el compound hay que crear uno nuevo!!"
			#No existe el compuesto y hay que añadir uno nuevo
			#sql="insert into compounds (id_compound,textmining_compound_name)values(NULL,%s)"
			#rc = cur.execute( sql,(str(textminingCompoundName),))
			sqlInsertCompound="insert into compounds (id_compound,textmining_compound_name) values (NULL,'"+textminingCompoundName+"')"
			rc=cur.execute(sqlInsertCompound)
			DB_CONN.commit()
			#Hemos añadido un nuevo compound a la tabla compounds. Queremos saber ahora cual es el id_compound del compound que acabamos de añadir
			selectSQL2="select max(id_compound) from compounds";
			cur.execute(selectSQL2)
			row2 = cur.fetchone()
			idCompound=row2[0]
		
		##Al salir de aqui tenemos el compuesto en la tabla de compounds y su id_compuesto es idCompuesto
		#print "<br/>El idCompound es "+str(idCompound)
		
		#Tenemos que guardar una entrada en la tabla evidences_compounds, pero primero tenemos que saber si ya existe para modificarla.
		#para ello tenemos que buscar si existe una entrada para ese compound y esa evidencia
		selectBuscarCompuestoEvidencia="select id_evidences_compounds from evidences_compounds where id_Evidence='"+str(idEvidence)+"' and id_compound="+str(idCompound)
		#print "<br/>"+selectBuscarCompuestoEvidencia
		try:
			
			cur.execute(selectBuscarCompuestoEvidencia)
			row3 = cur.fetchone()
			#Existe este compuesto para esta evidencia asi que lo que hacemos es recuperarla y modificarla con los nuevos datos.
			idEvidencesCompounds=row3[0]
			updateEvidencesCompounds="update evidences_compounds set input_output="+str(inputOutput)+", id_chebi='"+str(idChebi)+"' where id_evidences_compounds="+str(idEvidencesCompounds)+""
			#print "<br/>Existe la evidencecompound: "+str(updateEvidencesCompounds)
			rc = cur.execute(updateEvidencesCompounds)
			DB_CONN.commit()
			#Como hemos modificado el compuesto de esta evidencia entonces la añadimos al array arrayEvidencesCompoundsAddedModified
			arrayEvidencesCompoundsAddedModified.append(idEvidencesCompounds)
			#print "<br/>"+str(updateEvidencesCompounds)

		except:
			#print "<br/>NO Existe la evidencecompound: "+str(selectBuscarCompuestoEvidencia)
			#No existe el compound para esa evidencia asi que lo añadimos en la tabla evidences_compounds
			#print "<br>No existe el compound para esa evidencia (si que existe porque lo hemos creado) asi que lo añadimos en la tabla evidences_compounds"
			sqlInsertEvidenceCompound="insert into evidences_compounds (id_evidences_compounds,id_evidence,id_compound, input_output,id_chebi,recognition_method) values (NULL,'"+str(idEvidence)+"',"+str(idCompound)+",'"+str(inputOutput)+"','"+str(idChebi)+"','manually_annotated')"
			#print "<br/>"+str(sqlInsertEvidenceCompound)
			rc = cur.execute( sqlInsertEvidenceCompound)
			DB_CONN.commit()
			#Como hemos añadido este compuesto para esta evidencia entonces la añadimos al array arrayEvidencesCompoundsAddedModified
			sqlMaxEvidencesCompounds="select max(id_evidences_compounds) from evidences_compounds";
			cur.execute(sqlMaxEvidencesCompounds)
			row = cur.fetchone()
			idMaxEvidencesCompounds=row[0]
			#print "<br/> El id_compound del compound insertado en evidences_compounds es "+str(idMaxEvidencesCompounds)
			arrayEvidencesCompoundsAddedModified.append(idMaxEvidencesCompounds)
			#sqlInsertEvidenceCompound="insert into evidences_compounds (id_evidences_compounds,id_evidence, id_compound, input_output, id_chebi) values(NULL,%d,%d,%d,%s)"
			#rc = cur.execute( sqlInsertEvidenceCompound,(int(idEvidence),int(idCompound),int(inputOutput),str(idChebi),))
			#print "<br/>"+sqlInsertEvidenceCompound
	
	#Si no estan en el arrayEvidencesCompoundsAddedModified entonces eliminarlos de la tabla evidences_compounds ya que esos compounds se han eliminado de la evidencia
	#print "<br/> el arrayEvidencesCompoundsAddedModified contiene: "+str(arrayEvidencesCompoundsAddedModified)		
	
	for idEvidencesCompounds in arrayExistentEvidencesCompounds:
		#print "<br>"+str(idEvidencesCompounds)
		if idEvidencesCompounds in arrayEvidencesCompoundsAddedModified:
			#si el idEvidencesCompouns se encuentra entre los existentes en la base de datos lo dejamos
			pass
		else:
			#si el idEvidencesCompouns no se encuentra entre los existentes en la base de datos lo eliminamos
			deleteSQL="delete from evidences_compounds where id_evidences_compounds="+str(idEvidencesCompounds)
			#print "<br/>"+str(deleteSQL)
			try:
				rc = cur.execute( deleteSQL)
				DB_CONN.commit()
			except MySQLdb.OperationalError, e:
				print "Location: "+str(redirectionKOcurated)+"error=deleteOldCompounds&idEvidence="+idEvidence+" \n\n"
				sys.exit()
	
	#print "<br/>Fin de analizar compuestos!!"
	
	#Recuperamos datos de las enzymes
	#Hay que ir guardando un array con los idEvidencesEnzymes añadidos/modificados, porque una vez añadidos/modificados tendremos
	#que eliminar el resto de compuestos que formaban parte de la evidencia.
	arrayEvidencesEnzymesAddedModified=[]
	
	for i in range (NUM_MAX_ENZYMES):
			
		#print "<br/>*********************"+str(i)+"*************************<br/>"
		
		try:
			textminingEnzymeName=fs['textminingEnzymeName_'+str(i)].value
			
		except:
			continue
		#print "<br>"+str(textminingEnzymeName)
		#Si tenemos un textminingEnzymeName ya podemos guardarlo por si no existe o recuperar su idEnzyme si es que ya existe:
		selectEnzyme="select id_enzyme from enzymes where textmining_enzyme_name='"+str(textminingEnzymeName)+"'"
		#print "<br/>"+str(selectEnzyme)
		try:
			cur.execute(selectEnzyme)
			rowSelectEnzyme = cur.fetchone()
			#Existe esta enzima recuperamos su id_enzyme
			idEnzyme=rowSelectEnzyme[0]
		except:
			#No existe entrada en la tabla enzymes para esta enzima. La creamos
			insertEnzyme="insert into enzymes (id_enzyme,textmining_enzyme_name) values(NULL,'"+str(textminingEnzymeName)+"')"
			cur.execute(insertEnzyme)
			DB_CONN.commit()
			selectMaxEnzyme="select max(id_enzyme) from enzymes"
			cur.execute(selectMaxEnzyme)
			rowMaxEnzyme = cur.fetchone()
			idEnzyme=rowMaxEnzyme[0]
		
		#Aqui tenemos el idEnzyme del enzyme que nos ocupa.
		
		##################################################################
		##################################################################
		###### RECUPERAMOS LAS PROTEINAS QUE FORMAN PARTE DE LA ENZIMA####
		####   OJO QUE PUEDEN NO LLEGAR PROTEINAS!!!
		##################################################################
		
		selector='listOfProteins_'+str(i)
		#listOfProteins=fs[selector]
		form = cgi.FormContentDict()
		value = fs.getvalue(selector, "")
		if isinstance(value, ListType):
			# Multiple username fields specified
			stringWithProteins = ",".join(value)
			#print"<br/>"+str(stringWithProteins)
		else:
			# Single or no username field specified
			stringWithProteins = value
			#print"<br/>"+str(stringWithProteins)
		if stringWithProteins=="":
			existenProteinsInsideEnzyme=False
		else:
			existenProteinsInsideEnzyme=True
		
		#print "<br/>"+str(existenProteinsInsideEnzyme)
		if existenProteinsInsideEnzyme==False:
			#No existen proteínas para esta enzima. No guardamos nada en enzymes_proteins, ni en proteins. Pero si guardamos la información en la tabla evidences_enzymes
			#Primero buscamos si existe la entrada en tabla evidences_enzymes. Si no existe la creamos	y si existe la modificamos (han desaparecido el posible id_enzymes_proteins)
			arrayWithIdProteins=[]
			selectEvidencesEnzymes="select id_evidences_enzymes from evidences_enzymes where id_evidence='"+str(idEvidence)+"' and id_enzyme="+str(idEnzyme)
			#print "<br/>"+ selectEvidencesEnzymes
			try:
				cur.execute(selectEvidencesEnzymes)
				#Existe la entrada en la tabla evidences_enzymes
				rowSelectEvidencesEnzymes = cur.fetchone()
				idEvidencesEnzymes=rowSelectEvidencesEnzymes[0]
				arrayEvidencesEnzymesAddedModified.append(idEvidencesEnzymes)
			except:
				#No Existe la entrada en la tabla evidences_enzymes para esa enzyme. La generamos pero sin un id_enzymes_proteins
				insertEvidencesEnzymes="insert into evidences_enzymes (id_evidences_enzymes,id_enzyme,id_evidence,id_enzymes_proteins,recognition_method) values (NULL,"+str(idEnzyme)+",'"+str(idEvidence)+"',NULL,'manually_annotated')"
				cur.execute(insertEvidencesEnzymes)
				DB_CONN.commit()
				selectMaxEvidencesEnzymes="select max(id_evidences_enzymes) from evidences_enzymes"
				cur.execute(selectMaxEvidencesEnzymes)
				rowMaxEvidencesEnzymes = cur.fetchone()
				idMaxEvidencesEnzymes=rowMaxEvidencesEnzymes[0]
				arrayEvidencesEnzymesAddedModified.append(idMaxEvidencesEnzymes)
				#print "<br>"+str(arrayEvidencesEnzymesAddedModified)
		if existenProteinsInsideEnzyme==True:
			arrayWithProteins=stringWithProteins.split(",");
			#print "<br/>arrayWithProteins "+str(arrayWithProteins)
			#Recorremos el arrayWithProteins y las guardamos en la tabla proteins, si es que no estan. De cada proteina este o no este tenemos que guardar el id_protein para generar el string
			#con las id_proteinas que guardaremos en la tabla enzymes proteins mas adelante
			arrayWithIdProteins=[]
			for protein in arrayWithProteins:
				#obtenemos el proteinName para esta protein
				url="http://www.uniprot.org/uniprot/"+str(protein)+".txt"
				#print "<br/>"+url
				try:
					filehandle = urllib.urlopen(url)
				except:
					print "Location: "+str(redirectionKOcurated)+"error=UniprotConnectionProblem&idEvidence="+idEvidence+" \n\n"
					sys.exit()
				record = SwissProt.read(filehandle)
				#print dir(record)
				description=str(record.description)
				#En description podemos tener algo así: 
				#RecName: Full=Aspartate aminotransferase, mitochondrial; Short=mAspAT; EC=2.6.1.1; AltName: Full=Fatty acid-binding protein; Short=FABP-1; AltName: Full=Glutamate oxaloacetate transaminase 2; AltName: Full=Plasma membrane-associated fatty acid-binding protein; Short=FABPpm; AltName: Full=Transaminase A; Flags: Fragment; 
				#Nos quedamos con la primera parte.
				arrayNombres=description.split(";")
				proteinName=arrayNombres[0]
				#En proteinName ahora tenemos algo así: 
				##RecName: Full=Aspartate aminotransferase, mitochondrial
				#Tenemos que quitar la parte de RecName: Full=
				proteinName=proteinName.replace("RecName: Full=","")
				proteinName=proteinName.replace("SubName: Full=","")
				#Ya tenemos todos los datos para guardar la proteina
				filehandle.close()
				selectProtein="select id_protein, id_uniprot, protein_name from proteins where id_uniprot='"+str(protein)+"'"
				#print "<br/>"+str(selectProtein)
				try:
					cur.execute(selectProtein)
					rowProtein=cur.fetchone()
					idProtein=rowProtein[0]
					arrayWithIdProteins.append(str(idProtein))
				except:
					#Quiere decir que esta proteína no estaba en la tabla de proteins y tenemos que añadirla y guardar su id_protein
					insertProtein="insert into proteins (id_protein, id_uniprot, protein_name) values (NULL, '"+str(protein)+"','"+str(proteinName)+"' )"
					cur.execute(insertProtein)
					DB_CONN.commit()
					selectMaxProtein="select max(id_protein) from proteins";
					cur.execute(selectMaxProtein)
					rowMaxProtein = cur.fetchone()
					idProtein=rowMaxProtein[0]
					arrayWithIdProteins.append(str(idProtein))
		
		#print "<br/>arrayWithIdProteins anadidas: "+str(arrayWithIdProteins)
		stringWithProteins=",".join(arrayWithIdProteins)
		if stringWithProteins=="":
			existeListProteins=False
		else:
			existeListProteins=True
		#print "<br/>El stringProteins es: "+str(stringWithProteins)+"***** y existeEnzymeProteins="+str(existeListProteins)
		#sys.exit()
		#En stringWithProteins tenemos una cadena con los id_protein de las proteinas implicadas en la enzima. Tenemos que guardarla. Pero primero hay que ver si
		#Existe una entrada para esta enzima en este organismo y si existe se modifica y sino existe se crea. Obtendremos un id_enzymes_proteins para usarlo en la tabla evidences_enzymes

		#Aqui tenemos una enzima con su textminingEnzymeName y tenemos que ver si esa enzima ya pertenece a la evidencia y ha sido modificada o 
		#es una enzima nueva
		#Lo que nos llega es la informacion asociada a una evidencia. Tenemos que ver si dicha evidencia existe y sino existe la creamos. 
		#Para ello primero vemos cual es el idEnzyme de esa enzima y sino existe lo creamos y lo guardamos en idEnzyme
		selectSQL="select id_enzyme, textmining_enzyme_name from enzymes where textmining_enzyme_name='"+str(textminingEnzymeName)+"'"
		#print "<br/>"+selectSQL
		try:
			cur.execute(selectSQL)
			row = cur.fetchone()
			idEnzyme=row[0]
			#print "<br/>"+str(row[0])
			#print "<br/>"+str(row[1])
		except:
			#print "<br/>no existe el enzyme hay que crear uno nuevo!!"
			#No existe la enzima y hay que añadir una nueva
			#sql="insert into compounds (id_compound,textmining_compound_name)values(NULL,%s)"
			#rc = cur.execute( sql,(str(textminingCompoundName),))
			sql="insert into enzymes (id_enzyme,textmining_enzyme_name) values (NULL,'"+textminingEnzymeName+"')"
			rc=cur.execute(sql)
			DB_CONN.commit()
			#Hemos añadido un nuevo enzyme a la tabla enzyme. Queremos saber ahora cual es el id_enzyme del enzyme que acabamos de añadir
			selectSQL2="select max(id_enzyme) from enzymes";
			cur.execute(selectSQL2)
			row2 = cur.fetchone()
			idEnzyme=row2[0]
		#Al salir de aqui tenemos la enzima en la tabla de enzymes y su id_enzyme es idEnzyme
		#print "<br/>El idEnzyme es "+str(idEnzyme)+"<br/>"
		#Antes de nada tenemos que ver si existe un listado de proteinas para esta enzima en ese organismo!!!. Si no existe lo creamos y si existe lo modificamos con este que es el definitivo
		
		#"Solo guardaremos enzymes proteins en el caso de que haya un idOrganismNCBI valido así que aquí solo entrará si"
		#print "<br>idOrganismNCBI es: "+str(idOrganismNCBI)
		
		if idOrganismNCBI!="unknown":#Lo inicializamos así al principio pero cuando hay idOrganismNCBI lo susituimos
			selectEnzymesProteins="select id_enzymes_proteins,proteins_list from enzymes_proteins where id_enzyme="+str(idEnzyme)+" and id_organism_ncbi='"+idOrganismNCBI+"'"
			#print "<br/>"+selectEnzymesProteins
			try:
				cur.execute(selectEnzymesProteins)
				row = cur.fetchone()
				#Si seguimos aqui es porque existe la entrada en la tabla enzymes_proteins. La modificamos con la nueva lista de proteinas.
				idEnzymesProteins=row[0]
				#print "<br/>Entra ya que existen proteínas relacionadas a esa enzima en ese organismo. El idEnzymesProteins es "+str(idEnzymesProteins)
				updateEnzymesProteins="update enzymes_proteins set proteins_list='"+str(stringWithProteins)+"' ,id_organism_ncbi='"+str(idOrganismNCBI)+"' where id_enzymes_proteins="+str(idEnzymesProteins)
				#print "<br/>"+updateEnzymesProteins
				cur.execute(updateEnzymesProteins)
				DB_CONN.commit()
			except:
				#print "<br/>No existe la entrada en enzymes_proteins asi que la tenemos que crear"
				insertEnzymesProteins="insert into enzymes_proteins (id_enzymes_proteins,id_enzyme,proteins_list,id_organism_ncbi) values (NULL,"+str(idEnzyme)+",'"+str(stringWithProteins)+"','"+str(idOrganismNCBI)+"')"
				cur.execute(insertEnzymesProteins)
				DB_CONN.commit()
				#Hemos añadido una nueva entrada en la tabla enzymes_proteins. Queremos saber ahora cual es el id_enzymes_proteins que acabamos de añadir
				selectMaxEnzymesProteins="select max(id_enzymes_proteins) from enzymes_proteins";
				cur.execute(selectMaxEnzymesProteins)
				row = cur.fetchone()
				idEnzymesProteins=row[0]
			#Aqui ya tenemos el idEnzymesProteins, nuevo o modificado, que necesitaremos para añadirle a la entrada evidences_enzymes
			#print "<br/>El id_enzymes_proteins para esta enzima es: "+str(idEnzymesProteins)
			
			#Tenemos que guardar una entrada en la tabla evidences_enzymes, pero primero tenemos que saber si ya existe para modificarla.
			#para ello tenemos que buscar si existe una entrada para esa enzyme y esa evidencia
			selectBuscarEnzimaEvidencia="select id_evidences_enzymes from evidences_enzymes where id_evidence='"+str(idEvidence)+"' and id_enzyme="+str(idEnzyme)
			#print "<br/>"+selectBuscarEnzimaEvidencia
			try:
				cur.execute(selectBuscarEnzimaEvidencia)
				row3 = cur.fetchone()
				#Existe esta enzima para esta evidencia asi que lo que hacemos es recuperarla y modificarla con los nuevos datos.
				idEvidencesEnzymes=row3[0]
				if existeListProteins==True:
					
					updateEvidencesEnzymes="update evidences_enzymes set id_enzymes_proteins="+str(idEnzymesProteins)+" where id_evidences_enzymes="+str(idEvidencesEnzymes)
					#print "<br/>"+updateEvidencesEnzymes
					rc = cur.execute(updateEvidencesEnzymes)
					DB_CONN.commit()
					#Como hemos modificado la enzima de esta evidencia entonces la añadimos al array arrayEvidencesEnzymesAddedModified
					arrayEvidencesEnzymesAddedModified.append(idEvidencesEnzymes)
					#print "<br/>"+str(updateEvidencesEnzymes)	
			except:
				#No existe la enzima para esa evidencia asi que la añadimos en la tabla evidences_enzymes
				#print "<br>No existe la enzima para esa evidencia (si que existe porque lo hemos creado) asi que lo añadimos en la tabla evidences_compounds"
				if existeListProteins==True:
					sqlInsertEvidenceEnzyme="insert into evidences_enzymes (id_evidences_enzymes,id_enzyme,id_evidence,id_enzymes_proteins) values (NULL,"+str(idEnzyme)+",'"+str(idEvidence)+"',"+str(idEnzymesProteins)+")"
				else:
					sqlInsertEvidenceEnzyme="insert into evidences_enzymes (id_evidences_enzymes,id_enzyme,id_evidence,id_enzymes_proteins) values (NULL,"+str(idEnzyme)+",'"+str(idEvidence)+"',NULL)"
				rc = cur.execute( sqlInsertEvidenceEnzyme)
				DB_CONN.commit()
				#Como hemos añadido esta enzima para esta evidencia entonces la añadimos al array arrayEvidencesCompoundsAddedModified
				sqlMaxEvidencesEnzymes="select max(id_evidences_enzymes) from evidences_enzymes";
				cur.execute(sqlMaxEvidencesEnzymes)
				row = cur.fetchone()
				idMaxEvidencesEnzymes=row[0]
				arrayEvidencesEnzymesAddedModified.append(idMaxEvidencesEnzymes)
				#sqlInsertEvidenceCompound="insert into evidences_compounds (id_evidences_compounds,id_evidence, id_compound, input_output, id_chebi) values(NULL,%d,%d,%d,%s)"
				#rc = cur.execute( sqlInsertEvidenceCompound,(int(idEvidence),int(idCompound),int(inputOutput),str(idChebi),))
	#En arrayEvidencesEnzymesAddedModified tenemos las enzimas que formaran parte de esa evidencia, El resto no, hay que quitarlas. Para ello tenemos que recorrer todas las enzimas de la evidencia y si no estan en el arrayEvidencesEnzymesAddedModified entonces eliminarlas de la tabla evidences_enzymes ya que esas enzymes se han eliminado de la evidencia
	#print "<br/> el arrayEvidencesEnzymesAddedModified contiene: "+str(arrayEvidencesEnzymesAddedModified)		
	#Creamos un array con los id_evidences_enzymes de las entradas que contienen esta evidencia para compararlos:
	arrayExistentEvidencesEnzymes=[]
	selectEvidencesEnzymes="select id_evidences_enzymes from evidences_enzymes where id_evidence='"+str(idEvidence)+"'"
	cur.execute(selectEvidencesEnzymes)
	while(1):
		row = cur.fetchone()
		if row==None: 
			break
		idEvidencesEnzymes=row[0]
		arrayExistentEvidencesEnzymes.append(idEvidencesEnzymes)
	#print "<br/> el arrayExistentEvidencesEnzymes contiene: "+str(arrayExistentEvidencesEnzymes)		
	for idEvidencesEnzymes in arrayExistentEvidencesEnzymes:
		#print "<br>"+str(idEvidencesEnzymes)
		if idEvidencesEnzymes in arrayEvidencesEnzymesAddedModified:
			#si el idEvidencesEnzymes se encuentra entre los existentes en la base de datos lo dejamos
			pass
		else:
			#si el idEvidencesEnzymes no se encuentra entre los existentes en la base de datos lo eliminamos
			deleteSQL="delete from evidences_enzymes where id_evidences_enzymes="+str(idEvidencesEnzymes)
			#print "<br/>"+str(deleteSQL)
			#sys.exit()
			try:
				rc = cur.execute( deleteSQL)
				DB_CONN.commit()
			except MySQLdb.OperationalError, e:
				print "Location: "+str(redirectionKOcurated)+"error=deleteOldEnzyme&idEvidence="+idEvidence+" \n\n"
				sys.exit()

	updateEvidence="update evidences set curated=1 where id_evidence='"+str(idEvidence)+"'"
	try:
		rc=cur.execute(updateEvidence)
	except:
		print "Location: "+str(redirectionKOcurated)+"idEvidence="+str(idEvidence)+"&error=updateEvidence \n\n"
		sys.exit()
	print "Location: "+str(redirectionOKcurated)+"idEvidence="+str(idEvidence)+" \n\n"		
	sys.exit()		
		
#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
##   HASTA AQUI HEMOS CURADO/ELIMINADO LA EVIDENCIA PERO AHORA DEBERiAMOS CREAR UN STRING QUE CONTENGA LA PAGINA DE CURAR 
##   LA EVIDENCIA PERO YA CON LOS DATOS CURADOS. PARA ELLO PODEMOS HACER UNA LLAMADA AL SYSTEMA QUE EJECUTE EL SCRIPT
##   curateEvidence.php  ????????????????
#####################################################################################################################
#####################################################################################################################
#####################################################################################################################

#tmpString=os.system("/Library/WebServer/Documents/reactions/wp-content/themes/reactions/curateEvidence.php")
#print "Location: http://localhost/reactions/wp-content/themes/reactions/curateEvidence.php?idEvidence="+idEvidence+" \n\n"


if metodo=="delete":
	sys.exit()
	#Tenemos que saber que tipo de evidencia se intenta borrar (organismo,enzima,compuesto).Para ello:
	typeEvidence=fs['type'].value
	sql="delete from evidences_"+str(typeEvidence)+"s where id_evidences_"+str(typeEvidence)+"s="+str(indice)+" id_evidence='"+str(idEvidence)+"'"
	print sql
	try:
		cur.execute(sql)
		row=cur.fetchone()
		strIdEnzymes=row[0]
		arrayIdEnzymes=strIdEnzymes.split(",")
		strIdCompoundsInput=row[1]
		arrayIdCompoundsInput=strIdCompoundsInput.split(",")
		strIdCompoundsOutput=row[2]
		arrayIdCompoundsOutput=strIdCompoundsOutput.split(",")
	except:
		#No se ha podido seleccionar seguramente debido a que no existe en la base de datos.
		pass
	#Ahora recorremos los arrays borrando la evidencia idEvidence del listado de evidencias del que forman parte
	#Quitamos primero las enzimas
	###########           	###########    OJO!!!!! TERMINAR DE IMPLEMENTAR SI ES NECESARIO EL METODO DELETE   ###########           	###########
	
	
		
	sql2="delete * from evidences where id_evidence="+idEvidence
	try:
		cur.execute(sql2)
	except:
		#No se ha podido borrar seguramente debido a que no existe en la base de datos.
		pass
	#Una vez borrada la evidencia de la base de datos, tenemos que eliminar esta evidencia de