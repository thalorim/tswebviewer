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

var modules = new Array();

$(document).ready(function(){
    
    // Check for return 
    if(typeof ret != 'undefined' && ret != undefined && ret != null && ret == true)
    {
        loc = "?action=return";
        window.location.href = loc;
    }
    
    // Tabs
    $('#tabs').tabs({
        fx: {
            height: "toggle", 
            duration: "slow"
        },
        show: function(event, ui) {
            $.smoothScroll({
                scrollTarget: '#last',
                speed: 1200
            });
        }
    });
    
    // jQueryUI
    $("button, input:submit, input:button, .button").button();  
    $("input:text, input:password").addClass("ui-state-hover ui-corner-all").css("padding", "5px");

    // ********************************************************************** \\
    // Modules Start
    // ********************************************************************** \\
        
    $( "#sort1, #sort2" ).sortable({
        connectWith: ".sortable"
    }).disableSelection();
    
    modules = $('#sort1').sortable('toArray');
    
    if(document.getElementById("modules_hidden") != null)
    {               
        document.getElementById("modules_hidden").value = modules;
    }

     
    $('#sort1').bind("sortstop sortremove sortreceive", function(event,ui){
        updateModules();
    });    
    // ********************************************************************** \\
    // Modules End
    // ********************************************************************** \\
    
    
    // Display Warnings, Errors, etc.
    $(".warning, .info, .alert").delay(500).fadeIn(500).delay(4000).fadeOut(2000);
    
    // ********************************************************************** \\
    // Hiding of several fields Start
    // ********************************************************************** \\
    // Hide username and password field if login needed = false
    $("#login-needed-false").change(function(){
        if($("#login-needed-false").attr("checked") == "checked")
        {
            $("#config-username, #config-password").fadeOut(500);
        }  
    });
    
    if($("#login-needed-false").attr("checked") == "checked")
    {
        $("#config-username, #config-password").fadeOut(500);
    } 
    
    // Hide username and password field if login needed = false
    $("#login-needed-true").change(function(){
        if($("#login-needed-true").attr("checked") == "checked")
        {
            $("#config-username, #config-password").fadeIn(500);
        }  
    });
    

    // Hide imgaepack if servericons get automatically downloaded
    $("#servericons-true").change(function(){
        if($("#servericons-true").attr("checked") == "checked")
        {
            $("#imagepack-config").fadeOut(500);
        }
    });
    
    if($("#servericons-true").attr("checked") == "checked")
    {
        $("#imagepack-config").fadeOut(500);
    }
    
    // Hide imgaepack if servericons get automatically downloaded
    $("#servericons-false").change(function(){
        if($("#servericons-false").attr("checked") == "checked")
        {
            $("#imagepack-config").fadeIn(500);
        }
    });
    
// ********************************************************************** \\
// Hiding of several fields Stop
// ********************************************************************** \\
});

// Sets the requested language
function setLang(language)
{
    var lang = "index.php?action=setlang&lang=" + language;
    window.location.href = lang;
}

// Enables all modules
function enableAllModules()
{
    var modules = $("#sort2>li");
    $("#sort1").append(modules);
    updateModules();
}

// Disable all modules
function disableAllModules()
{
    var modules = $("#sort1>li")
    $("#sort2").append(modules);
    updateModules();

}

// Updates the hidden modules field
function updateModules()
{       
    modules = null;
    modules = new Array();         
    modules = $('#sort1').sortable('toArray');          
    document.getElementById("modules_hidden").value = modules;
}

// Opens the module configuration in a jQueryUI Dialog
function openModuleConfig(module)
{
    var dialogOptions = {
        minWidth: 1100, 
        minHeight: 700, 
        title: "devMX Webviewer", 
        hide: 'fade', 
        show: 'fade',
        modal: true
    }
    
    $("#module-config").dialog(dialogOptions).attr("src", 'core/xmledit.php?module=' + module);
}

