<?php

namespace MolnApps\Testing\Response;

use \MolnApps\Testing\Request\FormRequestBuilder;
use \MolnApps\Testing\Request\FormUploadRequestBuilder;
use \MolnApps\Testing\Request\LinkRequestBuilder;

use \Gajus\Dindent\Indenter;

use \MolnApps\Testing\Router\RouterInterface;

class ResponseInspector
{
    private $controller;
    private $router;

    private $response;

    private $formInspector;
    private $formRequestBuilder;
    private $formUploadRequestBuilder;

    private $domInspector;

    public function __construct(Controller $controller, RouterInterface $router)
    {
        $this->controller = $controller;
        $this->router = $router;
    }

    public function __call($methodName, $params)
    {
        if ($this->formInspector && $this->isFormInspectorMethod($methodName)) {
            return $this->callFluentApi($this->formInspector, $methodName, $params);
        }

        if ($this->domInspector && $this->isDomInspectorMethod($methodName)) {
            return $this->callFluentApi($this->domInspector, $methodName, $params);
        }

        throw new \Exception("Unknown method [{$methodName}]");
    }

    private function callFluentApi($receiver, $methodName, $params)
    {
        $result = call_user_func_array([$receiver, $methodName], $params);

        return ($result instanceof $receiver) ? $this : $result;
    }

    private function isFormInspectorMethod($methodName)
    {
        $formInspectorMethods = [
            'seeField', 'dontSeeField',
            'seeArrayField', 'dontSeeArrayField',
            'seeInput', 'dontSeeInput',
            'seeArrayInput', 'dontSeeArrayInput',
            'seeSelect', 'dontSeeSelect',
            'seeTextarea', 'dontSeeTextarea',
            'seeCheckbox', 'dontSeeCheckbox',
            'seeFile', 'dontSeeFile',

            'withOptions', 'withoutOptions', 'withValue',
            
            'enter', 'choose', 'check', 'uncheck',
        ];

        return in_array($methodName, $formInspectorMethods);
    }

    private function isDomInspectorMethod($methodName)
    {
        $domInspectorMethods = [
            'prophecy', 'shouldSeeElement',
            'seeElement', 'dontSeeElement',
            'atLeast', 'atMost', 'ordered',
            'never', 'once', 'times',
            'withAttributes', 'withAttribute', 'withChild', 'withText',
            'getElement',
        ];

        return in_array($methodName, $domInspectorMethods);
    }

    // !Response methods

    public function setResponse($response = '')
    {
        $this->response = $response;
        
        $this->domInspector = new DomInspector($this->response);

        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function dump()
    {
        $indenter = new Indenter();
        $formatted = $indenter->indent($this->getResponse());

        var_dump($formatted);
        
        return $this;
    }

    // !Controller methods
    public function bootApplication()
    {
        $this->controller->bootApplication();

        return $this;
    }

    public function shutdownApplication()
    {
        $this->controller->shutdownApplication();
        
        return $this;
    }

    public function signedInAs($userId)
    {
        $this->controller->signedInAs($userId);

        return $this;
    }

    public function signedOut()
    {
        $this->controller->signedOut();

        return $this;
    }

    // !Text methods
    
    public function seeText($text)
    {
        return $this->seeTextOrFail($text, true, 'Could not find text [%s]');
    }

    public function dontSeeText($text)
    {
        return $this->seeTextOrFail($text, false, 'Text [%s] was found');
    }

    private function seeTextOrFail($text, $shouldBeFound, $message)
    {
        foreach ((array)$text as $str) {
            if ($this->findText($str) != $shouldBeFound) {
                throw new \Exception(sprintf($message, $str));
            }
        }

        return $this;
    }

    private function findText($text)
    {
        $encodedText = htmlentities($text, ENT_QUOTES, 'UTF-8');

        return strstr($this->response, $encodedText) || strstr($this->response, $text);
    }

    // !Visit method

    public function visit(array $params = [])
    {
        $this->takeSnapshot($params);

        $this->setResponse($this->controller->run($params));
        
        return $this;
    }

    private function takeSnapshot(array $params)
    {
    	return ;
    	
    	// This code takes a jpg snapshot of the website
    	// We should consider finding a more elegant solution, possibly detached from tests as the request made by phantom could potentially invalidate test result.
        // Note that right now it's not compatible with ::click() and ::submit() methods.

        $p = [];
        foreach ($params as $key => $param) {
            $p[] = $key . '=' . urlencode($param);
        }

        $url = "http://www.molnapps.dev/accounts/index.php?" . implode("&", $p);
        $filename = urlencode($url);

        $client = \JonnyW\PhantomJs\Client::getInstance();
	    
	    $width  = 800;
	    $height = 600;
	    $top    = 0;
	    $left   = 0;
	    
	    $request = $client->getMessageFactory()->createCaptureRequest($url, 'GET');
	    $request->setOutputFile('/var/www/screenshots/' . $filename . '.jpg');
	    $request->setViewportSize($width, $height);
        $request->setViewportSize(1280, 720);
	    //$request->setCaptureDimensions($width, $height, $top, $left);

	    $response = $client->getMessageFactory()->createResponse();

	    // Send the request
	    $client->send($request, $response);
    }

    // !Form methods

    public function seeForm($formId)
    {
        $form = $this->findFormElementOrFail($formId);
        
        $this->formInspector = new FormInspector($form);
        $this->formRequestBuilder = FormRequestBuilder::make($form)->withRouter($this->router);
        $this->formUploadRequestBuilder = new FormUploadRequestBuilder($form);

        return $this;
    }

    public function fillForm(array $fieldValues)
    {
        foreach ($fieldValues as $fieldName => $value) {
            $this->seeField($fieldName)->enter($value);
        }

        return $this;
    }

    public function filledWith(array $fieldValues)
    {
        foreach ($fieldValues as $fieldName => $value) {
            $this->seeField($fieldName)->withValue($value);
        }

        return $this;
    }

    private function findFormElementOrFail($formId)
    {
        return $this->seeElement('form')->withAttribute('id', $formId)->getElement();
    }

    public function submit()
    {
        $this->setResponse(
            $this->controller->run(
                $this->formRequestBuilder->getParams(),
                $this->formUploadRequestBuilder->getParams()
            )
        );

        return $this;
    }

    // !Links methods

    public function seeLink($text, $classOrId = '')
    {
        $link = $this->findLinkElementOrFail($text, $classOrId);

        $this->linkRequestBuilder = LinkRequestBuilder::make($link)->withRouter($this->router);

        return $this;
    }

    private function findLinkElementOrFail($text, $classOrId)
    {
        return $this->seeElement('a'.$classOrId)->withAttribute('text', $text)->getElement();
    }

    public function click(array $paramsToOverride = [])
    {
        $params = $this->linkRequestBuilder->getParams();

        $this->setResponse($this->controller->run(array_merge($params, $paramsToOverride)));

        return $this;
    }
}
