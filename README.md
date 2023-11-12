# plg_system_pagebreaksliderghsvs
Joomla system plugin. Replaces placeholders of the type `{pagebreakghsvs-slider ...}` in an article with accordion sliders. Uses bootstrap collapse feature.

## Releases
https://github.com/GHSVS-de/plg_system_pagebreaksliderghsvs/releases

## Requirements
Editor button: https://github.com/GHSVS-de/plg_editors-xtd_pagebreakghsvs/releases

## Examples

### Use the PagebreakSliderGhsvsHelper from outside

A) Boot plugin

```
use Joomla\CMS\Factory;

...

// Optional. If empty or not set the plugin parameters rule.
$options = [
 'headingTagGhsvs' => 'h2',
 'activeToSession' => 0,
 'parent' => true,
 'toggleContainer' => 'div',
];

$html = '{pagebreakghsvs-slider title="Blah Blah Blah"}' . 'I am the text.';

Factory::getApplication()->bootPlugin('pagebreaksliderghsvs', 'system')
	->helper->buildSliders(
		$html,
		$id, // Optional. If none set a uniqe id will be created by the helper.
		$options // Optional
	);

echo $html;
```

B) Use static method
```
use GHSVS\Plugin\System\PagebreakSliderGhsvs\Helper\PagebreakSliderGhsvsHelper;

...

// Optional. If empty or not set the plugin parameters rule.
$options = [];

$html = '{pagebreakghsvs-slider title="Blah Blah Blah"}' . 'I am the text.';

PagebreakSliderGhsvsHelper::buildSlidersStatic(
 $html,
 $id, // Optional. If none set a uniqe id will be created by the helper.
 $options // Optional
);

echo $html;
```

----------------------

# My personal build procedure (WSL 1, Debian, Win 10)

- Prepare/adapt `./package.json`.
- `cd /mnt/z/git-kram/plg_system_pagebreaksliderghsvs`

## node/npm updates/installation
- `npm run updateCheck` or (faster) `npm outdated`
- `npm run update` (if needed) or (faster) `npm update --save-dev`
- `npm install` (if needed)

## Build installable ZIP package
- `node build.js`
- New, installable ZIP is in `./dist` afterwards.
- All packed files for this ZIP can be seen in `./package`. **But only if you disable deletion of this folder at the end of `build.js`**.s

#### For Joomla update server
- Use/See `dist/release_no-changelog.txt` as basic release text.
- Create new release with new tag.
- Extracts(!) of the update XML for update servers are in `./dist` as well. Check for necessary additions! Then copy/paste.
