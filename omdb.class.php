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

        //json, if you edit
        //this one you will
        //have to rewrite the
        //parse method
        'r' => 'json',

        //Rotten Tomatoes
        //TRUE, FALSE
        'tomatoes' => FALSE,

        //API Key, you must pass this one otherwise the requests will fail
        'apikey' => '',
        
        //api version. Don't edit this one
        //if you don't know what you're doing
        'v' => 1
    ];

    //$params = array(param => value)
    //$timeout = request timeout in seconds
    //$date = see this page for format http://php.net/manual/function.date.php,
    //can be NULL and returns UNIX-time
    public function __construct($params = [], $timeout = 5, $date_format = 'Y-m-d') {

        //Set the API parameters
        $this->setParams($params);

        //Set the cURL timeout
        $this->timeout = $timeout;

        //Set the date format
        $this->date_format = $date_format;
    }

    //Set the parameters for the API request
    //$params = array(param => value)
    public function setParams($params) {
        //Make sure $params is an array
        if(is_array($params) !== TRUE) {
            throw new Exception('$params has to be an array.');
        }
        $validParams = array_keys($this->params);
        foreach($params as $param => $value) {
            //lowered key
            $k = strtolower($param);

            //Check if parameter is valid
            //and make an edit to it
            if(in_array($k, $validParams)) {
                $this->params[$k] = $value;
            }else {
                throw new Exception($param . ' isn\'t a valid parameter.');
            }
        }
    }

    //Set only one parameter
    public function setParam($param, $value) {
        //Sends the parameter as an array to the method setParams
        $this->setParams( [ $param => $value ] );
    }

    //Unset a parameter
    public function unsetParam($param) {
        $this->setParams( [ $param => NULL ] );
    }

    //Create URL, including extra params like id or title params
    // array( array(type, value) )
    private function createURL($p) {
        $params = $this->params;

        //Add all params from $p
        foreach($p as $value) {
            $params[$value[0]] = $value[1];
        }

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
                'Request failed. HTTP CODE: '
                . $info['http_code']
            );
        }
        return json_decode($content);
    }

    //Handels the requests from get_by_* methods
    private function get_data($url) {
        $request = $this->request($url);
        //Parse the request
        $parsed = $this->parse_result($request);

        return $parsed;
    }

    //Get by IMDb id
    //$id = tt[0-9]
    //returns an array
    public function get_by_id($id, $season = NULL, $episode = NULL) {
        //Checks if the IMDb id is valud
        if($this->valid_imdb_id($id) === FALSE) {
            throw new Exception('The IMDb id is invalid.');
        }

        $params = [
            ['i', $id]
        ];

        if($season !== NULL) {
            $params[] = ['Season', $season];

            if($episode !== NULL) {
                $params[] = ['Episode', $episode];
            }
        }

        //Gets the URL
        $url = $this->createURL($params);

        //Gets the data and returns it
        return $this->get_data($url);
    }

    //Get by title
    //returns an array
    public function get_by_title($title) {

        $params = [
            ['t', $title]
        ];

        //Gets the URL
        $url = $this->createURL($params);

        //Gets the data and returns it
        return $this->get_data($url);
    }

    //This function search for multiple movies
    //ignores the plot and tomatoes parameters
    //returns array(
    //      Search => array(Title, Year, imdbID, Type), array(...)
    //              )
    public function search($s, $page = NULL) {
        $params = [
            ['s', $s]
        ];

        if($page !== NULL) {
            $params[] = ['page', $page];
        }

        //Gets the URL
        $url = $this->createURL($params);

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

    //Parses array
    private function parse_array($value) {
        $result = [];
        foreach ($value as $item) {
            $parsedItem = [];
			foreach ($item as $key=>$value) {
				$parsedItem[$key] = $value;
			}
			$result[] = $parsedItem;
        }
        return $result;
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

    //Parses all the result
    //with the connected method
    //and returns an array
    //with the data
    private function parse_result($object) {
        //Rules for how to parse the data
        //array,date,runtime,many,int,float,bool,search and NULL
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
            'Ratings' => 'array',
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
            'tomatoURL' => NULL,
            'DVD' => NULL,
            'BoxOffice' => NULL,
            'Production' => NULL,
            'Website' => NULL,
            'Response' => 'bool',
            'Search' => 'search',
            'Error' => NULL,
            'totalResults' => 'int',
            'totalSeasons' => 'int',
            'Episodes' => 'array',
            'Season' => 'int',
            'Episode' => 'int',
            'seriesID' => NULL
        ];
        //Object to array
        $unParsed = (array)$object;
        //Holds the parsed data
        $data = [];

        //Calls the appropriate method
        //based on the rule connected
        //with the key
        foreach($unParsed as $key => $value) {
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
                    case 'array':
                        $v = $this->parse_array($value);
                        break;
                    case 'search':
                        //There is multiple titles, parses
                        //each of them and adds them to an array
                        $v = [];
                        foreach($value as $arr) {
                            $v[] = $this->parse_result($arr);
                        }
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
