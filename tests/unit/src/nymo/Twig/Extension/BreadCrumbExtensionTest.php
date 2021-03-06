<?php
/**
 * This file is part of silex-twig-breadcrumb-extension
 *
 * (c) Gregor Panek <gp@gregorpanek.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nymo\Twig\Extension;

use PHPUnit\Framework\TestCase;
use nymo\Resources\Library\BreadCrumbCollection;

/**
 * Testcases for the BreadCrumbExtension class
 * @author Gregor Panek <gp@gregorpanek.de>
 */
class BreadCrumbExtensionTest extends TestCase
{

    /**
     * BreadCrumbExtension
     * @var BreadCrumbExtension
     */
    protected $extension;

    /**
     * A pimple Container
     * @var Container
     */
    protected $app;


    public function setUp()
    {
        $viewPath = __DIR__.'/../../../../../../src/nymo/Views';
        $this->app = new \Pimple\Container();
        $this->app['charset'] = 'utf-8';
        $this->app['debug'] = false;
        //change default separator
        $this->app['breadcrumbs.separator'] = '...:::...';
        $this->app->register(new \Silex\Provider\LocaleServiceProvider());
        $this->app->register(new \Silex\Provider\TranslationServiceProvider(), array(
            'locale_fallbacks' => array('en'),
        ));
        $this->app->register(new \Silex\Provider\TwigServiceProvider(), array('twig.path' => $viewPath));

        $this->app['twig.loader'] = new \Twig_Loader_Chain();
        $this->extension = new BreadCrumbExtension($this->app);
    }

    public function testGetFunctions()
    {
        $functions = $this->extension->getFunctions();
        $this->assertInstanceOf(\Twig_SimpleFunction::class, $functions['renderBreadCrumbs']);
    }

    public function testRenderBreadCrumbs()
    {
        $breadcrumbs = BreadCrumbCollection::getInstance();
        $breadcrumbs->addItem('Amazon', 'www.amazon.de');
        $breadcrumbs->addItem('Something', 'www.isThere.com');
        $this->app['breadcrumbs'] = $breadcrumbs;

        $htmlBreadcrumbs = $this->extension->renderBreadCrumbs();
        $this->assertRegExp('/<a href="www.amazon.de">Amazon<\/a>/', $htmlBreadcrumbs);
        $this->assertRegExp('/...:::.../', $htmlBreadcrumbs);
    }

    public function testGetName()
    {
        $this->assertEquals('renderBreadCrumbs', $this->extension->getName());
    }
}
