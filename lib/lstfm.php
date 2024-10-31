<?php
/** 
 * @author: 	schmiddi
 * @mail:		schmiddim@gmx.at
 * @date:		10.03.2009
 * @desc:		simple last.fm class
 * 				getting a list of tracks and a status messeage with current track
 */


class lastfm{
	#############
	###Variables
	##########
	private $xml;
	
	#############################
	#is the radio on?
	public function UserListeningToMusic(){
		$result = $this->xml->xpath('//track[@nowplaying="true"]');
		$ctr=0;
		while(list( , $node) = each($result)) {
			$ctr++;
		}#while
		return $ctr;		
	}#UserListeningMusic
	
	#writes Trackinfo on the screen
	

	public function echoRecentTracks() {
		foreach($this->xml->recenttracks->track as $node){
			$this->echoTrack($node);	
		}
	
	}#echoTrack
	
	#echoes the recent tracks without the current playing one
	#arg $n number of tracks to shows
	public function echoRecentTracks_1($n) {
		
		if ($n>0){
			$ctr=0;
			foreach($this->xml->recenttracks->track as $node){
				if ($ctr>0 &&$ctr<$n)
				$this->echoTrack($node);
				else 
					;
				$ctr++;	
			}//each
		}//fi
	}#echoTrack	
	
	#display a little frame with infos about current track and online status
	public function echoCurrentTrack(){
		$track =$this->xml->recenttracks->track[0];
		#status
		if( $this->UserListeningToMusic()) 
			$img= "<a href=\"http://www.lastfm.de/user/".$this->getUserName()."\"><img src=\"wp-content/plugins/schmie_lstfm2/img/gruen.jpg\" alt=\"online\"/></a>";
		else
			$img ="<a href=\"http://www.lastfm.de/user/".$this->getUserName()."\"><img src=\"wp-content/plugins/schmie_lstfm2/img/rot.jpg\" alt=\"user offnline\"/>";
		#album cover
		if (!empty($track->image[1])) #is there an image?
			$cover ='<img src="'.$track->image[1]. '"  width="64" height="64" alt="'.$track->album.'"/>';
		#title
		if ($track->streamable)
				$title= '<a href="'.$track->url.'">'.$track->name.'</a>';
			else
				$title= $track->name.'<p>';
				
		
						echo '				
			<table>
				<tr>
					<td>'.$img.'</td>	
					<td>'.$cover.'</td>
				</tr>					
			</table>
			<table>	
				<tr>
				<td>				
				<b>Track: </b>'.$title.'<p>
				<b>Artist:</b> '.$track->artist.'<p>
				<b>Album:</b> '.$track->album.'<p>
				
				</td>			
				</tr>
			</table>
			
			';
				

	}
	
	#writes info about a single track on the screen
	private function echoTrack($track){
			#@todo fetching albumcover + info
			$album=  $track->album;
			$artist= $track->artist;
		
			if ($track->streamable)
				$title= '<a href="'.$track->url.' ">'.$track->name.'</a>';
			else
				$title= $track->name;
			#image
			if (!empty($track->image[1])) #is there an image?
				$img= '<img src="'.$track->image[1]. '"  width="64" height="64" alt="'.$track->album.'"/>';

			
			echo "
				<table>
				<tr>
				<td>a:</td><td>$artist</td>
				</tr><tr>
				<td>t:</td><td>$title</td>
				</tr>
				</table>
				<p>
			
			
			
			
			";
			
	}#echoTrack
		
	
	#param the lastfm username
	#@todo err. handling	
	public function __construct($username) {
		$command ='user.getRecentTracks';
		$apikey='&api_key=ed7775a274a0700dd80ac7ed1a76af9e';
		$url = 'http://ws.audioscrobbler.com/2.0/?method=';
		$user = "&user=$username";
		
		$order = $url.$command .$user.$apikey;
		$xml =&$this->xml;
		$ch = curl_init("$order");
		curl_getinfo($ch);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		#put it in an xml dome
		$xml = simplexml_load_string("$output");
	}#construct
	
	#returns the lastfm username
	public function getUserName(){	
		return $this->xml->recenttracks['user'];
	}#getUsername
}#class
?>