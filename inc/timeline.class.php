<?php

class Timeline
{
	private $_session;  
	private $_bdd;

	public function __construct($session,PDO $bdd){
		$this->_session = $session;
		$this->_bdd = $bdd;
	}

	public function buildTimeline(){
		$timeline = array();

		$getVotes = $this->_bdd->query("SELECT * FROM tovote WHERE validity = 1 ORDER BY datecreated DESC");
		if($getVotes->rowCount()>0){
			while($d = $getVotes->fetch()){
				if($d['type'] == "0"){
					$participants = json_decode($d['whosvoting'],1);
					if(in_array($this->_session['email'], $participants)){
						$timeline[] = $d;
					}
				}
				if($d['type'] == "1"){
					$timeline[] = $d;
				}
				if($d['type'] == "0+"){
					$groups = json_decode($d['whosvoting'],1);
					if(isset($this->_session['groups'])){
						$mygroups = json_decode($this->_session['groups'],1);
					}else{
						$mygroups = array();
					}
					

					$match = 0;
					foreach ($groups as $g) {
						if(in_array($g, $mygroups)){
							$match = 1;
						}
					}
					if($match==1){
						$timeline[] = $d;
					}
				}
			}
			return $timeline;
		}else{
			return array();
		}
	}
}


?>