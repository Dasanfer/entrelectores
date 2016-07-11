<?php
namespace AppBundle\Services\Twig;

class GlobalParameters extends \Twig_Extension
{
    private $parameters;

    public function __construct($parameters){
        $this->parameters = $parameters;
    }

    public function getFunctions()
    {
        return array(
            'getAppParameter' => new \Twig_Function_Method($this, 'getParameter'),
        );
    }

    public function getFilters()
    {
        return array(
        );
    }

    public function getParameter($parameterName){
        if(array_key_exists($parameterName,$this->parameters))
            return $this->parameters[$parameterName];
        else
            throw new \Exception($parameterName.' parameter not found '.print_r($this->parameters, true));
    }

    public function getName()
    {
        return 'app_twig_parameters';
    }

}
