<?php
namespace Application\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;
use Zend\Mail;
use Zend\xmlrpc;
use zendxml;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
//use SimpleXMLElement;
use DOMDocument;
use XSLTProcessor;


class Tools extends AbstractPlugin{


	public function createFileXml($data){
		
		$fecha_actual             = $data[0]["fecha_actual"];
		$rfcEmisor                = $data[0]["rfcEmisor"];
		$nombreEmisor             = $data[0]["nombreEmisor"];
		$calleEmisor              = $data[0]["calleEmisor"];
		$noExteriorEmisor         = $data[0]["noExteriorEmisor"];
		$coloniaEmisor            = $data[0]["coloniaEmisor"];
		$municipioEmisor          = $data[0]["municipioEmisor"];
		$estadoEmisor             = $data[0]["estadoEmisor"];
		$paisEmisor               = $data[0]["paisEmisor"];
		$codigoPostalEmisor       = $data[0]["codigoPostalEmisor"];
		
		$calleExpedidoEn          = $data[0]["calleExpedidoEn"];
		$noExteriorExpedidoEn     = $data[0]["noExteriorExpedidoEn"];
		$coloniaExpedidoEn        = $data[0]["coloniaExpedidoEn"];
		$municipioExpedidoEn      = $data[0]["municipioExpedidoEn"];
		$estadoExpedidoEn         = $data[0]["estadoExpedidoEn"];
		$paisExpedidoEn           = $data[0]["paisExpedidoEn"];
		$codigoPostalExpedidoEn   = $data[0]["codigoPostalExpedidoEn"];
		$noExteriorExpedidoEn     = $data[0]["noExteriorExpedidoEn"];
		
		$nameReceptor             = $data[0]["nameReceptor"];
		$rfcReceptor              = $data[0]["rfcReceptor"];
		$calleReceptor            = $data[0]["calleReceptor"];
		$noExteriorReceptor       = $data[0]["noExteriorReceptor"];
		$noInteriorReceptor       = $data[0]["noInteriorReceptor"];
		$coloniaReceptor          = $data[0]["coloniaReceptor"];
		$municipioReceptor        = $data[0]["municipioReceptor"];
		$estadoReceptor           = $data[0]["estadoReceptor"];
		$paisReceptor             = $data[0]["paisReceptor"];
		$codigoPostalReceptor     = $data[0]["codigoPostalReceptor"];
		$localidadReceptor        = $data[0]["localidadReceptor"];
		
		//$valor = $data[0]["valor"];
		
		$cfdi = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cfdi:Comprobante xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd" xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xs="http://www.w3.org/2001/XMLSchema" version="3.2" fecha="$fecha_actual" tipoDeComprobante="ingreso" noCertificado="" certificado="" sello="" formaDePago="Pago en una sola exhibición" metodoDePago="Transferencia Electrónica" NumCtaPago="No identificado" LugarExpedicion="México, DF." subTotal="10.00" total="11.60">
<cfdi:Emisor nombre="Envio Divino SA de CV" rfc="$rfcEmisor">
  <cfdi:RegimenFiscal Regimen="No aplica"/>
</cfdi:Emisor>
<cfdi:Receptor nombre="$nameReceptor" rfc="$rfcReceptor ">
	<cfdi:Domicilio codigoPostal="$codigoPostalReceptor" pais="México" estado="$estadoReceptor" municipio="$municipioReceptor"
            localidad="$localidadReceptor" colonia="$coloniaReceptor" noExterior="$noExteriorReceptor"
            calle="$calleReceptor"/>

</cfdi:Receptor>
<cfdi:Conceptos>
</cfdi:Conceptos>
<cfdi:Impuestos totalImpuestosTrasladados="13.60">
  <cfdi:Traslados>
    <cfdi:Traslado impuesto="IVA" tasa="16.00" importe="1.6"></cfdi:Traslado>
  </cfdi:Traslados>
</cfdi:Impuestos>
</cfdi:Comprobante>
XML;
		return $cfdi;

	}

	
	public function sellarXML($cfdi, $numero_certificado, $archivo_cer, $archivo_pem, $cadenaoriginal_3_2){
	//public function sellarXML($cfdi){

	
		$private = openssl_pkey_get_private(file_get_contents($archivo_pem));
		$certificado = str_replace(array('\n', '\r'), '', base64_encode(file_get_contents($archivo_cer)));
		$xdoc = new DomDocument();
		$xdoc->loadXML($cfdi) or die("XML invalido");

		$XSL = new DOMDocument();
		/*
		echo "<pre>";
		print_r($xdoc);
		echo "</pre>";
		die("qqq");*/
		
		$XSL->load($cadenaoriginal_3_2);
		
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($XSL);
		$cadena_original = $proc->transformToXML($xdoc);
		openssl_pkcs12_read($cadena_original, $sig, $private);
		$sello = base64_encode($sig);
		$c = $xdoc->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', 'Comprobante')->item(0); 
		$c->setAttribute('sello', $sello);
		$c->setAttribute('certificado', $certificado);
		$c->setAttribute('noCertificado', $numero_certificado);
		
		/*
		$root = new SimpleXMLElement($c);
		$root->asXml('Juan.xml');*/
		
		return $xdoc->saveXML();
		


	}

