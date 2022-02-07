<?php

/**
 * junos.inc.php
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
 * @copyright  2022 Beanfield Technologies Inc
 * @author     Jeremy Ouellet <jouellet@beanfield.com>
 */

$ifAliases = snmpwalk_group($device, 'mcrUserDefinedModuleName.1', 'PERLE-MCR-MGT-MIB');
foreach ($port_stats as $index => $port_stat) {
    preg_match_all('!\d+!', $port_stat['ifDescr'], $matches);
    if (count($matches[0]) < 2) {
        continue;
    }
    $slot = $matches[0][0];
    $port = $matches[0][1];
    $port_stats[$index]['ifAlias'] = $ifAliases['1']['mcrUserDefinedModuleName'][strval($slot)];
}
