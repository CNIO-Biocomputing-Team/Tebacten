#!/usr/bin/python
# -*- coding: utf-8 -*-
import sys
import os
import re
import MySQLdb
import cgi
import Constants
import cgitb; cgitb.enable();
import datetime
from datetime import timedelta
from types import ListType
sys.stderr = sys.stdout

DB_READ_USER, DB_READ_PWD, DB_WRITE_USER, DB_WRITE_PWD, DB_LOCAL_DB = Constants.initConfig()
charmap = {
    "\"":"\\\"",
    # ...
}

NUM_MAX_PROJECTS=15

def commonOutput():
    print "Content-type: text/html\n\n"
    print """
    <html>
    <head>
    <title>INB</title>
    </head>
    <body>
    <center>
    Sorry, we're updating some Website functionalities...<br>
    Please, come back later.<br>

    """

def devolverLaborables(diaInicio,diaFin):
	#Funcion que calcula el numero de dias laborables que tiene un rango de fechas.
	contadorLaborables=0
	diaStart=(diaInicio.rsplit("-"))[0]
	mesStart=(diaInicio.rsplit("-"))[1]
	anyoStart=(diaInicio.rsplit("-"))[2]
	
	diaEnd=(diaFin.rsplit("-"))[0]
	mesEnd=(diaFin.rsplit("-"))[1]
	anyoEnd=(diaFin.rsplit("-"))[2]
	
	start=datetime.datetime(int(anyoStart),int(mesStart),int(diaStart),int(0),int(0),int(0),0)
	end=datetime.datetime(int(anyoEnd),int(mesEnd),int(diaEnd),int(0),int(0),int(0),0)
	
	daysDifference=(end-start).days
	
	num_day=start.isoweekday()
	while (daysDifference!=0):
		if ((num_day!=6)and(num_day!=7)):  #Es decir, si no es ni sabado ni domingo
			contadorLaborables=contadorLaborables+1
			daysDifference=daysDifference-1
			if (num_day==7):
				num_day=0
			else:
				num_day=num_day+1
		else:
			daysDifference=daysDifference-1
			if (num_day==7):
				num_day=0
			else:
				num_day=num_day+1
	
	return (contadorLaborables+1)

    
fs = cgi.FieldStorage()
page_idOK="16"
page_idKO="18"
redirectionOKgestionTiempo="http://www.inab.org/?action=showOK"
redirectionOKgestionTiempo="http://www.inab.org/private-area/personnel/time-tracking/?action=showOK"
redirectionKOgestionTiempo="http://www.inab.org/private-area/personnel/time-tracking/?action=showKO"


try:
    replicateWeek=fs['replicateWeek'].value
    replicateWeek=1#True
except:
    replicateWeek=0#False

#commonOutput()
    
