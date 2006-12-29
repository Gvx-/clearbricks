<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Clearbricks.
# Copyright (c) 2006 Olivier Meunier and contributors. All rights
# reserved.
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

class http
{
	/**
	@function getHost
	
	Return current scheme, host and port.
	*/
	public static function getHost()
	{
		$server_name = explode(':',$_SERVER['HTTP_HOST']);
		$server_name = $server_name[0];
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
		{
			$scheme = 'https';
			$port = ($_SERVER['SERVER_PORT'] != '443') ? ':'.$_SERVER['SERVER_PORT'] : '';
		}
		else
		{
			$scheme = 'http';
			$port = ($_SERVER['SERVER_PORT'] != '80') ? ':'.$_SERVER['SERVER_PORT'] : '';
		}
		
		return $scheme.'://'.$server_name.$port;
	}
	
	/**
	@function getSelfURI
	
	Returns current URI with full hostname
	*/
	public static function getSelfURI()
	{
		return self::getHost().$_SERVER['REQUEST_URI'];
	}
	
	/**
	@function redirect
	
	Performs a conforming HTTP redirect for a relative URL.
	
	@param page	string		Relative URL
	*/
	public static function redirect($page)
	{
		if (preg_match('%^http[s]?://%',$page))
		{
			$redir = $page;
		}
		else
		{
			$host = self::getHost();
			$dir = dirname($_SERVER['PHP_SELF']);
			
			if (substr($page,0,1) == '/') {
				$redir = $host.$page;
			} else {
				if (substr($dir,-1) == '/') {
					$dir =  substr($dir,0,-1);
				}
				$redir = $host.$dir.'/'.$page;
			}
		}
		
		# Close session if exists
		if (session_id()) {
			session_write_close();
		}
		
		header('Location: '.$redir);
		exit;
	}
	
	/**
	@function concatURL
	
	Appends a path to a given URL. If path begins with "/" it will replace the
	original URL path.
	
	@param url	string		URL
	@param path	string		Path to append
	@return string
	*/
	public static function concatURL($url,$path)
	{
		if (substr($path,0,1) != '/') {
			return $url.$path;
		}
		
		return preg_replace('#^(.+?//.+?)/(.*)$#','$1'.$path,$url);
	}
	
	/**
	@function realIP
	
	Returns the real client IP (or tries to do its best)
	Taken from http://uk.php.net/source.php?url=/include/ip-to-country.inc
	
	@return string
	*/
	public static function realIP()
	{
		# No IP found (will be overwritten by for
		# if any IP is found behind a firewall)
		$ip = FALSE;
		
		# If HTTP_CLIENT_IP is set, then give it priority
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		
		# User is behind a proxy and check that we discard RFC1918 IP addresses
		# if they are behind a proxy then only figure out which IP belongs to the
		# user.  Might not need any more hackin if there is a squid reverse proxy
		# infront of apache.
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			
			# Put the IP's into an array which we shall work with shortly.
			$ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
			
			for ($i = 0; $i < count($ips); $i++)
			{
				# Skip RFC 1918 IP's 10.0.0.0/8, 172.16.0.0/12 and
				# 192.168.0.0/16 -- jim kill me later with my regexp pattern
				# below.
				if (!preg_match('/^(10|172\.16|192\.168)\./',$ips[$i])) {
					if (ip2long($ips[$i]) != false) {
						$ip = $ips[$i];
						break;
					}
				}
			}
		}
		
