<?php
/** 
 * @brief URL helper class
 *
 * @author Gergely Aradszki "garpeer"
 * 
 * @license GPLv3
 *
 * Copyright (C) 2011  Gergely Aradszki
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    For more info, see <http://www.gnu.org/licenses/>.
 * 
 */
class URL {
    /*
     * @brief ignored query parameters
     */
    private static $ignore;
    /*
     * @brief used protocol
     */
    private static $protocol = "http";
    /*
     * @brief current url
     */
    private static $url;
    /*
     * @brief basepath
     */
    private static $basepath;
    
    private $data = Array('ignore'=>null, 'protocol'=>'http','url'=>null,'basepath'=>null);
    
    /**
     * @brief constructor
     * @param $basepath basepath (see URL::basepath)
     */
    public function __construct($basepath=false){
        self::basepath($basepath);
    }
    
    /**
     * @brief ignore given query parameters
     * 
     * @param $args args to ignore (overloaded)
     * 
     * The given parameters will be removed from the urls 
     * 
     */
    public function ignore($args){
        self::setval('ignore', func_get_args());
    }
    
    /**
     * @brief set / get protocol
     * @param $protocol
     * @return protocol 
     */
    public function protocol($protocol=false){
        if ($protocol && is_string($protocol)){
            self::setval('protocol',$protocol);
        }
        return self::getval('protocol');
    }
    
    /**
     * @brief set / get basepath
     * @param $basepath
     * @return $basepath 
     */
    public function basepath($basepath=false){
        if (is_array($basepath)){
            $basepath = implode("/",$basepath);
        }
        if ($basepath && is_string($basepath)){
            if (substr($basepath,0,1)!="/"){
                $basepath = "/".$basepath;
            }            
            if (substr($basepath,-1)!="/"){
                $basepath .= "/";
            }
            self::setval('basepath', $basepath);
        }
        if ($basepath){
            return self::get();
        }else{
            return self::getval('basepath');
        }
        
    }
    
    /**
     * @brief get canonical link
     * @param $url base url
     * @param $args show query params
     * @return string url 
     */
    public function canonical($url = false,$args = false){      
        if (!$url){
            $url = self::get();
        }
        if (!$args){
            $url = self::remove(false, $url,false);
        }
        return self::protocol() . "://" . $_SERVER['HTTP_HOST'] . $url;
    }
    
    /**
     * @brief get / set args
     * @param $arg if given, replaces all arguments with the given set
     * @param $value
     * @param $base
     * @return string on set, array on get 
     */
    public function args($arg=false, $value=false, $base=false){        
        if ($arg){
            $query = self::remove(false, $base);
            return self::add($arg, $value, $query);
        }else{
            $query = self::base($base);
            return $query['query'];
       }
    }
    
    /**
     * @brief add URL parameter
     * @param $arg key or associative array of key => value pairs
     * @param $value value
     * @param $base base url string
     * @param $update bool update current url
     * @return string url
     *
     */
    public function add($arg, $value=false, $base=false, $update=true) {        
        $query = self::base($base);
        if (is_array($arg)) {
            foreach ($arg as $key => $value) {
                $query['query'][$key] = $value;
            }
        } else {
            $query['query'][$arg] = $value;
        } 
        if ($update){
            self::update($query);
        }
        return self::get($query);
    }
    
    /**
     * @brief set url path
     * @param $path
     * @return string url 
     */
    public function path($path=false) {         
        if ($path){            
            if (func_num_args()>1){
                $args = func_get_args();
                $path = implode("/",$args);
            }else{
                if (is_array($path)){
                    $path = implode("/",$path);
                }
            }
            $query['query']=Array();
            if (substr($path,-1,1)!="/"){
                $path .="/";
            }
            $query['path'] = $path;
            self::update($query);
            return self::get($query);
        }else{            
            return self::remove(false, false ,false);
        }
    }
    /**
     * @brief set file path
     * @param $path
     * @return string url 
     */
    public function file($path=false){
        if ($path){
            if (func_num_args()>1){
                $args = func_get_args();
                $path = implode("/",$args);
            }else{
                if (is_array($path)){
                    $path = implode("/",$path);
                }
            }
            $query['query']=Array();
            if (substr($path,-1,1)=="/"){
                $path = substr($path,-1);
            }
            $query['path'] = $path;
            self::update($query);
            return self::get($query);
        }else{
            $file = pathinfo(self::get());
            return $file;
        }
    }

