<?php
   
   /* /////////////////////////////////////////////////////////////////////////
                           URL Class
   ///////////////////////////////////////////////////////////////////////// */
   
   class Url {
      
      // usefull variables
      public static $logged_in_pages = array('profile');
      public static $admin_pages = array();
      public static $super_pages = array();
      
      // return the current page
      public static function current_page()
      {
         return Url::page().".php";
      }
      
      // return the page we want
		public static function page()
      {
			$page = "home";
			$params = Url::uris();
			if(in_array("error", $params))
         {
				$error = $params[array_search("error",$params)+1];
				$page = "error".$error;
			}
         elseif(count($params) > 0 && substr($params[0],0,1) != "?")
         {
				$page = $params[0];
			}
         // echo $page;
         // exit;
			return $page;
		}

      // return the server name or the domain of the website
      public static function domain()
      {
         return $_SERVER['SERVER_NAME'];
      }
      
      // return a base path link to the domain
      public static function base()
      {
         return "http://".$_SERVER['SERVER_NAME'].DS;
      }
      
      // return a full path link to the domain
      public static function full()
      {
         return "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
      }
      
      // return the server uri
      public static function uri()
      {
         return $_SERVER['REQUEST_URI'];
      }
      
      // return the GET string
      public static function gets()
      {
         $uri = Url::uri();
         $gets = substr($uri,strpos($uri,'?'));
         return (strlen($gets) > 1) ? $gets : false;
      }
      
      // return the domain name without the extension
      public static function prefix()
      {
         $domain = Url::domain();
         $prefix = substr($domain,0,strpos($domain,'.'));
         return $prefix;
      }
      
      // return the uri parameters
      public static function uris()
      {
         $uri = Url::uri();
         $url_params = explode("/",$uri); 
         $params = array();
         foreach($url_params as $param)
         {
            if(!empty($param))
            {
               $params[] = strtolower($param);
            }
         }
         return $params;
      }
      
	}
	
	// Url::authenticate();
	
?>