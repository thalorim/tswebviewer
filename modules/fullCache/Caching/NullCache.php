<?php

/**
 * This file is part of devMX TS3 Webviewer Lite.
 * Copyright (C) 2012 Maximilian Narr
 *
 * devMX Webviewer Lite is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TeamSpeak3 Webviewer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with devMX TS3 Webviewer Lite. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *
 * @author drak3
 */
class NullCache implements CachingInterface
{

    public function cache($key, $data)
    {
        
    }

    public function flush($key)
    {
        
    }

    public function flushCache()
    {
        
    }

    public function getCache($key)
    {
        return null;
    }

    public function isCached($key)
    {
        return false;
    }

}

?>
