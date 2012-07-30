#!/usr/bin/python
DB_PERSONAL="personal"
def initConfig():
    initfile = open("config.txt", "r")
    for line in initfile:
        if line.split("=")[0] == 'DB_READ_USER':
            DB_READ_USER = line.split("=")[1].replace('\n','')
        if line.split("=")[0] == 'DB_READ_PWD':
            DB_READ_PWD = line.split("=")[1].replace('\n','')
        if line.split("=")[0] == 'DB_WRITE_USER':
            DB_WRITE_USER = line.split("=")[1].replace('\n','')
        if line.split("=")[0] == 'DB_WRITE_PWD':
            DB_WRITE_PWD = line.split("=")[1].replace('\n','')
        if line.split("=")[0] == 'DB_JABBA_DB':
            DB_JABBA_DB = line.split("=")[1].replace('\n','')
        if line.split("=")[0] == 'HOME_URL':
            HOME_URL = line.split("=")[1].replace('\n','')
        if line.split("=")[0] == 'DB_HOST':
            DB_HOST = line.split("=")[1].replace('\n','')

    initfile.close()
    return DB_READ_USER, DB_READ_PWD, DB_WRITE_USER, DB_WRITE_PWD, DB_JABBA_DB, HOME_URL, DB_HOST