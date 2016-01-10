#!/usr/bin/env python
# DHT22 Temperature and Humidity Logging Software
import sqlite3
import os
import time
from ctypes import c_short
import Adafruit_DHT

#
# global variables
#

# Database connection
conn = None
# Database location
DBNAME='/media/databases/temphumidata.db'
# Sensor to be Used
sensor = Adafruit_DHT.DHT22
# Raspberry PI GPIO Pin used for data
pin = 18

#
# Store the Readings  in the SQLITE3 database
#
def log_temperature(temperature,humidity):
    conn=sqlite3.connect(DBNAME)
    curs=conn.cursor()
    curs.execute("INSERT INTO weather VALUES(datetime('now','localtime'), (?),(?))", (temperature,humidity))
    conn.commit()

# Main Function
# This is where the program starts 
#
def main():

        humidity, temperature = Adafruit_DHT.read_retry(sensor, pin)
        while humidity is None and temperature is None:
                time.sleep(3)
                humidity, temperature = Adafruit_DHT.read_retry(sensor, pin)
        temperature = float("{0:.1f}".format(temperature))
        humidity = float("{0:.1f}".format(humidity))
        log_temperature(temperature,humidity)

if __name__=="__main__":
    main()
