<?php

namespace Icinga\Module\Urlshipper\ProvidedHook\Director;

use Icinga\Application\Config;
use Icinga\Exception\ConfigurationError;
use Icinga\Exception\IcingaException;
use Icinga\Module\Director\Exception\JsonException;
use Icinga\Module\Director\Hook\ImportSourceHook;
use Icinga\Module\Director\Web\Form\QuickForm;
use RuntimeException;

class ImportSource extends ImportSourceHook
{
    public function getName()
    {
        return 'Import from urls (urlshipper)';
    }

    /**
     * @return object[]
     * @throws ConfigurationError
     * @throws IcingaException
     */
    public function fetchData()
    {
        // Config
        $url = $this->getSetting('url');
        $format = $this->getSetting('format');
        $headers = $this->getSetting('headers');

        // Content
        $content = $this->fetchUrlContent($url, $headers);

        // Format & return
        switch ($format) {
            case 'json':
                return $this->handleJsonContent($content);
            default:
                throw new ConfigurationError(
                    'Unsupported file format: %s',
                    $format
                );
        }
    }

    /**
     * @return array
     * @throws ConfigurationError
     * @throws IcingaException
     */
    public function listColumns()
    {
        return array_keys((array) current($this->fetchData()));
    }

    /**
     * @param QuickForm $form
     * @return \Icinga\Module\Director\Forms\ImportSourceForm|QuickForm
     * @throws \Zend_Form_Exception
     */
    public static function addSettingsFormFields(QuickForm $form)
    {
        $form->addElement('text', 'url', array(
            'label' => 'URL',
            'description' => 'URL to scrape',
            'required' => true,
            'class' => 'autosubmit',
        ));

        $form->addElement('select', 'format', array(
            'label' => 'Format',
            'value' => 'json',
            'description' => 'Available formats, JSON',
            'required' => true,
            'class' => 'autosubmit',
            'multiOptions' => $form->optionalEnum(
                static::listAvailableFormats($form)
            ),
        ));

        $form->addElement('textarea', 'headers', array(
            'label' => 'Headers',
            'description' => 'Headers',
            'required' => false,
            'class' => 'autosubmit'
        ));

        return $form;
    }

    /**
     * @param $url
     * @param $headers
     * @return object[]
     * @throws RuntimeException
     */
    protected function fetchUrlContent($url, $headers)
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => $headers
            ]
        ]);

        $content = @file_get_contents($url, false, $context);
        if ($content === false) {
            throw new RuntimeException(sprintf(
                'Unable to fetch url "%s"',
                $url
            ));
        }


        return $content;
    }

    /**
     * @param $content
     * @return object[]
     * @throws RuntimeException
     */
    protected function handleJsonContent($content)
    {
        $data = @json_decode($content);
        if ($data === null) {
            throw new RuntimeException('Unable to decode json');
        }

        return $data;
    }

    /**
     * @param QuickForm $form
     * @return array
     */
    protected static function listAvailableFormats(QuickForm $form)
    {
        $formats = array(
            'json' => $form->translate('JSON (JavaScript Object Notation)'),
        );

        return $formats;
    }
}
