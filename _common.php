<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Clearbricks.
# Copyright (c) 2006 Olivier Meunier and contributors.
# All rights reserved.
#
# Clearbricks is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# Clearbricks is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Clearbricks; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****

require dirname(__FILE__).'/common/_main.php';

# Database Abstraction Layer
$__autoload['dbLayer']		= dirname(__FILE__).'/dblayer/dblayer.php';

# Files Manager
$__autoload['filemanager']	= dirname(__FILE__).'/filemanager/class.filemanager.php';
$__autoload['fileItem']		= dirname(__FILE__).'/filemanager/class.filemanager.php';

# Feed Reader
$__autoload['feedParser']	= dirname(__FILE__).'/net.http.feed/class.feed.parser.php';
$__autoload['feedReader']	= dirname(__FILE__).'/net.http.feed/class.feed.reader.php';

# HTML Filter
$__autoload['htmlFilter']	= dirname(__FILE__).'/html.filter/class.html.filter.php';

# Image Manipulation Tools
$__autoload['imageMeta']		= dirname(__FILE__).'/image/class.image.meta.php';
$__autoload['imageTools']	= dirname(__FILE__).'/image/class.image.tools.php';

# Send Mail Utilities
$__autoload['mail']			= dirname(__FILE__).'/mail/class.mail.php';

# Send Mail Through Sockets
$__autoload['socketMail']	= dirname(__FILE__).'/mail/class.socket.mail.php';

# HTML Pager
$__autoload['pager']		= dirname(__FILE__).'/pager/class.pager.php';

# REST Server
$__autoload['restServer']	= dirname(__FILE__).'/rest/class.rest.php';
$__autoload['xmlTag']		= dirname(__FILE__).'/rest/class.rest.php';

# Database PHP Session
$__autoload['sessionDB']		= dirname(__FILE__).'/session.db/class.session.db.php';

# Simple Template Systeme
$__autoload['template']		= dirname(__FILE__).'/template/class.template.php';

# URL Handler
$__autoload['urlHandler']	= dirname(__FILE__).'/url.handler/class.url.handler.php';

# Wiki to XHTML Converter
$__autoload['wiki2xhtml']	= dirname(__FILE__).'/text.wiki2xhtml/class.wiki2xhtml.php';

# SQL Batch on XML Files
$__autoload['xmlsql']		= dirname(__FILE__).'/xmlsql/class.xmlsql.php';

# Common Socket Class
$__autoload['netSocket']		= dirname(__FILE__).'/net/class.net.socket.php';

# HTTP Client
$__autoload['netHttp']		= dirname(__FILE__).'/net.http/class.net.http.php';
$__autoload['HttpClient']	= dirname(__FILE__).'/net.http/class.net.http.php';

# Incutio XML/RPC Server
$__autoload['IXR_Value']				= dirname(__FILE__).'/ext/incutio.ixr_library.php';
$__autoload['IXR_Message']			= dirname(__FILE__).'/ext/incutio.ixr_library.php';
$__autoload['IXR_Server']			= dirname(__FILE__).'/ext/incutio.ixr_library.php';
$__autoload['IXR_Request']			= dirname(__FILE__).'/ext/incutio.ixr_library.php';
$__autoload['IXR_Client']			= dirname(__FILE__).'/ext/incutio.ixr_library.php';
$__autoload['IXR_Error']				= dirname(__FILE__).'/ext/incutio.ixr_library.php';
$__autoload['IXR_Date']				= dirname(__FILE__).'/ext/incutio.ixr_library.php';
$__autoload['IXR_Base64']			= dirname(__FILE__).'/ext/incutio.ixr_library.php';
$__autoload['IXR_IntrospectionServer']	= dirname(__FILE__).'/ext/incutio.ixr_library.php';
$__autoload['IXR_ClientMulticall']	= dirname(__FILE__).'/ext/incutio.ixr_library.php';
?>