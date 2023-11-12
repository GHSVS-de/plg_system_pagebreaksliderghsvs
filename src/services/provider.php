<?php
\defined('_JEXEC') or die;
use Joomla\CMS\Extension\PluginInterface;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\CMS\HTML\Registry;
use GHSVS\Plugin\System\PagebreakSliderGhsvs\Extension\PagebreakSliderGhsvs;
use GHSVS\Plugin\System\PagebreakSliderGhsvs\Helper\PagebreakSliderGhsvsHelper;

return new class () implements ServiceProviderInterface
{
	public function register(Container $container): void
	{
		$container->set(
			PluginInterface::class,
			function (Container $container)
			{
				$dispatcher = $container->get(DispatcherInterface::class);
				$plugin = new PagebreakSliderGhsvs(
					$dispatcher,
					(array) PluginHelper::getPlugin('system', 'pagebreaksliderghsvs'),
					new PagebreakSliderGhsvsHelper()
				);
				$plugin->setApplication(Factory::getApplication());
				$plugin->setRegistry($container->get(Registry::class));

				return $plugin;
			}
		);
	}
};
