SOAP Service Layer
========

Service Layer vendor, for SOA communication (SOAP).

## Installation

For installation use composer: 

```bash
composer require vallbo/soap-service-layer
```
## Adapters
### Vallbo\SoapServiceLayer\Adapter\BasicAdapter

#### Configuration

  * dependencies
    * soapClientFactory - *Vallbo\SoapServiceLayer\Adapter\SoapClientFactoryInterface* - factory for creating SOAP client.
    * responseFactory - *Vallbo\SoapServiceLayer\Response\ResponseFactoryInterface* - response factory for creating specific response.
    * logger - if you want log what is going on in adapter, provide your logger service (PSR-3).
    * cache - if you want cache responses, provide your cache service (PSR-16).
    * cacheTTL - cache TTL for cache items.

## Exception

SoapFault is wrapped to *Vallbo\SoapServiceLayer\Exception\SoapException*. You can find SoapFault in previous exception in SoapException object.

## Response

**Response returned by adapter implements *Vallbo\SoapServiceLayer\Response\SoapResponseInterface*.**

We provide default response object *Vallbo\SoapServiceLayer\Response\SoapResponse* and default response factory *Vallbo\SoapServiceLayer\Response\ResponseFactory* for faster implementation.
