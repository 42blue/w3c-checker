<?

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

class validatorCheck {

    protected static $validatorURL = 'http://validator.w3.org/check?uri=';    
    protected static $start        = 'X-W3C-Validator-Errors: '; 
    protected static $end          = 'X-W3C-Validator-Warnings'; 


    public function  __construct () {
      //$this->url = "42blue.de";    
        $this->url = $_GET["url"];
        $this->makeW3cRequest();
        $this->parseW3cResponse();

    }


    public function getResponse() {    

      $this->getIcon();
      
		echo '<table>'; 
		echo   '<tr>'; 
		echo     '<th>Check</th>'; 
		echo     '<th>Resultat</th>'; 
		echo     '<th class="third">#</th>';  
		echo   '</tr>'; 
		echo   '<tr>'; 
		echo     '<td class="first">Quelltext</td>'; 
		echo     '<td class="second">' . $this->validatorResult .' '. $this->validatorErrors .'.<br /><small><a href="'. $this->w3cRequest .'" rel="external">Link zum W3C Validator</a></small></td>'; 
		echo     '<td class="third">'; 
		echo       $this->icon; 
		echo     '</td>'; 
		echo   '</tr>';             
		echo '</table>';

    }


    protected function makeW3cRequest() {      
    
      // URL mit / ohne HTTP
      if (strpos($this->url, 'http://')!==false or strpos($this->url, 'https://')!==false) {
        $this->url = $this->url;
      } else {
        $this->url = 'http://'.$this->url;
      }
 
      $this->w3cRequest = self::$validatorURL . $this->url;  

      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_URL,$this->w3cRequest);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HEADER, true);
      curl_setopt($ch, CURLOPT_NOBODY, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
      curl_setopt($ch, CURLOPT_REFERER,''); 
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); 
      curl_setopt($ch, CURLOPT_TIMEOUT,15);

      $this->response = curl_exec($ch);     

      if (curl_errno($ch)) {
         //print curl_error($ch);
         $this->response = 'abort';       
      } else {
         curl_close($ch);
      }   
      
    }


    protected function parseW3cResponse() {
    
      if ((stripos($this->response, 'abort')) === false) {  
        if ((stripos($this->response, 'invalid')) == true) {  
  	      $this->validatorResult = 'Nicht valide.';
          $this->validatorErrors = substr($this->response, strlen(self::$start)+strpos($this->response, self::$start), (strlen($this->response) - strpos($this->response, self::$end))*(-1));     
          $this->validatorErrors .= ' Fehler';
          $this->validatorGfx = false;
        } else {  
          $this->validatorResult = 'Ist valide.';
          $this->validatorErrors = '0 Fehler';
          $this->validatorGfx = true;
        }
      } else {
        $this->validatorResult = 'Abbruch:';
        $this->validatorErrors = 'URL ungültig / Dienst nicht erreichbar';
        $this->validatorGfx = false;
      }
      
    }


    protected function getIcon() {
    
      if ($this->validatorGfx == true) {
        $this->icon = '<img src="http://www.42blue.de/onpage-seo-check/util/good.png" alt="good" />';  
      } else {  
        $this->icon = '<img src="http://www.42blue.de/onpage-seo-check/util/error.png" alt="error" />';  
      } 
      
    }
    

}

$w3cCheck = new validatorCheck();
$w3cCheck->getResponse();

?>

