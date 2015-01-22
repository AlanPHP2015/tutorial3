<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Displayoverride
{
    /*
     * Declaration for the tag we're looking for, as well as the
     * regular expression version.
     */
    private $leadingtag = '<p class="lead">';
    private $trailingtag = '</p>';
    private $leadingregexdelimiter = '<p class="lead">';
    private $trailingregexdelimiter = '<\/p>';
    
    /**
     * Function which handles the display_override hook
     * If a paragraph tag with class="lead" is found, all words beginning
     * with a capital letter are surrounded by <strong> tags.
     */
    function Boldcapitals() {
        // To stored the result of preg_match, will contain the string we wish
        // to process. (without the tags)
        $string_to_modify; 
        // The index where the <p class="lead"> tag starts
        $index_of_beginning;
        // The index where the following </p> ends
        $index_of_ending;
        $regexpattern = '/'.$this->leadingregexdelimiter.'.*'.$this->trailingregexdelimiter.'/';
        
        $this->CI =& get_instance();
        $output = $this->CI->output->get_output();
        
        // Use regular expression to parse the text first, if the tags aren't found,
        // it means the current page is not a single quote page.
        // If so, skip all processing.
        if (preg_match($regexpattern, $output, $string_to_modify) === 1) {
            $index_of_beginning = $this->findbeginindex($output);
            $index_of_ending = $this->findendindex($output, $index_of_beginning);
            //Concatenate first part of the original string, with the modified
            //content, and finally the last part of the original string.
            $output = substr($output, 0, $index_of_beginning).
                    $this->massagedata($string_to_modify[0]).
                    substr($output, $index_of_ending);
            $this->CI->output->set_output($output);
        }
        $this->CI->output->_display();
    }
    
    /**
     * Finds the index where the <p> tag with class="lead" begins
     * @param string $original The original output
     * @return int The index of the beginning of the paragraph tag with class="lead" 
     */
    function findbeginindex($original) {
        return strpos($original, $this->leadingtag);
    }
    
    /**
     * Finds the index of the closing </p> tag and increases the index
     * by the amount of the characters in the closing </p> tag and returns
     * the value.
     * @param string $original The original output
     * @param int $offset The number of characters offset from the beginning,
     *                      before searching for the closing </p> tag
     * @return int The index of the end of the </p> tag
     */
    function findendindex($original, $offset) {
        $endindex = strpos($original, $this->trailingtag, $offset);
        $endindex += 4; // Length of ending tag
        return $endindex;
    }
    
    /**
     * Entry method for adding <strong> tags around capitals.
     * Removes the leading paragraph and trailing paragraph tags.
     * Passes the content within the tags to function processcapitals
     * @param type $original
     * @return type
     */
    function massagedata($original) {
        $splitregex = '/<p class="lead">|<\/p>/';
        $splitarray = preg_split($splitregex,
                                 $original,
                                    -1,
                                    PREG_SPLIT_NO_EMPTY);
        return $this->processcapitals($splitarray[0]);
    }
    
    /**
     * Surrounds all words beginning with a capital letter
     * with <strong> </strong> tags.
     * @param string $stringtobold The string, without tags, which is to be processed.
     * @return string The string with all capital words surrounded by <strong></strong>
     */
    function processcapitals($stringtobold) {
        $wordarray = preg_split('/ /', $stringtobold, -1, PREG_SPLIT_DELIM_CAPTURE);
//        die(var_dump($wordarray));
        for($i = 0; $i < count($wordarray); $i++) {
            if (ctype_upper(substr($wordarray[$i], 0, 1))) {
                $wordarray[$i] = '<strong>'.$wordarray[$i].'</strong>';
            }
        }
        return implode(' ', $wordarray);
    }
}