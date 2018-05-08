<?php
/**
 * Api class that used to query action=query
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

namespace HclearBot;

abstract class ApiQuery extends ApiBase {
    protected $action = 'query';

    /**
     * @var string|null Which properties to get for the queried pages
     */
    protected $prop;

    /**
     * @var string|null Which lists to get
     */
    protected $list;

    /**
     * @var array|null A list of titles to work on
     */
    protected $titles;

    /**
     * @var array|null A list of page IDs to work on.
     */
    protected $pageids;

    /**
     * @var array|null A list of revision IDs to work on
     */
    protected $revids;

    protected function setProp(string $prop) {
        $this->prop = $prop;
    }

    protected function setList(string $list) {
        $this->list = $list;
    }

    protected function setTitles(array $titles) {
        $this->titles = $titles;
    }

    protected function setPageids(array $pageids) {
        $this->pageids = $pageids;
    }

    protected function setRevids(array $revids) {
        $this->revids = $revids;
    }
}