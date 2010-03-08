<?php
    /**
     *	base include file for SimpleTest
     *	@package	SimpleTest
     *	@subpackage	UnitTester
     *	@version	$Id: reporter.php,v 1.3 2008/02/22 11:09:42 markop Exp $
     */

    /**#@+
     *	include other SimpleTest class files
     */
    require_once(dirname(__FILE__) . '/scorer.php');
    /**#@-*/
    
    /**
     *    Sample minimal test displayer. Generates only
     *    failure messages and a pass count.
	 *	  @package SimpleTest
	 *	  @subpackage UnitTester
     */
    class HtmlReporter extends SimpleReporter {
        var $_character_set;
        
        /**
         *    Does nothing yet. The first output will
         *    be sent on the first test start. For use
         *    by a web browser.
         *    @access public
         */
        function HtmlReporter($character_set = 'ISO-8859-1') {
            $this->SimpleReporter();
            $this->_character_set = $character_set;
        }
        
        /**
         *    Paints the top of the web page setting the
         *    title to the name of the starting test.
         *    @param string $test_name      Name class of test.
         *    @access public
         */
        function paintHeader($test_name) {
            $this->sendNoCacheHeaders();
            global $log;
            print "<html>\n<head>\n<title>$test_name</title>\n";
            print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=" .
                    $this->_character_set . "\">\n";
            print "<style type=\"text/css\">\n";
            print $this->_getCss() . "\n";
            print "</style>\n";
            print "</head>\n<body>\n";
            print "<h1>$test_name</h1>\n";
            flush();
        }
        
        /**
         *    Send the headers necessary to ensure the page is
         *    reloaded on every request. Otherwise you could be
         *    scratching your head over out of date test data.
         *    @access public
         *    @static
         */
        function sendNoCacheHeaders() {
            if (! headers_sent()) {
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
            }
        }
        
        /**
         *    Paints the CSS. Add additional styles here.
         *    @return string            CSS code as text.
         *    @access protected
         */
        function _getCss() {
            return ".fail { color: red; } pre { background-color: lightgray; }";
        }
        
        /**
         *    Paints the end of the test with a summary of
         *    the passes and failures.
         *    @param string $test_name        Name class of test.
         *    @access public
         */
        function paintFooter($test_name) {
            $colour = ($this->getFailCount() + $this->getExceptionCount() > 0 ? "red" : "green");
            print "<div style=\"";
            print "padding: 8px; margin-top: 1em; background-color: $colour; color: white;";
            print "\">";
            print $this->getTestCaseProgress() . "/" . $this->getTestCaseCount();
            print " test cases complete:\n<br>";
            print "<strong>" . $this->getPassCount() . "</strong> passes, ";
            print "<strong>" . $this->getFailCount() . "</strong> fails and ";
            print "<strong>" . $this->getExceptionCount() . "</strong> exceptions.";
            print "</div>\n";
            print "</body>\n</html>\n";
            var_dump($log);
        }
        
        /**
         *    Paints the test failure with a breadcrumbs
         *    trail of the nesting test suites below the
         *    top level test.
         *    @param string $message    Failure message displayed in
         *                              the context of the other tests.
         *    @access public
         */
        function paintFail($message) {
            parent::paintFail($message);
            print "<br /><span class=\"fail\">Fail</span>: ";
            $breadcrumb = $this->getTestList();
            array_shift($breadcrumb);
            print implode(" -&gt; ", $breadcrumb);
            print " -&gt; <br /><br />" . $this->_htmlEntities($message) . "<br />\n";
        }
        
        /**
         *    Paints a PHP error or exception.
         *    @param string $message        Message is ignored.
         *    @access public
         *    @abstract
         */
        function paintException($message) {
            parent::paintException($message);
            print "<span class=\"fail\">Exception</span>: ";
            $breadcrumb = $this->getTestList();
            array_shift($breadcrumb);
            print implode(" -&gt; ", $breadcrumb);
            print " -&gt; <br /><strong>" . $this->_htmlEntities($message) . "</strong><br />\n";
        }
        
        /**
         *    Paints formatted text such as dumped variables.
         *    @param string $message        Text to show.
         *    @access public
         */
        function paintFormattedMessage($message) {
            print '<pre>' . $this->_htmlEntities($message) . '</pre>';
        }
        
        /**
         *    Character set adjusted entity conversion.
         *    @param string $message    Plain text or Unicode message.
         *    @return string            Browser readable message.
         *    @access protected
         */
        function _htmlEntities($message) {
            return htmlentities($message, ENT_COMPAT, $this->_character_set);
        }
    }
    
    /**
     *    Sample minimal test displayer. Generates only
     *    failure messages and a pass count. For command
     *    line use. I've tried to make it look like JUnit,
     *    but I wanted to output the errors as they arrived
     *    which meant dropping the dots.
	 *	  @package SimpleTest
	 *	  @subpackage UnitTester
     */
    class TextReporter extends SimpleReporter {
        
        /**
         *    Does nothing yet. The first output will
         *    be sent on the first test start.
         *    @access public
         */
        function TextReporter() {
            $this->SimpleReporter();
        }
        
        /**
         *    Paints the title only.
         *    @param string $test_name        Name class of test.
         *    @access public
         */
        function paintHeader($test_name) {
            if (! SimpleReporter::inCli()) {
                header('Content-type: text/plain');
            }
            print "$test_name".$nl;
            flush();
        }
        
        /**
         *    Paints the end of the test with a summary of
         *    the passes and failures.
         *    @param string $test_name        Name class of test.
         *    @access public
         */
        function paintFooter($test_name) {
            global $log;
            
           $log["stuff"]["conc"] = "";
            if ($this->getFailCount() + $this->getExceptionCount() == 0) {
                print  $log["stuff"]["conc"].=$nl."<br>OK".$nl;
            } else {
                print $log["stuff"]["conc"].=$nl."FAILURES!!!".$nl;
            }
            print $log["stuff"]["conc"].=$nl." Test cases run: " . $this->getTestCaseProgress() .
                    "/" . $this->getTestCaseCount() .
                    ", <br>Passes: " . $this->getPassCount() .
                    ", <br>Failures: " . $this->getFailCount() .
                    ", <br>Exceptions: " . $this->getExceptionCount() . $nl;
            $log["tested"] = $this->getTestCaseCount();
            $log["passed"] = $this->getPassCount();
            $log["fail"] = $this->getFailCount();
            $log["exc"] = $this->getExceptionCount();
            if($autotest)  $log["stuff"]["conc"] = str_replace("\n" , "<br>" , $log["stuff"]["conc"]);
        }
        
        /**
         *    Paints the test failure as a stack trace.
         *    @param string $message        Failure message displayed in
         *                           the context of the other tests.
         *    @access public
         */
        function paintFail($message) {
		global $log, $run;
        	
            parent::paintFail($message);
            print $log["stuff"]["case"][$run] = $this->getFailCount() . ") $message".$nl;
            
         if($autotest)  $log["stuff"]["case"][$run] =  str_replace("\n" , "<br>" , $log["stuff"]["case"][$run]);
            $breadcrumb = $this->getTestList();
            array_shift($breadcrumb);
            print "\tin " . implode("\n<br>\tin ", array_reverse($breadcrumb));
            print $nl;
        }
        
        /**
         *    Paints a PHP error or exception.
         *    @param string $message        Message is ignored.
         *    @access public
         *    @abstract
         */
        function paintException($message) {
            parent::paintException($message);
            print "<br>Exception " . $this->getExceptionCount() . "!\n<br>$message\n<br>";
        }
        
        /**
         *    Paints formatted text such as dumped variables.
         *    @param string $message        Text to show.
         *    @access public
         */
        function paintFormattedMessage($message) {
            print $message.$nl;
            flush();
        }
    }
?>
