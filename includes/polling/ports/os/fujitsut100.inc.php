<?php
/**
 * fujitsut100.inc.php
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @link       https://www.librenms.org
 *
 * @copyright  2021 Beanfield Technologies Inc.
 * @author     Jeremy Ouellet <jouellet@beanfield.com>
 */

$ethernet = snmpwalk_group($device, 'ethernetOper-status', 'FSS-ETHERNET-INTERFACE'); /*6 ports*/
$och = snmpwalk_group($device, 'ochOper-status', 'FSS-OPTICAL-CHANNEL-INTERFACE'); /*2 ports*/
$otu = snmpwalk_group($device, 'otuOper-status', 'FSS-OTN-OTU-INTERFACE'); /*4 ports*/
$ifNames = snmpwalk_group($device, 'ifName', 'ALL');

$names = [];
foreach ($ifNames as $ifName => $value) {
    $names[$ifName] = $value['ifName'];
}

$fss_ports = [];

//This is for the two managment interfaces and the multilane ports (C1,C2,C3,C4)
foreach ($ethernet as $index => $port) {
    $fss_port = [];
    $fss_port['ifName'] = $names[$index];
    $fss_port['ifDescr'] = $names[$index];
    $fss_port['ifOperStatus'] = ($port['ethernetOper-status'] == 1 ? 'up' : 'down');
    $fss_port['ifAdminStatus'] = ($port['ethernetAdmin-status'] == 1 ? 'up' : 'down');
    $fss_port['ifConnectorPresent'] = $fss_port['ifOperStatus'] == 'up' ? 'true' : 'false';
    $fss_port['ifType'] = 'ethernetCsmacd';
    $fss_ports[] = $fss_port;
}

foreach ($och as $index => $port) {
    $fss_port = [];
    $fss_port['ifName'] = $names[$index];
    $fss_port['ifDescr'] = $names[$index];
    $fss_port['ifOperStatus'] = ($port['ochOper-status'] == 1 ? 'up' : 'down');
    $fss_port['ifAdminStatus'] = ($port['ochAdmin-status'] == 1 ? 'up' : 'down');
    $fss_port['ifConnectorPresent'] = $fss_port['ifOperStatus'] == 'up' ? 'true' : 'false';
    $fss_port['ifType'] = 'opticalChannel';
    $fss_ports[] = $fss_port;
}

foreach ($otu as $index => $port) {
    $fss_port = [];
    $fss_port['ifName'] = $names[$index];
    $fss_port['ifDescr'] = $names[$index];
    $fss_port['ifOperStatus'] = ($port['otuOper-status'] == 1 ? 'up' : 'down');
    $fss_port['ifAdminStatus'] = ($port['otuAdmin-status'] == 1 ? 'up' : 'down');
    $fss_port['ifConnectorPresent'] = $fss_port['ifOperStatus'] == 'up' ? 'true' : 'false';
    $fss_port['ifType'] = 'otnOtu';
    $fss_ports[] = $fss_port;
}

$port_stats = $fss_ports;
