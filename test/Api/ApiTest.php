<?php

namespace FicoEXTScored\Client;

use \GuzzleHttp\Client;
use \GuzzleHttp\Event\Emitter;
use \GuzzleHttp\Middleware;
use \GuzzleHttp\HandlerStack as handlerStack;

use \FicoEXTScored\Client\ApiException;
use \FicoEXTScored\Client\Configuration;
use \FicoEXTScored\Client\Model\Error;
use \FicoEXTScored\Client\Interceptor\KeyHandler;
use \FicoEXTScored\Client\Interceptor\MiddlewareEvents;

class FicoExtendedScoreApiTest extends \PHPUnit_Framework_TestCase{

    public function setUp(){
        $password = getenv('KEY_PASSWORD');
        $this->signer = new \FicoEXTScored\Client\Interceptor\KeyHandler(null, null, $password);

        $events = new \FicoEXTScored\Client\Interceptor\MiddlewareEvents($this->signer);
        $handler = handlerStack::create();
        $handler->push($events->add_signature_header('x-signature'));   
        $handler->push($events->verify_signature_header('x-signature'));
        $client = new \GuzzleHttp\Client(['handler' => $handler]);

        $config = new \FicoEXTScored\Client\Configuration();
        $config->setHost('the_url');

        $this->apiInstance = new \FicoEXTScored\Client\Api\FicoEXTScoredApi($client, $config);
        $this->x_api_key = "your_api_key";
        $this->username = "your_username";
        $this->password = "your_password";
    }
    
    public function testGetReporte(){

        $request = new \FicoEXTScored\Client\Model\Peticion();
        $persona = new \FicoEXTScored\Client\Model\Persona();
        $domicilio = new \FicoEXTScored\Client\Model\Domicilio();        
        $estado = new \FicoEXTScored\Client\Model\CatalogoEstados();
            
        $domicilio->setDireccion("CALVARIO");
        $domicilio->setColoniaPoblacion("LOMA DE LA PALMA");
        $domicilio->setDelegacionMunicipio("GUSTAVO A  MADERO");
        $domicilio->setCiudad("CIUDAD DE MEXICO");
        $domicilio->setEstado($estado::DF);
        $domicilio->setCP("07160");
        $domicilio->setFechaResidencia(null);
        $domicilio->setNumeroTelefono(null);
        $domicilio->setTipoDomicilio(null);
        $domicilio->setTipoAsentamiento(null);
        $domicilio->setFechaRegistroDomicilio(null);
        $domicilio->setTipoAltaDomicilio(null);
        $domicilio->setIdDomicilio(null);

        $persona->setApellidoPaterno("PATERNO");
        $persona->setApellidoMaterno("MATERNO");
        $persona->setApellidoAdicional(null);
        $persona->setNombres("NOMBRES");
        $persona->setFechaNacimiento("YYYY-MM-DD");
        $persona->setRFC("PAMN800825569");
        $persona->setCURP(null);
        $persona->setNacionalidad("MX");
        $persona->setResidencia(null);
        $persona->setEstadoCivil(null);
        $persona->setSexo(null);
        $persona->setNumeroDependientes(null);
        $persona->setFechaDefuncion(null);
        $persona->setDomicilio($domicilio);
         
        $request->setFolio("1235");
        $request->setPersona($persona);        

        try {
            $result = $this->apiInstance->getReporte($this->x_api_key, $this->username, $this->password, $request);
            print_r($result);
            $this->assertTrue($result->getFolioConsulta()!==null);

            return $result->getFolioConsulta();
        } catch (Exception $e) {
            echo 'Exception when calling FicoEXTScoredApi->getReporte: ', $e->getMessage(), PHP_EOL;
        }
    } 

}