    /**
     * @brief remove parameters from URL
     * @param $args key or array of keys
     * @param $base base url string
     * @param $update bool update current url
     * @return string url
     */
    public function remove($args=false, $base=false, $update=true) {
        $query = self::base($base);
        if (!$args){
            $query['query'] = Array();
        }else{
            if (!is_array($args)) {
                $args = Array($args);
            }
            foreach ($args as $arg) {
                unset($query['query'][$arg]);
            }
        }
        if ($update){            
            self::update($query);
        }
        return self::get($query);
    }

    /**
     * @brief build url
     * @param $url url data array
     * @return url string
     */
    public function get($url=false) {
        if (!$url) {            
            $url = self::get_url();
        }else{    
            if (is_string($url)){
                $url = self::parse_link($url);
            }
        }
        foreach ($url['query'] as $key => $value) {
          if (is_null($value) || $value=="") {
            unset($url['query'][$key]);
          }
        }
        
        if (self::getval('ignore')){
            foreach (self::getval('ignore') as $ignore){
                unset($url['query'][$ignore]);
            }
        }
        $url = self::fix_path($url);
        $output = $url['path'];
        $query = http_build_query($url['query'], '', '&amp;');
        //$query = str_replace(Array('%5D','%5B'),Array(']','['),$query);
        if ($query) {
            $output .= "?" . $query;
        }
        return $output;
    }
    /**
     * @brief toString
     * @return sring
     */
    public function __toString() {
        return self::get();
    }
    
    /**
     * @brief set hash
     * @param $hash 
     * @param $base base url string
     * @return string url
     */
    public function hash($hash, $base=false){
        $query = self::base($base);
        $query = self::get($query);
        if ($hash!==false){
            $hash = "#".$hash;
        }
        return $query.$hash;
    }
    
    private function update($url){
        if (isset($this) && get_class($this)==__CLASS__){            
            self::setval('url',$url);
        }
    }
    
    private function setval($id, $val){
        if (isset($this) && get_class($this)==__CLASS__){           
            $this->data[$id] = $val;
        }else{
            self::$$id = $val;            
        }
    }
    
    private function getval($id){ 
        if (isset($this) && get_class($this)==__CLASS__){            
            return $this->data[$id];
        }else{
            return self::$$id;            
        }
    }
    
    /**
     * @brief get / parse base url
     * @param $base url to parse
     * @return url array 
     */
    private function base($base){
        if ($base) {
            return self::parse_link($base);
        } else {
            return self::get_url();
        }
    }

    /**
     * @brief parse url string
     * @param $link url string
     * @return $url array
     */
    private function parse_link($link) {
        $parsed = Array();
        $url = parse_url(str_replace("&amp;", "&", $link));
        $parsed['path'] = $url['path'];
        if (isset($url['query'])){
            parse_str($url['query'], $parsed['query']);
        }else{
            $parsed['query']= Array();
        }
        return $parsed;
    }
    
    /**
     * @brief get current url
     * @return url array 
     */
    private function get_url(){
        if (!self::getval('url')){  
            $url = parse_url($_SERVER['REQUEST_URI']);
            if (isset($url['query'])) {
                parse_str($url['query'], $url['query']);
            } else {
                $url['query'] = Array();
            }   
            self::setval('url',$url);
        }
        return self::getval('url');
    }
    
    /**
     * @brief fix url
     * 
     * adds trailing spaces, prepends basepath
     * @param $url array
     * @return string url
     */
    private function fix_path($url){        
        if (substr($url['path'],0,strlen(self::getval('basepath')))!=self::getval('basepath')){
            $url['path'] = self::getval('basepath') . $url['path'];
        }
        if (substr($url['path'],0,1)!="/"){
            $url['path']="/".$url['path'];
        }
        return $url;
    }    
}

?>
