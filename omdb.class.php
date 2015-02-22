<?php
/*
OMDb API PHP Wrapper
Author: Rasmus Lindroth
Version: 0.1

OMDb's Webpage
http://www.omdbapi.com/
*/
class OMDb {
    //API url
    private $url = 'http://www.omdbapi.com/';

    //Request timeout
    private $timeout;

    //Date format
    private $date_format;

    //Default parameters
    private $params = [
        //movie, series, episode or NULL
        'type' => NULL,
        //Year of release or NULL
        'y' => NULL,
        //short, full
        'plot' => 'short',
        //json
        'r' => 'json',
        //true, false
        'tomatoes' => FALSE,
        //api version
        'v' => 1
    ];

    //$params = param => value
    //$timeout = request timeout in seconds
    //$date = see this page for format http://php.net/manual/function.date.php,
    //can be NULL and returns UNIX-time
    public function __construct($params = [], $timeout = 5, $date_format = 'Y-m-d') {
        //Make sure $params is an array
        if(is_array($params) !== TRUE) {
            throw new Exception('$params has to be an array.');
        }

        foreach($params as $param => $value) {
            //Check if parameter is valid
            //and make an edit to it
            if(isset($this->params[$param])) {
                $this->params[$param] = $value;
            }
        }

        $this->timeout = $timeout;
        $this->date_format = $date_format;
    }

    //Create URL, including id or title params
    //$type = i or t
    //$value = tt[0-9] or title
    private function createURL($type, $value) {
        $params = $this->params;
        //Adds title or id search
        $params[$type] = $value;

        $tmp_params = [];
        foreach($params as $param => $value) {
            //Bool to string
            if(is_bool($value)) {
                $value = ($value) ? 'true' : 'false';
            }
            //Ignore NULL values
            if(is_null($value) !== TRUE) {
                $tmp_params[$param] = $value;
            }
        }

        $query = http_build_query($tmp_params);
        return $this->url . '?' . $query;
    }

    //Fetches the url and runs json_decode
    private function request($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);
        $info = curl_getinfo($ch);

        //Checks if the request did succed
        if($info['http_code'] !== 200) {
            throw new Exception(
                'Request faild. Reflectoreturned HTTP CODE: '
                . $info['http_code']
            );
        }
        return json_decode($content);
    }

    //Handels the requests from get_by_* methods
    private function get_data($url) {
        $request = $this->request($url);
        //Parse the request
        $parsed = $this->parse_JSON($request);

        //Checks for errors from the API
        if(isset($parsed['Response'], $parsed['Error']) && $parsed['Response'] === FALSE) {
            throw new Exception('API error: ' . $parsed['Error']);
        }

        return $parsed;
    }

    //Get by IMDb id
    //$id = tt[0-9]
    public function get_by_id($id) {
        //Checks if the IMDb id is valud
        if($this->valid_imdb_id($id) === FALSE) {
            throw new Exception('The IMDb id is invalid.');
        }
        //Gets the URL
        $url = $this->createURL('i', $id);

        //Gets the data and returns it
        return $this->get_data($url);
    }

    //Get by title
    public function get_by_title($title) {
        //Gets the URL
        $url = $this->createURL('t', $title);

        //Gets the data and returns it
        return $this->get_data($url);
    }

    private static function valid_imdb_id($id) {
        return preg_match('/^tt\d+?$/', $id);
    }

    //Explodes string
    //foo, bar returns ['foo', 'bar']
    //foo returns foo
    private function parse_many($value) {
        $arr = explode(', ', $value);
        if(count($arr) === 1) {
            return $arr[0];
        }else {
            return $arr;
        }
    }

    //Parses date to
    //to the specified format
    private function parse_date($date) {
        $unix = strtotime($date);
        if(is_null($date) === FALSE) {
            return date($this->date_format, $unix);
        }else {
            return $unix;
        }
    }

    //Parses runtime to return a int with
    //the minutes
    private function parse_runtime($value) {
        return (int)strstr($value, ' min', true);
    }

    //String (with comma) to int
    private function parse_int($value) {
        return (int)str_replace(',', '', $value);
    }

    //Value to float
    private function parse_float($value) {
        return (float)$value;
    }

    //String to Bool
    private function parse_bool($value) {
        if(trim(strtolower($value)) === 'true') {
            return TRUE;
        }else {
            return FALSE;
        }
    }

    //Parses the JSON object
    //and returns an array
    //with the data
    private function parse_JSON($json) {
        //Rules for how to parse the data
        //date,runtime,many,int,float,bool and NULL
        $rules = [
            'Title' => NULL,
            'Year' => NULL,
            'Rated' => NULL,
            'Released' => 'date',
            'Runtime' => 'runtime',
            'Genre' => 'many',
            'Director' => 'many',
            'Writer' => 'many',
            'Actors' => 'many',
            'Plot' => NULL,
            'Language' => 'many',
            'Country' => 'many',
            'Awards' => NULL,
            'Poster' => NULL,
            'Metascore' => 'int',
            'imdbRating' => 'float',
            'imdbVotes' => 'int',
            'imdbID' => NULL,
            'Type' => NULL,
            'tomatoMeter' => 'int',
            'tomatoImage' => NULL,
            'tomatoRating' => 'float',
            'tomatoReviews' => 'int',
            'tomatoFresh' => 'int',
            'tomatoRotten' => 'int',
            'tomatoConsensus' => NULL,
            'tomatoUserMeter' => 'int',
            'tomatoUserRating' => 'float',
            'tomatoUserReviews' => 'int',
            'DVD' => NULL,
            'BoxOffice' => NULL,
            'Production' => NULL,
            'Website' => NULL,
            'Response' => 'bool',
            'Error' => NULL,
        ];
        //Object to array
        $json = (array)$json;
        //Holds the parsed data
        $data = [];

        //Calls the appropriate method
        //based on the rule connected
        //with the key
        foreach($json as $key => $value) {
            if($value === 'N/A') {
                $data[$key] = NULL;
            }else {
                $v = $value;
                switch($rules[$key]) {
                    case 'many':
                        $v = $this->parse_many($value);
                        break;
                    case 'date':
                        $v = $this->parse_date($value);
                        break;
                    case 'runtime':
                        $v = $this->parse_runtime($value);
                        break;
                    case 'int':
                        $v = $this->parse_int($value);
                        break;
                    case 'float':
                        $v = $this->parse_float($value);
                        break;
                    case 'bool':
                        $v = $this->parse_bool($value);
                        break;
                    default:
                        $v = $value;
                }
                $data[$key] = $v;
            }
        }
        return $data;
    }
}
?>