<?php
namespace GHSVS\Plugin\System\PagebreakSliderGhsvs\Extension;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Joomla\Event\DispatcherInterface;
use GHSVS\Plugin\System\PagebreakSliderGhsvs\Helper\PagebreakSliderGhsvsHelper;
use GHSVS\Plugin\System\PagebreakSliderGhsvs\Service\HTML\PagebreakSliderGhsvsJHtml;

final class PagebreakSliderGhsvs extends CMSPlugin
{
	use HTMLRegistryAwareTrait;

	/**
	 * Load plugin language files automatically
	 *
	 * @var    boolean
	 * @since  3.6.3
	 */
	protected $autoloadLanguage = true;

	private $helper;

	public function __construct(
		DispatcherInterface $dispatcher,
		array $config,
		PagebreakSliderGhsvsHelper $helper,
	) {
		parent::__construct($dispatcher, $config);

		$this->helper = $helper;
	}

	public function onAfterInitialise()
	{
		if (!$this->getApplication()->isClient('administrator'))
		{
			$htmlHelper = new PagebreakSliderGhsvsJHtml();
			$this->getRegistry()->register('pagebreaksliderghsvs', $htmlHelper);
		}
	}

	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		if (!$this->getApplication()->isClient('site'))
		{
			return;
		}

		###### pagebreakghsvs-slider - START
		if (
			$context === 'com_content.article'
			&& strpos($article->text, '{pagebreakghsvs-slider ') !== false
		) {
			// empty(id)? E.g. call via 'content.prepare'
			$this->helper->buildSliders($article->text, empty($article->id) ? uniqid() : $article->id);
		}
		###### pagebreakghsvs-slider - END
	}

	public function onAjaxSessionPagebreakSliderGhsvs()
	{
		if (strtolower($this->getApplication()->input->server->get('HTTP_X_REQUESTED_WITH', '')) !== 'xmlhttprequest')
		{
			return 'Have fun, laugh louder';
		}

		// AJAX-Input.
		$input = $this->getApplication()->input;
		$cmd = $input->get('cmd', '', 'ALNUM');
		$key = trim($input->get('key', '', 'STRING'));

		if (!$key || !in_array($cmd, ['add', 'get', 'destroy']))
		{
			return;
		}

		$data = $input->get('data', '', 'STRING');

		$node  = 'plg_system_pagebreaksliderghsvs';
		$session = $this->getApplication()->getSession();
		$sessionData = $session->get($node);

		switch ($cmd)
		{
			case 'add':
				$sessionData[$key] = $data;
				$session->set($node, $sessionData);

				return($sessionData[$key] . ' written.');
				break;
			case 'get':
				return isset($sessionData[$key]) ? $sessionData[$key] : null;
				break;
			case 'destroy':
				$sessionData = null;
				$session->set($node, $sessionData);

				return '$sessionData destroyed';
				break;
		}

		return;
	}
}
