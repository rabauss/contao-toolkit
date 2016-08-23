<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback\Wizard;

/**
 * FilePicker wizard.
 *
 * @package Netzmacht\Contao\Toolkit\View\Wizard
 */
final class FilePicker extends AbstractFieldPicker
{
    /**
     * {@inheritDoc}
     */
    public function generate($tableName, $fieldName, $rowId, $value)
    {
        $url = sprintf(
            'contao/file.php?do=%s&amp;table=%s&amp;field=%s&amp;value=%',
            $this->input->get('do'),
            $tableName,
            $fieldName,
            str_replace(
                array('{{link_url::', '}}'),
                '',
                $value
            )
        );

        $cssId   = $fieldName . (($this->input->get('act') === 'editAll') ? '_' . $rowId : '');
        $jsTitle = specialchars(
            str_replace('\'', '\\\'', $this->translator->translate('MOD.files.0', 'modules'))
        );

        $template = $this->createTemplate();
        $template
            ->set('url', $url)
            ->set('title', $this->translator->translate('MSC.filepicker'))
            ->set('jsTitle', $jsTitle)
            ->set('field', $fieldName)
            ->set('icon', 'pickfile.gif')
            ->set('id', $cssId);

        return $template->parse();
    }
}
