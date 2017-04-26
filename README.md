# zabbix-migateway
Zabbix plugin for reading the data from Xiaomi smarthome gateway

## Requirement
1. Linux host that can reach xiaomi gateway. (By default it uses UDP port 9898) with PHP 5.4 or above installed.
2. Xiaomi home gateway with the local network function enabled. (See [instruction](https://louiszl.gitbooks.io/lumi-gateway-local-api/content/device_discover.html)) * note if you can't find 4th menu item in step 3, please tap the version number at the bottom of that screen about 5-7 times.

## Usage
1. Edit the file `migateway.conf` to point to script path and IP address of Xiaomi home gateway.
2. Test if the local network works by run the command below. Please replace the IP address and port to your Xiaomi home gateway IP address.
```
./migateway.php 192.168.1.58 9898 discovery
```
3. Should you add an item in the zabbix according to your devices. I do not recommended you to use discovery as there's no descriptive name for each device in the discovered data. You may get the available data of each sensor by running the command below. Please make sure to replace <sid> with your device's sid found by discovery command above.
```
./migateway.php 192.168.1.58 9898 dump <sid>
```
The item key will be `mihome.read[<sid>, <key>]` e.g. `mihome.read[158d000156f920, load_power]` for my smart plug's load power data.
