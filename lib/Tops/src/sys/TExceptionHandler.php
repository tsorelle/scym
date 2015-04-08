<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/1/2015
 * Time: 7:24 AM
 */

namespace Tops\sys;


use Monolog\Logger;

class TExceptionHandler implements IExceptionHandler
{
    /**
     * @var TCollection
     */
    private $policies;
    private $loggingEnabled;
    /**
     * @var ILogger
     */
    private $logger;

    const RecoverableErrorPolicy = 'application-recoverable';
    const FatalErrorPolicy = 'application-fatal';
    const WarningPolicy = 'application-warning';


    public function __construct(ILogger $logger=null, IConfigManager $config = null)
    {
        $this->policies = new TCollection();
        $this->loggingEnabled = $logger !== null;
        $this->logger = $logger;
        if ($config) {
            $this->configure($config);
        }
    }

    public function configure(IConfigManager $config)
    {
        $configuration = $config->getLocal('appsettings', 'exceptions');
        $this->loggingEnabled = $configuration->IsTrue('logging', true);
        $this->addPolicies($configuration);
    }

    private function getNumericSeverity($value)
    {
        if (!empty($value)) {
            if (is_numeric($value)) {
                return ($value);
            }
            $levels = Logger::getLevels();
            $keys = array_keys($levels);
            foreach ($keys as $key) {
                if ($key == $value) {
                    return $levels[$key];
                }
            }
        }
        return Logger::ERROR;
    }

    public function addPolicies(IConfiguration $configuration)
    {
        $policyConfigs = $configuration->Value("policies");
        if (!empty($policyConfigs)) {
            $names = array_keys($policyConfigs);
            foreach ($names as $name) {
                $section = new TConfigSection($policyConfigs[$name]);
                $severity = $this->getNumericSeverity($section->Value("severity"));
                $this->addPolicy(
                    $name,
                    $severity,
                    $section->IsTrue("rethrow", null),
                    $section->Value("log", null)
                );
            }
        }
    }

    /**
     * @param $policyName
     * @return TExceptionPolicy
     */
    private function getPolicy($policyName) {
        if (empty($policyName)) {
            $policyName = 'default';
        }
        return $this->policies->get($policyName);
    }

    private function getLoggerLevel($severity) {
        $levels = Logger::getLevels();
        foreach($levels as $level) {
            if ($level == $severity) {
                return $severity;
            }
        }
        return Logger::ERROR;
    }

    function handleException(\Exception $ex, $policyName = null)
    {
        $policy = null;
        if (!empty($policyName)) {
            $policy = $this->getPolicy($policyName);
        }

        if ($policy != null) {
            $severity = $policy->getSeverity();
        }
        else {
            if (method_exists($ex,'getSeverity')) {
                $severity = $ex->getSeverity();
            }
            else {
                $severity = TException::SeverityError;
            }
            $policy = $this->getSeverityPolicy($severity);
        }

        if ($policy == null) {
            // if still no policy, rethrow the exception
            return true;
        }

        $logName = $policy->getLogName();
        $rethrow = $policy->getRethrow();
        if ($rethrow === null) {
            $rethrow = empty($logName);
        }

        if ($this->loggingEnabled && !empty($logName)) {
            $message = $ex->getMessage()."\nTrace: ".$ex->getTraceAsString();
            $level = $this->getLoggerLevel($severity);
            $this->logger->write($message,$level,$logName);
        }

        return $rethrow;
    }

    /**
     * @return TExceptionPolicy[]
     */
    private function sortPolicies() {
        return $this->policies->getSort(
            function (TExceptionPolicy $p1, TExceptionPolicy $p2) {

                $s1 = $p1->getSeverity();
                $s2 = $p2->getSeverity();

                if ($s1 == $s2) {
                    return 0;
                }
                return ($s1 > $s2) ? -1 : 1;
            }
        );
    }

    /**
     * @param $severity
     * @return null|TExceptionPolicy
     */
    public function getSeverityPolicy($severity)
    {
        if ($this->policies->getCount() == 0) {
            return null;
        }
        $policies = $this->sortPolicies();
        $result = $policies[0];
        for ($i=0;$i<sizeof($policies);$i++) {
            $policy = $policies[$i];
            if ($policy->getSeverity() < $severity) {
                break;
            }
            $result = $policy;
        }
        return $result;
    }


    /**
     * @param $name
     * @param int $severity
     * @param null $rethrow
     * @param null $logName
     */
    public function addPolicy($name, $severity = TException::SeverityError, $rethrow = null, $logName = null ) {
        $this->policies->set($name,
            new TExceptionPolicy($name,$severity,$rethrow,$logName));
    }




}