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

foreach ($pre_cache['fujitsut100'] as $slot => $slotdata) {
    foreach ($slotdata[0] as $port => $portdata) {
        if(isset($portdata['opticalPowerReceiveLane1'])){
            //Get data for the multilane cards
            $lane1rx = $portdata['opticalPowerReceiveLane1']['nearEnd']['receive']['a15-min'][0];
            $lane1tx = $portdata['opticalPowerTransmitLane1']['nearEnd']['transmit']['a15-min'][0];
            $lane2rx = $portdata['opticalPowerReceiveLane2']['nearEnd']['receive']['a15-min'][0];
            $lane2tx = $portdata['opticalPowerTransmitLane1']['nearEnd']['transmit']['a15-min'][0];
            $lane3rx = $portdata['opticalPowerReceiveLane3']['nearEnd']['receive']['a15-min'][0];
            $lane3tx = $portdata['opticalPowerTransmitLane1']['nearEnd']['transmit']['a15-min'][0];
            $lane4rx = $portdata['opticalPowerReceiveLane4']['nearEnd']['receive']['a15-min'][0];
            $lane4tx = $portdata['opticalPowerTransmitLane1']['nearEnd']['transmit']['a15-min'][0];
            genMultilane($slot, $port, $lane1rx, $lane1tx, $lane2rx, $lane2tx, $lane3rx, $lane3tx, $lane4rx, $lane4tx, $device, $valid);
        }
        elseif (isset($portdata['opticalPowerReceive'])){
            //Data for E1 and E2
            $lanerx = $portdata['opticalPowerReceive']['nearEnd']['receive']['a15-min'][0];
            $totlanerx = $portdata['totalOpticalPowerReceive']['nearEnd']['receive']['a15-min'][0];
            $lanetx = $portdata['opticalPowerTransmit']['nearEnd']['transmit']['a15-min'][0];
            genSensor($slot, $port, false, null, false, false, $lanerx, $device, $valid);
            genSensor($slot, $port, false, null, false, true, $totlanerx, $device, $valid);
            genSensor($slot, $port, false, null, true, false, $lanetx, $device, $valid);
        }
    }
}

//Generate the OID for the UI to be able to get the laser values.
function genOID($slot, $port, $multilane, $lane, $tx, $tot)
{
    if ($multilane == true) {
        $id = (11 + ($lane * 7)) + (intval($tx) * 3);
    } else {
        if ($tot) {
            $id = 10;
        } elseif ($tx) {
            $id = 13;
        } else {
            $id = 6;
        }
    }
    $oid = '.1.3.6.1.4.1.211.1.24.12.800.8.1.3.1.49.1.' . (48 + intval($slot)) . '.1.48.1.' .
        (48 + intval($port)) . '.' . $id . '.0.' . intval(! $tx) . '.1.0';

    return $oid;
}

//Generate the name of the interface
function genDesc($slot, $port, $multilane, $lane, $tx, $tot)
{
    $phyNames = ['nul', 'C1', 'C2', 'E1', 'E2', 'C3', 'C4'];
    $name = '1/' . $slot . '/0/' . $phyNames[intval($port)];
    if ($multilane) {
        $name = 'eth-' . $name . ' lane ' . $lane . ' ';
    } else {
        $name = 'och-' . $name . ' ';
    }
    if ($tx) {
        $name = $name . 'Tx Power';
    } else {
        $name = $name . 'Rx Power';
    }
    if ($tot) {
        $name = $name . ' Total';
    }

    return $name;
}

//Combine all parameters to discover the new sensor.
function genSensor($slot, $port, $multilane, $lane, $tx, $tot, $value, $device, &$valid)
{
    $name = 'opticalpower-slot' . $slot . '-port' . $port . ($multilane ? '-lane' . $lane : '') .
        ($tx ? '-tx' : '-rx') . ((! $multilane && $tot) ? '-tot' : '');
    $desc = genDesc($slot, $port, $multilane, $lane, $tx, $tot);
    $oid = genOID($slot, $port, $multilane, $lane, $tx, $tot);
    discover_sensor($valid['sensor'], 'dbm', $device, $oid, $name,
        $device['os'], $desc, 1, 1, null, null, null, null, $value);
    //echo(json_encode($valid));
}

//Just a helper function so the functio up top is clearer.
function genMultilane($slot, $port, $lane1rx, $lane1tx, $lane2rx, $lane2tx, $lane3rx, $lane3tx, $lane4rx, $lane4tx, $device, &$valid)
{
    genSensor($slot, $port, true, 1, false, false, $lane1rx, $device, $valid);
    genSensor($slot, $port, true, 1, true, false, $lane1tx, $device, $valid);
    genSensor($slot, $port, true, 2, false, false, $lane2rx, $device, $valid);
    genSensor($slot, $port, true, 2, true, false, $lane2tx, $device, $valid);
    genSensor($slot, $port, true, 3, false, false, $lane3rx, $device, $valid);
    genSensor($slot, $port, true, 3, true, false, $lane3tx, $device, $valid);
    genSensor($slot, $port, true, 4, false, false, $lane4rx, $device, $valid);
    genSensor($slot, $port, true, 4, true, false, $lane4tx, $device, $valid);
}