		# Return with the found IP or the remote address
		if ($ip) {
			return $ip;
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		} else {
			return null;
		}
	}
	
	/**
	@function getAcceptLanguage
	
	Returns a two letters language code take from HTTP_ACCEPT_LANGUAGE.
	
	@return string
	*/
	public static function getAcceptLanguage()
	{
		$dlang = '';
		if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$acclang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$L = explode(';', $acclang[0]);
			$dlang = substr(trim($L[0]),0,2);
		}
		
		return $dlang;
	}
	
	/**
	@function cache
	
	Sends HTTP cache headers (304) according to a list of files and an optionnal
	list of timestamps.
	
	@param files		array		Files on which check mtime
	@param mod_ts		array		List of timestamps
	*/
	public static function cache($files,$mod_ts=array())
	{
		if (empty($files) || !is_array($files)) {
			return;
		}
		
		array_walk($files,create_function('&$v','$v = filemtime($v);'));
		
		$array_ts = array_merge($mod_ts,$files);
		
		rsort($array_ts);
		$ts = $array_ts[0];
		
		$since = NULL;
		if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$since = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
			$since = preg_replace ('/^(.*)(Mon|Tue|Wed|Thu|Fri|Sat|Sun)(.*)(GMT)(.*)/', '$2$3 GMT', $since);
			$since = strtotime($since);
		}
		
		# Common headers list
		$headers[] = 'Last-Modified: '.gmdate('D, d M Y H:i:s',$ts).' GMT';
		$headers[] = 'Cache-Control: must-revalidate, max-age=0';
		$headers[] = 'Pragma:';
		
		if ($since >= $ts)
		{
			self::head(304,'Not Modified');
			foreach ($headers as $v) {
				header($v);
			}
			exit;
		}
		else
		{
			header('Date: '.gmdate('D, d M Y H:i:s').' GMT');
			foreach ($headers as $v) {
				header($v);
			}
		}
	}

	/**
	@function etag
	
	Sends HTTP cache headers (304) according to a list of etags in client request
	
	@param p_content	string		Response page content
	*/
	public static function etag()
	{
		# We create an etag from all arguments
		$args = func_get_args();
		if (empty($args)) {
			return;
		}
		
		$etag = md5(implode('',$args));
		unset($args);
		
		header('ETag: "'.$etag.'"');
		
		# Do we have a previously sent content?
		if (!empty($_SERVER['HTTP_IF_NONE_MATCH']))
		{
			foreach (explode(',',$_SERVER['HTTP_IF_NONE_MATCH']) as $i)
			{
				if (stripslashes(trim($i)) == $etag) {
					self::head(304,'Not Modified');
					exit;
				}
			}
		}
	}
	
	/**
	@function head
	
	Sends an HTTP code and message to client
	
	@param code	string		HTTP code
	@param msg	string		Message
	*/
	public static function head($code,$msg)
	{
		$status_mode = preg_match('/cgi/',php_sapi_name());
		
		if ($status_mode) {
			header('Status: '.$code.' '.$msg);
		} else {
			if (version_compare(phpversion(),'4.3.0','>=')) {
				header($msg, TRUE, $code);
			} else {
				header('HTTP/1.x '.$code.' '.$msg);
			}
		}
	}
	
	/**
	@function trimRequest
	
	Trims every value in GET, POST, REQUEST and COOKIE vars.
	Removes magic quotes if magic_quote_gpc is on.
	*/
	public static function trimRequest()
	{
		if(!empty($_GET)) {
			array_walk($_GET,array('self','trimRequestHandler'));
		}
		if(!empty($_POST)) {
			array_walk($_POST,array('self','trimRequestHandler'));
		}
		if(!empty($_REQUEST)) {
			array_walk($_REQUEST,array('self','trimRequestHandler'));
		}
		if(!empty($_COOKIE)) {
			array_walk($_COOKIE,array('self','trimRequestHandler'));
		}
	}
	
	private static function trimRequestHandler(&$v,$key)
	{
		$v = self::trimRequestInVar($v);
	}
	
	private static function trimRequestInVar($value)
	{
		if (is_array($value))
		{
			$result = array();
			foreach ($value as $k => $v)
			{
				if (is_array($v)) {
					$result[$k] = self::trimRequestInVar($v);
				} else {
					if (get_magic_quotes_gpc()) {
						$v = stripslashes($v);
					}
					$result[$k] = trim($v);
				}
			}
			return $result;
		}
		else
		{
			if (get_magic_quotes_gpc()) {
				$value = stripslashes($value);
			}
			return trim($value);
		}
	}
	
	/**
	@function unsetGlobals
	
	If register_globals is on, removes every GET, POST, COOKIE, REQUEST, SERVER,
	ENV, FILES vars from GLOBALS.
	*/
	public static function unsetGlobals()
	{
		if (!ini_get('register_globals')) {
			return;
		}
		
		if (isset($_REQUEST['GLOBALS'])) {
			throw new Exception('GLOBALS overwrite attempt detected');
		}
		
		# Variables that shouldn't be unset
		$no_unset = array('GLOBALS','_GET','_POST','_COOKIE','_REQUEST',
		'_SERVER','_ENV','_FILES');
		
		$input = array_merge($_GET,$_POST,$_COOKIE,$_SERVER,$_ENV,$_FILES,
				(isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array()));
		
		foreach ($input as $k => $v) { 
			if (!in_array($k,$no_unset) && isset($GLOBALS[$k]) ) {
				unset($GLOBALS[$k]);
			}
		}
	}
}
?>