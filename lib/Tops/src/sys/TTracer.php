<?php

namespace Tops\sys;

class TTracer
{

    /**
     * @var TTracer
     */
    private static $tracer;


    /**
     * @var ILogger;
     */
    private $traceLogger = null;

    private static $debugJs = false;

    const TRACE_MODE_CONSOLE = 2;
    const TRACE_MODE_LOGGING = 4;
    const TRACE_MODE_OFF = 0;

    private $traceMode;
    private $traces = array();
    private $outputMode;
    private $messages = array();
    private $echoMessage = false;
    private $suspended = array();
    private $traceId = array();
    private $traceCount = -1;
    private $sessionId;



    public static function isEnabled() {
        if (self::$tracer) {
            return self::$tracer->traceMode != self::TRACE_MODE_OFF;
        }
        return false;
    }

    public function __construct(IConfigManager $configManager, ILogger $traceLogger = null)
    {

        $this->traceMode = self::TRACE_MODE_OFF;
        $this->outputMode = self::getOutputMode();
        $this->sessionId = uniqid('tr');
        $traceSection = $configManager->getLocal("appsettings", "trace");
        $consoleEnabled = $traceSection->isTrue("console", false);
        self::$debugJs = $traceSection->isTrue('debugjs',false);
        if ($consoleEnabled) {
            $this->traceMode |= self::TRACE_MODE_CONSOLE;
        }

        $loggingEnabled = $traceSection->isTrue("logging", false);
        if ($loggingEnabled && $traceLogger != null) {
            $logName = $traceSection->Value("log", "trace");
            $traceLogger->setDefaultLogName($logName);
            $this->traceLogger = $traceLogger;
            $this->traceMode |= self::TRACE_MODE_LOGGING;
        }

        if ($this->traceMode) {
            $traces = $traceSection->Value('traces');
            foreach (array_keys($traces) as $trace) {
                if ($traces[$trace]) {
                    array_push($this->traces, $trace);
                }
            }
            if (empty($this->traces)) {
                array_push($this->traces,'default');
            }

        }

    }

    // static


    private static function getOutputMode()
    {
        return array_key_exists('REQUEST_METHOD', $_SERVER) ? 'html' : 'console';
    }

    public static function WriteLine($value)
    {
        if (self::getOutputMode() == "html")
            print "$value<br>";
        else
            print "$value\n";
    }


    public static function On($traceId = 'default')
    {
        if (!isset(self::$tracer)) {
            if (TObjectContainer::HasDefinition("tracer")) {
                self::$tracer = TObjectContainer::Get("tracer");
            } else {
                self::$tracer = null;
                return;
            }
        }

        self::$tracer->activateTrace($traceId);

    }  //  self::On


    public static function Off($traceId = 'default')
    {
        self::$tracer->stopTrace($traceId);
    }  //  traceOff

    public static function Suspend()
    {
        self::$tracer->suspendTrace();
    }  //  self::Suspend()

    public static function Resume()
    {
        self::$tracer->resumeTrace();
    }  //  resumeTrace

    public static function Assert($value, $trueMessage, $falseMessage = '', $file = '', $line = 0)
    {
        if (empty($value)) {
            if (empty($falseMessage))
                $falseMessage = 'NOT: ' . $trueMessage;
            self::Trace($falseMessage, $file, $line);
        } else
            self::Trace($trueMessage, $file, $line);
    }

    public static function Trace($message, $file = '', $line = 0)
    {
        if (self::isEnabled()) {
            if (!empty($file)) {
                if (!empty($line))
                    $message .= "  on line $line";
                $message .= " in file '$file'.";
            }
            self::$tracer->addMessage($message);
        }

    }  //  trace


    public static function WriteLines()
    {
        $messages = self::GetMessages();
        $count = count($messages);
        if ($count == 0)
            return '';
        $result = $count . ' trace messages\n';
        foreach ($messages as $message)
            $result .= "$message\n";
        return $result;
    }  //  dumpTrace


    public static function Render()
    {
        $messages = self::GetMessages();
        $br = self::getOutputMode() === 'html' ? '<br>' : '';
        $count = count($messages);
        if ($count == 0)
            return '';
        $result = $count . " trace messages$br\n";
        foreach ($messages as $message)
            $result .= "$message$br\n";
        return $result;
    }  //  dumpTrace

