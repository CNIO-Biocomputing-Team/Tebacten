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


#SUDS for SOAP web services stuff
import logging
from suds.client import Client
logging.basicConfig(level=logging.INFO)
#logging.getLogger('suds.client').setLevel(logging.DEBUG) #Enabling specific module logging mode of suds to debug


#Creamos  un cliente para el servicio a partir del WSDL


print "Content-Type: text/html\n"

url = 'http://www.ebi.ac.uk/webservices/chebi/2.0/webservice?wsdl'
client = Client(url)
tmpString= ""
#Script que recibe un textMiningOrganismName y devuelve una serie de opciones con el siguiente formato
"""
<OPTION VALUE=\"idChebi_1\">Organismo 1
<OPTION VALUE=\"idChebi_2\">Organismo 2
<OPTION VALUE=\"idChebi_3\">Organismo 3
<OPTION VALUE=\"idChebi_4\">Organismo 4
<OPTION VALUE=\"idChebi_5\">Organismo 5
"""

fs = cgi.FieldStorage()
compoundName=fs['compoundName'].value
identifier=fs['id'].value
#compoundName="1,4-dihydroxynaphthalene"



#############################################################################
entities=client.service.getLiteEntity(compoundName,'chebiId',200,'ALL')
#############################################################################
#print entities devuelve algo como esto:
#(LiteEntityList){ ListElement[] = (LiteEntity){ chebiId = "CHEBI:16205" chebiAsciiName = "biphenyl-2,3-diol" searchScore = 0.12 entityStar = 3 }, (LiteEntity){ chebiId = "CHEBI:28978" chebiAsciiName = "(2E,4Z)-6-(4-chlorophenyl)-2-hydroxy-6-oxohexa-2,4-dienoic acid" searchScore = 0.12 entityStar = 3 }, } 
#Colocamos una linea con todos los identificadores de Chebi(de momento) como un outlink a la web:
tmpString+="<div class=\"linksToCompounds\">"
try:
	listElements=entities[0]
except:
	#No devuelve ningún resultado
	tmpString+="<select id=\"listOfChebiIds_"+str(identifier)+"\" name=\"listOfChebiIds_"+str(identifier)+"\"></select>"
	tmpString+="</div>"
	print tmpString
	sys.exit()
for liteEntity in listElements:
	chebiId=liteEntity["chebiId"]
	tmpString+="<a href='http://www.ebi.ac.uk/chebi/advancedSearchFT.do?searchString="+str(chebiId)+"' target=\"_blank\">"+str(chebiId)+"</a>"+", "
#En result tenemos un listado con estructuras que contienen los chebiIds que coinciden para esa enzima. 
tmpString+="</div>"

tmpString+="<select id=\"listOfChebiIds_"+str(identifier)+"\" name=\"listOfChebiIds_"+str(identifier)+"\">"
tmpString+="<OPTION VALUE=\"\">Select\n"
for liteEntity in listElements:
	chebiId=liteEntity["chebiId"]
	#Tenemos el chebiId y podemos llamar al servicio getCompleteEntity(str(chebiId)). Para ello hacemos:
	completeEntity=client.service.getCompleteEntity(str(chebiId))
	#print "<br/>"+str(chebiId)+"<br/>"
	nombreCompuesto=str(completeEntity[1])
	tmpString+="<OPTION VALUE=\""+str(chebiId)+"\">"+str(nombreCompuesto)+"&nbsp;("+str(chebiId)+")\n"
tmpString+="</select>"

#client.service.getCompleteEntity('16205');

print tmpString