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


def get_title(pubmedId):
    """once we have the taxied, we can fetch the record"""
    search = Entrez.efetch(id = pubmedId, db = "pubmed", retmode = "xml")
    record=Entrez.read(search)
    articleTitle=record[0]['MedlineCitation']['Article']['ArticleTitle']
    print articleTitle
    return articleTitle
    sys.exit()   
pubmedId=sys.argv[1]
get_title(pubmedId)
sys.exit()