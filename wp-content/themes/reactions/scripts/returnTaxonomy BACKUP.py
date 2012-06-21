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
    	tmpString="<table><tr><td>Organism: </td><td><input id=\"textminingOrganismName_"+str(selectNumber)+"\" type=\"text\" NAME=\"textminingOrganismName_"+str(selectNumber)+"\" maxlenght=\"255\" size=\"20\" value=\""+organismName+"\" onChange=\"insertTaxonomy("+str(selectNumber)+");\">(<a href=\"javascript:;\" onClick=\"insertTaxonomy("+str(selectNumber)+");\">Click to search</a>)</td></tr><tr><td><input id=\"deleteOrganism_"+str(selectNumber)+"\" type=\"button\" onclick=\"deleteOrganism('"+str(selectNumber)+"')\" name=\"deleteOrganism_"+str(selectNumber)+"\" value=\"Delete Organism \" class=\"\">\n</td><td></td></tr></table>It has been a problem searching in NCBI Taxonomy. Please try again\n"
    	print tmpString
    	sys.exit()
    #sys.exit()
    return scientificName




organismName=fs['organismName'].value 
selectNumber= fs['selectNumber'].value 
try:  
	listaIdTaxonomy = get_tax_id(organismName)
except:
	tmpString="<table><tr><td>Organism: </td><td><input id=\"textminingOrganismName_"+str(selectNumber)+"\" type=\"text\" NAME=\"textminingOrganismName_"+str(selectNumber)+"\" maxlenght=\"255\" size=\"20\" value=\""+organismName+"\" onChange=\"insertTaxonomy("+str(selectNumber)+");\">"
	#(<a href=\"#\" onClick=\"insertTaxonomy();\">Click to search</a>)</td></tr></table>Nothing found, search again\n"
	tmpString+="(<a href=\"javascript:;\" onClick=\"overlayTaxonomy("+str(selectNumber)+");insertTaxonomy("+str(selectNumber)+");\">Click to search</a>)<div id=\"overlayTaxonomy_"+str(selectNumber)+"\">&nbsp;</div><script>$('#overlayTaxonomy_"+str(selectNumber)+"').unmask();</script></td>"
	print tmpString
	sys.exit()

#print listaIdTaxonomy
tmpString="<table><tr><td>Organism: </td><td><input id=\"textminingOrganismName_"+str(selectNumber)+"\" type=\"text\" NAME=\"textminingOrganismName_"+str(selectNumber)+"\" maxlenght=\"255\" size=\"20\" value=\""+organismName+"\" onChange=\"insertTaxonomy("+str(selectNumber)+");\">\n"
#tmpString+="(<a href=\"#\" onClick=\"insertTaxonomy();\">Click to search</a>)</td>"

tmpString+="(<a href=\"javascript:;\" onClick=\"overlayTaxonomy("+str(selectNumber)+");insertTaxonomy("+str(selectNumber)+");\">Click to search</a>)<div id=\"overlayTaxonomy_"+str(selectNumber)+"\">&nbsp;</div><script>$('#overlayTaxonomy_"+str(selectNumber)+"').unmask();</script></td>"

tmpString+="</tr><tr>"	
tmpString+= "<td>NCBI organism name: </td><td><select  id=\"idOrganismNCBI_"+str(selectNumber)+"\" type=\"text\" name=\"idOrganismNCBI_"+str(selectNumber)+"\">"
tmpString+= ""
contadorOpciones=0
if len(listaIdTaxonomy)==0:
	tmpString="<table><tr><td>Organism: </td><td><input id=\"textminingOrganismName_"+str(selectNumber)+"\" type=\"text\" NAME=\"textminingOrganismName"+str(selectNumber)+"\" maxlenght=\"255\" size=\"20\" value=\""+organismName+"\" onChange=\"insertTaxonomy("+str(selectNumber)+");\">"
	#(<a href=\"#\" onClick=\"insertTaxonomy();\">Click to search</a>)</td></tr></table>Nothing found, search again\n"
	tmpString+="(<a href=\"javascript:;\" onClick=\"overlayTaxonomy("+str(selectNumber)+");insertTaxonomy("+str(selectNumber)+");\">Click to search</a>)<div id=\"overlayTaxonomy_"+str(selectNumber)+"\">&nbsp;</div><script>$('#overlayTaxonomy_"+str(selectNumber)+"').unmask();</script></td></tr><tr><td><input id=\"deleteOrganism_"+str(selectNumber)+"\" type=\"button\" onclick=\"deleteOrganism('"+str(selectNumber)+"')\" name=\"deleteOrganism_"+str(selectNumber)+"\" value=\"Delete Organism \" class=\"\">\n</td><td></td></tr></table>Nothing found, search again\n"
	print tmpString
	sys.exit()
	
for taxid in listaIdTaxonomy:
	#Tomamos la informacion del taxid para generar la salida de OPTIONS del SELECT
	#print "<br/>"+taxid+"<br/>"
	scientificName=get_scientifc_name_from_tax_id(taxid,selectNumber)
	tmpString+="<option value=\""+str(scientificName)+"\" name=\"textminingOrganismOption_"+str(contadorOpciones)+"\" >"+str(scientificName)+"\n"
	contadorOpciones=contadorOpciones+1
tmpString+="</select></td>"
tmpString+="</tr><tr><td>Strain: </td><td><SELECT name=\"strain_"+str(selectNumber)+"\" id=\"strain_"+str(selectNumber)+"\" ><OPTION value=\"\" SELECTED>Select<OPTION value=\"+\">+<OPTION value=\"-\">-</SELECT></td></tr><tr><td><input id=\"deleteOrganism_"+str(selectNumber)+"\" type=\"button\" onclick=\"deleteOrganism('"+str(selectNumber)+"')\" name=\"deleteOrganism_"+str(selectNumber)+"\" value=\"Delete Organism \" class=\"\">\n</td><td></td></tr></table>"

print tmpString+"\n"
#data = get_tax_data(taxid)
#lineage = {d['Rank']:d['ScientificName'] for d in
#    data[0]['LineageEx'] if d['Rank'] in ['family', 'order']}
#print "<br/>"+str(lineage)+"<br>"