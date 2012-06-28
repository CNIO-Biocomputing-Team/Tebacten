#!/usr/bin/python
# -*- coding: utf-8 -*-
import sys
import os
import re
import MySQLdb
import cgi
import datetime
import cgitb; cgitb.enable();
sys.stderr = sys.stdout
from Bio import Entrez
import Constants
DB_READ_USER, DB_READ_PWD, DB_WRITE_USER, DB_WRITE_PWD, DB_JABBA_DB, HOME_URL, DB_HOST  = Constants.initConfig()
charmap = {
    "\"":"\\\"",
    # ...
}

DB_CONN = MySQLdb.connect(host= DB_HOST, port=3306, user = DB_READ_USER, passwd= DB_READ_PWD, db= DB_JABBA_DB, charset="utf8", init_command="set names utf8")

cur = DB_CONN.cursor()	

fs = cgi.FieldStorage()
ret_number=100
tmpString=""

print "Content-Type: text/html\n"


Entrez.email = "acanada@cnio.es"
if not Entrez.email:
    print "you must add your email address"
    sys.exit(2)

def get_tax_id(species):
    """
    	to get data from ncbi taxomomy, we need to have the taxid. we can
		get that by passing the species name to esearch, which will return
		the tax id
	"""
    species = species.replace(" ", "+").strip()
    search = Entrez.esearch(term = species, db = "taxonomy", retmode = "xml",RetMax=ret_number)
    record = Entrez.read(search)
    listaIdTaxonomy=record['IdList'] #En listaIdTaxonomy tenemos el listado de Taxonomy de la busqueda.
    #print record[]
    return listaIdTaxonomy

def get_scientifc_name_from_tax_id(taxid,selectNumber):
    """once we have the taxid, we can fetch the record"""
    search = Entrez.efetch(id = taxid, db = "taxonomy", retmode = "xml")
    record=Entrez.read(search)
    try:
    	scientificName=record[0]["ScientificName"]
    except:
    	pass
    	sys.exit()
    #sys.exit()
    return scientificName


organismName=fs['organismName'].value 
selectNumber= fs['selectNumber'].value

try:
	idEvidencesOrganisms=fs['idEvidencesOrganisms'].value
	existeIdEvidencesOrganisms=True
except:
	existeIdEvidencesOrganisms=False

if existeIdEvidencesOrganisms==True:
	#podemos poner un valor para el strain
	selectExisteEvidencesOrganisms="select strain from evidences_organisms where id_evidences_organisms='"+str(idEvidencesOrganisms)+"'"
	rc = cur.execute(selectExisteEvidencesOrganisms)
	row = cur.fetchone()
	strain=row[0]

tmpString+= "<table><tr><td>NCBI organism name: </td><td><select  id=\"idOrganismNCBI_"+str(selectNumber)+"\" type=\"text\" name=\"idOrganismNCBI_"+str(selectNumber)+"\">"
contadorOpciones=0
listaIdTaxonomy=get_tax_id(organismName)
	
for taxid in listaIdTaxonomy:
	#Tomamos la informacion del taxid para generar la salida de OPTIONS del SELECT
	#print "<br/>"+taxid+"<br/>"
	scientificName=get_scientifc_name_from_tax_id(taxid,selectNumber)
	tmpString+="<option value=\""+str(taxid)+"\" name=\"textminingOrganismOption_"+str(contadorOpciones)+"\" >"+str(scientificName)+"\n"
	contadorOpciones=contadorOpciones+1
tmpString+="</select></td>"
tmpString+="</tr><tr><td>Strain: </td><td><SELECT name=\"strain_"+str(selectNumber)+"\" id=\"strain_"+str(selectNumber)+"\" >";

if strain=="":
	tmpString+="<OPTION value=\"\" SELECTED>Select<OPTION value=\"+\">+<OPTION value=\"-\">-</SELECT></td></tr></table>"
elif strain=="+":
	tmpString+="<OPTION value=\"\">Select<OPTION value=\"+\" SELECTED>+<OPTION value=\"-\">-</SELECT></td></tr></table>"
elif strain=="-":
	tmpString+="<OPTION value=\"\">Select<OPTION value=\"+\">+<OPTION value=\"-\" SELECTED>-</SELECT></td></tr></table>"
	

print tmpString+"\n"
#data = get_tax_data(taxid)
#lineage = {d['Rank']:d['ScientificName'] for d in
#    data[0]['LineageEx'] if d['Rank'] in ['family', 'order']}
#print "<br/>"+str(lineage)+"<br>"