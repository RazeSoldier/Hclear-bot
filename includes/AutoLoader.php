<?php
/**
 * Class autoloader
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

/* @var $gClassList array 全局变量，包含项目所有的类 */
$gClassList = require_once APP_PATH . '/includes/ClassList.php';

function classLoader($className) {
	global $gClassList;

	$wantLoadClass = str_replace( 'HclearBot\\', null, $className );

	if ( !isset( $gClassList[$wantLoadClass] ) ) {
		return false;
	}

	require $gClassList[$wantLoadClass];
	return true;
}

spl_autoload_register( 'classLoader' );