<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	
	class SlackResponder
	{
	
		private $chatID;
	
	
		public function fetchChatID($imURL)
		{
			$imList = json_decode(file_get_contents($imURL), true);
			$imListCount = count($imList['ims']);
			
			$chatID = [];
			
			for($i = 0; $i < $imListCount; $i++)
			{
				$chatID[] = $imList['ims'][$i]['id'];
			}
			if(is_array($chatID))
			{
				$this->chatID = $chatID;
			}
			else {
				$this->chatID = NULL;
			}
			return $this->chatID;
		}
		
		public function getChatHistory($imHistoryURL) {
			$myID = NULL;
			$text = NULL;
			foreach($this->chatID AS $key => $value)
			{
				$imHistoryURLResponse = json_decode(file_get_contents($imHistoryURL.$value.'&count=1&pretty=1'), true);
				if(empty($imHistoryURLResponse['messages']))
				{
					continue;
				}
				
				$messageDate =  date('d.m.Y H:i:s', $imHistoryURLResponse['messages'][0]['ts']);
				$convertMessageDate = strtotime($messageDate);
				$mathDate = time() - $convertMessageDate;
				if($mathDate <= 0) {
					
					$callURI = $uri.'='.$this->chatID[$key].'&text='.$text.'&as_user='.$myID.'&pretty=1';
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $callURI);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$strResult=curl_exec($ch);
					curl_close ($ch);
				}
				else {
					continue;
				}
			}
		}
	}
	
	$slack = new SlackResponder;
	
	$slack->fetchChatID($imURI);
	$slack->getChatHistory($chatURI);
	
?>
