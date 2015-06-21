<?php
namespace ArtemFrolov\Bundle\LocaleSwitcherBundle\Twig;

use Symfony\Component\DependencyInjection\Container;

class BootstrapLocaleSwitcher extends \Twig_Extension
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'bootstrap_locale_switcher';
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
            )
        );
    }

    /**
     * @return string
     */
    public function getDropdown()
    {
        return $this->container->get('templating')->render(
            'ArtemFrolovLocaleSwitcherBundle:bootstrap:switcher_dropdown.html.twig'
        );
    }

    /**
     * @return string
     */
    public function getList()
    {
        $languages = array(
            'ru' => 'Русский',
            'en' => 'English'
        );
        return $this->container->get('templating')->render(
            'ArtemFrolovLocaleSwitcherBundle:bootstrap:switcher_list.html.twig',
            array(
                'languages' => $languages
            )
        );
    }
}
