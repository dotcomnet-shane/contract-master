<?php

/**
 * Created by PhpStorm.
 * User: ali
 * Date: 2016/04/23
 * Time: 12:51 PM
 */
class functions
{
    public static function checkRequirements($requirements, $data)
    {
        $return = array();
        $message = '';
        $return['success'] = true;

        foreach($requirements as $requirement)
        {
            if(!isset($data[$requirement]))
            {
                //                error_log(var_dump($data));
                $message .= " The required parameter: '$requirement' has not been set! ";
                $return['success'] = false;
            }
        }

        $return['message'] = $message;

        return $return;
    }

    public static function ucsentence($str)
    {
        if($str)
        { // input
            // recursively replaces all double spaces with a space
            $str = preg_replace('/' . chr(32) . chr(32) . '+/', chr(32), $str);

            // sample of first 10 chars is ALLCAPS so convert $str to lowercase; if always done then any proper capitals would be lost
            if(($x = substr($str, 0, 10)) && ($x == strtoupper($x)))
            {
                $str = strtolower($str);
            }
            $na = array('. ', '! ', '? '); // punctuation needles

            // each punctuation needle
            foreach($na as $n)
            {
                // punctuation needle found
                if(strpos($str, $n) !== false)
                {
                    $sa = explode($n, $str); // split

                    foreach($sa as $s)
                    {
                        $ca[] = ucfirst($s);
                    } // capitalize
                    $str = implode($n, $ca); // replace $str with rebuilt version
                    unset($ca); //  clear for next loop
                }
            }
            return ucfirst(trim($str)); // capitalize first letter in case no punctuation needles found
        }
    }

    public static function csvToArray($csvFileName, $postData)
    {
        //Create file if from post
        if($postData)
        {
            $filename = ROOT_PATH . 'application/campaign/imports/' . $csvFileName;

            //Check if file still exists on disk
            if(file_exists($filename))
            {
                unlink($filename);
            }

            //Save file to disk
            try
            {
                file_put_contents($filename, base64_decode($postData));
                exec("chmod 777 $filename");
            }
            catch(Exception $e)
            {
                $return['error'] = true;
                $return['message'] = 'Couldnt create import file on server';

                trigger_error($return['message']);

                return $return;
            }
            $csvFileName = $filename;
        }


        $file = fopen($csvFileName, "r");

        $count = 0;
        $csvData = '';
        $keys = '';

        for($i = 0; !feof($file); $i++)
        {
            $currLine = fgetcsv($file);

            if(!$count)
            {
                //Get indexes
                foreach($currLine as $label)
                {
                    $csvData[$label] = array();
                }

                $keys = $currLine;

                $count++;
            }
            else
            {
                for($j = 0; $j < count($currLine); $j++)
                {
                    $csvData[$keys[$j]][$i] = $currLine[$j];
                }
            }
        }

        fclose($file);

        return $csvData;
    }

