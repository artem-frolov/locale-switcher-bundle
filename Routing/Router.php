<?php
namespace ArtemFrolov\Bundle\LocaleSwitcherBundle\Routing;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Router
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function __construct(
        Container $container,
        UrlGeneratorInterface $router
    )
    {
        $this->container = $container;
        $this->router = $router;
    }

    /**
     * @param string $name
     * @param null|string $fromLocale
     * @param null|string $toLocale
     *
     * @return string
     */
    public function getCustomRouteName($name, $fromLocale = null, $toLocale = null)
    {
        if (!$fromLocale) {
            $fromLocale = $this->router->getContext()->getParameter('_locale');
        }

        if (!$toLocale) {
            $toLocale = $fromLocale;
        }

        if (!$this->container->hasParameter('custom_locale_routes')) {
            return $name;
        }

        $customLocaleRoutes = $this->container->getParameter('custom_locale_routes');

        if (
            isset($customLocaleRoutes[$name])
            && isset($customLocaleRoutes[$name][$toLocale])
        ) {
            return $customLocaleRoutes[$name][$toLocale];
        }

        if ($fromLocale !== $toLocale) {
            foreach ($customLocaleRoutes as $route => $locales) {
                if (
                    isset($locales[$fromLocale])
                    && $locales[$fromLocale] == $name
                ) {
                    return $route;
                }
            }
        }

        return $name;
    }

    /**
     * @param string $name
     * @param array $parameters
     * @param bool $referenceType
     *
     * @return string
     *
     * @see UrlGeneratorInterface::generate
     */
    public function generate(
        $name,
        $parameters = array(),
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ) {

        $currentLocale = $this->router->getContext()->getParameter('_locale');
        if (isset($parameters['_locale'])) {
            $targetLocale = $parameters['_locale'];
            unset($parameters['_locale']);
        } else {
            $targetLocale = $currentLocale;
        }
        $this->router->getContext()->setParameter('_locale', $targetLocale);

        $result = $this->router->generate(
            $this->getCustomRouteName($name, $currentLocale, $targetLocale),
            $parameters,
            $referenceType
        );
        $this->router->getContext()->setParameter('_locale', $currentLocale);
        return $result;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getEnabledLocales()
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
