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
            'ArtemFrolovLocaleSwitcherBundle:bootstrap:switcher_dropdown.html.twig',
            array(
                'locales' => $this->getEnabledLocales()
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
                'locales' => $this->getEnabledLocales()
            )
        );
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getEnabledLocales()
    {
        $enabledLocaleCodes = explode(
            '|',
            $this->container->getParameter('enabled_locales')
        );

        $locales = array();
        foreach ($enabledLocaleCodes as $code) {
            $locales[$code] = array(
                'nativeLocale' => $this->getLocaleName($code, $code),
                'currentLocale' => $this->getLocaleName($code),
            );
        }
        return $locales;
    }

    /**
     * @param string $locale
     * @param null $inLocale
     *
     * @return string
     */
    private function getLocaleName($locale, $inLocale = null)
    {
        return mb_convert_case(
            // getDisplayName() returns the input string
            // when the second parameter is passed even if it's null.
            // call_user_func_array + func_get_args solve this issue
            call_user_func_array('\Locale::getDisplayName', func_get_args()),
            MB_CASE_TITLE,
            'UTF-8'
        );
    }
}
