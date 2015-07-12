<?php
namespace ArtemFrolov\Bundle\LocaleSwitcherBundle\Twig;

use ArtemFrolov\Bundle\LocaleSwitcherBundle\Routing\Router;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LocaleSwitcher extends \Twig_Extension
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param Container $container
     * @param Router $router
     */
    public function __construct(
        Container $container,
        Router $router
    ) {
        $this->container = $container;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'locale_switcher';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'bootstrap_locale_switcher_dropdown',
                array($this, 'getDropdown'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction(
                'bootstrap_locale_switcher_list',
                array($this, 'getList'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction(
                'path_locale',
                array($this, 'getPathLocale')
            )
        );
    }

    /**
     * @return string
     */
    public function getDropdown()
    {
        return $this->container->get('templating')->render(
            'ArtemFrolovLocaleSwitcherBundle:bootstrap:switcher_dropdown.html.twig',
            array(
                'locales' => $this->router->getEnabledLocales()
            )
        );
    }

    /**
     * @return string
     */
    public function getList()
    {
        return $this->container->get('templating')->render(
            'ArtemFrolovLocaleSwitcherBundle:bootstrap:switcher_list.html.twig',
            array(
                'locales' => $this->router->getEnabledLocales()
            )
        );
    }

    /**
     * @param string $name
     * @param array $parameters
     * @param bool $relative
     *
     * @return string
     */
    public function getPathLocale(
        $name,
        $parameters = array(),
        $relative = false
    ) {
        return $this->router->generate(
            $name,
            $parameters,
            $relative
                ? UrlGeneratorInterface::RELATIVE_PATH
                : UrlGeneratorInterface::ABSOLUTE_PATH
        );
    }
}