	public function log($content=null, $useDate=true, $newPrefix=''){
		if($content == null) return false;
		$content = print_r($content, true);
		$dbquery = $this->getController()->getServiceLocator()->get('Application/Model/Dbquery');
		$path = $dbquery->BASE_PATH.'/var/log/';
		
		
		
		//$path = 'log/';
		if($newPrefix != ''){
			$prefix = $newPrefix;
		}else{
			$prefix = 'system';
		}
		$file = $prefix.($useDate?'_'.date("Ymd"):'').'.log';
			if (!$gestor = fopen($path.$file, 'a')) {
				 //echo "No se puede abrir el archivo ($file)";
				 exit;
			}
			$content .= "\r\n";
			$content = date("Y-m-d H:i:s").' : '.$content;
			// Escribir $content a nuestro archivo abierto.
			if (fwrite($gestor, $content) === FALSE) {
				//echo "No se puede escribir en el archivo ($file)";
				exit;
			}
			//echo "éxito, se escribió ($content) en el archivo ($file)";
			fclose($gestor);
	}
	
	
	public function downloadFile($file){
		
		if (file_exists($file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename($file));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				ob_clean();
				flush();
				readfile($file);
				exit;
		}else{
			
			return false;
		}
	
	}
	
	
	public function curlJobsRapiddo($urlComplete){

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $urlComplete);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, false);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		//$headers[] = 'Accept: *';
		//$headers[] = 'User-Agent: Blako-Hookshot';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		$json = $result;
		
		$jsonD=json_decode($json);

		if(count($jsonD)>0){
			return $jsonD;
		}else{
			return false;
		}
		
		
	
	}
	
	
	public function GetTimeDiff($timestamp){
	
		$how_log_ago = '';
		$seconds = time() - $timestamp; 
		$minutes = (int)($seconds / 60);
		$hours = (int)($minutes / 60);
		$days = (int)($hours / 24);
		if ($days >= 1) {
		  $how_log_ago = $days . ' day' . ($days != 1 ? 's' : '');
		} else if ($hours >= 1) {
		  $how_log_ago = $hours . ' hour' . ($hours != 1 ? 's' : '');
		} else if ($minutes >= 1) {
		  $how_log_ago = $minutes . ' minute' . ($minutes != 1 ? 's' : '');
		} else {
		  $how_log_ago = $seconds . ' second' . ($seconds != 1 ? 's' : '');
		}
		return $how_log_ago;
	}
	
	public function getStartAndEndDate($week, $year){
	
    $time = strtotime("1 January $year", time());
    $day = date('w', $time);
    $time += ((7*$week)+1-$day)*24*3600;
    $return[0] = date('Y-n-j', $time);
    $time += 6*24*3600;
    $return[1] = date('Y-n-j', $time);
    return $return;
	}

	public function addHours($times) {

		$minutes=0;
		// loop throught all the times
		foreach ($times as $time) {
			list($hour, $minute) = explode(':', $time);
			$minutes += $hour * 60;
			$minutes += $minute;
		}
		$hours = floor($minutes / 60);
		$minutes -= $hours * 60;

		// returns the time already formatted
		return sprintf('%02d:%02d', $hours, $minutes);
	}
	
	
	public function Email($to='carlos@blako.com', $toName='Carlos Hernandez', $toBcc="ill_carlos@hotmail.com", $subject="Rapiddo Webmaster", $templateName='cotizacion', $templateVars=array('foo'=>'var'), $cron=false){
	
		$options = new Mail\Transport\SmtpOptions(array( 
			'name' => 'localhost',  
			'host' => 'smtp.gmail.com',  
			'port'=> 587,
			//'host' => 'localhost',  
			//'port'=> 26,  
			'connection_class' => 'login',  
			'connection_config' => array(  
				//'username' => 'sociosdf@rapiddo.mx',
				//'password' => 'rapiddo1010', 
				//'username' => 'ill.kkarlos@gmail.com', 
				//'password' => '2949@175paraque',  
				'ssl'=> 'tls',  
			),
		));  
		
		
		
		$basePath = getenv("APPLICATION_PATH")."/module/Application/view/email/templates/";
		if($cron == true){
			
			$renderer = new PhpRenderer();
			$renderer->resolver()->addPath($basePath);
			$model = new ViewModel();
			
			foreach($templateVars as $key=>$val){
				$model->setVariable($key, $val);
			}

			$model->setTemplate($templateName);
			$content = $renderer->render($model);
			
		}else{
			
			$renderer = new PhpRenderer();
			$renderer->resolver()->addPath($basePath);
			$model = new ViewModel();
			
			foreach($templateVars as $key=>$val){
				$model->setVariable($key, $val);
			}
			
			//$model->setTemplate($txtFilename);
			//$textContent = $renderer->render($model);

			$model->setTemplate($templateName);
			$content = $renderer->render($model);
			
			
		}
		
		// make a header as html  
		$html = new MimePart($content);  
		$html->type = "text/html";  
		$body = new MimeMessage();  
		$body->setParts(array($html,));  
		
		// instance mail   
		$mail = new Mail\Message();  
		$mail->setBody($body); // will generate our code html from template.phtml  
		$mail->setFrom('sociosdf@rapiddo.mx','Rapiddo'); 
		$mail->setEncoding("UTF-8");
		$mail->setTo($to, $toName);  
		//$mail->setBcc($toBcc);
		$mail->setSubject($subject);  
		
		$transport = new Mail\Transport\Smtp($options);  
		
		//print_r($transport);
		//die("--");
		
		
		$sent = true;
		try {
			$transport->send($mail); 
		} catch (Exception $e){
			$sent = false;
		}
		


		return $sent;
		
	}
	
}