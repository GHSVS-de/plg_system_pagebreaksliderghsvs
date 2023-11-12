<?php
/*
Aufruf via:
HTMLHelper::_('pagebreaksliderghsvs.xyz' ... );
*/

namespace GHSVS\Plugin\System\PagebreakSliderGhsvs\Service\HTML;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;
use GHSVS\Plugin\System\PagebreakSliderGhsvs\Helper\PagebreakSliderGhsvsHelper;

class PagebreakSliderGhsvsJHtml
{
	private $wa;
	protected static $loaded = [];

	/**
	 * Now Collapse
	*/
	public function startAccordion($selector = 'myAccordian', $options = [])
	{
		if (!isset(static::$loaded[__METHOD__][$selector]))
		{
			HTMLHelper::_('bootstrap.collapse');
			$divAttributes = [];
			$opt = [];

			// OHNE parent KANNST DU MEHRERE GLEICHZEITIG ÖFFNEN.

			/* Kein Typecast-Vergleich, weil ich noch nicht alle aufrufenden
			Codestellen geprüft habe. */
			if (isset($options['parent']) && $options['parent'])
			{
				$opt['parent'] = '#' . $selector;
			}

			if (isset($opt['parent']))
			{
				$divAttributes['aria-multiselectable'] = 'false';
			}
			else
			{
				$divAttributes['aria-multiselectable'] = 'true';
			}

			/* Scroll-JS funktioniert in dieser simplen Form nicht mit
			multiselectable = JA. Scrollt zum falschen Slider. */

			if (
				$divAttributes['aria-multiselectable'] === 'false'
				&& $options['scrollToSliderHead'] === 1
			){
				$js = <<<JS
;(function(){
document.addEventListener('DOMContentLoaded',function()
{
document.getElementById("$selector").addEventListener('shown.bs.collapse', function ()
{
	jQuery.fn.scrollToSliderHead("$selector");
});
});})();
JS;
				$this->getWa()->addInline(
					'script',
					$js,
					[],
					['name' => 'pagebreaksliderghsvs.HTMLHelper.' . 'selector-' . str_replace(' ', '', $selector)]
				);
			}

			static::$loaded[__METHOD__][$selector] = $opt;

			$divAttributes['class'] = 'panel-group accordion';
			$divAttributes['id'] = $selector;

			return PHP_EOL . '<!--startAccordion-->' . PHP_EOL
				. '<div ' . ArrayHelper::toString($divAttributes) . '>';
		}
	}

	/**
	 * bootstrap.addSlide BS5
	 */
	public function addSlide(
		$selector,
		$text,
		$id,
		$class = '',
		$headingTagGhsvs = '',
		$title = ''
	) {
		// "in" = BS3. "show" = BS4/BS5.
		//$in = (static::$loaded[__CLASS__ . '::startAccordion'][$selector]['active'] == $id)
		//? ' in show' : '';
		$in = '';
		$parent = '';

		if (!empty(
			static::$loaded[__CLASS__ . '::startAccordion'][$selector]['parent'])
		) {
			// Dies Attribut gehört in den Slide, nicht in den Toggler!
			$parent = ' data-bs-parent="'
				. static::$loaded[__CLASS__ . '::startAccordion'][$selector]['parent']
				. '"';
		}

		$aClass = 'accordion-toggle btn btn-link text-left p-0';

		if (!trim($headingTagGhsvs))
		{
			$headingTagGhsvs = 'div';
		}

		if ($title = trim($title))
		{
			$title = ' <span class="pageBreakSlideTitle">- ' . $title . '</span>';
		}

		$html = [];
		$html[] = '<div class="card pageBreakGhsvsCard">';

		$html[] = '<div class="card-header" id="heading' . $id . '">';
		$html[] = '<' . $headingTagGhsvs . ' class="panel-title">';

		// The Toggler element.
		$html[] = '<button class="' . $aClass . '" data-bs-toggle="collapse"'
			. ' data-bs-target="#collapse' . $id . '" aria-expanded="false"'
			. ' aria-controls="collapse' . $id . '" role="button">';

		if (PluginHelper::isEnabled('system', 'iconsghsvs'))
		{
		$html[] = '{svg{bi/arrows-expand}class="hideIfActive"}';
		$html[] = '{svg{bi/arrows-collapse}class="showIfActive"}';
			$css = <<<CSS
button .showIfActive{display: none;}
button[aria-expanded=true] .showIfActive{display: inline;}
button[aria-expanded=true] .hideIfActive{display: none;}
CSS;
			$this->getWa()->addInline(
				'style',
				$css,
				[],
				['name' => 'pagebreaksliderghsvs.HTMLHelper.' . 'selector-' . str_replace(' ', '', $selector)]
			);
		}

		$html[] = $text . $title;
		$html[] = '</button>';

		$html[] = '</' . $headingTagGhsvs . '>';
		$html[] = '</div><!--/heading' . $id . '-->';

		$html[] = '<div id="collapse' . $id . '" class="collapse ' . $in . '"'
			. ' aria-labelledby="heading' . $id . '"' . $parent . '>';
		$html[] = '<div class="card-body">';

		return implode("\n", $html);
	}

