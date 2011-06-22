<?php

/*

   Copyright 2011 Aurélien Hérault

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.

*/


class cache{
	
	private $server 	= 'localhost';
	private $port 		= '11211';
	private $compress 	= true;
	
	private $prefix 	= 'DZAPI_';
	private $expire		= 604800; //1 week
	
	public $active		= true; //1 week
	
	public $cache;
	
	public function __construct(){
		
		include 'cache.config.php';
		
		if(DZ_CUSTOM_CONFIG == true){
		
			$this->server 	= DZ_MEMCACHE_SERVER;
			$this->port   	= DZ_MEMCACHE_PORT;
			$this->compress = DZ_MEMCACHE_COMPRESS;
			$this->prefix 	= DZ_MEMCACHE_PREFIX;
			$this->expire 	= DZ_MEMCACHE_EXPIRE;
			$this->active 	= DZ_MEMCACHE_ACTIVE;
		}
		
		$this->connect();
				
	}
	
	
	private function connect(){
		
		if( isset($this->server) && isset($this->port) && isset($this->compress)  && isset($this->prefix) && isset($this->expire) ) {
			
			if(class_exists(Memcache)){
			
				$this->cache = new Memcache;
				
				$status = $this->cache->connect($this->server, $this->port);
				
				if($status == false){
					$this->active = false;	
				}
				
				return $status;
				
			}else{
				
				$this->active = false;
				
				return false;	
			
			}
			
			
		}else{
			
			return false;
				
		}
		
	}
	
	public function setKey($key, $value){
	
		if( isset($key) && !empty($key) && isset($value) && !empty($value) ){
			
			if($this->compress == true){
			
				$status = $this->cache->set( $this->prefix.$key, $value, 1, $this->expire);
		
			}else{
				
				$status = $this->cache->set( $this->prefix.$key, $value, 0, $this->expire);
				
			}
		
			return $status;
			
		}else{
		
			return false;
			
		}
		
	}
	
	public function getKey($key){
		
		if( isset($key) && !empty($key) ){
			
			$data = $this->cache->get($this->prefix.$key);


			return $data;		
			
		}else{
		
			return false;	
			
		}
		
	}
	
	public function deleteKey($key){
		
		if( isset($key) && !empty($key) ){
			
			$status =  $this->cache->delete( $this->prefix.$key);
			
			return $status;
			
		}else{
		
			return false;
			
		}
		
	}
	
	public function flush(){
	
		$status =  $this->cache->flush();
		
		return $status;
		
	}
	
}



?>