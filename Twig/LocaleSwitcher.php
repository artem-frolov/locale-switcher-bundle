<?php
namespace ArtemFrolov\Bundle\LocaleSwitcherBundle\Twig;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LocaleSwitcher extends \Twig_Extension
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @param Container $container
     * @param UrlGeneratorInterface $generator
     */
    public function __construct(
        Container $container,
        UrlGeneratorInterface $generator
    ) {
        $this->container = $container;
        $this->generator = $generator;
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
                'path_per_locale',
                array($this, 'getPathPerLocale')
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
     * @param string $name
     * @param array $namesPerLocale
     * @param array $parameters
     * @param bool $relative
     *
     * @return string
     */
    public function getPathPerLocale(
        $name,
        $namesPerLocale = array(),
        $parameters = array(),
        $relative = false
    ) {
        $currentLocale = $this->generator->getContext()->getParameter('_locale');
        if (isset($namesPerLocale[$currentLocale])) {
            $name = $namesPerLocale[$currentLocale];
        }
        return $this->generator->generate(
            $name,
            $parameters,
            $relative
                ? UrlGeneratorInterface::RELATIVE_PATH
                : UrlGeneratorInterface::ABSOLUTE_PATH
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