    public static function validateNumber($tel, $mobileCheck = false)
    {
        //Remove any spaces or special chars in the given string
        $tel = preg_replace("/[^0-9]/", '', $tel);
        $error = false;

        //check length of number

        //Check if 0 got removed by formatting
        if(strlen($tel) == 9)
        {
            //Add a leading 0
            $tel = '0' . $tel;

            error_log("Added leading 0");
        }

        if(strlen($tel) == 10)
        {
            //check number starts with a zero
            if(!substr($tel, 0, 1) == '0')
            {
                $error = true;
                error_log("TEN DIGIT NUMBER DOES NOT START WITH A ZERO");
            }
        }
        else
        {
            //Tel is not 10 digits
            //check if number starts with two zeros or 27
            if(substr($tel, 0, 2) == '27')
            {
                //starts with 27
                $tel = '0' . substr($tel, 2);

                if(!strlen($tel) == 10)
                {
                    $error = true;
                    error_log("27 CONVERTED TEN DIGIT NUMBER DOES NOT CONTAIN 10 DIGITS");
                }
            }
            elseif(substr($tel, 0, 4) == '0027')
            {
                //Starts with two zeros
                $tel = '0' . substr($tel, 4);

                if(!strlen($tel) == 10)
                {
                    $error = true;
                    error_log("00 CONVERTED TEN DIGIT NUMBER DOES NOT CONTAIN 10 DIGITS");
                }
            }
            elseif(substr($tel, 0, 3) == '+27')
            {
                //Starts with two zeros
                $tel = '0' . substr($tel, 3);

                if(!strlen($tel) == 10)
                {
                    $error = true;
                    error_log("+27 CONVERTED TEN DIGIT NUMBER DOES NOT CONTAIN 10 DIGITS");
                }
            }
            else
            {
                //Unknown format provided;
                $error = true;
                error_log("UNKNOWN FORMAT PROVIDED: '$tel'");
            }
        }

        if(!$error && $mobileCheck)
        {
            //Check that tel number's second digit is higher than a five (cellphone number)
            if(!(substr($tel, 1, 1) > 5))
            {
                //Number is mobile-ish
                //Check if a landline number
                $landLineNumbers = array('Johannesburg Non Telkom' => 10, 'Johannesburg' => 11, 'Pretoria' => 12, 'Middelburg' => 13, 'Rustenburg' => 14, 'Vereeniging' => 16, 'Ermelo' => 17, 'Cape Town' => 21, 'Malmesbury' => 22, 'Worcester' => 23, 'Northern Cape' => 27, 'Swellendam' => 28, 'Durban' => 31, 'Stanger' => 32, 'Pietermaritzburg' => 33, 'Vryheid' => 34, 'Richards Bay' => 35, 'Ladysmith' => 36, 'Port Shepstone' => 39, 'Bisho' => 40, 'Port Elizabeth' => 41, 'Humansdorp' => 42, 'East London' => 43, 'Garden Route' => 44, 'Queenstown' => 45, 'Grahamstown' => 46, 'Umtata' => 47, 'Steynsburg' => 48, 'Bloemfontein' => 51, 'Kimberley' => 53, 'Upington' => 54, 'Kroonstad' => 56, 'Welkom' => 57, 'Bethlehem' => 58);

                //				if(in_array(substr($tel,1,2),$landLineNumbers))
                //				{
                //					//The number matches our array of known land line prefixes
                $error = true;

                //				error_log("SECOND DIGIT OF TEL NUMBER IS NOT GREATER THAN 5");
                //				}
            }
        }

        if($error)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * This function will test if the supplied string is base64 encoded
     *
     * @param $data string  String to test
     *
     * @return bool Whether or not the test was successful
     */
    public static function is_base64_encoded($data)
    {
        if(preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * This function will assist in renaming associative arrays if required.
     *
     * @param   $arr    array   Array whose indexes are to be renamed
     * @param   $index  array   An array to match the index and what it should be renamed as
     *
     * @return  array   The array with its indexes renamed
     *
     * //Tested, Ali is awesome
     */
    public static function renameArray($arr, $index)
    {
        foreach($index as $old => $new)
        {
            $temp = $arr[$old];
            unset($arr[$old]);

            $arr[$new] = $temp;
            unset($temp);
        }

        return $arr;
    }

    /**
     * This function will convert a string representing an associative array into an associative array
     *
     * @param $string           string      The string representing the associative array
     * @param $valueSeparator   string      The separator between the field and its corresponding value
     * @param $delimiter        string      The delimiter separating each field and value pair
     * @param $indexIsFirst     bool        Whether or not the string is in the format 'index:val'
     *
     * @return array
     *
     * @example stringToAssocArray('1-350,9-390.99','-',',')
     */
    public static function stringToAssocArray($string, $valueSeparator = ',', $delimiter = ':', $indexIsFirst = true)
    {
        $chunks = array_chunk(preg_split('/(' . $valueSeparator . '|' . $delimiter . ')/', $string), 2);
        if($indexIsFirst)
        {
            $result = array_combine(array_column($chunks, 0), array_column($chunks, 1));
        }
        else
        {
            $result = array_combine(array_column($chunks, 1), array_column($chunks, 0));
        }

        return $result;
    }

    public static function stringStartsWith($needle, $haystack)
    {
        //String starts with
        return preg_match('/^' . $needle . '/', $haystack); // "^" here means beginning of string
    }

    public static function stringEndsWith($needle, $haystack)
    {
        //String ends with
        return preg_match('/' . $needle . '$/', $haystack); // "$" here means end of string
    }

    public static function cleanInput($input)
    {
        $search = array('@<script[^>]*?>.*?</script>@si',   // Strip out javascript
            '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
            '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
        );

        $output = preg_replace($search, '', $input);
        return $output;
    }

    public static function dirToArray($dir)
    {
        $result = array();

        $cdir = scandir($dir);
        foreach($cdir as $key => $value)
        {
            if(!in_array($value, array(".", "..")))
            {
                if(is_dir($dir . DIRECTORY_SEPARATOR . $value))
                {
                    $result[$value] = self::dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                }
                else
                {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    public static function unzip($file, $fileExtractPath)
    {
        $zip = new ZipArchive;
        if($zip->open($file) === true)
        {
            $zip->extractTo($fileExtractPath);
            $zip->close();

            return true;
        }

        return false;
    }

    /**
     * Simple wrapper function for concurrent request processing with PHP's cURL functions (i.e., using curl_multi* functions.)
     *
     * From: http://adamjonrichardson.com/2013/09/23/making-concurrent-curl-requests-using-phps-curl_multi-functions/
     *
     * @param array $requests Array containing request url, post_data[post_array[]], and settings.
     * @param array $opts     Optional array containing general options for all requests.
     *
     * @return array Array containing keys from requests array and values of arrays each containing data (response, null if response empty or error), info (curl info, null if error), and error (error string if there was an error, otherwise null).
     */
    public static function multi(array $requests, array $opts = [])
    {
        //var_dump($requests);
        // create array for curl handles
        $chs = [];
        // merge general curl options args with defaults
        $opts += [CURLOPT_CONNECTTIMEOUT => 3, CURLOPT_TIMEOUT => 3, CURLOPT_RETURNTRANSFER => 1];
        // create array for responses
        $responses = [];
        // init curl multi handle
        $mh = curl_multi_init();
        // create running flag
        $running = null;
        // cycle through requests and set up
        foreach($requests as $key => $request)
        {
            //var_dump('This is the key: ' . $key);
            // init individual curl handle
            $chs[$key] = curl_init();
            // set url
            curl_setopt($chs[$key], CURLOPT_URL, $request['url']);
            // check for post data and handle if present
            if($request['post_data'])
            {
                curl_setopt($chs[$key], CURLOPT_POST, 1);
                curl_setopt($chs[$key], CURLOPT_POSTFIELDS, $request['post_array']);
            }

            foreach($request['opts'] as $r_opt)
            {
                $opts[] = $r_opt;
            }

            // set opts
            curl_setopt_array($chs[$key], $opts);
            curl_multi_add_handle($mh, $chs[$key]);
        }
        do
        {
            // execute curl requests
            curl_multi_exec($mh, $running);
            // block to avoid needless cycling until change in status
            curl_multi_select($mh);
            // check flag to see if we're done
        }
        while($running > 0);

        // cycle through requests
        foreach($chs as $key => $ch)
        {
            //var_dump($key);
            // handle error
            if(curl_errno($ch))
            {
                $responses[$key] = ['data' => null, 'info' => null, 'error' => curl_error($ch)];
            }
            else
            {
                // save successful response
                $responses[$key] = ['data' => curl_multi_getcontent($ch), 'info' => curl_getinfo($ch), 'error' => null];
            }
            // close individual handle
            curl_multi_remove_handle($mh, $ch);
        }
        // close multi handle
        curl_multi_close($mh);

        if($curlError = curl_error($mh))
        {
            var_dump('Error encountered');
            var_dump($curlError);
        }
        // return response
        return $responses;
    }

    public static function multiRequest($data, $path, $options = array())
    {
        // array of curl handles
        $curly = array();
        // data to be returned
        $result = array();

        // multi handle
        $mh = curl_multi_init();

        // loop through $data and create curl handles
        // then add them to the multi-handle
        foreach($data as $id => $url)
        {
            $file = $path . self::getFileNameFromUrl($url);

            if(file_exists($file))
            {
                unlink($file);
            }

            //$fp = fopen($path, 'x'); Remove

            $curly[$id] = curl_init($url);
            curl_setopt($curly[$id], CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curly[$id], CURLOPT_HEADER, 0);
            curl_setopt($curly[$id], CURLOPT_FILE, fopen($file, 'x'));

            //fclose($fp); Remove

            curl_multi_add_handle($mh, $curly[$id]);

            $result[] = 'Processed image ' . $url;
        }

        // execute the handles
        $running = null;
        do
        {
            curl_multi_exec($mh, $running);
        }
        while($running > 0);


        // get content and remove handles
        foreach($curly as $id => $c)
        {
            curl_multi_remove_handle($mh, $c);
        }

        // all done
        curl_multi_close($mh);

        return $result;
    }

    public static function getFileNameFromUrl($url)
    {
        $pos = strripos($url, '/');
        $filename = substr($url, $pos + 1);

        return $filename;
    }

    /**
     * @param $array array
     * @param $xml   SimpleXMLElement
     *
     * @return SimpleXMLElement
     */
    public static function array_to_xml($array, &$xml)
    {
        foreach($array as $key => $value)
        {
            if(is_array($value))
            {
                if(!is_numeric($key))
                {
                    $subnode = $xml->addChild("$key");
                    self::array_to_xml($value, $subnode);
                }
                else
                {
                    self::array_to_xml($value, $xml);
                }
            }
            else
            {
                $xml->addChild("$key", "$value");
            }
        }
    }

    /**
     * @param array $array the array to be converted
     * @param string? $rootElement if specified will be taken as root element, otherwise defaults to
     *                     <root>
     * @param SimpleXMLElement? if specified content will be appended, used for recursion
     *
     * @return string XML version of $array
     */
    public static function arrayToXml($array, $rootElement = null, $xml = null)
    {
        $_xml = $xml;

        if($_xml === null)
        {
            $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
        }

        foreach($array as $k => $v)
        {
            if(is_array($v))
            { //nested array
                self::arrayToXml($v, $k, $_xml->addChild($k));
            }
            else
            {
                $_xml->addChild($k, $v);
            }
        }

        return $_xml->asXML();
    }

    public static function lowercaseFirstChar($string)
    {
        $firstChar = substr($string, 0, 1);
        $remainingChars = substr($string, 1);

        $firstChar = strtolower($firstChar);

        return $firstChar . $remainingChars;
    }

    public static function remoteFileExists($url)
    {
        $curl = curl_init($url);

        //don't fetch the actual page, you only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, true);

        //do request
        $result = curl_exec($curl);

        $ret = false;

        //if request did not fail
        if($result !== false)
        {
            //if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if($statusCode == 200)
            {
                $ret = true;
            }
        }

        curl_close($curl);

        return $ret;
    }

    public static function convert_ascii($string)
    {
        // Replace Single Curly Quotes
        $search[] = chr(226) . chr(128) . chr(152);
        $replace[] = "'";
        $search[] = chr(226) . chr(128) . chr(153);
        $replace[] = "'";
        // Replace Smart Double Curly Quotes
        $search[] = chr(226) . chr(128) . chr(156);
        $replace[] = '"';
        $search[] = chr(226) . chr(128) . chr(157);
        $replace[] = '"';
        // Replace En Dash
        $search[] = chr(226) . chr(128) . chr(147);
        $replace[] = '--';
        // Replace Em Dash
        $search[] = chr(226) . chr(128) . chr(148);
        $replace[] = '---';
        // Replace Bullet
        $search[] = chr(226) . chr(128) . chr(162);
        $replace[] = '*';
        // Replace Middle Dot
        $search[] = chr(194) . chr(183);
        $replace[] = '*';
        // Replace Ellipsis with three consecutive dots
        $search[] = chr(226) . chr(128) . chr(166);
        $replace[] = '...';
        // Apply Replacements
        $string = str_replace($search, $replace, $string);
        // Remove any non-ASCII Characters
        $string = preg_replace("/[^\x01-\x7F]/", "", $string);
        return $string;
    }

    /**
     * This function will make sure all array indexes specified in $standard are set in $array
     * If $allowSubArray is true, if any of $array's values are an array, they will be set to NULL
     *
     * @param $array            array  The array containing the data that will be work with
     * @param $standard         array   The standard to be enforced
     * @param $allowSubArray    bool    Whether or not values are allowed to be array
     *
     * @return  array
     */
    public static function buildArrayToStandard($array, $standard, $allowSubArray)
    {
        $returnArray = array();

        foreach($standard as $index)
        {
            if(isset($array[$index]))
            {
                if(is_array($array[$index]) && !$allowSubArray)
                {
                    $returnArray[$index] = null;
                }
                else
                {
                    $returnArray[$index] = $array[$index];
                }
            }
            else
            {
                $returnArray[$index] = null;
            }
        }

        return $returnArray;
    }

    public static function getCallingFunction()
    {
        $dbt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        return isset($dbt[2]['function']) ? $dbt[2]['function'] : null;
    }

    public static function getCallingClass()
    {
        $dbt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        return isset($dbt[2]['class']) ? $dbt[2]['class'] : null;
    }

    public static function getTextSizeInKB($text)
    {
        return (int)strlen($text) / 1024;
    }

    public static function getTextSizeInMB($text)
    {
        return (int)strlen($text) / 1000000;
    }

    public static function ArrayFromXml($xml)
    {
        $obj = SimpleXML_Load_String($xml);
        if($obj === false)
        {
            return $xml;
        }

        // GET NAMESPACES, IF ANY
        $nss = $obj->getNamespaces(true);
        if(empty($nss))
        {
            return $xml;
        }

        // CHANGE ns: INTO ns_
        $nsm = array_keys($nss);
        foreach($nsm as $key)
        {
            // A REGULAR EXPRESSION TO MUNG THE XML
            $rgx = '#'               // REGEX DELIMITER
                . '('               // GROUP PATTERN 1
                . '\<'              // LOCATE A LEFT WICKET
                . '/?'              // MAYBE FOLLOWED BY A SLASH
                . preg_quote($key)  // THE NAMESPACE
                . ')'               // END GROUP PATTERN
                . '('               // GROUP PATTERN 2
                . ':{1}'            // A COLON (EXACTLY ONE)
                . ')'               // END GROUP PATTERN
                . '#'               // REGEX DELIMITER
            ;
            // INSERT THE UNDERSCORE INTO THE TAG NAME
            $rep = '$1'          // BACK REFERENCE TO GROUP 1
                . '_'           // LITERAL UNDERSCORE IN PLACE OF GROUP 2
            ;
            // PERFORM THE REPLACEMENT
            $xml = preg_replace($rgx, $rep, $xml);
        }

        return json_decode(json_encode(SimpleXML_Load_String($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    // DocuSigner Functions

    public static function getBrowser()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = 'N/A';

        $browsers = array('/msie/i' => 'Internet explorer', '/firefox/i' => 'Firefox', '/safari/i' => 'Safari', '/chrome/i' => 'Chrome', '/edge/i' => 'Edge', '/opera/i' => 'Opera', '/mobile/i' => 'Mobile browser');

        foreach($browsers as $regex => $value)
        {
            if(preg_match($regex, $user_agent))
            {
                $browser = $value;
            }
        }

        return $browser;
    }

    public static function getUrl()
    {
        $url = @($_SERVER["HTTPS"] != 'on') ? 'http://' . $_SERVER["SERVER_NAME"] : 'https://' . $_SERVER["SERVER_NAME"];
        $url .= ($_SERVER["SERVER_PORT"] !== 80) ? ":" . $_SERVER["SERVER_PORT"] : "";
        $url .= $_SERVER["REQUEST_URI"];

        return substr($url, 0, -4) . '.html';
    }

    public static function get_client_ip_env()
    {
        $ipaddress = '';

        if(getenv('HTTP_CLIENT_IP'))
        {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        }
        elseif(getenv('HTTP_X_FORWARDED_FOR'))
        {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif(getenv('HTTP_X_FORWARDED'))
        {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        }
        elseif(getenv('HTTP_FORWARDED_FOR'))
        {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        }
        elseif(getenv('HTTP_FORWARDED'))
        {
            $ipaddress = getenv('HTTP_FORWARDED');
        }
        elseif(getenv('REMOTE_ADDR'))
        {
            $ipaddress = getenv('REMOTE_ADDR');
        }
        else
        {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }

    public static function emailMembers()
    {

    }
}