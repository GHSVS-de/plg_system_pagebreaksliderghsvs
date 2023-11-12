# plg_system_pagebreaksliderghsvs
Joomla system plugin. Replaces placeholders of the type `{pagebreakghsvs-slider ...}` in an article with accordion sliders. Uses bootstrap collapse feature.

## Releases
https://github.com/GHSVS-de/plg_system_pagebreaksliderghsvs/releases

## Requirements
Editor button: https://github.com/GHSVS-de/plg_editors-xtd_pagebreakghsvs/releases

## Examples

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