    public static function PrintMessages()
    {
        $messageText = self::Render();
        if (!empty($messageText)) {
            if (self::getOutputMode() == 'html')
                print '<div id="trace">' . $messageText . '</div>';
            else
                print "$messageText\n";
        }
    }

    public static function RenderTraceMessages()
    {
        $messageText = self::Render();
        $styles = 'background-color: white; border: 1px solid black; color: black; padding: 3px';
        $markup = ($messageText) ? "<div id='tops.trace' style='$styles'>".$messageText.'</div>' :  '';
        if ($markup) {
            return array('#markup' => $markup);
        }
        return '';
    }


    public static function GetMessages()
    {
        if (self::isEnabled())
            return self::$tracer->getTraceMessages();
        return array();
    }  //  getTraceMessages

    public static function EchoOn()
    {
        if (self::isEnabled())
            self::$tracer->setEchoOn();
    }  //  self::EchoOn

    public static function EchoOff()
    {
        if (self::isEnabled())
            self::$tracer->setEchoOff();
    }  //  self::EchoOff


    public static function ShowArray($arr)
    {
        if (isset(self::$tracer) && self::$tracer->active()) {
            if (self::GetOutputMode() != "html") {
                print "\n";
                print_r($arr);
                print "\n";
            } else {
                print '<div style="padding:3px;background-color:white;color:black;text-align:left">';
                print '<pre>';
                print_r($arr);
                print '</pre></div>';
            }
        }
    }

    public static function setJsDebug($on) {
        self::$debugJs = $on;
    }

    public static function JsDebuggingOn() {
        return self::$debugJs;
    }



    // private


    private function activateTrace($traceId = 'default')
    {
        if (in_array($traceId, $this->traces)) {
            $this->startTrace($traceId);
        } else {
            $this->suspendTrace();
        }
    }

    public function active()
    {
        return ($this->traceCount > -1 &&
            (!$this->suspended[$this->traceCount]));
    }

    public function addMessage($message)
    {

        if ($this->traceCount > -1) {
            if (!$this->suspended[$this->traceCount]) {
                $traceId = $this->traceId[$this->traceCount];
                if (!empty($traceId))
                    $message = "[$traceId] " . $message;
                array_push($this->messages, $message);

                if ($this->traceMode & self::TRACE_MODE_LOGGING) {
                    $this->traceLogger->writeInfo("$this->sessionId: $message");
                }

                if ($this->echoMessage) {
                    $outputMode = self::getOutputMode();
                    echo
                    $outputMode == "html" ?
                        '<span style="background-color:white">Trace: ' . $message . '</span><br/>' :
                        "$message\n";
                }
            }
        }
    }  //  addMessage

    public function setEchoOn()
    {
        $this->echoMessage = true;
    }  //  echoOn

    public function setEchoOff()
    {
        $this->echoMessage = false;
    }  //  echoOff

    public function suspendTrace()
    {
        if ($this->traceMode) {
            $this->suspended[$this->traceCount] = true;
        }
    }  //  suspend

    public function resumeTrace()
    {
        if (!$this->traceMode) {
            $this->suspended[$this->traceCount] = false;
        }
    }  //  resume

    public function resetTraces()
    {
        $this->messages = array();
        $this->suspended = array();
    }  //  reset

    public function startTrace($traceId = 'default')
    {
        array_push($this->traceId, $traceId);
        array_push($this->suspended, false);
        ++$this->traceCount;
    }  //  start

    public function stopTrace($traceId = 'default')
    {
        if (!$this->traceMode) {
            return;
        }
        if ($this->traceCount < 0)
            return; // already off
        if ($this->traceId[$this->traceCount] == $traceId) {
            array_pop($this->traceId);
            array_pop($this->suspended);
            --$this->traceCount;
        }
    }  //  end

    public function getTraceMessages($traceId = 'default')
    {
        if (in_array($traceId,$this->traces))
            return $this->messages;
        return array();
    }  //  getMessages


} // end TTracer
