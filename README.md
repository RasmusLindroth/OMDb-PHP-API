# OMDb-PHP-API
A wrapper for the OMDb API that gets movie info from IMDb and Rotten Tomatoes.

### How to use
Do not forget to replace your apikey that you must get from [the OMDb website](http://www.omdbapi.com/apikey.aspx).
```php
//Init OMDb
$omdb = new OMDb();

//Set parameters, include data from Rotten Tomatoes and show full plot
$omdb->setParams( ['tomatoes' => TRUE, 'plot' => 'full', 'apikey' => '00000000'] );

//Only set one parameter, the movie has to be from 2015
$omdb->setParam( 'y', 2015 );

//Remove one parameter
$omdb->unsetParam('y');

//Get by title
$movie = $omdb->get_by_title( 'Pulp Fiction' );

//Get by IMDb id
$movie = $omdb->get_by_id( 'tt0057012' );

//Get all episodes in season 1, (also works for get_by_title)
$movie = $omdb->get_by_id('tt2085059', 1 );

//Get episode 2 for season 1, (also works for get_by_title)
$movie = $omdb->get_by_id('tt2085059', 1, 2 );

//Search for (multiple) movies
//ignores the params plot and tomatoes
$movie = $omdb->search( 'James Bond' );

//Search with pagination
$movie = $omdb->search( 'Alfred', 2 );
```

### Parameters for the constructor (can be left empty, except for your apikey)
```php
$omdb = new OMDb($params = ['apikey' => '00000000'], $timeout = 5, $date_format = 'Y-m-d');
```

<b>params</b>: has to be an array, see API parameters for parameter reference<br>
<b>timeout</b>: cURL/request timeout in seconds<br>
<b>date_format</b>: http://php.net/manual/function.date.php and NULL for UNIX time


### API parameters
<table>
    <tr>
        <th>Parameter</th>
        <th>Valid Options</th>
        <th>Default value</th>
        <th>Description</th>
    </tr>
    <tr>
        <td>apikey</td>
        <td></td>
        <td>00000000</td>
        <td>API key as received from <a href="http://www.omdbapi.com/apikey.aspx">the OMDb website</a>.</td>
    </tr>
    <tr>
        <td>type</td>
        <td>movie, series, episode</td>
        <td>NULL</td>
        <td>Type of result</td>
    </tr>
    <tr>
        <td>y</td>
        <td></td>
        <td>NULL</td>
        <td>Year of release </td>
    </tr>
    <tr>
        <td>plot</td>
        <td>short, full</td>
        <td>short</td>
        <td>Plot-length, ignored when you use the search-method</td>
    </tr>
    <tr>
        <td>tomatoes</td>
        <td>TRUE, FALSE</td>
        <td>FALSE</td>
        <td>Include Rotten Tomatoes, ignored when you use the search-method</td>
    </tr>
</table>

### Methods
```php
//Returns array(Title, Year, imdbID, Type, ...)
$omdb->get_by_title( 'title', [, $season = NULL, $episode = NULL] );
$omdb->get_by_id( 'tt[0-9]', [, $season = NULL, $episode = NULL] );

//Returns array(
//      'Search' => array(Title, Year, imdbID, Type), array(...)
//             )
$omdb->search( 'Search term', [, $page = NULL] );
```

### Errors
This class throws exceptions if you for instance sends a string to a function
that's expecting an array. If the API runs in to some error I have choosen not
to throw an exception. You will have to implent it yourself.

You can check for API errors if the value of the key 'Response' is TRUE or if
the key 'Error' exists in the result.

Example:
```php
$omdb->get_by_title( 'gasdgasdgadgasdgasdg' );

//Returns
array(
    'Response' => FALSE,
    'Error' => 'Movie not found!'
);
```

### Output example
```php
array (size=34)
  'Title' => string 'Dr. Strangelove or: How I Learned to Stop Worrying and Love the Bomb'
  'Year' => string '1964'
  'Rated' => string 'PG'
  'Released' => string '1964-01-29'
  'Runtime' => int 95
  'Genre' =>
    array (size=2)
      0 => string 'Comedy'
      1 => string 'War'
  'Director' => string 'Stanley Kubrick'
  'Writer' =>
    array (size=4)
      0 => string 'Stanley Kubrick (screenplay)'
      1 => string 'Terry Southern (screenplay)'
      2 => string 'Peter George (screenplay)'
      3 => string 'Peter George (based on the book: "Red Alert" by)'
  'Actors' =>
    array (size=4)
      0 => string 'Peter Sellers'
      1 => string 'George C. Scott'
      2 => string 'Sterling Hayden'
      3 => string 'Keenan Wynn'
  'Plot' => string 'An insane general triggers a path to nuclear holocaust that a war room full of politicians and generals frantically try to stop.'
  'Language' =>
    array (size=2)
      0 => string 'English'
      1 => string 'Russian'
  'Country' =>
    array (size=2)
      0 => string 'USA'
      1 => string 'UK'
  'Awards' => string 'Nominated for 4 Oscars. Another 15 wins & 4 nominations.'
  'Poster' => string 'http://ia.media-imdb.com/images/M/MV5BMTU2ODM2NTkxNF5BMl5BanBnXkFtZTcwOTMwMzU3Mg@@._V1_SX300.jpg'
  'Metascore' => int 96
  'imdbRating' => float 8.5
  'imdbVotes' => int 291737
  'imdbID' => string 'tt0057012'
  'Type' => string 'movie'
  'tomatoMeter' => int 99
  'tomatoImage' => string 'certified'
  'tomatoRating' => float 9
  'tomatoReviews' => int 68
  'tomatoFresh' => int 67
  'tomatoRotten' => int 1
  'tomatoConsensus' => string 'Stanley Kubrick's brilliant Cold War satire remains as funny and razor-sharp today as it was in 1964.'
  'tomatoUserMeter' => int 94
  'tomatoUserRating' => float 4.2
  'tomatoUserReviews' => int 204995
  'DVD' => string '02 Nov 2004'
  'BoxOffice' => null
  'Production' => string 'Sony Pictures'
  'Website' => null
  'Response' => boolean true
  'Ratings' =>
    array(3)
      0 =>
        array(2)
          Source => string "Internet Movie Database"
          value => string "8.9/10"
      1 =>
        array(2)
          Source => string "Rotten Tomatoes"
          value => string "94%"
      2 =>
        array(2)
          Source => string "Metacritic"
          value => string "94/100"
  'tomatoURL' => string 'http://www.rottentomatoes.com/m/dr_strangelove/'
```

Thanks to Brian Fritz, the author of OMDb APIs<br>
The API webpage
http://www.omdbapi.com/

This PHP wrapper is made by Rasmus Lindroth
