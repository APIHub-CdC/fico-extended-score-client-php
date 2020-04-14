# fico-extended-score-client-php

Es el primer score en el mercado mexicano que califica el nivel de cumplimiento de pago de un individuo, considerando al grupo de personas con las que comparto domicilio utilizando un algoritmo exclusivo de Círculo de Crédito.

## Requisitos

PHP 7.1 ó superior
### Dependencias adicionales
- Se debe contar con las siguientes dependencias de PHP:
    - ext-curl
    - ext-mbstring
- En caso de no ser así, para linux use los siguientes comandos
```sh
#ejemplo con php en versión 7.3 para otra versión colocar php{version}-curl
apt-get install php7.3-curl
apt-get install php7.3-mbstring
```
- Composer [vea como instalar][1]
## Instalación

Ejecutar: `composer install`

## Guía de inicio

### Paso 1. Generar llave y certificado

- Se tiene que tener un contenedor en formato PKCS12.
- En caso de no contar con uno, ejecutar las instrucciones contenidas en **lib/Interceptor/key_pair_gen.sh** ó con los siguientes comandos.
**opcional**: Para cifrar el contenedor, colocar una contraseña en una variable de ambiente.
```sh
export KEY_PASSWORD=your_password
```
- Definir los nombres de archivos y alias.
```sh
export PRIVATE_KEY_FILE=pri_key.pem
export CERTIFICATE_FILE=certificate.pem
export SUBJECT=/C=MX/ST=MX/L=MX/O=CDC/CN=CDC
export PKCS12_FILE=keypair.p12
export ALIAS=circulo_de_credito
```
- Generar llave y certificado.
```sh
#Genera la llave privada.
openssl ecparam -name secp384r1 -genkey -out ${PRIVATE_KEY_FILE}
#Genera el certificado público.
openssl req -new -x509 -days 365 \
    -key ${PRIVATE_KEY_FILE} \
    -out ${CERTIFICATE_FILE} \
    -subj "${SUBJECT}"
```
- Generar contenedor en formato PKCS12.
```sh
# Genera el archivo pkcs12 a partir de la llave privada y el certificado.
# Deberá empaquetar la llave privada y el certificado.
openssl pkcs12 -name ${ALIAS} \
    -export -out ${PKCS12_FILE} \
    -inkey ${PRIVATE_KEY_FILE} \
    -in ${CERTIFICATE_FILE} -password pass:${KEY_PASSWORD}
```

### Paso 2. Cargar el certificado dentro del portal de desarrolladores

 1. Iniciar sesión.
 2. Dar clic en la sección "**Mis aplicaciones**".
 3. Seleccionar la aplicación.
 4. Ir a la pestaña de "**Certificados para @tuApp**".
    <p align="center">
      <img src="https://github.com/APIHub-CdC/imagenes-cdc/blob/master/applications.png">
    </p>
 5. Al abrirse la ventana emergente, seleccionar el certificado previamente creado y dar clic en el botón "**Cargar**":
    <p align="center">
      <img src="https://github.com/APIHub-CdC/imagenes-cdc/blob/master/upload_cert.png" width="268">
    </p>

### Paso 3. Descargar el certificado de Círculo de Crédito dentro del portal de desarrolladores

 1. Iniciar sesión.
 2. Dar clic en la sección "**Mis aplicaciones**".
 3. Seleccionar la aplicación.
 4. Ir a la pestaña de "**Certificados para @tuApp**".
    <p align="center">
        <img src="https://github.com/APIHub-CdC/imagenes-cdc/blob/master/applications.png">
    </p>
 5. Al abrirse la ventana emergente, dar clic al botón "**Descargar**":
    <p align="center">
        <img src="https://github.com/APIHub-CdC/imagenes-cdc/blob/master/download_cert.png" width="268">
    </p>
 > Es importante que este contenedor sea almacenado en la siguiente ruta:
 > **/path/to/repository/lib/Interceptor/keypair.p12**
 >
 > Así mismo el certificado proporcionado por círculo de crédito en la siguiente ruta:
 > **/path/to/repository/lib/Interceptor/cdc_cert.pem**
- En caso de que no se almacene así, se debe especificar la ruta donde se encuentra el contenedor y el certificado. Ver el siguiente ejemplo:
```php
$password = getenv('KEY_PASSWORD');
$this->signer = new \RCCFicoScorePLD\Client\Interceptor\KeyHandler(
    "/example/route/keypair.p12",
    "/example/route/cdc_cert.pem",
    $password
);
```
 > **NOTA:** Sólamente en caso de que el contenedor haya cifrado, se debe colocar la contraseña en una variable de ambiente e indicar el nombre de la misma, como se ve en la imagen anterior.
 
### Paso 4. Modificar URL
 Modificar la URL de la petición en ***test/Api/ApiTest.php***, como se muestra en el siguiente fragmento de código:
 ```php
$config = new \FicoEXTScored\Client\Configuration();
$config->setHost('the_url');
 ```
 
### Paso 5. Capturar los datos de la petición

Es importante contar con el setUp() que se encargará de firmar y verificar la petición.

```php

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

```

## Pruebas unitarias

Para ejecutar las pruebas unitarias:
```sh
./vendor/bin/phpunit
```
[1]: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos
