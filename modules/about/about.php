<?php
/**
 *  This file is part of devMX TeamSpeak3 Webviewer.
 *  Copyright (C) 2011 - 2012 Max Rath and Maximilian Narr
 *
 *  devMX TeamSpeak3 Webviewer is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  TeamSpeak3 Webviewer is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with devMX TeamSpeak3 Webviewer.  If not, see <http://www.gnu.org/licenses/>.
 */

class about extends ms_Module
{
    
    protected $sent = FALSE;
    
    function init()
    {
        $this->mManager->loadModule("jQueryUI");
    }
    
    function getText() {
        return '<span class="ui-state-highlight" style="padding:5px; font-size:10px; margin-bottom:5px; float:right;">Powered by <a href="http://devmx.de/en/software/teamspeak3-webviewer"  target="_blank">devMX Webviewer</a></span>';
    }
    
    function onBeforeTabs() {
        
            return $this->getText();
        
    }
    
    function getFooter() {
        if(!$this->mManager->moduleIsLoaded('infoTab')) {
            return $this->getText();
        }
    }

}

?>
