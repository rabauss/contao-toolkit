<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener;

use Netzmacht\Contao\Toolkit\Assertion\AssertionFailed;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;

use function trim;

/**
 * This listener defines an data-operation attribute for each operation which has a toolkit config section.
 *
 * The button callback doesn't have an identifier to the button which the button is rendering. To overcome this
 * limitation an data attribute is set.
 */
class SetOperationDataAttributeListener
{
    /**
     * Data container manager.
     *
     * @var DcaManager
     */
    private $dcaManager;

    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private $scopeMatcher;

    /**
     * @param DcaManager          $dcaManager   Data container manager.
     * @param RequestScopeMatcher $scopeMatcher The scope matcher.
     */
    public function __construct(DcaManager $dcaManager, RequestScopeMatcher $scopeMatcher)
    {
        $this->dcaManager   = $dcaManager;
        $this->scopeMatcher = $scopeMatcher;
    }

    /**
     * Listen to the on load data container callback.
     *
     * @param string $dataContainerName Data container name.
     */
    public function onLoadDataContainer(string $dataContainerName): void
    {
        if (! $this->scopeMatcher->isContaoRequest() || $this->scopeMatcher->isInstallRequest()) {
            return;
        }

        try {
            $definition = $this->dcaManager->getDefinition($dataContainerName);
        } catch (AssertionFailed $e) {
            // No valid dca config found. Just ignore the data container.
            return;
        }

        $buttons = (array) $definition->get(['list', 'operations']);

        foreach ($buttons as $name => $config) {
            if (! isset($config['toolkit'])) {
                continue;
            }

            if (! isset($config['attributes'])) {
                $config['attributes'] = '';
            }

            $attributes = trim($config['attributes'] . ' data-operation="' . $name . '"');
            $definition->set(['list', 'operations', $name, 'attributes'], $attributes);
        }
    }
}