if(fs['action'].value == "replicateReport"):
    	
    #Recogemos todos los datos
    ############RECOGIDA DE DATOS GENERALES#############
    try:
        idPersonal= fs['idPersonal'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=noIdPersonal \n\n"
        sys.exit()
    try:
        idNodo= fs['idNodo'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=noIdNodo \n\n"
        sys.exit()
    try:
        startDate= fs['startDate'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=noStartDate \n\n"
        sys.exit()
    try:
        endDate= fs['endDate'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=noEndDate \n\n"
        sys.exit()
    try:
        onHolidays= fs['daysNotWorked'].value
    except:
        onHolidays=0
    
    laborables=devolverLaborables(startDate,endDate)
    totalReportedHours=(int(laborables)-int(onHolidays))*8
    #print "diaslaborables "+str(laborables)+"<br/>"
    #print "<br/>días de vacaciones****"+str(onHolidays)+"<br/>"
    #print "totalReportedHours "+str(totalReportedHours)+"<br/>"
    try:
        totalHoursProjects= fs['totalHoursProjectsReplicating'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=noTotalHoursProjects \n\n"
        sys.exit()
    #print "<br/>totalHoursProjects****"+str(totalHoursProjects)
    #print "laborables: "+str(laborables)+" onHolidays: "+str(onHolidays)+" totalReportedHours: "+str(totalReportedHours)
    #totalHoursProjects ahora es un porcentaje. Hay que pasarlo a horas teniendo en cuenta el totalHoursProjects
    totalHoursProjects=(float(totalHoursProjects)*totalReportedHours)/100.0
    
    #print "totalReportedHours "+str(totalReportedHours)+"<br/>"
    #print "totalHoursProjects "+ str(totalReportedHours)+"<br/>"
    
    ############RECOGIDA DE DATOS DE TIEMPO DE DEDICACIÓN A PROYECTOS INB#############
    arrayProjectsReported={}
    proyectosAnadidos=[]
    for i in range(NUM_MAX_PROJECTS):
        try:
            especify=fs['especifyReplicating'+str(i+1)].value
        except:
            especify="off"
        try:
            idProyecto=fs['projectReportedReplicating'+str(i+1)].value
        except:
            if especify=="on":
                idProyecto="0"
            else:
                idProyecto="-1"
        if especify=="off":#Es decir, ha seleccionado un proyecto de la lista de proyectos. Recogemos los valores asociados.
            if(idProyecto in proyectosAnadidos):
                pass
            elif(idProyecto!="-1"):
                diccionarioDatosProyectos={}
                #print "diccionarioDatosProyectos: "+str(diccionarioDatosProyectos)+"<br>"
                #idProyecto=fs['projectReported'+str(i+1)].value
                diccionarioDatosProyectos["idProyecto"]=idProyecto
                proyectosAnadidos.append(idProyecto)
                #print "proyectosAnadidos contiene: "+str(proyectosAnadidos)+"<br>"
                #Guardamos sus horas asociadas pero primero pasamos el valor que tenemos, en porcentaje, a numero de horas
                porcentajeHoras=fs['invertedTimeProjectsReplicating'+str(i+1)].value
                horas=(float(totalReportedHours)*float(porcentajeHoras))/100.0
                diccionarioDatosProyectos["horas"]=horas
                #Guardamos en el campo "otros" una cadena vacia
                diccionarioDatosProyectos["otros"]=""
                arrayProjectsReported[i]=diccionarioDatosProyectos
            
        elif((especify=="on")and(idProyecto!="-1")):
            diccionarioDatosProyectos={}
            #Guardamos el idProyecto con valor de 0 ya que no se ha seleccionado ninguno de la lista
            diccionarioDatosProyectos["idProyecto"]=0
            #Guardamos sus horas asociadas pero primero pasamos el valor que tenemos, en porcentaje, a numero de horas
            porcentajeHoras=fs['invertedTimeProjectsReplicating'+str(i+1)].value
            horas=(float(totalHoursProjects)*float(porcentajeHoras))/100.0
            diccionarioDatosProyectos["horas"]=horas
            #Guardamos en el campo otros una cadena vacia
            diccionarioDatosProyectos["otros"]=fs['otherProjectTaskReplicating'+str(i+1)].value
            arrayProjectsReported[i]=diccionarioDatosProyectos

    #  print "<br>"+str(arrayProjectsReported)
    #En arrayProjectsReported tenemos un diccionario con claves 1,2,3,4.... y values son diccionarios con los campos 'idProyecto','horas','otros'
    
    #############RECOGIDA DE DATOS DE INB SOFTWARE/SYSTEMS#############
    try:
        usageTotalSoftware= fs['usageTotalSoftwareReplicating'].value
    except:
        usageTotalSoftware=0
    try:
        usageBabelomicsGepas= fs['usageBabelomicsGepasReplicating'].value
    except:
        usageBabelomicsGepas=0
    try:
        usageBiocreativeMetaserver= fs['usageBiocreativeMetaserverReplicating'].value
    except:
        usageBiocreativeMetaserver=0
    try:
        usageBlast2Go= fs['usageBlast2GoReplicating'].value
    except:
        usageBlast2Go=0
    try:
        usageCargo= fs['usageCargoReplicating'].value
    except:
        usageCargo=0
    try:
        usageDnalive= fs['usageDnaliveReplicating'].value
    except:
        usageDnalive=0
    try:
        usageFatiGo= fs['usageFatiGoReplicating'].value
    except:
        usageFatiGo=0
    try:
        usageForensicator= fs['usageForensicatorReplicating'].value
    except:
        usageForensicator=0
    try:
        usageFunCut= fs['usageFunCutReplicating'].value
    except:
        usageFunCut=0
    try:
        usageGeneId= fs['usageGeneIdReplicating'].value
    except:
        usageGeneId=0
    try:
        usageIntogen= fs['usageIntogenReplicating'].value
    except:
        usageIntogen=0
    try:
        usageIwwem= fs['usageIwwemReplicating'].value
    except:
        usageIwwem=0
    try:
        usageJorca= fs['usageJorcaReplicating'].value
    except:
        usageJorca=0
    try:
        usageKaryotypeViewer= fs['usageKaryotypeViewerReplicating'].value
    except:
        usageKaryotypeViewer=0
    try:
        usageMethylizer= fs['usageMethylizerReplicating'].value
    except:
        usageMethylizer=0
    try:
        usageMobyMiner= fs['usageMobyMinerReplicating'].value
    except:
        usageMobyMiner=0
    try:
        usageModel= fs['usageModelReplicating'].value
    except:
        usageModel=0
    try:
        usageMowServ= fs['usageMowServReplicating'].value
    except:
        usageMowServ=0
    try:
        usageNemusTools= fs['usageNemusToolsReplicating'].value
    except:
        usageNemusTools=0
    try:
        usagePmut= fs['usagePmutReplicating'].value
    except:
        usagePmut=0
    try:
        usagePupaSuite= fs['usagePupaSuiteReplicating'].value
    except:
        usagePupaSuite=0
    try:
        usageSide= fs['usageSideReplicating'].value
    except:
        usageSide=0
    try:
        usageSysnp= fs['usageSysnpReplicating'].value
    except:
        usageSysnp=0
    try:
        usageVariantSplicing= fs['usageVariantSplicingReplicating'].value
    except:
        usageVariantSplicing=0
    try:
        usageVisualGenomics= fs['usageVisualGenomicsReplicating'].value
    except:
        usageVisualGenomics=0
    try:
        usageExternalWebApplication= fs['usageExternalWebApplicationReplicating'].value
    except:
        usageExternalWebApplication=0
    try:
        usageOther= fs['usageOtherReplicating'].value
    except:
        usageOther=0
    try:
        usageOtherApplications= fs['usageOtherApplicationsReplicating'].value
    except:
        usageOtherApplications=""
    
    #############RECOGIDA DE DATOS DEL TIEMPO DEDICADO A TAREAS#############
    #Todo lo que recogemos en principio son porcentajes y por eso lo pasamos todo a horas.
    
    try:
        percentageTotalHoursTasks= fs['totalHoursTasksReplicating'].value
        totalHoursTasks=(float(percentageTotalHoursTasks)*float(totalReportedHours))/100
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=noTotalHoursTasks \n\n"
        sys.exit()
    try:
        percentageTimeSystemsAdministration= fs['timeSystemsAdministrationReplicating'].value
        timeSystemsAdministration=(float(percentageTimeSystemsAdministration)*float(totalReportedHours))/100.0
    except:
        timeSystemsAdministration=0
    try:
        percentageTimeDataAnalysisMicroarray= fs['timeDataAnalysisMicroarrayReplicating'].value
        timeDataAnalysisMicroarray=(float(percentageTimeDataAnalysisMicroarray)*float(totalReportedHours))/100.0
    except:
        timeDataAnalysisMicroarray=0
    try:
        percentageTimeDataFunctionalAnnotation= fs['timeDataFunctionalAnnotationReplicating'].value
        timeDataFunctionalAnnotation=(float(percentageTimeDataFunctionalAnnotation)*float(totalReportedHours))/100.0
    except:
        timeDataFunctionalAnnotation=0
    try:
        percentageTimeDataSimulations= fs['timeDataSimulationsReplicating'].value
        timeDataSimulations=(float(percentageTimeDataSimulations)*float(totalReportedHours))/100.0
    except:
        timeDataSimulations=0
    try:
        percentageTimeDataNextGenSequencing= fs['timeDataNextGenSequencingReplicating'].value
        timeDataNextGenSequencing=(float(percentageTimeDataNextGenSequencing)*float(totalReportedHours))/100.0
    except:
        timeDataNextGenSequencing=0
    try:
        percentageTimeGenomeComparison= fs['timeGenomeComparisonReplicating'].value
        timeGenomeComparison=(float(percentageTimeGenomeComparison)*float(totalReportedHours))/100.0
    except:
    
        timeGenomeComparison=0
    try:
        percentageTimeGenomeProteomeAnnotation= fs['timeGenomeProteomeAnnotationReplicating'].value
        timeGenomeProteomeAnnotation=(float(percentageTimeGenomeProteomeAnnotation)*float(totalReportedHours))/100.0
    except:
        timeGenomeProteomeAnnotation=0
    try:
        percentageTimeOtherData= fs['timeOtherDataReplicating'].value
        timeOtherData=(float(percentageTimeOtherData)*float(totalReportedHours))/100.0
    except:
        timeOtherData=0
    try:
        percentageTimeTrainingGiven= fs['timeTrainingGivenReplicating'].value
        timeTrainingGiven=(float(percentageTimeTrainingGiven)*float(totalReportedHours))/100.0
    except:
        timeTrainingGiven=0
    try:
        percentageTimeTrainingAttended= fs['timeTrainingAttendedReplicating'].value
        timeTrainingAttended=(float(percentageTimeTrainingAttended)*float(totalReportedHours))/100.0
    except:
        timeTrainingAttended=0
    try:
        percentageTimeDevelopmentWebTools= fs['timeDevelopmentWebToolsReplicating'].value
        timeDevelopmentWebTools=(float(percentageTimeDevelopmentWebTools)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentWebTools=0
    try:
        percentageTimeDevelopmentApis= fs['timeDevelopmentApisReplicating'].value
        timeDevelopmentApis=(float(percentageTimeDevelopmentApis)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentApis=0
    try:
        percentageTimeDevelopmentWebServices= fs['timeDevelopmentWebServicesReplicating'].value
        timeDevelopmentWebServices=(float(percentageTimeDevelopmentWebServices)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentWebServices=0
    try:
        percentageTimeDevelopmentWorkflows= fs['timeDevelopmentWorkflowsReplicating'].value
        timeDevelopmentWorkflows=(float(percentageTimeDevelopmentWorkflows)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentWorkflows=0
    try:
        percentageTimeDevelopmentDatabases= fs['timeDevelopmentDatabasesReplicating'].value
        timeDevelopmentDatabases=(float(percentageTimeDevelopmentDatabases)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentDatabases=0
    try:
        percentageTimeDevelopmentManteinance= fs['timeDevelopmentManteinanceReplicating'].value
        timeDevelopmentManteinance=(float(percentageTimeDevelopmentManteinance)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentManteinance=0
    try:
        percentageTimeTechnicalSupport= fs['timeTechnicalSupportReplicating'].value
        timeTechnicalSupport=(float(percentageTimeTechnicalSupport)*float(totalReportedHours))/100.0
    except:
        timeTechnicalSupport=0
    try:
        percentageTimeDevelopmentDatabases= fs['timeDevelopmentDatabasesReplicating'].value
        timeDevelopmentDatabases=(float(percentageTimeDevelopmentDatabases)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentDatabases=0
    try:
        percentageTimeDevelopmentDataResources= fs['timeDevelopmentDataResourcesReplicating'].value
        timeDevelopmentDataResources=(float(percentageTimeDevelopmentDataResources)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentDataResources=0
    try:
        percentageTimeDocumentation= fs['timeDocumentationReplicating'].value
        timeDocumentation=(float(percentageTimeDocumentation)*float(totalReportedHours))/100.0
    except:
        timeDocumentation=0
    try:
        percentageTimeOtherTasks= fs['timeOtherTasksReplicating'].value
        timeOtherTasks=(float(percentageTimeOtherTasks)*float(totalReportedHours))/100.0
    except:
        timeOtherTasks=0
    try:
        otherTasksEspecify= fs['otherTasksEspecify'].value
    except:
        otherTasksEspecify=""
    ######Ya tenemos todos los datos, vamos a guardarlos en la base de datos#####

    #print "***"+str(idNodo)+"****"
    #print "<br>idPersonal***"+str(idPersonal)+"****"
    #print "<br>startDate***"+str(startDate)+"****"
    #print "<br>endDate***"+str(endDate)+"****"
    #print "<br>totalReportedHours***"+str(totalReportedHours)+"****"
    #print "<br>onHolidays***"+str(onHolidays)+"****"
    #print "<br>totalHoursProjects***"+str(totalHoursProjects)+"****"
    #print "<br>usageTotalSoftware***"+str(usageTotalSoftware)+"****"
    #print "<br>totalHoursTasks***"+str(totalHoursTasks)+"****"
   
    #sys.exit()
    
     
    
    #Ya tenemos los datos. 
    #Tenemos que controlar que el numero total de horas reportadas para software no es mayor que el numero de horas totales (el resto no puede serlo ya que provienen de porcentajes)
    usageTotalSoftware=float(usageTotalSoftware)
    round(usageTotalSoftware,3)
    if (int(usageTotalSoftware)>int(totalReportedHours)):
    	print int(totalReportedHours) 
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=tooManySoftwareHours \n\n"
        sys.exit()
    #Tenemos que comprobar que en la base de datos no existe ninguna entrada con esos startDate y endDate o comprendidas entre ellos
    #Recuperamos todas las entradas que hay de ese usuario en la tabla gestionTiempo2 para ver si existe algun registro que se solape con el que queremos insertar nuevo
    DB_CONN = MySQLdb.connect(host= DB_LOCAL_DB, port=3306, user = DB_READ_USER, passwd= DB_READ_PWD, db= Constants.DB_PERSONAL, charset="utf8", init_command="set names utf8")
    cur = DB_CONN.cursor()
    sql="select * from gestionTiempo2 where idPersonal=\""+str(idPersonal)+"\""
    cur.execute(sql)
    while(1):
        row = cur.fetchone()
        if row==None: 
            break
        #en row tenemos una entrada para ese usuario. Comprobar fechas
        fechaInicioGuardada=row[2]
        fechaFinGuardada=row[3]
        #print "<br>fechaInicioGuardada="+str(fechaInicioGuardada)+"  fechaFinGuardada="+str(fechaFinGuardada)+"  startDate="+str(startDate)+"  endDate="+str(endDate)+"<br>"
        startDateFormated=datetime.date(int(startDate[6:]),int(startDate[3:5]),int(startDate[0:2]))
        endDateFormated=datetime.date(int(endDate[6:]),int(endDate[3:5]),int(endDate[0:2]))
        fechaInicioGuardadaFormated=datetime.date(int(fechaInicioGuardada[6:]),int(fechaInicioGuardada[3:5]),int(fechaInicioGuardada[0:2]))
        fechaFinGuardadaFormated=datetime.date(int(fechaFinGuardada[6:]),int(fechaFinGuardada[3:5]),int(fechaFinGuardada[0:2]))
        #print "<br>fechaInicioGuardadaFormated="+str(fechaInicioGuardadaFormated)+"  fechaFinGuardadaFormated="+str(fechaFinGuardadaFormated)+"  startDateFormated="+str(startDateFormated)+"  endDateFormated="+str(endDateFormated)+"<br>"
        #primero comprobamos que startDate sea menor que endDate
        if (startDateFormated>=endDateFormated):
            print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=startGreaterEnd \n\n"
            sys.exit()
        if (startDateFormated>=fechaInicioGuardadaFormated)and(startDateFormated<=fechaFinGuardadaFormated):
            print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=dateExists \n\n"
            sys.exit()
        elif (endDateFormated>=fechaInicioGuardadaFormated)and(endDateFormated<=fechaFinGuardadaFormated):
            print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=dateExists \n\n"
            sys.exit()
    
    ################################################################################################################################################# 
    ############OJO, LAS DOS LINEAS SIGUIENTES FUERZAN A QUE SOLO SE PUEDAN INSERTAR REGISTROS QUE INICIEN UN LUNES Y FINALICEN UN VIERNES###########
    #################################################################################################################################################
    #diaSemanaStartDate=startDateFormated.isoweekday() #Esta variable debe valer 1!!!!
    #diaSemanaEndDate=endDateFormated.isoweekday() #Esta variable debe valer 5!!!!
    #if diaSemanaStartDate!=1:
    #    print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=startDateNoMonday \n\n"
    #    sys.exit()
    #if diaSemanaEndDate!=5:
    #    print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=endDateNoFriday \n\n"
    #    sys.exit()
    ########################################################INSERTAMOS DATOS GENERALES EN LA TABLA GESTIONTIEMPO#####################################
    #Si llegamos aqui es que no tenemos registros para ese usuario durante ese periodo, asi insertamos en la base de datos personal, en la tabla gestionTiempo2, un registro con los campos.
    #Pasamos los datos a unicode para enviarlos a la base de datos
    
    startDate = unicode( startDate, "utf-8" )
    endDate= unicode( endDate, "utf-8" )

    #Conectamos a la base de datos
    DB_CONN = MySQLdb.connect(host= DB_LOCAL_DB, port=3306, user = DB_READ_USER, passwd= DB_READ_PWD, db= Constants.DB_PERSONAL, charset="utf8", init_command="set names utf8")
    cur = DB_CONN.cursor()
    #Modificamos el formato de la fecha startEnd y endDate que llega asi: 25-01-11  y pasarla asi: 25-01-2011
    arrayTmp=startDate.split("-")
    startDate=arrayTmp[0]+"-"+arrayTmp[1]+"-20"+arrayTmp[2]
    arrayTmp=endDate.split("-")
    endDate=arrayTmp[0]+"-"+arrayTmp[1]+"-20"+arrayTmp[2]
    #Creamos la sentencia SQL
    #sql="insert into gestionTiempo2 (idGestionTiempo,idPersonal,startDate,endDate,onHolidays,totalHoursReported,timeProjects,timeSoftware,timeTasks)values(NULL,%s,%s,%s,%s,%s,%s,%s,%s)"
    sql="insert into gestionTiempo2 (idGestionTiempo,idPersonal,startDate,endDate,onHolidays,totalHoursReported,timeProjects,timeSoftware,timeTasks)values(NULL,'"+str(idPersonal)+"','"+str(startDate)+"','"+str(endDate)+"','"+str(onHolidays)+"','"+str(int(totalReportedHours))+"','"+str(int(totalHoursProjects))+"','"+str(int(usageTotalSoftware))+"','"+str(int(totalHoursTasks))+"')"
    
    try:
        #rc = cur.execute( sql,(idPersonal,startDate,endDate,onHolidays,totalReportedHours,totalHoursProjects,usageTotalSoftware,totalHoursTasks,))
        rc = cur.execute( sql)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la insercion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=notAddedToGestionTiempo \n\n"
        sys.exit()
    
    
    #Ahora necesitamos recoger el idGestionTiempo de la anterior transaccion para utilizarlo en las demas tablas (tiempoEnProyectos2,tiempoEnSoftware2,tiempoEnTareas2)
    sql2="select max(idGestionTiempo) from gestionTiempo2 WHERE idPersonal= "+str(idPersonal)
    #print sql2
    try:
        cur.execute(sql2)
        row2 = cur.fetchone()
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la seleccion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=badSelectSentence \n\n"
        DB_CONN.close()
        sys.exit()
    #En row2[0]tenemos el idGestionTiempo que acabamos de insertar y que utilizaremos de ahora en adelante para las siguientes inserciones.
    idGestionTiempo=row2[0]
    #print str(idGestionTiempo)+"<br>"
    ########################################################INSERTAMOS HORAS ASOCIADAS A PROYECTOS##########################################
    #La informacion la tenemos en el arrayProjectsReported:
    #print arrayProjectsReported
    for indice in arrayProjectsReported:
        diccionario = arrayProjectsReported[indice]
        #print str(diccionario)+"<br>"
        #recogemos los datos
        #ya tenemos el idGestionTiempo para relacionarlo con la tabla gestionTiempo2
        idProyecto=diccionario["idProyecto"]
        hours=diccionario["horas"]
        otherProjectTask=diccionario["otros"]
        #Pasamos otherProjectTask a unicode para enviarlos a la base de datos
        otherProjectTask = unicode( otherProjectTask, "utf-8" )
        #Creamos la sentencia sql
        sql3="insert into tiempoEnProyectos2 (idTiempoProyecto,idGestionTiempo,idProyecto,otherProjectTask,hours)values(NULL,%s,%s,%s,%s)"
        #print str(sql3)+"<br>"
        try:
            rc3 = cur.execute( sql3,(int(idGestionTiempo),int(idProyecto),otherProjectTask,hours,))
            #rc3 = cur.execute( sql3)
        except MySQLdb.OperationalError, e:
            #redireccionar a pagina de error en la insercion y sys.exit() de este script
            print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=notAddedToTiempoEnProyectos&errorCode="+str(idGestionTiempo)+" \n\n"
            sys.exit()
    
    #print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason="+"llega"+" \n\n"
    #sys.exit()
    ########################################################INSERTAMOS HORAS ASOCIADAS A SOFTWARE##########################################
    #Tenemos los datos recuperados mas arriba. Pasamos a unicode utf-8 el campo  otherApplicationEspecified (viene como usageOtherApplications)
    usageOtherApplications = unicode( usageOtherApplications, "utf-8" )
    #Creamos la sentencia de insercion sql:
    
    
    sql4="insert into tiempoEnSoftware2 (idTiempoSoftware,idGestionTiempo,babelomicsGepas,biocreativeMetaserver,blast2go,cargo,dnalive,fatigo,forensicator,funcut,geneId,intoGen,iwwem,jorca,karyotypeViewer,methylizer,mobyMiner,model,mowserv,nemusTools,pmut,pupaSuite,side,sysnp,variantSplicing,visualGenomics,externalWebApplication,otherApplications,otherApplicationEspecified)values(NULL,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    #print str(sql4)+"<br>"
    
    try:
        rc4 = cur.execute( sql4,(int(idGestionTiempo),round(float(usageBabelomicsGepas),2),round(float(usageBiocreativeMetaserver),2),round(float(usageBlast2Go),2),round(float(usageCargo),2),round(float(usageDnalive),2),round(float(usageFatiGo),2),round(float(usageForensicator),2),round(float(usageFunCut),2),round(float(usageGeneId),2),round(float(usageIntogen),2),round(float(usageIwwem),2),round(float(usageJorca),2),round(float(usageKaryotypeViewer),2),round(float(usageMethylizer),2),round(float(usageMobyMiner),2),round(float(usageModel),2),round(float(usageMowServ),2),round(float(usageNemusTools),2),round(float(usagePmut),2),round(float(usagePupaSuite),2),round(float(usageSide),2),round(float(usageSysnp),2),round(float(usageVariantSplicing),2),round(float(usageVisualGenomics),2),round(float(usageExternalWebApplication),2),round(float(usageOther),2),usageOtherApplications,))
        #rc4 = cur.execute( sql4)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la insercion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=notAddedToTiempoEnProyectos&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
        
    ########################################################INSERTAMOS HORAS ASOCIADAS A TASKS##########################################
    #Tenemos los datos recuperados mas arriba. Pasamos a unicode utf-8 el campo  otherTasksEspecify
    otherTasksEspecify = unicode( otherTasksEspecify, "utf-8" )
    #Creamos la sentencia de insercion sql:
    sql5="insert into tiempoEnTareas2 (idTiempoTarea,idGestionTiempo,timeSystemsAdministration,timeDataAnalysisMicroarray,timeDataFunctionalAnnotation,timeDataSimulations,timeDataNextGenSequencing,timeGenomeComparison,timeGenomeProteomeAnnotation,timeOtherData,timeTrainingGiven,timeTrainingAttended,timeDevelopmentWebTools,timeDevelopmentApis,timeDevelopmentWebServices,timeDevelopmentWorkflows,timeDevelopmentDatabases,timeDevelopmentManteinance,timeTechnicalSupport,timeDocumentation,timeOtherTasks,otherTasksEspecify,timeDevelopmentDataResources)values(NULL,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    try:
        rc5 = cur.execute( sql5,(int(idGestionTiempo),timeSystemsAdministration,timeDataAnalysisMicroarray,timeDataFunctionalAnnotation,timeDataSimulations,timeDataNextGenSequencing,timeGenomeComparison,timeGenomeProteomeAnnotation,timeOtherData,timeTrainingGiven,timeTrainingAttended,timeDevelopmentWebTools,timeDevelopmentApis,timeDevelopmentWebServices,timeDevelopmentWorkflows,timeDevelopmentDatabases,timeDevelopmentManteinance,timeTechnicalSupport,timeDocumentation,timeOtherTasks,otherTasksEspecify,timeDevelopmentDataResources,))
        #rc5 = cur.execute( sql5)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la insercion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=notAddedToTiempoEnProyectos&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
    
    #Redireccionamos a mensaje de insercion con exito
    print "Location: "+str(redirectionOKgestionTiempo)+"&from=insert \n\n"
    sys.exit()
	

if(fs['action'].value == "insertarRegistro"):
	#Creamos la conexion con la base de datos
    DB_CONN = MySQLdb.connect(host= DB_LOCAL_DB, port=3306, user = DB_READ_USER, passwd= DB_READ_PWD, db= Constants.DB_PERSONAL, charset="utf8", init_command="set names utf8")
    cur = DB_CONN.cursor()
    #Recogemos todos los datos
    ############RECOGIDA DE DATOS GENERALES#############
    try:
        idPersonal= fs['idPersonal'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=noIdPersonal \n\n"
        sys.exit()
    try:
        idNodo= fs['idNodo'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=noIdNodo \n\n"
        sys.exit()
    try:
        startDate= fs['startDate'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=noStartDate \n\n"
        sys.exit()
    try:
        endDate= fs['endDate'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=noEndDate \n\n"
        sys.exit()
    try:
        onHolidays= fs['daysNotWorked'].value
    except:
        onHolidays=0
    
    laborables=devolverLaborables(startDate,endDate)
    totalReportedHours=(int(laborables)-int(onHolidays))*8
    #print "laborables "+str(laborables)+"<br/>"
    #print "totalReportedHours "+str(totalReportedHours)+"<br/>"
    try:
        totalHoursProjects= fs['totalHoursProjects'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=noTotalHoursProjects \n\n"
        sys.exit()
    #print "<br/>"+str(totalHoursProjects)
    #print "laborables: "+str(laborables)+" onHolidays: "+str(onHolidays)+" totalReportedHours: "+str(totalReportedHours)
    #totalHoursProjects ahora es un porcentaje. Hay que pasarlo a horas teniendo en cuenta el totalHoursProjects
    totalHoursProjects=(float(totalHoursProjects)*totalReportedHours)/100.0
    
    #print "totalReportedHours "+str(totalReportedHours)+"<br/>"
    #print "totalHoursProjects "+ str(totalReportedHours)
    
    ############RECOGIDA DE DATOS DE TIEMPO DE DEDICACIÓN A PROYECTOS INB#############
    arrayProjectsReported={}
    proyectosAnadidos=[]
    for i in range(NUM_MAX_PROJECTS):
        try:
            especify=fs['especify'+str(i+1)].value
        except:
            especify="off"
        try:
            idProyecto=fs['projectReported'+str(i+1)].value
        except:
            if especify=="on":
                idProyecto="0"
            else:
                idProyecto="-1"
        if especify=="off":#Es decir, ha seleccionado un proyecto de la lista de proyectos. Recogemos los valores asociados.
            if(idProyecto in proyectosAnadidos):
                pass
            elif(idProyecto!="-1"):
                diccionarioDatosProyectos={}
                #print "diccionarioDatosProyectos: "+str(diccionarioDatosProyectos)+"<br>"
                #idProyecto=fs['projectReported'+str(i+1)].value
                diccionarioDatosProyectos["idProyecto"]=idProyecto
                proyectosAnadidos.append(idProyecto)
                #print "proyectosAnadidos contiene: "+str(proyectosAnadidos)+"<br>"
                #Guardamos sus horas asociadas pero primero pasamos el valor que tenemos, en porcentaje, a numero de horas
                porcentajeHoras=fs['invertedTimeProjects'+str(i+1)].value
                horas=(float(totalReportedHours)*float(porcentajeHoras))/100.0
                diccionarioDatosProyectos["horas"]=horas
                #Guardamos en el campo "otros" una cadena vacia
                diccionarioDatosProyectos["otros"]=""
                arrayProjectsReported[i]=diccionarioDatosProyectos
            
        elif((especify=="on")and(idProyecto!="-1")):
            diccionarioDatosProyectos={}
            #Guardamos el idProyecto con valor de 0 ya que no se ha seleccionado ninguno de la lista
            diccionarioDatosProyectos["idProyecto"]=0
            #Guardamos sus horas asociadas pero primero pasamos el valor que tenemos, en porcentaje, a numero de horas
            porcentajeHoras=fs['invertedTimeProjects'+str(i+1)].value
            horas=(float(totalHoursProjects)*float(porcentajeHoras))/100.0
            diccionarioDatosProyectos["horas"]=horas
            #Guardamos en el campo otros una cadena vacia
            diccionarioDatosProyectos["otros"]=fs['otherProjectTask'+str(i+1)].value
            arrayProjectsReported[i]=diccionarioDatosProyectos

   	#print "<br>"+str(arrayProjectsReported)
    #En arrayProjectsReported tenemos un diccionario con claves 1,2,3,4.... y values son diccionarios con los campos 'idProyecto','horas','otros'
     
    #############RECOGIDA DE DATOS DE INB SOFTWARE/SYSTEMS#############
    try:
        usageTotalSoftware= fs['usageTotalSoftware'].value
    except:
        usageTotalSoftware=0
    try:
        usageBabelomicsGepas= fs['usageBabelomicsGepas'].value
    except:
        usageBabelomicsGepas=0
    try:
        usageBiocreativeMetaserver= fs['usageBiocreativeMetaserver'].value
    except:
        usageBiocreativeMetaserver=0
    try:
        usageBlast2Go= fs['usageBlast2Go'].value
    except:
        usageBlast2Go=0
    try:
        usageCargo= fs['usageCargo'].value
    except:
        usageCargo=0
    try:
        usageDnalive= fs['usageDnalive'].value
    except:
        usageDnalive=0
    try:
        usageFatiGo= fs['usageFatiGo'].value
    except:
        usageFatiGo=0
    try:
        usageForensicator= fs['usageForensicator'].value
    except:
        usageForensicator=0
    try:
        usageFunCut= fs['usageFunCut'].value
    except:
        usageFunCut=0
    try:
        usageGeneId= fs['usageGeneId'].value
    except:
        usageGeneId=0
    try:
        usageIntogen= fs['usageIntogen'].value
    except:
        usageIntogen=0
    try:
        usageIwwem= fs['usageIwwem'].value
    except:
        usageIwwem=0
    try:
        usageJorca= fs['usageJorca'].value
    except:
        usageJorca=0
    try:
        usageKaryotypeViewer= fs['usageKaryotypeViewer'].value
    except:
        usageKaryotypeViewer=0
    try:
        usageMethylizer= fs['usageMethylizer'].value
    except:
        usageMethylizer=0
    try:
        usageMobyMiner= fs['usageMobyMiner'].value
    except:
        usageMobyMiner=0
    try:
        usageModel= fs['usageModel'].value
    except:
        usageModel=0
    try:
        usageMowServ= fs['usageMowServ'].value
    except:
        usageMowServ=0
    try:
        usageNemusTools= fs['usageNemusTools'].value
    except:
        usageNemusTools=0
    try:
        usagePmut= fs['usagePmut'].value
    except:
        usagePmut=0
    try:
        usagePupaSuite= fs['usagePupaSuite'].value
    except:
        usagePupaSuite=0
    try:
        usageSide= fs['usageSide'].value
    except:
        usageSide=0
    try:
        usageSysnp= fs['usageSysnp'].value
    except:
        usageSysnp=0
    try:
        usageVariantSplicing= fs['usageVariantSplicing'].value
    except:
        usageVariantSplicing=0
    try:
        usageVisualGenomics= fs['usageVisualGenomics'].value
    except:
        usageVisualGenomics=0
    try:
        usageExternalWebApplication= fs['usageExternalWebApplication'].value
    except:
        usageExternalWebApplication=0
    try:
        usageOther= fs['usageOther'].value
    except:
        usageOther=0
    try:
        usageOtherApplications= fs['usageOtherApplications'].value
    except:
        usageOtherApplications=""
    
    #############RECOGIDA DE DATOS DEL TIEMPO DEDICADO A TAREAS#############
    #Todo lo que recogemos en principio son porcentajes y por eso lo pasamos todo a horas.
    try:
        percentageTotalHoursTasks= fs['totalHoursTasks'].value
        totalHoursTasks=(int(percentageTotalHoursTasks)*int(totalReportedHours))/100
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=noTotalHoursTasks \n\n"
        sys.exit()
    try:
        percentageTimeSystemsAdministration= fs['timeSystemsAdministration'].value
        timeSystemsAdministration=(float(percentageTimeSystemsAdministration)*float(totalReportedHours))/100.0
    except:
        timeSystemsAdministration=0
    try:
        percentageTimeDataAnalysisMicroarray= fs['timeDataAnalysisMicroarray'].value
        timeDataAnalysisMicroarray=(float(percentageTimeDataAnalysisMicroarray)*float(totalReportedHours))/100.0
    except:
        timeDataAnalysisMicroarray=0
    try:
        percentageTimeDataFunctionalAnnotation= fs['timeDataFunctionalAnnotation'].value
        timeDataFunctionalAnnotation=(float(percentageTimeDataFunctionalAnnotation)*float(totalReportedHours))/100.0
    except:
        timeDataFunctionalAnnotation=0
    try:
        percentageTimeDataSimulations= fs['timeDataSimulations'].value
        timeDataSimulations=(float(percentageTimeDataSimulations)*float(totalReportedHours))/100.0
    except:
        timeDataSimulations=0
    try:
        percentageTimeDataNextGenSequencing= fs['timeDataNextGenSequencing'].value
        timeDataNextGenSequencing=(float(percentageTimeDataNextGenSequencing)*float(totalReportedHours))/100.0
    except:
        timeDataNextGenSequencing=0
    try:
        percentageTimeGenomeComparison= fs['timeGenomeComparison'].value
        timeGenomeComparison=(float(percentageTimeGenomeComparison)*float(totalReportedHours))/100.0
    except:
    
        timeGenomeComparison=0
    try:
        percentageTimeGenomeProteomeAnnotation= fs['timeGenomeProteomeAnnotation'].value
        timeGenomeProteomeAnnotation=(float(percentageTimeGenomeProteomeAnnotation)*float(totalReportedHours))/100.0
    except:
        timeGenomeProteomeAnnotation=0
    try:
        percentageTimeOtherData= fs['timeOtherData'].value
        timeOtherData=(float(percentageTimeOtherData)*float(totalReportedHours))/100.0
    except:
        timeOtherData=0
    try:
        percentageTimeTrainingGiven= fs['timeTrainingGiven'].value
        timeTrainingGiven=(float(percentageTimeTrainingGiven)*float(totalReportedHours))/100.0
    except:
        timeTrainingGiven=0
    try:
        percentageTimeTrainingAttended= fs['timeTrainingAttended'].value
        timeTrainingAttended=(float(percentageTimeTrainingAttended)*float(totalReportedHours))/100.0
    except:
        timeTrainingAttended=0
    try:
        percentageTimeDevelopmentWebTools= fs['timeDevelopmentWebTools'].value
        timeDevelopmentWebTools=(float(percentageTimeDevelopmentWebTools)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentWebTools=0
    try:
        percentageTimeDevelopmentApis= fs['timeDevelopmentApis'].value
        timeDevelopmentApis=(float(percentageTimeDevelopmentApis)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentApis=0
    try:
        percentageTimeDevelopmentWebServices= fs['timeDevelopmentWebServices'].value
        timeDevelopmentWebServices=(float(percentageTimeDevelopmentWebServices)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentWebServices=0
    try:
        percentageTimeDevelopmentWorkflows= fs['timeDevelopmentWorkflows'].value
        timeDevelopmentWorkflows=(float(percentageTimeDevelopmentWorkflows)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentWorkflows=0
    try:
        percentageTimeDevelopmentDatabases= fs['timeDevelopmentDatabases'].value
        timeDevelopmentDatabases=(float(percentageTimeDevelopmentDatabases)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentDatabases=0
    try:
        percentageTimeDevelopmentManteinance= fs['timeDevelopmentManteinance'].value
        timeDevelopmentManteinance=(float(percentageTimeDevelopmentManteinance)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentManteinance=0
    try:
        percentageTimeTechnicalSupport= fs['timeTechnicalSupport'].value
        timeTechnicalSupport=(float(percentageTimeTechnicalSupport)*float(totalReportedHours))/100.0
    except:
        timeTechnicalSupport=0
    try:
        percentageTimeDevelopmentDatabases= fs['timeDevelopmentDatabases'].value
        timeDevelopmentDatabases=(float(percentageTimeDevelopmentDatabases)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentDatabases=0
    try:
        percentageTimeDevelopmentDataResources= fs['timeDevelopmentDataResources'].value
        timeDevelopmentDataResources=(float(percentageTimeDevelopmentDataResources)*float(totalReportedHours))/100.0
    except:
        timeDevelopmentDataResources=0
    try:
        percentageTimeDocumentation= fs['timeDocumentation'].value
        timeDocumentation=(float(percentageTimeDocumentation)*float(totalReportedHours))/100.0
    except:
        timeDocumentation=0
    try:
        percentageTimeOtherTasks= fs['timeOtherTasks'].value
        timeOtherTasks=(float(percentageTimeOtherTasks)*float(totalReportedHours))/100.0
    except:
        timeOtherTasks=0
    try:
        otherTasksEspecify= fs['otherTasksEspecify'].value
    except:
        otherTasksEspecify=""
    ######Ya tenemos todos los datos, vamos a guardarlos en la base de datos#####
	"""
   	print "***"+str(idNodo)+"****"
    print "<br>***"+str(idPersonal)+"****"
    print "<br>***"+str(startDate)+"****"
    print "<br>***"+str(endDate)+"****"
    print "<br>***"+str(totalReportedHours)+"****"
    print "<br>***"+str(onHolidays)+"****"
    print "<br>***"+str(totalHoursProjects)+"****"
    print "<br>***"+str(usageTotalSoftware)+"****"
    print "<br>***"+str(totalHoursTasks)+"****"
    """
    
    #Ya tenemos los datos. 
    #Tenemos que controlar que el numero total de horas reportadas para software no es mayor que el numero de horas totales (el resto no puede serlo ya que provienen de porcentajes)
    if (float(usageTotalSoftware)>int(totalReportedHours)):
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=tooManySoftwareHours \n\n"
        sys.exit()
    #Tenemos que comprobar que en la base de datos no existe ninguna entrada con esos startDate y endDate o comprendidas entre ellos
    #Recuperamos todas las entradas que hay de ese usuario en la tabla gestionTiempo2 para ver si existe algun registro que se solape con el que queremos insertar nuevo
    sql="select * from gestionTiempo2 where idPersonal=\""+str(idPersonal)+"\""
    cur.execute(sql)
    while(1):
        row = cur.fetchone()
        if row==None: 
            break
        #en row tenemos una entrada para ese usuario. Comprobar fechas
        fechaInicioGuardada=row[2]
        fechaFinGuardada=row[3]
        #print "<br>fechaInicioGuardada="+str(fechaInicioGuardada)+"  fechaFinGuardada="+str(fechaFinGuardada)+"  startDate="+str(startDate)+"  endDate="+str(endDate)+"<br>"
        startDateFormated=datetime.date(int(startDate[6:]),int(startDate[3:5]),int(startDate[0:2]))
        endDateFormated=datetime.date(int(endDate[6:]),int(endDate[3:5]),int(endDate[0:2]))
        fechaInicioGuardadaFormated=datetime.date(int(fechaInicioGuardada[6:]),int(fechaInicioGuardada[3:5]),int(fechaInicioGuardada[0:2]))
        fechaFinGuardadaFormated=datetime.date(int(fechaFinGuardada[6:]),int(fechaFinGuardada[3:5]),int(fechaFinGuardada[0:2]))
        #print "<br>fechaInicioGuardadaFormated="+str(fechaInicioGuardadaFormated)+"  fechaFinGuardadaFormated="+str(fechaFinGuardadaFormated)+"  startDateFormated="+str(startDateFormated)+"  endDateFormated="+str(endDateFormated)+"<br>"
        #primero comprobamos que startDate sea menor que endDate
        if (startDateFormated>=endDateFormated):
            print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=startGreaterEnd \n\n"
            sys.exit()
        if (startDateFormated>=fechaInicioGuardadaFormated)and(startDateFormated<=fechaFinGuardadaFormated):
            print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=dateExists \n\n"
            sys.exit()
        elif (endDateFormated>=fechaInicioGuardadaFormated)and(endDateFormated<=fechaFinGuardadaFormated):
            print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=dateExists \n\n"
            sys.exit()
    
    ################################################################################################################################################# 
    ############OJO, LAS DOS LINEAS SIGUIENTES FUERZAN A QUE SOLO SE PUEDAN INSERTAR REGISTROS QUE INICIEN UN LUNES Y FINALICEN UN VIERNES###########
    #################################################################################################################################################
    #diaSemanaStartDate=startDateFormated.isoweekday() #Esta variable debe valer 1!!!!
    #diaSemanaEndDate=endDateFormated.isoweekday() #Esta variable debe valer 5!!!!
    #if diaSemanaStartDate!=1:
    #    print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=startDateNoMonday \n\n"
    #    sys.exit()
    #if diaSemanaEndDate!=5:
    #    print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=endDateNoFriday \n\n"
    #    sys.exit()
    ########################################################INSERTAMOS DATOS GENERALES EN LA TABLA GESTIONTIEMPO#####################################
    #Si llegamos aqui es que no tenemos registros para ese usuario durante ese periodo, asi insertamos en la base de datos personal, en la tabla gestionTiempo2, un registro con los campos.
    #Pasamos los datos a unicode para enviarlos a la base de datos
    
    startDate = unicode( startDate, "utf-8" )
    endDate= unicode( endDate, "utf-8" )

    #Conectamos a la base de datos
    DB_CONN = MySQLdb.connect(host= DB_LOCAL_DB, port=3306, user = DB_READ_USER, passwd= DB_READ_PWD, db= Constants.DB_PERSONAL, charset="utf8", init_command="set names utf8")
    cur = DB_CONN.cursor()
    #Modificamos el formato de la fecha startEnd y endDate que llega asi: 25-01-11  y pasarla asi: 25-01-2011
    arrayTmp=startDate.split("-")
    startDate=arrayTmp[0]+"-"+arrayTmp[1]+"-20"+arrayTmp[2]
    arrayTmp=endDate.split("-")
    endDate=arrayTmp[0]+"-"+arrayTmp[1]+"-20"+arrayTmp[2]
    #Creamos la sentencia SQL
    #Ojo! totalReportedHours tiene que actualizarse si hay dias de vacaciones:
    #if int(totalReportedHours)!=0:
    #    horasVacaciones=int(onHolidays)*8
    #    totalReportedHours=int(totalReportedHours)-int(horasVacaciones)
    #print "********"+str(totalReportedHours)+"***********"
    sql="insert into gestionTiempo2 (idGestionTiempo,idPersonal,startDate,endDate,onHolidays,totalHoursReported,timeProjects,timeSoftware,timeTasks)values(NULL,%s,%s,%s,%s,%s,%s,%s,%s)"
    try:
        rc = cur.execute( sql,(idPersonal,startDate,endDate,onHolidays,totalReportedHours,totalHoursProjects,usageTotalSoftware,totalHoursTasks,))
        #rc = cur.execute( sql)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la insercion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=notAddedToGestionTiempo \n\n"
        sys.exit()
    
    #Ahora necesitamos recoger el idGestionTiempo de la anterior transaccion para utilizarlo en las demas tablas (tiempoEnProyectos2,tiempoEnSoftware2,tiempoEnTareas2)
    sql2="select max(idGestionTiempo) from gestionTiempo2 WHERE idPersonal= "+str(idPersonal)
    #print sql2
    try:
        cur.execute(sql2)
        row2 = cur.fetchone()
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la seleccion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=badSelectSentence \n\n"
        DB_CONN.close()
        sys.exit()
    #En row2[0]tenemos el idGestionTiempo que acabamos de insertar y que utilizaremos de ahora en adelante para las siguientes inserciones.
    idGestionTiempo=row2[0]
    #print str(idGestionTiempo)+"<br>"
    ########################################################INSERTAMOS HORAS ASOCIADAS A PROYECTOS##########################################
    #La informacion la tenemos en el arrayProjectsReported:
    #print arrayProjectsReported
    for indice in arrayProjectsReported:
        diccionario = arrayProjectsReported[indice]
        #print str(diccionario)+"<br>"
        #recogemos los datos
        #ya tenemos el idGestionTiempo para relacionarlo con la tabla gestionTiempo2
        idProyecto=diccionario["idProyecto"]
        hours=diccionario["horas"]
        otherProjectTask=diccionario["otros"]
        #Pasamos otherProjectTask a unicode para enviarlos a la base de datos
        otherProjectTask = unicode( otherProjectTask, "utf-8" )
        #Creamos la sentencia sql
        
        sql3="insert into tiempoEnProyectos2 (idTiempoProyecto,idGestionTiempo,idProyecto,otherProjectTask,hours)values(NULL,%s,%s,%s,%s)"
        
        #sql3="insert into tiempoEnProyectos2 (idTiempoProyecto,idGestionTiempo,idProyecto,otherProjectTask,hours)values(NULL,'"+str(idGestionTiempo)+"','"+str(idProyecto)+"','"+otherProjectTask+"','"+str(hours)+"')"
        #print str(sql3)+"<br>"
        try:
            rc3 = cur.execute( sql3,(int(idGestionTiempo),idProyecto,otherProjectTask,hours,))
            #rc3 = cur.execute( sql3)
        except MySQLdb.OperationalError, e:
            #redireccionar a pagina de error en la insercion y sys.exit() de este script
            print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=notAddedToTiempoEnProyectos&errorCode="+str(idGestionTiempo)+" \n\n"
            sys.exit()
    
    ########################################################INSERTAMOS HORAS ASOCIADAS A SOFTWARE##########################################
    #Tenemos los datos recuperados mas arriba. Pasamos a unicode utf-8 el campo  otherApplicationEspecified (viene como usageOtherApplications)
    usageOtherApplications = unicode( usageOtherApplications, "utf-8" )
    #Creamos la sentencia de insercion sql:
    sql4="insert into tiempoEnSoftware2 (idTiempoSoftware,idGestionTiempo,babelomicsGepas,biocreativeMetaserver,blast2go,cargo,dnalive,fatigo,forensicator,funcut,geneId,intoGen,iwwem,jorca,karyotypeViewer,methylizer,mobyMiner,model,mowserv,nemusTools,pmut,pupaSuite,side,sysnp,variantSplicing,visualGenomics,externalWebApplication,otherApplications,otherApplicationEspecified)values(NULL,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    #print str(sql4)+"<br>"
    try:
        rc4 = cur.execute( sql4,(int(idGestionTiempo),round(float(usageBabelomicsGepas),2),round(float(usageBiocreativeMetaserver),2),round(float(usageBlast2Go),2),round(float(usageCargo),2),round(float(usageDnalive),2),round(float(usageFatiGo),2),round(float(usageForensicator),2),round(float(usageFunCut),2),round(float(usageGeneId),2),round(float(usageIntogen),2),round(float(usageIwwem),2),round(float(usageJorca),2),round(float(usageKaryotypeViewer),2),round(float(usageMethylizer),2),round(float(usageMobyMiner),2),round(float(usageModel),2),round(float(usageMowServ),2),round(float(usageNemusTools),2),round(float(usagePmut),2),round(float(usagePupaSuite),2),round(float(usageSide),2),round(float(usageSysnp),2),round(float(usageVariantSplicing),2),round(float(usageVisualGenomics),2),round(float(usageExternalWebApplication),2),round(float(usageOther),2),usageOtherApplications,))
        #rc4 = cur.execute( sql4)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la insercion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=notAddedToTiempoEnProyectos&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
        
    ########################################################INSERTAMOS HORAS ASOCIADAS A TASKS##########################################
    #Tenemos los datos recuperados mas arriba. Pasamos a unicode utf-8 el campo  otherTasksEspecify
    otherTasksEspecify = unicode( otherTasksEspecify, "utf-8" )
    #Creamos la sentencia de insercion sql:
    #Redireccionamos a mensaje de insercion con exito
    
    sql5="insert into tiempoEnTareas2 (idTiempoTarea,idGestionTiempo,timeSystemsAdministration,timeDataAnalysisMicroarray,timeDataFunctionalAnnotation,timeDataSimulations,timeDataNextGenSequencing,timeGenomeComparison,timeGenomeProteomeAnnotation,timeOtherData,timeTrainingGiven,timeTrainingAttended,timeDevelopmentWebTools,timeDevelopmentApis,timeDevelopmentWebServices,timeDevelopmentWorkflows,timeDevelopmentDatabases,timeDevelopmentManteinance,timeTechnicalSupport,timeDocumentation,timeOtherTasks,otherTasksEspecify,timeDevelopmentDataResources)values(NULL,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    try:
        rc5 = cur.execute( sql5,(int(idGestionTiempo),timeSystemsAdministration,timeDataAnalysisMicroarray,timeDataFunctionalAnnotation,timeDataSimulations,timeDataNextGenSequencing,timeGenomeComparison,timeGenomeProteomeAnnotation,timeOtherData,timeTrainingGiven,timeTrainingAttended,timeDevelopmentWebTools,timeDevelopmentApis,timeDevelopmentWebServices,timeDevelopmentWorkflows,timeDevelopmentDatabases,timeDevelopmentManteinance,timeTechnicalSupport,timeDocumentation,timeOtherTasks,otherTasksEspecify,timeDevelopmentDataResources,))
        #rc5 = cur.execute( sql5)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la insercion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=insert&reason=notAddedToTiempoEnProyectos&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
    print "Location: "+str(redirectionOKgestionTiempo)+"&from=insert \n\n"
    sys.exit()


if(fs['action'].value == "eliminarGestionTiempo"):
    #commonOutput()
    #Creamos la conexion con la base de datos
    DB_CONN = MySQLdb.connect(host= DB_LOCAL_DB, port=3306, user = DB_READ_USER, passwd= DB_READ_PWD, db= Constants.DB_PERSONAL, charset="utf8", init_command="set names utf8")
    curDel = DB_CONN.cursor()
    #Recogemos todos los datos del fs
    try:
        idGestionTiempo= fs['idGestionTiempo'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=delete&reason=noIdGestionTiempo \n\n"
        sys.exit()
    #Tenemos el idGestionTiempo del registro que queremos eliminar. Tenemos que eliminar todas las entradas en las tablas: tiempoEnTareas2,tiempoEnSoftware2,tiempoEnProyectos2 y gestionTiempo2, que tengan ese idGestionTiempo!!!
    #Creamos la sentencia de borrado:
    sql="delete from tiempoEnTareas2 where idGestionTiempo="+idGestionTiempo
    sql2="delete from tiempoEnSoftware2 where idGestionTiempo="+idGestionTiempo
    sql3="delete from tiempoEnProyectos2 where idGestionTiempo="+idGestionTiempo
    sql4="delete from gestionTiempo2 where idGestionTiempo="+idGestionTiempo
    #print sql+"<br>"+sql2+"<br>"+sql3+"<br>"+sql4
    #Ejecutamos las sentencias:
    try:
        curDel.execute(sql)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la eliminacion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=delete&reason=notRemovedFromTiempoEnTareas&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
    try:
        curDel.execute(sql2)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la eliminacion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=delete&reason=notRemovedFromTiempoEnSoftware&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
    try:
        curDel.execute(sql3)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la eliminacion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=delete&reason=notRemovedFromTiempoEnProyectos&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
    try:
        curDel.execute(sql4)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la eliminacion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=delete&reason=notRemovedFromGestionTiempo&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
    #Se han eliminado todos los campos con exito.
    #Redireccionamos a mensaje de exito en el borrado
    print "Location: "+str(redirectionOKgestionTiempo)+"&from=delete \n\n"
    sys.exit()


if(fs['action'].value == "modificarRegistro"):
    #Creamos la conexion con la base de datos
    DB_CONN = MySQLdb.connect(host= DB_LOCAL_DB, port=3306, user = DB_READ_USER, passwd= DB_READ_PWD, db= Constants.DB_PERSONAL, charset="utf8", init_command="set names utf8")
    cur = DB_CONN.cursor()
    #Lo primero es borrar las entradas que hay de ese idGestionTiempo
    curDel = DB_CONN.cursor()
    #Recogemos todos los datos del fs
    try:
        idGestionTiempo= fs['idGestionTiempo'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=delete&reason=noIdGestionTiempo \n\n"
        sys.exit()
    #Tenemos el idGestionTiempo del registro que queremos eliminar. Tenemos que eliminar todas las entradas en las tablas: tiempoEnTareas2,tiempoEnSoftware2,tiempoEnProyectos2 y gestionTiempo2, que tengan ese idGestionTiempo!!!
    #Creamos la sentencia de borrado:
    sql="delete from tiempoEnTareas2 where idGestionTiempo="+idGestionTiempo
    sql2="delete from tiempoEnSoftware2 where idGestionTiempo="+idGestionTiempo
    sql3="delete from tiempoEnProyectos2 where idGestionTiempo="+idGestionTiempo
    sql4="delete from gestionTiempo2 where idGestionTiempo="+idGestionTiempo
    #print sql+"<br>"+sql2+"<br>"+sql3+"<br>"+sql4
    
    #Ejecutamos las sentencias:

    try:
        curDel.execute(sql)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la eliminacion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=delete&reason=notRemovedFromTiempoEnTareas&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
    try:
        curDel.execute(sql2)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la eliminacion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=delete&reason=notRemovedFromTiempoEnSoftware&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
    try:
        curDel.execute(sql3)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la eliminacion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=delete&reason=notRemovedFromTiempoEnProyectos&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
    try:
        curDel.execute(sql4)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la eliminacion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=delete&reason=notRemovedFromGestionTiempo&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
    #Se han eliminado todos los campos con exito.
    #Lo siguiente es insertar toda la nueva información:
    ############RECOGIDA DE DATOS GENERALES#############
    try:
        idPersonal= fs['idPersonal'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=noIdPersonal \n\n"
        sys.exit()
    try:
        idNodo= fs['idNodo'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=noIdNodo \n\n"
        sys.exit()
    try:
        startDate= fs['startDate'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=noStartDate \n\n"
        sys.exit()
    try:
        endDate= fs['endDate'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=noEndDate \n\n"
        sys.exit()
    try:
        onHolidays= fs['daysNotWorked'].value
    except:
        onHolidays=0#False
    try:
        percentageTotalHoursProjects= fs['totalHoursProjects'].value
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=noTotalHoursProjects \n\n"
        sys.exit()
    
    #totalHoursProjects llega en forma de porcentaje con respecto a totalReportedHours
    laborables=devolverLaborables(startDate,endDate)
    totalReportedHours=(int(laborables)-int(onHolidays))*8
    #Pasamos totalHoursProjects de porcentaje a numero de horas
    totalHoursProjects=(int(percentageTotalHoursProjects)*totalReportedHours)/100
    ############RECOGIDA DE DATOS DE TIEMPO DE DEDICACIÓN A PROYECTOS INB#############
    arrayProjectsReported={}
    proyectosAnadidos=[]
    for i in range(NUM_MAX_PROJECTS):
        try:
            especify=fs['especify'+str(i+1)].value
        except:
            especify="off"
        try:
            idProyecto=fs['projectReported'+str(i+1)].value
        except:
            if especify=="on":
                idProyecto="0"
            else:
                idProyecto="-1"
        if especify=="off":#Es decir, ha seleccionado un proyecto de la lista de proyectos. Recogemos los valores asociados.
            if(idProyecto in proyectosAnadidos):
                pass
            elif(idProyecto!="-1"):
                diccionarioDatosProyectos={}
                #print "diccionarioDatosProyectos: "+str(diccionarioDatosProyectos)+"<br>"
                #idProyecto=fs['projectReported'+str(i+1)].value
                diccionarioDatosProyectos["idProyecto"]=idProyecto
                proyectosAnadidos.append(idProyecto)
                #print "proyectosAnadidos contiene: "+str(proyectosAnadidos)+"<br>"
                #Guardamos sus horas asociadas
                diccionarioDatosProyectos["horas"]=fs['invertedTimeProjects'+str(i+1)].value
                #Guardamos en el campo "otros" una cadena vacia
                diccionarioDatosProyectos["otros"]=""
                arrayProjectsReported[i]=diccionarioDatosProyectos
            
        elif((especify=="on")and(idProyecto!="-1")):
            diccionarioDatosProyectos={}
            #Guardamos el idProyecto con valor de 0 ya que no se ha seleccionado ninguno de la lista
            diccionarioDatosProyectos["idProyecto"]=0
            #Guardamos sus horas asociadas
            diccionarioDatosProyectos["horas"]=fs['invertedTimeProjects'+str(i+1)].value
            #Guardamos en el campo otros una cadena vacia
            diccionarioDatosProyectos["otros"]=fs['otherProjectTask'+str(i+1)].value
            arrayProjectsReported[i]=diccionarioDatosProyectos

    #En arrayProjectsReported tenemos un diccionario con claves 1,2,3,4.... y values son diccionarios con los campos 'idProyecto','horas','otros'
    #Pasamos el contenido de arrayProjects desde porcentajes a horas.
    diasLaborables=devolverLaborables(startDate,endDate)
    #print "<BR/>dias Laborables "+str(diasLaborables)
    totalHoursReported=(int(diasLaborables)-int(onHolidays))*8
    #print "<BR/>totalHoursReported "+str(totalHoursReported)
    diccionarioProyecto={}
    for indice in arrayProjectsReported:
        diccionarioProyecto[indice]=arrayProjectsReported[indice]
        diccionarioProyecto[indice]["horas"]=(float(arrayProjectsReported[indice]["horas"])*totalHoursReported)/100.0
    arrayProjectsReported=diccionarioProyecto
    #print "<br>"+str(arrayProjectsReported)
    
    
    #print "<br>fechaInicioGuardada="+str(fechaInicioGuardada)+"  fechaFinGuardada="+str(fechaFinGuardada)+"  startDate="+str(startDate)+"  endDate="+str(endDate)+"<br>"
    #diasLaborables=devolverLaborables(startDate,endDate)
    #totalHoursReported=(int(diasLaborables)-int(onHolidays))*8
    #print "<br>TotalHoursReported "+str(totalHoursReported)
    #En arrayProjectsReported tenemos un diccionario con claves 1,2,3,4.... y values son diccionarios con los campos 'idProyecto','horas','otros'
    #############RECOGIDA DE DATOS DE INB SOFTWARE/SYSTEMS#############
    try:
        usageTotalSoftware= fs['usageTotalSoftware'].value
    except:
        usageTotalSoftware=0
    try:
        usageBabelomicsGepas= fs['usageBabelomicsGepas'].value
    except:
        usageBabelomicsGepas=0
    try:
        usageBiocreativeMetaserver= fs['usageBiocreativeMetaserver'].value
    except:
        usageBiocreativeMetaserver=0
    try:
        usageBlast2Go= fs['usageBlast2Go'].value
    except:
        usageBlast2Go=0
    try:
        usageCargo= fs['usageCargo'].value
    except:
        usageCargo=0
    try:
        usageDnalive= fs['usageDnalive'].value
    except:
        usageDnalive=0
    try:
        usageFatiGo= fs['usageFatiGo'].value
    except:
        usageFatiGo=0
    try:
        usageForensicator= fs['usageForensicator'].value
    except:
        usageForensicator=0
    try:
        usageFunCut= fs['usageFunCut'].value
    except:
        usageFunCut=0
    try:
        usageGeneId= fs['usageGeneId'].value
    except:
        usageGeneId=0
    try:
        usageIntogen= fs['usageIntogen'].value
    except:
        usageIntogen=0
    try:
        usageIwwem= fs['usageIwwem'].value
    except:
        usageIwwem=0
    try:
        usageJorca= fs['usageJorca'].value
    except:
        usageJorca=0
    try:
        usageKaryotypeViewer= fs['usageKaryotypeViewer'].value
    except:
        usageKaryotypeViewer=0
    try:
        usageMethylizer= fs['usageMethylizer'].value
    except:
        usageMethylizer=0
    try:
        usageMobyMiner= fs['usageMobyMiner'].value
    except:
        usageMobyMiner=0
    try:
        usageModel= fs['usageModel'].value
    except:
        usageModel=0
    try:
        usageMowServ= fs['usageMowServ'].value
    except:
        usageMowServ=0
    try:
        usageNemusTools= fs['usageNemusTools'].value
    except:
        usageNemusTools=0
    try:
        usagePmut= fs['usagePmut'].value
    except:
        usagePmut=0
    try:
        usagePupaSuite= fs['usagePupaSuite'].value
    except:
        usagePupaSuite=0
    try:
        usageSide= fs['usageSide'].value
    except:
        usageSide=0
    try:
        usageSysnp= fs['usageSysnp'].value
    except:
        usageSysnp=0
    try:
        usageVariantSplicing= fs['usageVariantSplicing'].value
    except:
        usageVariantSplicing=0
    try:
        usageVisualGenomics= fs['usageVisualGenomics'].value
    except:
        usageVisualGenomics=0
    try:
        usageExternalWebApplication= fs['usageExternalWebApplication'].value
    except:
        usageExternalWebApplication=0
    try:
        usageOther= fs['usageOther'].value
    except:
        usageOther=0
    try:
        usageOtherApplications= fs['usageOtherApplications'].value
    except:
        usageOtherApplications=""
        
    
    #############RECOGIDA DE DATOS DEL TIEMPO DEDICADO A TAREAS#############
    #OJO obtenemos los datos en forma de porcentajes y los tenemos que pasar a horas para guardarlos en la base de datos.
    try:
        percentageTotalHoursTasks= fs['totalHoursTasks'].value
        totalHoursTasks=(int(percentageTotalHoursTasks)*int(totalHoursReported))/100
    except:
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=noTotalHoursTasks \n\n"
        sys.exit()
    try:
        percentageTimeSystemsAdministration= fs['timeSystemsAdministration'].value
        timeSystemsAdministration=(float(percentageTimeSystemsAdministration)*float(totalHoursReported))/100.0
    except:
        timeSystemsAdministration=0
    try:
        percentageTimeDataAnalysisMicroarray= fs['timeDataAnalysisMicroarray'].value
        timeDataAnalysisMicroarray=(float(percentageTimeDataAnalysisMicroarray)*float(totalHoursReported))/100.0
    except:
        timeDataAnalysisMicroarray=0
    try:
        percentageTimeDataFunctionalAnnotation= fs['timeDataFunctionalAnnotation'].value
        timeDataFunctionalAnnotation=(float(percentageTimeDataFunctionalAnnotation)*float(totalHoursReported))/100.0     
    except:
        timeDataFunctionalAnnotation=0
    try:
        percentageTimeDataSimulations= fs['timeDataSimulations'].value
        timeDataSimulations=(float(percentageTimeDataSimulations)*float(totalHoursReported))/100.0
    except:
        timeDataSimulations=0
    try:
        percentageTimeDataNextGenSequencing= fs['timeDataNextGenSequencing'].value
        timeDataNextGenSequencing=(float(percentageTimeDataNextGenSequencing)*float(totalHoursReported))/100.0
    except:
        timeDataNextGenSequencing=0
    try:
        percentageTimeGenomeComparison= fs['timeGenomeComparison'].value
        timeGenomeComparison=(float(percentageTimeGenomeComparison)*float(totalHoursReported))/100.0
    except:
        timeGenomeComparison=0
    try:
        percentageTimeGenomeProteomeAnnotation= fs['timeGenomeProteomeAnnotation'].value
        timeGenomeProteomeAnnotation=(float(percentageTimeGenomeProteomeAnnotation)*float(totalHoursReported))/100.0
    except:
        timeGenomeProteomeAnnotation=0
    try:
        percentageTimeOtherData= fs['timeOtherData'].value
        timeOtherData=(float(percentageTimeOtherData)*float(totalHoursReported))/100.0
    except:
        timeOtherData=0
    try:
        percentageTimeTrainingGiven= fs['timeTrainingGiven'].value
        timeTrainingGiven=(float(percentageTimeTrainingGiven)*float(totalHoursReported))/100.0
    except:
        timeTrainingGiven=0
    try:
        percentageTimeTrainingAttended= fs['timeTrainingAttended'].value
        timeTrainingAttended=(float(percentageTimeTrainingAttended)*float(totalHoursReported))/100.0
    except:
        timeTrainingAttended=0
    try:
        percentageTimeDevelopmentWebTools= fs['timeDevelopmentWebTools'].value
        timeDevelopmentWebTools=(float(percentageTimeDevelopmentWebTools)*float(totalHoursReported))/100.0
    except:
        timeDevelopmentWebTools=0
    try:
        percentageTimeDevelopmentApis= fs['timeDevelopmentApis'].value
        timeDevelopmentApis=(float(percentageTimeDevelopmentApis)*float(totalHoursReported))/100.0
    except:
        timeDevelopmentApis=0
    try:
        percentageTimeDevelopmentWebServices= fs['timeDevelopmentWebServices'].value
        timeDevelopmentWebServices=(float(percentageTimeDevelopmentWebServices)*float(totalHoursReported))/100.0
    except:
        timeDevelopmentWebServices=0
    try:
        percentageTimeDevelopmentWorkflows= fs['timeDevelopmentWorkflows'].value
        timeDevelopmentWorkflows=(float(percentageTimeDevelopmentWorkflows)*float(totalHoursReported))/100.0
    except:
        timeDevelopmentWorkflows=0
    try:
        percentageTimeDevelopmentDatabases= fs['timeDevelopmentDatabases'].value
        timeDevelopmentDatabases=(float(percentageTimeDevelopmentDatabases)*float(totalHoursReported))/100.0
    except:
        timeDevelopmentDatabases=0
    try:
        percentageTimeDevelopmentManteinance= fs['timeDevelopmentManteinance'].value
        timeDevelopmentManteinance=(float(percentageTimeDevelopmentManteinance)*float(totalHoursReported))/100.0
    except:
        timeDevelopmentManteinance=0
    try:
        percentageTimeTechnicalSupport= fs['timeTechnicalSupport'].value
        timeTechnicalSupport=(float(percentageTimeTechnicalSupport)*float(totalHoursReported))/100.0
    except:
        timeTechnicalSupport=0
    try:
        percentageTimeDevelopmentDatabases= fs['timeDevelopmentDatabases'].value
        timeDevelopmentDatabases=(float(percentageTimeDevelopmentDatabases)*float(totalHoursReported))/100.0
    except:
        timeDevelopmentDatabases=0
    try:
        percentageTimeDocumentation= fs['timeDocumentation'].value
        timeDocumentation=(float(percentageTimeDocumentation)*float(totalHoursReported))/100.0
    except:
        timeDocumentation=0
    try:
        percentageTimeOtherTasks= fs['timeOtherTasks'].value
        timeOtherTasks=(float(percentageTimeOtherTasks)*float(totalHoursReported))/100.0
    except:
        timeOtherTasks=0
    try:
        otherTasksEspecify= fs['otherTasksEspecify'].value
    except:
        otherTasksEspecify=""
    try:
        percentageTimeDevelopmentDataResources= fs['timeDevelopmentDataResources'].value
        timeDevelopmentDataResources=(float(percentageTimeDevelopmentDataResources)*float(totalHoursReported))/100.0
    except:
        timeDevelopmentDataResources=0
        
    ######Ya tenemos todos los datos, vamos a guardarlos en la base de datos#####
    
    #Ya tenemos los datos. Tenemos que comprobar que en la base de datos no existe ninguna entrada con esos startDate y endDate o comprendidas entre ellos
    #Recuperamos todas las entradas que hay de ese usuario en la tabla gestionTiempo para ver si existe algun registro que se solape con el que queremos insertar nuevo
    
    """
    sql="select * from gestionTiempo2 where idPersonal=\""+str(idPersonal)+"\""
    cur.execute(sql)
    while(1):
        row = cur.fetchone()
        if row==None: 
            break
        #en row tenemos una entrada para ese usuario. Comprobar fechas
        fechaInicioGuardada=row[2]
        fechaFinGuardada=row[3]
        #print "<br>fechaInicioGuardada="+str(fechaInicioGuardada)+"  fechaFinGuardada="+str(fechaFinGuardada)+"  startDate="+str(startDate)+"  endDate="+str(endDate)+"<br>"
        startDateFormated=datetime.date(int(startDate[6:]),int(startDate[3:5]),int(startDate[0:2]))
        endDateFormated=datetime.date(int(endDate[6:]),int(endDate[3:5]),int(endDate[0:2]))
        fechaInicioGuardadaFormated=datetime.date(int(fechaInicioGuardada[6:]),int(fechaInicioGuardada[3:5]),int(fechaInicioGuardada[0:2]))
        fechaFinGuardadaFormated=datetime.date(int(fechaFinGuardada[6:]),int(fechaFinGuardada[3:5]),int(fechaFinGuardada[0:2]))
        #print "<br>fechaInicioGuardadaFormated="+str(fechaInicioGuardadaFormated)+"  fechaFinGuardadaFormated="+str(fechaFinGuardadaFormated)+"  startDateFormated="+str(startDateFormated)+"  endDateFormated="+str(endDateFormated)+"<br>"
        #primero comprobamos que startDate sea menor que endDate
        if (startDateFormated>=endDateFormated):
            print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=startGreaterEnd \n\n"
            sys.exit()
        if (startDateFormated>=fechaInicioGuardadaFormated)and(startDateFormated<=fechaFinGuardadaFormated):
            print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=dateExists \n\n"
            sys.exit()
        elif (endDateFormated>=fechaInicioGuardadaFormated)and(endDateFormated<=fechaFinGuardadaFormated):
            print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=dateExists \n\n"
            sys.exit()
    """
    ################################################################################################################################################# 
    ############OJO, LAS DOS LINEAS SIGUIENTES FUERZAN A QUE SOLO SE PUEDAN INSERTAR REGISTROS QUE INICIEN UN LUNES Y FINALICEN UN VIERNES###########
    #################################################################################################################################################
    #diaSemanaStartDate=startDateFormated.isoweekday() #Esta variable debe valer 1!!!!
    #diaSemanaEndDate=endDateFormated.isoweekday() #Esta variable debe valer 5!!!!
    #if diaSemanaStartDate!=1:
    #    print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=startDateNoMonday \n\n"
    #    sys.exit()
    #if diaSemanaEndDate!=5:
    #    print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=endDateNoFriday \n\n"
    #    sys.exit()
    ########################################################INSERTAMOS DATOS GENERALES EN LA TABLA GESTIONTIEMPO#####################################
    #Si llegamos aqui es que no tenemos registros para ese usuario durante ese periodo, asi insertamos en la base de datos personal, en la tabla gestionTiempo2, un registro con los campos.
    #Pasamos los datos a unicode para enviarlos a la base de datos
    
    startDate = unicode( startDate, "utf-8" )
    endDate= unicode( endDate, "utf-8" )
    #Tenemos las fechas en formato 15-01-11 y hay que pasarla a 15-01-2011
    arrayStartDate=startDate.rsplit("-")
    startDate=arrayStartDate[0]+"-"+arrayStartDate[1]+"-20"+arrayStartDate[2]
    arrayEndDate=endDate.rsplit("-")
    endDate=arrayEndDate[0]+"-"+arrayEndDate[1]+"-20"+arrayEndDate[2]
    #Ojo! totalHoursReported tiene que actualizarse si hay dias de vacaciones:
    if int(totalHoursReported)!=0:
        horasVacaciones=int(onHolidays)*8
        totalHoursReported=int(totalHoursReported)-int(horasVacaciones)
    #Conectamos a la base de datos
    DB_CONN = MySQLdb.connect(host= DB_LOCAL_DB, port=3306, user = DB_READ_USER, passwd= DB_READ_PWD, db= Constants.DB_PERSONAL, charset="utf8", init_command="set names utf8")
    cur = DB_CONN.cursor()
    #Creamos la sentencia SQL
    sql="insert into gestionTiempo2 (idGestionTiempo,idPersonal,startDate,endDate,onHolidays,totalHoursReported,timeProjects,timeSoftware,timeTasks)values(NULL,%s,%s,%s,%s,%s,%s,%s,%s)"
    try:
        rc = cur.execute( sql,(idPersonal,startDate,endDate,onHolidays,totalHoursReported,round(float(totalHoursProjects),2),round(float(usageTotalSoftware),2),round(float(totalHoursTasks),2),))
        #rc = cur.execute( sql)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la insercion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=notAddedToGestionTiempo \n\n"
        sys.exit() 
    #Ahora necesitamos recoger el idGestionTiempo de la anterior transaccion para utilizarlo en las demas tablas (tiempoEnProyectos2,tiempoEnSoftware2,tiempoEnTareas2)
    sql2="select max(idGestionTiempo) from gestionTiempo2 WHERE idPersonal= "+str(idPersonal)
    #print sql2
    try:
        cur.execute(sql2)
        row2 = cur.fetchone()
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la seleccion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=badSelectSentence \n\n"
        DB_CONN.close()
        sys.exit()
    #En row2[0]tenemos el idGestionTiempo que acabamos de insertar y que utilizaremos de ahora en adelante para las siguientes inserciones.
    idGestionTiempo=row2[0]
    #print str(idGestionTiempo)+"<br>"
    ########################################################INSERTAMOS HORAS ASOCIADAS A PROYECTOS##########################################
    #La informacion la tenemos en el arrayProjectsReported:
    #print arrayProjectsReported
    for indice in arrayProjectsReported:
        diccionario = arrayProjectsReported[indice]
        #print str(diccionario)+"<br>"
        #recogemos los datos
        #ya tenemos el idGestionTiempo para relacionarlo con la tabla gestionTiempo
        idProyecto=diccionario["idProyecto"]
        hours=diccionario["horas"]
        otherProjectTask=diccionario["otros"]
        #Pasamos otherProjectTask a unicode para enviarlos a la base de datos
        otherProjectTask = unicode( otherProjectTask, "utf-8" )
        #Creamos la sentencia sql
        sql3="insert into tiempoEnProyectos2 (idTiempoProyecto,idGestionTiempo,idProyecto,otherProjectTask,hours)values(NULL,%s,%s,%s,%s)"
	try:
            rc3 = cur.execute( sql3,(int(idGestionTiempo),int(idProyecto),otherProjectTask,hours,))
            #rc3 = cur.execute( sql3)
        except MySQLdb.OperationalError, e:
            #redireccionar a pagina de error en la insercion y sys.exit() de este script
            print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=notAddedToTiempoEnProyectos&errorCode="+str(idGestionTiempo)+" \n\n"
            sys.exit()
            
    ########################################################INSERTAMOS HORAS ASOCIADAS A SOFTWARE##########################################
    #Tenemos los datos recuperados mas arriba. Pasamos a unicode utf-8 el campo  otherApplicationEspecified (viene como usageOtherApplications)
    usageOtherApplications = unicode( usageOtherApplications, "utf-8" )
    #Creamos la sentencia de insercion sql:
    sql4="insert into tiempoEnSoftware2 (idTiempoSoftware,idGestionTiempo,babelomicsGepas,biocreativeMetaserver,blast2go,cargo,dnalive,fatigo,forensicator,funcut,geneId,intoGen,iwwem,jorca,karyotypeViewer,methylizer,mobyMiner,model,mowserv,nemusTools,pmut,pupaSuite,side,sysnp,variantSplicing,visualGenomics,externalWebApplication,otherApplications,otherApplicationEspecified)values(NULL,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    #print str(sql4)+"<br>"
    try:
        rc4 = cur.execute( sql4,(int(idGestionTiempo),round(float(usageBabelomicsGepas),2),round(float(usageBiocreativeMetaserver),2),round(float(usageBlast2Go),2),round(float(usageCargo),2),round(float(usageDnalive),2),round(float(usageFatiGo),2),round(float(usageForensicator),2),round(float(usageFunCut),2),round(float(usageGeneId),2),round(float(usageIntogen),2),round(float(usageIwwem),2),round(float(usageJorca),2),round(float(usageKaryotypeViewer),2),round(float(usageMethylizer),2),round(float(usageMobyMiner),2),round(float(usageModel),2),round(float(usageMowServ),2),round(float(usageNemusTools),2),round(float(usagePmut),2),round(float(usagePupaSuite),2),round(float(usageSide),2),round(float(usageSysnp),2),round(float(usageVariantSplicing),2),round(float(usageVisualGenomics),2),round(float(usageExternalWebApplication),2),round(float(usageOther),2),usageOtherApplications,))
        #rc4 = cur.execute( sql4)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la insercion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=notAddedToTiempoEnProyectos&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
        
    ########################################################INSERTAMOS HORAS ASOCIADAS A TASKS##########################################
    #Tenemos los datos recuperados mas arriba. Pasamos a unicode utf-8 el campo  otherTasksEspecify
    otherTasksEspecify = unicode( otherTasksEspecify, "utf-8" )
    #Creamos la sentencia de insercion sql:
    sql5="insert into tiempoEnTareas2 (idTiempoTarea,idGestionTiempo,timeSystemsAdministration,timeDataAnalysisMicroarray,timeDataFunctionalAnnotation,timeDataSimulations,timeDataNextGenSequencing,timeGenomeComparison,timeGenomeProteomeAnnotation,timeOtherData,timeTrainingGiven,timeTrainingAttended,timeDevelopmentWebTools,timeDevelopmentApis,timeDevelopmentWebServices,timeDevelopmentWorkflows,timeDevelopmentDatabases,timeDevelopmentManteinance,timeTechnicalSupport,timeDocumentation,timeOtherTasks,otherTasksEspecify,timeDevelopmentDataResources)values(NULL,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    #print str(sql5)+"<br>"
    try:
        rc5 = cur.execute( sql5,(int(idGestionTiempo),timeSystemsAdministration,timeDataAnalysisMicroarray,timeDataFunctionalAnnotation,timeDataSimulations,timeDataNextGenSequencing,timeGenomeComparison,timeGenomeProteomeAnnotation,timeOtherData,timeTrainingGiven,timeTrainingAttended,timeDevelopmentWebTools,timeDevelopmentApis,timeDevelopmentWebServices,timeDevelopmentWorkflows,timeDevelopmentDatabases,timeDevelopmentManteinance,timeTechnicalSupport,timeDocumentation,timeOtherTasks,otherTasksEspecify,int(timeDevelopmentDataResources),))
        #rc5 = cur.execute( sql5)
    except MySQLdb.OperationalError, e:
        #redireccionar a pagina de error en la insercion y sys.exit() de este script
        print "Location: "+str(redirectionKOgestionTiempo)+"&from=modify&reason=notAddedToTiempoEnProyectos&errorCode="+str(idGestionTiempo)+" \n\n"
        sys.exit()
    
    #Redireccionamos a mensaje de modificacion con exito
    print "Location: "+str(redirectionOKgestionTiempo)+"&from=modify \n\n"
    sys.exit()