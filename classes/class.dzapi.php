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
require 'class.cache.php';


class dzapi{

	private static $api_url 	 = "http://api-v3.deezer.com/1.0/";
	private static $current_url =  '';
	
	public static $result 	 	=  false;
	public static $error 		=  false;
	
	
	//Constructor to set API URL
	
	public function __construct(){
	
		self::$current_url = self::$api_url;	
		
	}
	
	
   /**
    * @function  public, static  search          # Search in Deezer Simple API
    *
    * @param       string                  $query                  		   # Query String
    * @param       string                  $type		                   # Search Filter (track, artist, album)
    * @param       int                     $start              			   # Position where the search starts
    * @param       int                     $limit               		   # Number of items
    *
    * @return      object array or false
    **/


	public static function search( $query, $type = 'track', $start = 0, $limit = 10 ) {
	
		if(isset($query) && !empty($query) && isset($type) && isset($start) && isset($limit)){
			
			self::$current_url = self::$api_url;
		
			switch($type){
				
				case 'artist':
					self::$current_url .= 'search/artist/?q='.$query.'&index='.$start.'&limit='.$limit.'&output=json';
				break;
				
				case 'album':
					self::$current_url .= 'search/album/?q='.$query.'&index='.$start.'&limit='.$limit.'&output=json';
				break;
				
				case 'track':
					self::$current_url .= 'search/track/?q='.$query.'&index='.$start.'&limit='.$limit.'&output=json';
				break;
				
				default:
					self::$current_url .= 'search/track/?q='.$query.'&index='.$start.'&limit='.$limit.'&output=json';
				break;

			}
			
				self::getRemoteData();
		}
		
	
		return self::$result;
		
		
	}
	
	 /**
    * @function  public, static  lookup          						   # Lookup method to get information about artist, album or track with id
    *
    * @param       int	                   $element_id            		   # Element id : artist_id, album_id, track_id
    * @param       string                  $type		                   # Lookup Filter (track, artist, album)
    * @param       bool                    $options              		   # Activate options for lookup : True or False 
    *
    * @return      object array or false
    **/

	
	public static function lookup( $elment_id, $type, $options = false ) {
		
		if(isset($elment_id) && !empty($elment_id) && isset($type) && isset($options)){

			switch($type){
				
				case 'artist':
					//options : similar_artists,discography,discography_details
					if($options == false){
						self::$current_url .= 'lookup/artist/?id='.$elment_id.'&output=json';
					}else{
						self::$current_url .= 'lookup/artist/?id='.$elment_id.'&options=similar_artists,discography,discography_details&output=json';
						
					}
				break;
				
				case 'album':
					self::$current_url .= 'lookup/album/?id='.$elment_id.'&index='.$start.'&limit='.$limit.'&output=json';
				break;
				
				case 'track':
					//options : tracks
					
					if($options == false){
						self::$current_url .= 'lookup/track/?id='.$elment_id.'&output=json';
					}else{
						self::$current_url .= 'lookup/track/?id='.$elment_id.'&options=tracks&output=json';		
					}
					
				break;
				
				default:
					self::$current_url .= 'lookup/track/?id='.$elment_id.'&index='.$start.'&limit='.$limit.'&output=json';
				break;

			}
			
				self::getRemoteData();
		}
		
			return self::$result;

	}
	
	 /**
    * @function  private, static  getRemoteData          			 	   # Private function to get remote data with cURL
    *
    * @return    bool
    **/
	
	private static function getRemoteData(){
		
			$memcache = new cache;
			
			$cacheKey = md5(self::$current_url);
			
			if($memcache->active == true){
			
				$cached   = $memcache->getKey( $cacheKey );
		
			}else{
	
				$cached = false;	
			}
			
		
			if($cached != false){
		
				self::$result = $cached;
				
				return true;
			
			}else{
		
				$rCurl = curl_init();
				
				curl_setopt($rCurl, CURLOPT_URL, self::$current_url);
				curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, true);
			
				$data = curl_exec($rCurl);
	
				$error_no = curl_errno($rCurl);
				curl_close($rCurl);		
				
				self::$current_url = self::$api_url;

				
				if($error_no != 0){
					
					self::$result = false;

					return false;
					
				}else{
					
					$data_decode = json_decode($data);
					
					if(isset($data_decode->errors)){
				
						self::$error = $data_decode->errors;
						self::$result = false;
					
						return false;
						
					}else{
				
						self::$result = $data_decode;
						if($memcache->active == true){
							$memcache->setKey($cacheKey,  $data_decode);
						}
				
						return true;
			
					}
				}
			
			}
			
			
	}


}




?>