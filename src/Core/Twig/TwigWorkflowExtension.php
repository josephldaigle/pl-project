<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 8/31/18
 * Time: 6:09 AM
 */


namespace PapaLocal\Core\Twig;


use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\Registry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


/**
 * Class TwigWorkflowExtension
 *
 * @package PapaLocal\Core\Twig
 */
class TwigWorkflowExtension extends AbstractExtension
{
    /**
     * @var Registry
     */
    private $workflowRegistry;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * TwigWorkflowExtension constructor.
     *
     * @param Registry        $workflowRegistry
     * @param LoggerInterface $logger
     */
    public function __construct(Registry $workflowRegistry,
                                LoggerInterface $logger)
    {
        $this->workflowRegistry = $workflowRegistry;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('build_workflow_blocker_list', array($this, 'getBlockerList'))
        );
    }

    /**
     * Fetch the transition blocker list.
     *
     * @param        $item
     * @param string $workflowName
     * @param string $transition
     *
     * @return array
     */
    public function getBlockerList($item, string $workflowName, string $transition)
    {
        try {
            $workflow = $this->workflowRegistry->get($item, $workflowName);

            $blockerList = $workflow->buildTransitionBlockerList($item, $transition)->getIterator()->getArrayCopy();

            return $blockerList;

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An error occurred retrieving the blocker list for transition: [%s] workflow name [%s].', $transition, $workflowName), array('exception' => $exception, 'trace' => $exception->getTrace()));

            return array('error');
        }

    }

}