	public static function endSlide(): string
	{
		return '</div></div></div>';
	}

	public static function endAccordion(): string
	{
		return '</div>';
	}

	/**
	 * Bei Klick auf Accordion-Slides Status in Session schreiben,
	 * In Session gespeicherte, aktive Slides öffnen.
	 */
	public function activeToSession($selector = 'myAccordian')
	{
		$key = '#' . $selector . '.accordion';

		if (!isset(static::$loaded[__METHOD__][$key]))
		{
			$sessionData = Factory::getSession()->get('plg_system_pagebreaksliderghsvs');
			$IDs = [];

			if (!empty($sessionData[$key]))
			{
				$IDs = explode('|', $sessionData[$key]);
			}

			$js = [];
			$js[] = '
(function($)
{
	$(document).ready(function()
	{
		if (! $("' . $key . '").length)
		{
			return;
		}';

			// Open active slides in session.
			foreach ($IDs as $id)
			{
				$js[] = '$("#' . $id . '").collapse("show");';
			}

			// AJAX-save status of open slides in session if any slide clicked.
			$js[] = '
		$("' . $key . '").on("shown.bs.collapse hidden.bs.collapse", function (event)
		{
			$actives = $("' . $key . ' .show");
			var activeIds = [];
			$actives.each(function()
			{
				activeIds.push($(this).attr("id"));
			});

			var activeIds = activeIds.join("|");
			var KEY = "' . urlencode($key) . '"; //Key in der Session
			var PLUGIN = "SessionPagebreakSliderGhsvs";
			var GROUP = "system";
			var FORMAT = "raw";
			var OPTION = "com_ajax";
			var CMD = "add";
			var DATA = activeIds;

			var systemPaths = Joomla.getOptions("system.paths");
			var Uri = (systemPaths ? systemPaths.root + "/index.php" : window.location.pathname) + "?"
				+ "option=" + OPTION + "&group=" + GROUP + "&plugin=" + PLUGIN + "&format=" + FORMAT
				+ "&cmd=" + CMD + "&key=" + KEY + "&data=" + DATA;

			Joomla.request({
				url: Uri,
				method: "POST",
				onError: function(xhr)
				{
					console.log("error: " + activeIds);
				}
			});
		});
	});
})(jQuery);';

			// Wegen Umstellung von $.ajax auf Joomla.request.
			$this->getWa()->addInline(
				'script',
				implode('', $js),
				[],
				["name" => 'pagebreaksliderghsvs.activeToSession.inline'],
				['core', 'jquery-migrate']
			);
		}
	}

	public function getWa()
	{
		if (empty($this->wa))
		{
			$this->wa = Factory::getApplication()->getDocument()->getWebAssetManager();
			$this->wa->getRegistry()->addExtensionRegistryFile('plg_system_pagebreaksliderghsvs');
		}

		return $this->wa;
	}
}
