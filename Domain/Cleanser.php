<?php
/**
 * Cleanser.php
 * 
 * Cleans and formats form input data before passing it to the webservice
 * 
 * @since 7-26-2009
 * @author Jesse Snyder at NPower Seattle <jesses@npowerseattle.org>
 * @package PMSC_Domain
 */

class PMSC_Domain_DataCleanser {
    
    private $input;
    private $reporter;
    private $regex = "/^\s*[A-Z0-9]+\s*,\s*[A-Z0-9]+\s*,\s*[\d]{4}-[\d]{2}-[\d]{2}-[\d]{2}-[\d]{2}-[\d]{2}\s*$/";
    private $timeFormat = '%s-%s-%s %s:%s:%s';
    
    public function __construct($input) {
        $this->input = $input;
        $this->reporter = new PMSC_Domain_InputErrorReporter();
    }
    
    public function cleansed() {
        $clean = array();
        $curLine = 0;
        foreach(split("\n", $this->input) as $line) {
            $curLine += 1;
            $trimmed = trim($line);
            if (!$trimmed) {
                // blank lines are silently ignored
                continue;
            }
            if (!preg_match($this->regex, $line)) {
                $this->reporter->addError(array('lineNum'=>$curLine, 'content'=>$line));
                continue;
            }
            list($activity, $client, $time) = preg_split("/\s*,\s*/", $line);
            $niceTime = vsprintf($this->timeFormat, split('-', $time));
            $niceVals = array($activity, $client, $niceTime);
            array_push($clean, join(',', $niceVals));
        }
        if (count($clean) < 1) {
            return Null;
        }
        return $clean;
    }
    
    public function report() {
        return $this->reporter;
    }
}
?>