<?php
/**
 * Define customized functions used in twig template
 * author: Haiping Lu
 */
namespace AdminBundle\Twig\Extension;

use CG\Core\ClassUtils;

use Symfony\Component\DependencyInjection\Container;
use AppBundle\Entity as Entity;

class AdminExtension extends \Twig_Extension
{
    protected $_loader;
    protected $_controller;
    protected $_container;
    
    protected $_criteria;

    protected $_searchValue;
    
    protected $_country;

    public function __construct(\Twig_LoaderInterface $loader, Container $container)
    {
        $this->_loader = $loader;
        $this->_container = $container;
    }

    public function setController($controller)
    {
        $this->_controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('code', array($this, 'getCode'), array('is_safe' => array('html'))),
			new \Twig_SimpleFunction('getTripRequestSummary', array($this, 'getTripRequestSummary')),
			new \Twig_SimpleFunction('getRequestTotalCount', array($this, 'getRequestTotalCount')),
			new \Twig_SimpleFunction('getTripSummary', array($this, 'getTripSummary')),
			new \Twig_SimpleFunction('getTripTotalCount', array($this, 'getTripTotalCount'))
        );
    }
    
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('money', array($this, 'number2money'))
        );
    }

	public function getRequestTotalCount()
	{
		$em = $this->_container->get('doctrine')->getManager();
		return $em->getRepository('AppBundle:Trip')
		          ->getRequestTotalCount();
	}
	
	public function getTripRequestSummary()
	{
		$em = $this->_container->get('doctrine')->getManager();
		
		$summary = $em->getRepository('AppBundle:Trip')
		              ->getTripRequestSummary();
		
		$array = array();
		
		foreach($summary as $sum){
			$array[] = $sum['num'];
		}
		
		return implode(',', $array);
	}
	
	public function getTripTotalCount()
	{
		$em = $this->_container->get('doctrine')->getManager();
		return $em->getRepository('AppBundle:Trip')
		          ->getTripTotalCount();
	}
	
	public function getTripSummary()
	{
		$em = $this->_container->get('doctrine')->getManager();
		
		$summary = $em->getRepository('AppBundle:Trip')
		              ->getTripSummary();

		$array = array();
		
		foreach($summary as $sum){
			$array[] = $sum['num'];
		}
		
		return implode(',', $array);
	}
    
    /*
    public function getClassifications()
    {
        if (!$this->_country) {
            $this->_country = $this->_container
                       ->get('request_stack')
                       ->getCurrentRequest()
                       ->attributes
                       ->get('_country');
        }              
        $em = $this->_container->get('doctrine')->getManager();

        return $em->getRepository('AppBundle:Category')
                  ->getCategoryByCountryCode($this->_country);
        
        
    }
    
    public function getRegions()
    {
        if (!$this->_country) {
            $this->_country = $this->_container
                       ->get('request_stack')
                       ->getCurrentRequest()
                       ->attributes
                       ->get('_country');
        }   
        
        $em = $this->_container->get('doctrine')->getManager();

        return $em->getRepository('AppBundle:Region')
                  ->getRegionByCountryCode($this->_country);
    }*/
    
    

    public function getCode($template)
    {
        // highlight_string highlights php code only if '<?php' tag is present.
        $controller = highlight_string("<?php".$this->getControllerCode(), true);
        $controller = str_replace('<span style="color: #0000BB">&lt;?php&nbsp;&nbsp;&nbsp;&nbsp;</span>', '&nbsp;&nbsp;&nbsp;&nbsp;', $controller);

        $template = htmlspecialchars($this->getTemplateCode($template), ENT_QUOTES, 'UTF-8');

        // remove the code block
        $template = str_replace('{% set code = code(_self) %}', '', $template);

        return <<<EOF
<p><strong>Controller Code</strong></p>
<pre>$controller</pre>

<p><strong>Template Code</strong></p>
<pre>$template</pre>
EOF;
    }

    protected function getControllerCode()
    {
        $class = get_class($this->_controller[0]);
        if (class_exists('CG\Core\ClassUtils')) {
            $class = ClassUtils::getUserClass($class);
        }

        $r = new \ReflectionClass($class);
        $m = $r->getMethod($this->_controller[1]);

        $code = file($r->getFilename());

        return '    '.$m->getDocComment()."\n".implode('', array_slice($code, $m->getStartline() - 1, $m->getEndLine() - $m->getStartline() + 1));
    }

    protected function getTemplateCode($template)
    {
        return $this->_loader->getSource($template->getTemplateName());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'front';
    }
    
    public function maxCloseDate($days)
    {
        $dateTime = new \DateTime();
        $days = (int) $days;
        return $dateTime->add(new \DateInterval('P' . $days . 'D'));
    }
}
