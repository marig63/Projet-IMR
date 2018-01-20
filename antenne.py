#from bt_proximity import BluetoothRSSI
import time
#import bluetooth
import urllib2



#def recherche():
#    try:
#        devices = bluetooth.discover_devices(lookup_names=True)
#        return devices
#    except:
#        print "No Bluetooth devices detected"
#        return


#url = 'http://88.182.69.156/projetm2/trunk/index.php?action?=requestSignal'

#id = "BC:A8:A6:FD:03:5D"  # TODO find a way to get mac adress in python
##id = read_local_bdaddr()

#while True:
#    res = recherche()
#
#    try:
#        for addr, name in res:
#            print (addr)
#            #btrssi = BluetoothRSSI(addr=addr)
#            #print (btrssi.get_rssi())
#            print (name)
#            print(" ")

#            urll = url + "&macId=" + id + "&hardwaddr=" + addr + "&sig=" + str(20)
#            print (urll)
#            try:
#                reponse = urllib2.urlopen(urll)
#                data = reponse.read()
#                print data
#                reponse.close()
#                time.sleep(1)
#            except:
#                print "Network ERROR : sending http request failed (Check if network is alive and check if your internet is OK)"

#        print (" ")
#        print (" ")
#        time.sleep(5)
#    except:
#        print (" ")



import pexpect



url = 'http://88.182.69.156/projetm2/trunk/index.php?action?=requestSignal'

child = pexpect.spawn("bluetoothctl")

child.expect("Controller (([0-9A-Fa-f]{2}:){5}([0-9A-Fa-f]{2}))")
myaddr = child.match.group(1)
print ("Current Addr : " + myaddr)



child.send("scan on\n")

try:
    while True:
		try:
			child.expect("Device (([0-9A-Fa-f]{2}:){5}([0-9A-Fa-f]{2})) RSSI: (-[0-9]{2,3})")
			#child.expect("")
			bdaddr = child.match.group(1)
			rssi = child.match.group(4)
			
			#print child.match.group(1)
			#if bdaddr not in bdaddrs:
			#    bdaddrs.append(bdaddr)
			#print(bdaddr+" : "+rssi+"\n")
			
			urll = url + "&macId=" + myaddr + "&hardwaddr=" + bdaddr + "&sig=" + str(rssi)
			print (urll)
			
			try:
				reponse = urllib2.urlopen(urll)
				data = reponse.read()
				print data
				reponse.close()
				time.sleep(1)
			except:
				print "Network ERROR : sending http request failed (Check if network is alive and check if your internet is OK)"

			
			
		except pexpect.exceptions.TIMEOUT:
			print ("Timeout")
			child = pexpect.spawn("bluetoothctl")
			child.send("scan on\n")
			
		time.sleep(1)
			
        
except KeyboardInterrupt:
    child.close()
    results.close()


